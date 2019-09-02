<?php declare(strict_types=1);
/**
 * This file is part of the Phootwork package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license MIT License
 * @copyright Thomas Gossmann
 */
namespace phootwork\lang;

use phootwork\lang\parts\ArrayConversionsPart;
use phootwork\lang\parts\CheckerPart;
use phootwork\lang\parts\ComparisonPart;
use phootwork\lang\parts\InternalPart;
use phootwork\lang\parts\SearchPart;
use phootwork\lang\parts\TransformationsPart;

/**
 * Object representation of an immutable String
 *
 * @author gossi
 */
class Text implements Comparable {
	use ArrayConversionsPart;
	use CheckerPart;
	use ComparisonPart;
	use SearchPart;
	use InternalPart;
	use TransformationsPart;

	/** @var string */
	private $string;

	/** @var string */
	private $encoding;

	/**
	 * Initializes a String object ad assigns both string and encoding properties
	 * the supplied values. $string is cast to a string prior to assignment, and if
	 * $encoding is not specified, it defaults to mb_internal_encoding(). Throws
	 * an InvalidArgumentException if the first argument is an array or object
	 * without a __toString method.
	 *
	 * @param mixed $string Value to modify, after being cast to string
	 * @param string $encoding The character encoding
	 *
	 * @throws \InvalidArgumentException if an array or object without a __toString method is passed as the first argument
	 *
	 * @psalm-suppress PossiblyInvalidPropertyAssignmentValue mb_internal_encoding always return string when called as getter
	 */
	public function __construct($string = '', ?string $encoding = null) {
		if (is_array($string)) {
			throw new \InvalidArgumentException('The constructor parameter cannot be an array');
		} elseif (is_object($string) && !method_exists($string, '__toString')) {
			throw new \InvalidArgumentException('Passed object must implement  `__toString` method');
		}

		$this->string = (string) $string;
		$this->encoding = $encoding ?? mb_internal_encoding();
	}

	public function __clone() {
		return new self($this->string, $this->encoding);
	}

	/**
	 * Static initializing a String object.
	 *
	 * @see Text::__construct()
	 *
	 * @param mixed $string
	 * @param string $encoding
	 *
	 * @return Text
	 */
	public static function create($string, ?string $encoding = null) {
		return new self($string, $encoding);
	}

	/**
	 * Returns the used encoding
	 *
	 * @return string
	 */
	public function getEncoding(): string {
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
	public function length(): int {
		return mb_strlen($this->string, $this->encoding);
	}

	/**
	 * Appends <code>$string</code> and returns as a new <code>Text</code>
	 *
	 * @param string|Text $string
	 *
	 * @return Text
	 */
	public function append($string): self {
		return new self($this->string . $string, $this->encoding);
	}

	/**
	 * Prepends <code>$string</code> and returns as a new <code>Text</code>
	 *
	 * @param string|Text $string
	 *
	 * @return Text
	 */
	public function prepend($string): self {
		return new self($string . $this->string, $this->encoding);
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
	 *
	 * @return Text
	 */
	public function insert($substring, int $index): self {
		if ($index <= 0) {
			return $this->prepend($substring);
		}

		if ($index > $this->length()) {
			return $this->append($substring);
		}

		$start = mb_substr($this->string, 0, $index, $this->encoding);
		$end = mb_substr($this->string, $index, $this->length(), $this->encoding);

		return new self($start . $substring . $end);
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
	 *
	 * @param int $offset
	 * @param int $length
	 *
	 * @return Text
	 */
	public function slice(int $offset, ?int $length = null): self {
		$offset = $this->prepareOffset($offset);
		$length = $this->prepareLength($offset, $length);

		if ($length === 0) {
			return new self('', $this->encoding);
		}

		return new self(mb_substr($this->string, $offset, $length, $this->encoding), $this->encoding);
	}

	/**
	 * Slices a piece of the string from a given start to an end.
	 * If no length is given, the String is sliced to its maximum length.
	 *
	 * @see #slice
	 *
	 * @param int $start
	 * @param int $end
	 *
	 * @return Text
	 */
	public function substring(int $start, ?int $end = null): self {
		$length = $this->length();

		if (null === $end) {
			$end = $length;
		}

		if ($end < 0) {
			$end = $length + $end;
		}

		$end = min($end, $length);
		$start = min($start, $end);
		$end = max($start, $end);
		$end = $end - $start;

		return new self(mb_substr($this->string, $start, $end, $this->encoding), $this->encoding);
	}

	/**
	 * Count the number of substring occurrences.
	 *
	 * @param string|Text $substring The substring to count the occurrencies
	 * @param bool $caseSensitive Force case-sensitivity
	 *
	 * @return int
	 */
	public function countSubstring($substring, bool $caseSensitive = true): int {
		$this->verifyNotEmpty($substring, '$substring');
		if ($caseSensitive) {
			return mb_substr_count($this->string, (string) $substring, $this->encoding);
		}
		$str = mb_strtoupper($this->string, $this->encoding);
		$substring = mb_strtoupper((string) $substring, $this->encoding);

		return mb_substr_count($str, (string) $substring, $this->encoding);
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
	 *
	 * @param Arrayable|Text|array|string $search
	 * 		The value being searched for, otherwise known as the needle. An array may be used
	 * 		to designate multiple needles.
	 * @param Arrayable|Text|array|string $replace
	 * 		The replacement value that replaces found search values. An array may be used to
	 * 		designate multiple replacements.
	 *
	 * @return Text
	 */
	public function replace($search, $replace): self {
		if ($search instanceof self) {
			$search = $search->toString();
		} elseif ($search instanceof Arrayable) {
			$search = $search->toArray();
		}

		if ($replace instanceof self) {
			$replace = $replace->toString();
		} elseif ($replace instanceof Arrayable) {
			$replace = $replace->toArray();
		}

		return new self(str_replace($search, $replace, $this->string), $this->encoding);
	}

	/**
	 * Replaces all occurences of given replacement map. Keys will be replaced with its values.
	 *
	 * @param array $map the replacements. Keys will be replaced with its value.
	 *
	 * @return Text
	 */
	public function supplant(array $map): self {
		return new self(str_replace(array_keys($map), array_values($map), $this->string), $this->encoding);
	}

	/**
	 * Replace text within a portion of a string.
	 *
	 * @param string|Text $replacement
	 * @param int $offset
	 * @param int|null $length
	 *
	 * @throws \InvalidArgumentException If $offset is greater then the string length or $length is too small.
	 *
	 * @return Text
	 */
	public function splice($replacement, int $offset, ?int $length = null): self {
		$offset = $this->prepareOffset($offset);
		$length = $this->prepareLength($offset, $length);

		$start = $this->substring(0, $offset);
		$end = $this->substring($offset + $length);

		return new self($start . $replacement . $end);
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
	public function trim(string $characters = " \t\n\r\v\0"): self {
		return new self(trim($this->string, (string) $characters), $this->encoding);
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
	public function trimStart(string $characters = " \t\n\r\v\0"): self {
		return new self(ltrim($this->string, (string) $characters), $this->encoding);
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
	public function trimEnd(string $characters = " \t\n\r\v\0"): self {
		return new self(rtrim($this->string, (string) $characters), $this->encoding);
	}

	/**
	 * Adds padding to the start and end
	 *
	 * @param int $length
	 * @param string $padding
	 *
	 * @return Text
	 */
	public function pad(int $length, string $padding = ' '): self {
		$len = $length - $this->length();

		return $this->applyPadding(floor($len / 2), ceil($len / 2), $padding);
	}

	/**
	 * Adds padding to the start
	 *
	 * @param int $length
	 * @param string|Text $padding
	 *
	 * @return Text
	 */
	public function padStart(int $length, $padding = ' ') {
		return $this->applyPadding($length - $this->length(), 0, $padding);
	}

	/**
	 * Adds padding to the end
	 *
	 * @param int $length
	 * @param string|Text $padding
	 *
	 * @return Text
	 */
	public function padEnd(int $length, $padding = ' '): self {
		return $this->applyPadding(0, $length - $this->length(), $padding);
	}

	/**
	 * Adds the specified amount of left and right padding to the given string.
	 * The default character used is a space.
	 *
	 * @see https://github.com/danielstjules/Stringy/blob/master/src/Stringy.php
	 *
	 * @param int|float $left Length of left padding
	 * @param int|float $right Length of right padding
	 * @param string|Text $padStr String used to pad
	 *
	 * @return Text the padded string
	 */
	protected function applyPadding($left = 0, $right = 0, $padStr = ' ') {
		$length = mb_strlen((string) $padStr, $this->encoding);
		$strLength = $this->length();
		$paddedLength = $strLength + $left + $right;
		if (!$length || $paddedLength <= $strLength) {
			return $this;
		}

		$leftPadding = mb_substr(str_repeat((string) $padStr, (int) ceil($left / $length)), 0, (int) $left, $this->encoding);
		$rightPadding = mb_substr(str_repeat((string) $padStr, (int) ceil($right / $length)), 0, (int) $right, $this->encoding);

		return new self($leftPadding . $this->string . $rightPadding);
	}

	/**
	 * Ensures a given substring at the start of the string
	 *
	 * @param string $substring
	 *
	 * @return Text
	 */
	public function ensureStart(string $substring): self {
		if (!$this->startsWith($substring)) {
			return $this->prepend($substring);
		}

		return $this;
	}

	/**
	 * Ensures a given substring at the end of the string
	 *
	 * @param string $substring
	 *
	 * @return Text
	 */
	public function ensureEnd(string $substring): self {
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
	 *
	 * @return Text Returns the string wrapped at the specified length.
	 */
	public function wrapWords(int $width = 75, string $break = "\n", bool $cut = false): self {
		return new self(wordwrap($this->string, $width, $break, $cut), $this->encoding);
	}

	/**
	 * Repeat the string $times times. If $times is 0, it returns ''.
	 *
	 * @param int $multiplier
	 *
	 * @throws \InvalidArgumentException If $times is negative.
	 *
	 * @return Text
	 */
	public function repeat(int $multiplier): self {
		$this->verifyNotNegative($multiplier, 'Number of repetitions');

		return new self(str_repeat($this->string, $multiplier), $this->encoding);
	}

	/**
	 * Reverses the character order
	 *
	 * @return Text
	 */
	public function reverse(): self {
		return new self(strrev($this->string), $this->encoding);
	}

	/**
	 * Truncates the string with a substring and ensures it doesn't exceed the given length
	 *
	 * @param int $length
	 * @param string $substring
	 *
	 * @return Text
	 */
	public function truncate(int $length, string $substring = ''): self {
		if ($this->length() <= $length) {
			return new self($this->string, $this->encoding);
		}

		$substrLen = mb_strlen($substring, $this->encoding);

		if ($this->length() + $substrLen > $length) {
			$length -= $substrLen;
		}

		return $this->substring(0, $length)->append($substring);
	}

	/**
	 * Returns the native string
	 *
	 * @return string
	 */
	public function toString(): string {
		return $this->string;
	}

	protected function getString(): string {
		return $this->toString();
	}

	//
	//
	// MAGIC HAPPENS HERE
	//
	//

	public function __toString(): string {
		return $this->string;
	}
}
