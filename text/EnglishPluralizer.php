<?php
namespace phootwork\lang\text;

/**
 * Standard replacement English pluralizer class. Based on the links below
 *
 * @link http://kuwamoto.org/2007/12/17/improved-pluralizing-in-php-actionscript-and-ror/
 * @link http://blogs.msdn.com/dmitryr/archive/2007/01/11/simple-english-noun-pluralizer-in-c.aspx
 * @link http://api.cakephp.org/view_source/inflector/
 *
 * @author paul.hanssen
 * @author Cristiano Cinotti
 */
class EnglishPluralizer implements Pluralizer {
	/**
	 * @var array
	 */
	protected $plural = [
		'(ind|vert)ex' => '\1ices',
		'(alumn|bacill|cact|foc|fung|nucle|radi|stimul|syllab|termin|vir)us' => '\1i',
		'(buffal|tomat)o' => '\1oes',

		'x' => 'xes',
		'ch' => 'ches',
		'sh' => 'shes',
		'ss' => 'sses',

		'ay' => 'ays',
		'ey' => 'eys',
		'iy' => 'iys',
		'oy' => 'oys',
		'uy' => 'uys',
		'y' => 'ies',

		'ao' => 'aos',
		'eo' => 'eos',
		'io' => 'ios',
		'oo' => 'oos',
		'uo' => 'uos',
		'o' => 'os',

		'us' => 'uses',

		'cis' => 'ces',
		'sis' => 'ses',
		'xis' => 'xes',

		'zoon' => 'zoa',

		'itis' => 'itis',
		'ois' => 'ois',
		'pox' => 'pox',
		'ox' => 'oxes',

		'foot' => 'feet',
		'goose' => 'geese',
		'tooth' => 'teeth',
		'quiz' => 'quizzes',
		'alias' => 'aliases',

		'alf' => 'alves',
		'elf' => 'elves',
		'olf' => 'olves',
		'arf' => 'arves',
		'nife' => 'nives',
		'life' => 'lives'
	];

	protected $irregular = [
		'matrix' => 'matrices',
		'leaf' => 'leaves',
		'loaf' => 'loaves',
		'move' => 'moves',
		'foot' => 'feet',
		'goose' => 'geese',
		'genus' => 'genera',
		'sex' => 'sexes',
		'ox' => 'oxen',
		'child' => 'children',
		'man' => 'men',
		'tooth' => 'teeth',
		'person' => 'people',
		'wife' => 'wives',
		'mythos' => 'mythoi',
		'testis' => 'testes',
		'numen' => 'numina',
		'quiz' => 'quizzes',
		'alias' => 'aliases',
	];

	protected $uncountable = [
		'sheep',
		'fish',
		'deer',
		'series',
		'species',
		'money',
		'rice',
		'information',
		'equipment',
		'news',
		'people',
	];

	/** @var array */
	protected $singular;

	/**
	 * Array of words that could be ambiguously interpreted. Eg:
	 * `isPlural` method can't recognize 'menus' as plural, because it considers 'menus' as the
	 * singular of 'menuses'.
	 *
	 * @var array
	 */
	protected $ambiguous = [
		'menu' => 'menus'
	];


	public function __construct() {
		// Create the $singular array
		$this->singular = array_flip($this->plural);
		$this->singular = array_slice($this->singular, 3);

		$reg = [
			'(ind|vert)ices' => '\1ex',
			'(alumn|bacill|cact|foc|fung|nucle|radi|stimul|syllab|termin|vir)i' => '\1us',
			'(buffal|tomat)oes' => '\1o'
		];

		$this->singular = array_merge($reg, $this->singular);

		// We have an ambiguity: -xes is the plural form of -x or -xis. By now, we choose -x. Words with -xis suffix
		// should be added to the $ambiguous array.
		$this->singular['xes'] = 'x';
	}

	/**
	 * Generate a plural name based on the passed in root.
	 *
	 * @param  string $root The root that needs to be pluralized (e.g. Author)
	 * @return string The plural form of $root (e.g. Authors).
	 * @throws \InvalidArgumentException If the parameter is not a string.
	 */
	public function getPluralForm($root) {
		$pluralForm = $root;

		if (!is_string($root)) {
			throw new \InvalidArgumentException('The pluralizer expects a string.');
		}

		if (!in_array(strtolower($root), $this->uncountable)) {
			// This check must be run before `checkIrregularForm` call
			if (!$this->isAmbiguousPlural($root)) {
				if (null !== $replacement = $this->checkIrregularForm($root, $this->irregular)) {
					$pluralForm = $replacement;
				} elseif (null !== $replacement = $this->checkIrregularSuffix($root, $this->plural)) {
					$pluralForm = $replacement;
				} elseif (!$this->isPlural($root)) {
					// fallback to naive pluralization
					$pluralForm = $root . 's';
				}
			}
		}

		return $pluralForm;
	}

	/**
	 * Generate a singular name based on the passed in root.
	 *
	 * @param  string $root The root that needs to be pluralized (e.g. Author)
	 * @return string The singular form of $root (e.g. Authors).
	 * @throws \InvalidArgumentException If the parameter is not a string.
	 */
	public function getSingularForm($root) {
		$singularForm = $root;

		if (!is_string($root)) {
			throw new \InvalidArgumentException('The pluralizer expects a string.');
		}

		if (!in_array(strtolower($root), $this->uncountable)) {
			if (null !== $replacement = $this->checkIrregularForm($root, array_flip($this->irregular))) {
				$singularForm = $replacement;
			} elseif (null !== $replacement = $this->checkIrregularSuffix($root, $this->singular)) {
				$singularForm = $replacement;
			} elseif (!$this->isSingular($root)) {
				// fallback to naive singularization
				return substr($root, 0, -1);
			}
		}

		return $singularForm;
	}

	/**
	 * Check if $root word is plural.
	 *
	 * @param string $root
	 *
	 * @return bool
	 */
	public function isPlural($root) {
		$out = false;

		if ('' !== $root) {
			if (in_array(strtolower($root), $this->uncountable)) {
				$out = true;
			} else {
				$out = $this->isIrregular($this->irregular, $root);

				if (!$out) {
					$out = $this->isIrregular(array_keys($this->singular), $root);
				}

				if (!$out && 's' == $root[strlen($root) - 1]) {
					$out = true;
				}
			}
		}

		return $out;
	}

	/**
	 * Check if $root word is singular.
	 *
	 * @param $root
	 *
	 * @return bool
	 */
	public function isSingular($root) {
		$out = false;

		if ('' === $root) {
			$out = true;
		} elseif (in_array(strtolower($root), $this->uncountable)) {
			$out = true;
		} elseif (!$this->isAmbiguousPlural($root)) {
			$out = $this->isIrregular($this->irregular, $root);

			if (!$out) {
				$out = $this->isIrregular(array_keys($this->plural), $root);
			}

			if (!$out && 's' !== $root[strlen($root) - 1]) {
				$out = true;
			}
		}

		return $out;
	}

	/**
	 * Pluralize/Singularize irregular forms.
	 *
	 * @param string $root The string to pluralize/singularize
	 * @param array $irregular Array of irregular forms
	 *
	 * @return null|string
	 */
	private function checkIrregularForm($root, $irregular) {
		foreach ($irregular as $pattern => $result) {
			$searchPattern = '/' . $pattern . '$/i';
			if ($root !== $replacement = preg_replace($searchPattern, $result, $root)) {
				// look at the first char and see if it's upper case
				// I know it won't handle more than one upper case char here (but I'm OK with that)
				if (preg_match('/^[A-Z]/', $root)) {
					$replacement = ucfirst($replacement);
				}

				return $replacement;
			}
		}

		return null;
	}

	/**
	 * @param string $root
	 * @param array $irregular Array of irregular suffixes
	 *
	 * @return null|string
	 */
	private function checkIrregularSuffix($root, $irregular) {
		foreach ($irregular as $pattern => $result) {
			$searchPattern = '/' . $pattern . '$/i';
			if ($root !== $replacement = preg_replace($searchPattern, $result, $root)) {
				return $replacement;
			}
		}

		return null;
	}

	/**
	 * @param $root
	 *
	 * @return bool
	 */
	private function isAmbiguousPlural($root) {
		foreach ($this->ambiguous as $pattern) {
			if (preg_match('/' . $pattern . '$/i', $root)) {
				return true;
			}
		}

		return false;
	}

	/**
	 * @param array $irregular
	 * @param $root
	 *
	 * @return bool
	 */
	private function isIrregular($irregular, $root) {
		foreach ($irregular as $pattern) {
			if (preg_match('/' . $pattern . '$/i', $root)) {
				return true;
			}
		}

		return false;
	}
}
