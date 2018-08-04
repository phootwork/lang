<?php
namespace phootwork\lang;

use phootwork\lang\text\CheckerTrait;
use phootwork\lang\text\EnglishPluralizer;
use phootwork\lang\text\Pluralizer;

/**
 * Object representation of an immutable String
 *
 * @author gossi
 */
class Text implements Comparable {

	use CheckerTrait;

	private $string;
	private $encoding;

	/**
	 * Initializes a String object and assigns both string and encoding properties
	 * the supplied values. $string is cast to a string prior to assignment, and if
	 * $encoding is not specified, it defaults to mb_internal_encoding(). Throws
	 * an InvalidArgumentException if the first argument is an array or object
	 * without a __toString method.
	 *
	 * @param mixed $string Value to modify, after being cast to string
	 * @param string $encoding The character encoding
	 * @throws \InvalidArgumentException if an array or object without a __toString method is passed as the first argument
	 */
	public function __construct($string = '', $encoding = null) {
		if (is_array($string)) {
			throw new \InvalidArgumentException('Passed value cannot be an array');
		} elseif (is_object($string) && !method_exists($string, '__toString')) {
			throw new \InvalidArgumentException('Passed object must have a __toString method');
		}

		$this->string = (string) $string;
		$this->encoding = $encoding ?: mb_internal_encoding();
	}

	public function __clone() {
		return new Text($this->string, $this->encoding);
	}

	/**
	 * Static initializing a String object.
	 *
	 * @see Text::__construct()
	 * @param mixed $string
	 * @param string $encoding
	 * @return Text
	 */
	public static function create($string, $encoding = null) {
		return new Text($string, $encoding);
	}

	/**
	 * Returns the used encoding
	 * @return string
	 */
	public function getEncoding() {
		return $this->encoding;
	}

	/**
	 * Get string length
	 *
	 * <code>
	 * $str = new Text('Hello World!');<br>
	 * $str->length(); // 12
	 *
	 * $str = new Text('いちりんしゃ');<br>
	 * $str->length(); // 6
	 * </code>
	 *
	 * @return int Returns the length
	 */
	public function length() {
		return mb_strlen($this->string, $this->encoding);
	}

	/**
	 * Appends <code>$string</code> and returns as a new <code>Text</code>
	 *
	 * @param string $string
	 * @return Text
	 */
	public function append($string) {
		return new Text($this->string . $string, $this->encoding);
	}

	/**
	 * Prepends <code>$string</code> and returns as a new <code>Text</code>
	 *
	 * @param string $string
	 * @return Text
	 */
	public function prepend($string) {
		return new Text($string . $this->string, $this->encoding);
	}

	/**
	 * Inserts a substring at the given index
	 *
	 * <code>
	 * $str = new Text('Hello World!');<br>
	 * $str->insert('to this ', 5); // Hello to this World!
	 * </code>
	 *
	 * @param string|Text $substring
	 * @param int $index
	 * @return Text
	 */
	public function insert($substring, $index) {
		if ($index <= 0) {
			return $this->prepend($substring);
		}

		if ($index > $this->length()) {
			return $this->append($substring);
		}

		$start = mb_substr($this->string, 0, $index, $this->encoding);
		$end = mb_substr($this->string, $index, $this->length(), $this->encoding);
		return new Text($start . $substring . $end);
	}

	//
	//
	// COMPARISON
	//
	//

	/**
	 * Compares this string to another
	 *
	 * @see \phootwork\lang\Comparable::compareTo()
	 */
	public function compareTo($compare) {
		return $this->compare($compare);
	}

	/**
	 * Compares this string to another string, ignoring the case
	 *
	 * @param mixed $compare
	 * @return int Return Values:<br>
	 * 		&lt; 0 if the object is less than comparison<br>
	 *  	&gt; 0 if the object is greater than comparison<br>
	 * 		0 if they are equal.
	 */
	public function compareCaseInsensitive($compare) {
		return $this->compare($compare, 'strcasecmp');
	}

	/**
	 * Compares this string to another
	 *
	 * @param string $compare string to compare to
	 * @param callable $callback
	 * @return int
	 */
	public function compare($compare, callable $callback = null) {
		if ($callback === null) {
			$callback = 'strcmp';
		}
		return $callback($this->string, (string) $compare);
	}

	/**
	 * Checks whether the string and the given object are equal
	 *
	 * @param mixed $string
	 * @return boolean
	 */
	public function equals($string) {
		return $this->compareTo($string) === 0;
	}

	/**
	 * Checks whether the string and the given object are equal ignoring the case
	 *
	 * @param mixed $string
	 * @return boolean
	 */
	public function equalsIgnoreCase($string) {
		return $this->compareCaseInsensitive($string) === 0;
	}

	//
	//
	// SLICING AND SUBSTRING
	//
	//

	/**
	 * Slices a piece of the string from a given offset with a specified length.
	 * If no length is given, the String is sliced to its maximum length.
	 *
	 * @see #substring
	 * @param int $offset
	 * @param int $length
	 * @return Text
	 */
	public function slice($offset, $length = null) {
		$offset = $this->prepareOffset($offset);
		$length = $this->prepareLength($offset, $length);

		if ($length === 0) {
			return new Text('', $this->encoding);
		}

		return new Text(mb_substr($this->string, $offset, $length, $this->encoding), $this->encoding);
	}

	/**
	 * Slices a piece of the string from a given start to an end.
	 * If no length is given, the String is sliced to its maximum length.
	 *
	 * @see #slice
	 * @param int $start
	 * @param int $end
	 * @return Text
	 */
	public function substring($start, $end = null) {
		$length = $this->length();
		if ($end < 0) {
			$end = $length + $end;
		}
		$end = $end !== null ? min($end, $length) : $length;
		$start = min($start, $end);
		$end = max($start, $end);
		$end = $end - $start;

		return new Text(mb_substr($this->string, $start, $end, $this->encoding), $this->encoding);
	}

	/**
	 * Count the number of substring occurrences.
	 *
	 * @param string|Text $substring The substring to count the occurrencies
	 * @param boolean $caseSensitive Force case-sensitivity
	 * @return int
	 */
	public function countSubstring($substring, $caseSensitive = true) {
		$this->verifyNotEmpty($substring, '$substring');
		if ($caseSensitive) {
			return mb_substr_count($this->string, $substring, $this->encoding);
		}
		$str = mb_strtoupper($this->string, $this->encoding);
		$substring = mb_strtoupper($substring, $this->encoding);
		return mb_substr_count($str, $substring, $this->encoding);
	}

	//
	//
	// REPLACING
	//
	//

	/**
	 * Replace all occurrences of the search string with the replacement string
	 *
	 * @see #supplant
	 * @param Arrayable|Text|array|string $search
	 * 		The value being searched for, otherwise known as the needle. An array may be used
	 * 		to designate multiple needles.
	 * @param Arrayable|Text|array|string $replace
	 * 		The replacement value that replaces found search values. An array may be used to
	 * 		designate multiple replacements.
	 *
	 * @return Text
	 */
	public function replace($search, $replace) {
		if ($search instanceof Text) {
			$search = $search->toString();
		} else if ($search instanceof Arrayable) {
			$search = $search->toArray();
		}

		if ($replace instanceof Text) {
			$replace = $replace->toString();
		} else if ($replace instanceof Arrayable) {
			$replace = $replace->toArray();
		}

		return new Text(str_replace($search, $replace, $this->string), $this->encoding);
	}

	/**
	 * Replaces all occurences of given replacement map. Keys will be replaced with its values.
	 *
	 * @param array $map the replacements. Keys will be replaced with its value.
	 * @return Text
	 */
	public function supplant(array $map) {
		return new Text(str_replace(array_keys($map), array_values($map), $this->string), $this->encoding);
	}

	/**
	 * Replace text within a portion of a string.
	 *
	 * @param string|Text $replacement
	 * @param int $offset
	 * @param int|null $length
	 * @return Text
	 * @throws \InvalidArgumentException If $offset is greater then the string length or $length is too small.
	 */
	public function splice($replacement, $offset, $length = null) {
		$offset = $this->prepareOffset($offset);
		$length = $this->prepareLength($offset, $length);

		$start = $this->substring(0, $offset);
		$end = $this->substring($offset + $length);

		return new Text($start . $replacement . $end);
	}

	//
	//
	// SEARCH
	//
	//

	/**
	 * Returns the character at the given zero-related index
	 *
	 * <code>
	 * $str = new Text('Hello World!');<br>
	 * $str->at(6); // W
	 *
	 * $str = new Text('いちりんしゃ');<br>
	 * $str->at(4) // し
	 * </code>
	 *
	 * @param int $index zero-related index
	 * @return string the found character
	 */
	public function at($index) {
		return mb_substr($this->string, $index, 1, $this->encoding);
	}

	/**
	 * Returns an ArrayObject consisting of the characters in the string.
	 *
	 * @return ArrayObject An ArrayObject of all chars
	 */
	public function chars() {
		$chars = new ArrayObject();
		for ($i = 0, $l = $this->length(); $i < $l; $i++) {
			$chars->push($this->at($i));
		}
		return $chars;
	}

	/**
	 * Returns the index of a given string, starting at the optional zero-related offset
	 *
	 * @param string $string
	 * @param int $offset zero-related offset
	 * @return int|boolean int for the index or false if the given string doesn't occur
	 */
	public function indexOf($string, $offset = 0) {
		$offset = $this->prepareOffset($offset);

		if ($string == '') {
			return $offset;
		}

		return mb_strpos($this->string, (string) $string, $offset, $this->encoding);
	}

	/**
	 * Returns the last index of a given string, starting at the optional offset
	 *
	 * @param string $string
	 * @param int $offset
	 * @return int|boolean int for the index or false if the given string doesn't occur
	 */
	public function lastIndexOf($string, $offset = null) {
		if (null === $offset) {
			$offset = $this->length();
		} else {
			$offset = $this->prepareOffset($offset);
		}

		if ($string === '') {
			return $offset;
		}

		/* Converts $offset to a negative offset as strrpos has a different
		 * behavior for positive offsets. */
		return mb_strrpos($this, (string) $string, $offset - $this->length(), $this->encoding);
	}

	/**
	 * Checks whether the string starts with the given string. Case sensitive!
	 *
	 * @see Text::startsWithIgnoreCase()
	 * @param string|Text $substring The substring to look for
	 * @return boolean
	 */
	public function startsWith($substring) {
		$substringLength = mb_strlen($substring, $this->encoding);
		$startOfStr = mb_substr($this->string, 0, $substringLength, $this->encoding);

		return (string) $substring === $startOfStr;
	}

	/**
	 * Checks whether the string starts with the given string. Ignores case.
	 *
	 * @see Text::startsWith()
	 * @param string|Text $substring The substring to look for
	 * @return boolean
	 */
	public function startsWithIgnoreCase($substring) {
		$substring = mb_strtolower($substring, $this->encoding);
		$substringLength = mb_strlen($substring, $this->encoding);
		$startOfStr = mb_strtolower(mb_substr($this->string, 0, $substringLength, $this->encoding));

		return (string) $substring === $startOfStr;
	}

	/**
	 * Checks whether the string ends with the given string. Case sensitive!
	 *
	 * @see Text::endsWithIgnoreCase()
	 * @param string|Text $substring The substring to look for
	 * @return boolean
	 */
	public function endsWith($substring) {
		$substringLength = mb_strlen($substring, $this->encoding);
		$endOfStr = mb_substr($this->string, $this->length() - $substringLength, $substringLength, $this->encoding);

		return (string) $substring === $endOfStr;
	}

	/**
	 * Checks whether the string ends with the given string. Ingores case.
	 *
	 * @see Text::endsWith()
	 * @param string|Text $substring The substring to look for
	 * @return boolean
	 */
	public function endsWithIgnoreCase($substring) {
		$substring = mb_strtolower($substring, $this->encoding);
		$substringLength = mb_strlen($substring, $this->encoding);
		$endOfStr = mb_strtolower(mb_substr($this->string, $this->length() - $substringLength, $substringLength, $this->encoding));

		return (string) $substring === $endOfStr;
	}

	/**
	 * Checks whether the given string occurs
	 *
	 * @param string $string
	 * @return boolean
	 */
	public function contains($string) {
		return $this->indexOf($string) !== false;
	}

	/**
	 * Performs a regular expression matching with the given regexp
	 *
	 * @param string $regexp
	 * @return boolean
	 */
	public function match($regexp) {
		return (bool) preg_match($regexp, $this->string);
	}

	//
	//
	// STRING OPERATIONS
	//
	//

	/**
	 * Strip whitespace (or other characters) from the beginning and end of the string
	 *
	 * @param string $characters
	 * 		Optionally, the stripped characters can also be specified using the mask parameter.
	 * 		Simply list all characters that you want to be stripped. With .. you can specify a
	 * 		range of characters.
	 *
	 * @return Text
	 */
	public function trim($characters = " \t\n\r\v\0") {
		return new Text(trim($this->string, (string) $characters), $this->encoding);
	}

	/**
	 * Strip whitespace (or other characters) from the beginning of the string
	 *
	 * @param string $characters
	 * 		Optionally, the stripped characters can also be specified using the mask parameter.
	 * 		Simply list all characters that you want to be stripped. With .. you can specify a
	 * 		range of characters.
	 *
	 * @return Text
	 */
	public function trimStart($characters = " \t\n\r\v\0") {
		return new Text(ltrim($this->string, (string) $characters), $this->encoding);
	}

	/**
	 * Strip whitespace (or other characters) from the end of the string
	 *
	 * @param string $characters
	 * 		Optionally, the stripped characters can also be specified using the mask parameter.
	 * 		Simply list all characters that you want to be stripped. With .. you can specify a
	 * 		range of characters.
	 *
	 * @return Text
	 */
	public function trimEnd($characters = " \t\n\r\v\0") {
		return new Text(rtrim($this->string, (string) $characters), $this->encoding);
	}

	/**
	 * Adds padding to the start and end
	 *
	 * @param int $length
	 * @param string $padding
	 * @return Text
	 */
	public function pad($length, $padding = ' ') {
		$len = $length - $this->length();
		return $this->applyPadding(floor($len / 2), ceil($len / 2), $padding);
	}

	/**
	 * Adds padding to the start
	 *
	 * @param int $length
	 * @param string $padding
	 * @return Text
	 */
	public function padStart($length, $padding = ' ') {
		return $this->applyPadding($length - $this->length(), 0, $padding);
	}

	/**
	 * Adds padding to the end
	 *
	 * @param int $length
	 * @param string $padding
	 * @return Text
	 */
	public function padEnd($length, $padding = ' ') {
		return $this->applyPadding(0, $length - $this->length(), $padding);
	}

	/**
	 * Adds the specified amount of left and right padding to the given string.
	 * The default character used is a space.
	 *
	 * @see https://github.com/danielstjules/Stringy/blob/master/src/Stringy.php
	 * @param int $left Length of left padding
	 * @param int $right Length of right padding
	 * @param string $padStr String used to pad
	 * @return Text the padded string
	 */
	protected function applyPadding($left = 0, $right = 0, $padStr = ' ') {
		$length = mb_strlen($padStr, $this->encoding);
		$strLength = $this->length();
		$paddedLength = $strLength + $left + $right;
		if (!$length || $paddedLength <= $strLength) {
			return $this;
		}

		$leftPadding = mb_substr(str_repeat($padStr, ceil($left / $length)), 0, $left, $this->encoding);
		$rightPadding = mb_substr(str_repeat($padStr, ceil($right / $length)), 0, $right, $this->encoding);
		return new Text($leftPadding . $this->string . $rightPadding);
	}

	/**
	 * Ensures a given substring at the start of the string
	 *
	 * @param string $substring
	 * @return Text
	 */
	public function ensureStart($substring) {
		if (!$this->startsWith($substring)) {
			return $this->prepend($substring);
		}
		return $this;
	}

	/**
	 * Ensures a given substring at the end of the string
	 *
	 * @param string $substring
	 * @return Text
	 */
	public function ensureEnd($substring) {
		if (!$this->endsWith($substring)) {
			return $this->append($substring);
		}
		return $this;
	}

	/**
	 * Returns a copy of the string wrapped at a given number of characters
	 *
	 * @param int $width The number of characters at which the string will be wrapped.
	 * @param string $break The line is broken using the optional break parameter.
	 * @param bool $cut
	 * 		If the cut is set to TRUE, the string is always wrapped at or before the specified
	 * 		width. So if you have a word that is larger than the given width, it is broken apart.
	 * @return Text Returns the string wrapped at the specified length.
	 */
	public function wrapWords($width = 75, $break = "\n", $cut = false) {
		return new Text(wordwrap($this->string, $width, $break, $cut), $this->encoding);
	}

	/**
	 * Repeat the string $times times. If $times is 0, it returns ''.
	 *
	 * @param int $multiplier
	 * @return Text
	 * @throws \InvalidArgumentException If $times is negative.
	 */
	public function repeat($multiplier) {
		$this->verifyNotNegative($multiplier, 'Number of repetitions');
		return new Text(str_repeat($this->string, $multiplier), $this->encoding);
	}

	/**
	 * Reverses the character order
	 *
	 * @return Text
	 */
	public function reverse() {
		return new Text(strrev($this->string), $this->encoding);
	}

	/**
	 * Truncates the string with a substring and ensures it doesn't exceed the given length
	 *
	 * @param int $length
	 * @param string $substring
	 * @return Text
	 */
	public function truncate($length, $substring = '') {
		if ($this->length() <= $length) {
			return new Text($this->string, $this->encoding);
		}

		$substrLen = mb_strlen($substring, $this->encoding);

		if ($this->length() + $substrLen > $length) {
			$length -= $substrLen;
		}

		return $this->substring(0, $length)->append($substring);
	}


	//
	//
	// ARRAY CONVERSIONS
	//
	//

	/**
	 * Splits the string by string
	 *
	 * @param string $delimiter The boundary string.
	 * @param integer $limit
	 * 		If limit is set and positive, the returned array will contain a maximum of
	 * 		limit elements with the last element containing the rest of string.
	 *
	 * 		If the limit parameter is negative, all components except the last
	 * 		-limit are returned.
	 *
	 * 		If the limit parameter is zero, then this is treated as 1.
	 *
	 * @return ArrayObject
	 * 		Returns an array of strings created by splitting the string parameter on boundaries
	 * 		formed by the delimiter.
	 *
	 * 		If delimiter is an empty string (""), split() will return FALSE. If delimiter contains
	 * 		a value that is not contained in string and a negative limit is used, then an empty
	 * 		array will be returned, otherwise an array containing string will be returned.
	 *
	 * @TODO: Maybe throw an exception or something on those odd delimiters?
	 */
	public function split($delimiter, $limit = PHP_INT_MAX) {
		return new ArrayObject(explode($delimiter, $this->string, $limit));
	}

	/**
	 * Join array elements with a string
	 *
	 * @param array $pieces The array of strings to join.
	 * @param string $glue Defaults to an empty string.
	 * @param string $encoding the desired encoding
	 * @return Text
	 * 		Returns a string containing a string representation of all the array elements in the
	 * 		same order, with the glue string between each element.
	 */
	public static function join(array $pieces, $glue = '', $encoding = null) {
		return new Text(implode($pieces, $glue), $encoding);
	}

	/**
	 * Convert the string to an array
	 *
	 * @param int $splitLength Maximum length of the chunk.
	 *
	 * @return ArrayObject
	 * 		If the optional splitLength parameter is specified, the returned array will be
	 * 		broken down into chunks with each being splitLength in length, otherwise each chunk
	 * 		will be one character in length.
	 *      If the split_length length exceeds the length of string, the entire string is returned
	 *      as the first (and only) array element.
	 * @throws \InvalidArgumentException If splitLength is less than 1.
	 */
	public function chunk($splitLength = 1) {
		$this->verifyPositive($splitLength, 'The chunk length');

		return new ArrayObject(str_split($this->string, $splitLength));
	}


	//
	//
	// TRANSFORMS
	//
	//

	/**
	 * Transforms the string to lowercase
	 *
	 * @return Text
	 */
	public function toLowerCase() {
		return new Text(mb_strtolower($this->string, $this->encoding), $this->encoding);
	}

	/**
	 * Transforms the string to first character lowercased
	 *
	 * @return Text
	 */
	public function toLowerCaseFirst() {
		$first = $this->substring(0, 1);
		$rest = $this->substring(1);

		return new Text(mb_strtolower($first, $this->encoding) . $rest, $this->encoding);
	}

	/**
	 * Transforms the string to uppercase
	 *
	 * @return Text
	 */
	public function toUpperCase() {
		return new Text(mb_strtoupper($this->string, $this->encoding), $this->encoding);
	}

	/**
	 * Transforms the string to first character uppercased
	 *
	 * @return Text
	 */
	public function toUpperCaseFirst() {
		$first = $this->substring(0, 1);
		$rest = $this->substring(1);

		return new Text(mb_strtoupper($first, $this->encoding) . $rest, $this->encoding);
	}

	/**
	 * Transforms the string to only its first character capitalized.
	 *
	 * @return Text
	 */
	public function toCapitalCase() {
		return $this->toLowerCase()->toUpperCaseFirst();
	}

	/**
	 * Transforms the string with the words capitalized.
	 *
	 * @return Text
	 */
	public function toCapitalCaseWords() {
		$encoding = $this->encoding;
		return $this->split(' ')->map(function ($str) use ($encoding) {
			return Text::create($str, $encoding)->toCapitalCase();
		})->join(' ');
	}

	/**
	 * Converts this string into camelCase. Numbers are considered as part of its previous piece.
	 *
	 * <code>
	 * $var = new Text('my_own_variable');<br>
	 * $var->toCamelCase(); // myOwnVariable
	 *
	 * $var = new Text('my_test3_variable');<br>
	 * $var->toCamelCase(); // myTest3Variable
	 * </code>
	 *
	 * @return Text
	 */
	public function toCamelCase() {
		return $this->toStudlyCase()->toLowerCaseFirst();
	}

	/**
	 * Converts this string into snake_case. Numbers are considered as part of its previous piece.
	 *
	 * <code>
	 * $var = new Text('myOwnVariable');<br>
	 * $var->toSnakeCase(); // my_own_variable
	 *
	 * $var = new Text('myTest3Variable');<br>
	 * $var->toSnakeCase(); // my_test3_variable
	 * </code>
	 *
	 * @return Text
	 */
	public function toSnakeCase() {
		return $this->toKebabCase()->replace('-', '_');
	}

	/**
	 * Converts this string into StudlyCase. Numbers are considered as part of its previous piece.
	 *
	 * <code>
	 * $var = new Text('my_own_variable');<br>
	 * $var->toStudlyCase(); // MyOwnVariable
	 *
	 * $var = new Text('my_test3_variable');<br>
	 * $var->toStudlyCase(); // MyTest3Variable
	 * </code>
	 *
	 * @return Text
	 */
	public function toStudlyCase() {
		$input = $this->trim('-_');
		if ($input->isEmpty()) {
			return $input;
		}
		$encoding = $this->encoding;
		return Text::create(preg_replace_callback('/([A-Z-_][a-z0-9]+)/', function ($matches) use ($encoding) {
			return Text::create($matches[0], $encoding)->replace(['-', '_'], '')->toUpperCaseFirst();
		}, $input), $this->encoding)->toUpperCaseFirst();
	}

	/**
	 * Convert this string into kebab-case. Numbers are considered as part of its previous piece.
	 *
	 * <code>
	 * $var = new Text('myOwnVariable');<br>
	 * $var->toKebapCase(); // my-own-variable
	 *
	 * $var = new Text('myTest3Variable');<br>
	 * $var->toKebapCase(); // my-test3-variable
	 * </code>
	 *
	 * @return Text
	 */
	public function toKebabCase() {
		if ($this->contains('_')) {
			return $this->replace('_', '-');
		}

		return new Text(mb_strtolower(preg_replace('/([a-z0-9])([A-Z])/', '$1-$2', $this->string)), $this->encoding);
	}

	/**
	 * Get the plural form of the Text object.
	 *
	 * @return Text
	 */
	public function toPlural(Pluralizer $pluralizer = null) {
		$pluralizer = $pluralizer ?: new EnglishPluralizer();

		return new Text($pluralizer->getPluralForm($this->string), $this->encoding);
	}

	/**
	 * Get the singular form of the Text object.
	 *
	 * @return Text
	 */
	public function toSingular(Pluralizer $pluralizer = null) {
		$pluralizer = $pluralizer ?: new EnglishPluralizer();

		return new Text($pluralizer->getSingularForm($this->string), $this->encoding);
	}

	/**
	 * Converts each tab in the string to some number of spaces, as defined by
	 * $tabLength. By default, each tab is converted to 4 consecutive spaces.
	 *
	 * @param int $tabLength Number of spaces to replace each tab with
	 * @return Text text with tabs converted to spaces
	 */
	public function toSpaces($tabLength = 4) {
		$spaces = str_repeat(' ', $tabLength);
		return $this->replace("\t", $spaces);
	}
	/**
	 * Converts each occurrence of some consecutive number of spaces, as
	 * defined by $tabLength, to a tab. By default, each 4 consecutive spaces
	 * are converted to a tab.
	 *
	 * @param int $tabLength Number of spaces to replace with a tab
	 * @return Text text with spaces converted to tabs
	 */
	public function toTabs($tabLength = 4) {
		$spaces = str_repeat(' ', $tabLength);
		return $this->replace($spaces, "\t");
	}

	/**
	 * Returns the native string
	 *
	 * @return string
	 */
	public function toString() {
		return $this->string;
	}

	//
	//
	// DEPRECATIONS - Remove with 0.10
	//
	//

	/**
	 * Returns the character at the given zero-related index
	 *
	 * @param int $index zero-related index
	 * @return string the found character
	 * @deprecated use <code>at()</code> method instead
	 */
	public function charAt($index) {
		return $this->at($index);
	}

	/**
	 * Strip whitespace (or other characters) from the beginning of the string
	 *
	 * @param string $characters
	 * 		Optionally, the stripped characters can also be specified using the mask parameter.
	 * 		Simply list all characters that you want to be stripped. With .. you can specify a
	 * 		range of characters.
	 *
	 * @return Text
	 * @deprecated use <code>trimStart()</code> instead
	 */
	public function trimLeft($characters = " \t\n\r\v\0") {
		return $this->trimStart($characters);
	}

	/**
	 * Strip whitespace (or other characters) from the end of the string
	 *
	 * @param string $characters
	 * 		Optionally, the stripped characters can also be specified using the mask parameter.
	 * 		Simply list all characters that you want to be stripped. With .. you can specify a
	 * 		range of characters.
	 *
	 * @return Text
	 * @deprecated use <code>trimEnd()</code> instead
	 */
	public function trimRight($characters = " \t\n\r\v\0") {
		return $this->trimEnd($characters);
	}

	/**
	 * Adds padding to the left
	 *
	 * @param int $length
	 * @param string $padding
	 * @return Text
	 * @deprecated Use <code>padStart()</code> instead
	 */
	public function padLeft($length, $padding = ' ') {
		return $this->padStart($length, $padding);
	}

	/**
	 * Adds padding to the right
	 *
	 * @param int $length
	 * @param string $padding
	 * @return Text
	 * @deprecated use <code>padEnd()</code> instead
	 */
	public function padRight($length, $padding = ' ') {
		return $this->padEnd($length, $padding);
	}

	/**
	 * Transforms the string to lowercase
	 *
	 * @return Text
	 * @deprecated Use <code>toLowercase()</code> method instead.
	 */
	public function lower() {
		return $this->toLowerCase();
	}

	/**
	 * Transforms the string to first character lowercased
	 *
	 * @return Text
	 * @deprecated Use <code>toLowerCaseFirst()</code> instead.
	 */
	public function lowerFirst() {
		return $this->toLowerCaseFirst();
	}

	/**
	 * Transforms the string to uppercase
	 *
	 * @return Text
	 * @deprecated Use <code>toUpperCase()</code> method instead.
	 */
	public function upper() {
		return $this->toUpperCase();
	}

	/**
	 * Transforms the string to first character uppercased
	 *
	 * @return Text
	 * @deprecated Use <code>toUpperCaseFirst()</code> method instead.
	 */
	public function upperFirst() {
		return $this->toUpperCaseFirst();
	}

	/**
	 * Transforms the string to first character of each word uppercased
	 *
	 * @return Text
	 * @deprecated Use <code>toCapitalCaseWords()</code> instead.
	 */
	public function upperWords() {
		return $this->toCapitalCaseWords();
	}

	/**
	 * Transforms the string to only its first character capitalized.
	 *
	 * @return Text
	 * @deprecated Use <code>toCapitalCase()</code> method instead.
	 */
	public function capitalize() {
		return $this->toCapitalCase();
	}

	/**
	 * Transforms the string with the words capitalized.
	 *
	 * @return Text
	 * @deprecated Use <code>toCapitalCaseWords()</code> method instead.
	 */
	public function capitalizeWords() {
		return $this->toCapitalCaseWords();
	}

	//
	//
	// MAGIC HAPPENS HERE
	//
	//

	public function __toString() {
		return $this->string;
	}

	//
	//
	// INTERNALS
	//
	//

	protected function prepareOffset($offset) {
		$len = $this->length();
		if ($offset < -$len || $offset > $len) {
			throw new \InvalidArgumentException('Offset must be in range [-len, len]');
		}

		if ($offset < 0) {
			$offset += $len;
		}

		return $offset;
	}

	protected function prepareLength($offset, $length) {
		if ($length === null) {
			return $this->length() - $offset;
		}

		if ($length < 0) {
			$length += $this->length() - $offset;

			if ($length < 0) {
				throw new \InvalidArgumentException('Length too small');
			}
		} else {
			if ($offset + $length > $this->length()) {
				throw new \InvalidArgumentException('Length too large');
			}
		}

		return $length;
	}

	protected function verifyNotEmpty($string, $name) {
		if (empty($string)) {
			throw new \InvalidArgumentException("$name cannot be empty");
		}
	}

	protected function verifyPositive($value, $name) {
		if ($value <= 0) {
			throw new \InvalidArgumentException("$name has to be positive");
		}
	}

	protected function verifyNotNegative($value, $name) {
		if ($value < 0) {
			throw new \InvalidArgumentException("$name can not be negative");
		}
	}

	protected function replacePairs($replacements, $limit) {
		if ($limit === null) {
			return strtr($this->string, $replacements);
		}

		$this->verifyPositive($limit, 'Limit');
		$str = $this->string;
		foreach ($replacements as $from => $to) {
			$str = $this->replaceWithLimit($str, $from, $to, $limit);
			if (0 === $limit) {
				break;
			}
		}
		return $str;
	}

	protected function replaceWithLimit($str, $from, $to, &$limit) {
		$lenDiff = $to->length() - $from->length();
		$index = 0;

		while (false !== $index = $str->indexOf($from, $index)) {
			$str = $str->splice($to, $index, $to->length());
			$index += $lenDiff;

			if (0 === --$limit) {
				break;
			}
		}

		return $str;
	}
}