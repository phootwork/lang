<?php
namespace phootwork\lang;

/**
 * Object representation of an immutable String
 * 
 * @author gossi
 */
class Text implements \ArrayAccess, Comparable {

	use TransformTrait, CheckerTrait;

	private $string;
	
	public function __construct($string = '') {
		$this->string = $string;
	}
	
	public static function create($string) {
		return new Text($string);
	}
	
	/*
	 * Convenience methods
	 */
	
	/**
	 * Get string length
	 *
	 * @return int Returns the length
	 */
	public function length() {
		return strlen($this->string);
	}

	/**
	 * Appends $string and returns as a new String
	 * 
	 * @param string $string
	 * @return Text
	 */
	public function append($string) {
		return new Text($this->string . $string);
	}
	
	/**
	 * Prepends a string and returns as a new String
	 * 
	 * @param string $string
	 * @return Text
	 */
	public function prepend($string) {
		return new Text($string . $this->string);
	}
	
	/*
	 * Comparison
	 */

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
	 * @return int 
	 * 		Return Values:
	 * 		< 0 if the object is less than comparison
	 * 		> 0 if the object is greater than comparison 
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
		return $callback($this->string, '' . $compare);
	}
	
	/**
	 * Checks whether the string and the given object are equal
	 * 
	 * @param mixed $string
	 * @return boolean
	 * @deprecated Use `isEqualTo` method instead
	 */
	public function equals($string) {
		return $this->isEqualTo($string);
	}
	
	/**
	 * Checks whether the string and the given object are equal ignoring the case
	 * 
	 * @param mixed $string
	 * @return boolean
	 * @deprecated Use `isEqualIgnoreCaseTo` method instead
	 */
	public function equalsIgnoreCase($string) {
		return $this->isEqualIgnoreCaseTo($string);
	}
	
	/*
	 * Slicing methods
	 */
	
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
			return new Text('');
		}
	
		return new Text(substr($this->string, $offset, $length));
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

		return new Text(substr($this->string, $start, $end));
	}
	
	/*
	 * Replacing methods 
	 */
	
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
		
		return new Text(str_replace($search, $replace, $this->string));
	}

	/**
	 * Replaces all occurences of given replacement map. Keys will be replaced with its values.
	 * 
	 * @param array $map the replacements. Keys will be replaced with its value.
	 * @return Text
	 */
	public function supplant(array $map) {
		return new Text(str_replace(array_keys($map), array_values($map), $this->string));
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
	
		return new Text(substr_replace($this->string, $replacement, $offset, $length));
	}
	
	/*
	 * Search methods
	 */

	public function charAt($index) {
		return $this->offsetGet($index);
	}

	/**
	 * Returns the index of a given string, starting at the optional offset
	 * 
	 * @param string $string
	 * @param int $offset
	 * @return int|boolean int for the index or false if the given string doesn't occur
	 */
	public function indexOf($string, $offset = 0) {
		$offset = $this->prepareOffset($offset);
	
		if ($string == '') {
			return $offset;
		}

		return strpos($this->string, '' . $string, $offset);
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
		return strrpos($this, '' . $string, $offset - $this->length());
	}

	/**
	 * Checks whether the string starts with the given string
	 * 
	 * @param string $search
	 * @return boolean
	 */
	public function startsWith($search) {
		return $this->indexOf($search) === 0;
	}
	
	/**
	 * Checks whether the string ends with the given string
	 *
	 * @param string $search
	 * @return boolean
	 */
	public function endsWith($search) {
		return substr($this->string, - strlen(''.$search)) === '' . $search;
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
	 * Count the number of substring occurrences.
	 *
	 * @param string|Text $string The substring to count the occurrencies
	 * @param int $offset
	 * @param int|null $length
	 * @return int
	 * @throws \InvalidArgumentException If $offset is greater then the string length or $length is too small.
	 */
	public function count($string, $offset = 0, $length = null) {
		$offset = $this->prepareOffset($offset);
		$length = $this->prepareLength($offset, $length);
	
		if ($string === '') {
			return $length + 1;
		}

		return substr_count($this->string, $string, $offset, $length);
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

	/*
	 * Formatting and transformation methods
	 */

// 	// should this be in a formatter?
// 	public function format() {
// 		return vsprintf($this->string, func_get_args());
// 	}

	/**
	 * Transforms the string to lowercase
	 * 
	 * @return Text
	 * @deprecated Use `toLowercase()` method instead.
	 */
	public function lower() {
		return $this->toLowerCase();
	}

	/**
	 * Transforms the string to first character lowercased
	 *
	 * @return Text
	 * @deprecated Use `toLowerCaseFirst()` instead.
	 */
	public function lowerFirst() {
		return $this->toLowerCaseFirst();
	}
	
	/**
	 * Transforms the string to uppercase
	 *
	 * @return Text
	 * @deprecated Use `toUpperCase()` method instead.
	 */
	public function upper() {
		return $this->toUpperCase();
	}
	
	/**
	 * Transforms the string to first character uppercased
	 *
	 * @return Text
	 * @deprecated Use `toUpperCaseFirst` method instead.
	 */
	public function upperFirst() {
		return $this->toUpperCaseFirst();
	}
	
	/**
	 * Transforms the string to first character of each word uppercased
	 * 
	 * @return Text
	 * @deprecated Use `toUpperCaseWords` instead.
	 */
	public function upperWords() {
		return $this->toUpperCaseWords();
	}
	
	/**
	 * Transforms the string to only its first character capitalized.
	 * 
	 * @return Text
	 * @deprecated Use `toCapitalCase` method instead.
	 */
	public function capitalize() {
		return $this->toCapitalCase();
	}
	
	/**
	 * Transforms the string with the words capitalized.
	 * 
	 * @return Text
	 * @deprecated Use `toCapitalCaseWords()` method instead.
	 */
	public function capitalizeWords() {
		return $this->toCapitalCaseWords();
	}
	
	/**
	 * Strip whitespace (or other characters) from the beginning and end of the string
	 * 
	 * @param string $mask 
	 * 		Optionally, the stripped characters can also be specified using the mask parameter. 
	 * 		Simply list all characters that you want to be stripped. With .. you can specify a 
	 * 		range of characters.
	 *  
	 * @return Text
	 */
	public function trim($characters = " \t\n\r\v\0") {
		return new Text(trim($this->string, ''.$characters));
	}
	
	/**
	 * Strip whitespace (or other characters) from the beginning of the string
	 *
	 * @param string $mask
	 * 		Optionally, the stripped characters can also be specified using the mask parameter.
	 * 		Simply list all characters that you want to be stripped. With .. you can specify a
	 * 		range of characters.
	 *
	 * @return Text
	 */
	public function trimLeft($characters = " \t\n\r\v\0") {
		return new Text(ltrim($this->string, ''.$characters));
	}
	
	/**
	 * Strip whitespace (or other characters) from the end of the string
	 *
	 * @param string $mask
	 * 		Optionally, the stripped characters can also be specified using the mask parameter.
	 * 		Simply list all characters that you want to be stripped. With .. you can specify a
	 * 		range of characters.
	 *
	 * @return Text
	 */
	public function trimRight($characters = " \t\n\r\v\0") {
		return new Text(rtrim($this->string, ''.$characters));
	}

	public function padLeft($length, $padding = ' ') {
		return new Text(str_pad($this->string, $length, ''.$padding, STR_PAD_LEFT));
	}
	
	public function padRight($length, $padding = ' ') {
		return new Text(str_pad($this->string, $length, ''.$padding, STR_PAD_RIGHT));
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
		return new Text(wordwrap($this->string, $width, $break, $cut));
	}

	/**
	 * Repeat the string $times times. If $times is 0, it returns ''.
	 *
	 * @param int $times
	 * @return Text
	 * @throws \InvalidArgumentException If $times is negative.
	 */
	public function repeat($times) {
		$this->verifyNotNegative($times, 'Number of repetitions');
		return new Text(str_repeat($this->string, $times));
	}

	public function reverse() {
		return new Text(strrev($this->string));
	}
	
	/*
	 * Array conversion
	 */
	
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
	 * @return String
	 * 		Returns a string containing a string representation of all the array elements in the
	 * 		same order, with the glue string between each element.
	 * 
	 * @TODO: Convenience method? Remove it in favor of ArrayObject.join() ?
	 */
	public static function join(array $pieces, $glue = '') {
		return new Text(implode($pieces, $glue));
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
	
	public function toString() {
		return $this->string;
	}

	/*
	 * Some magic here
	 */
	
	public function __toString() {
		return $this->string;
	}
	
	/**
	 * @internal
	 */
	public function offsetSet($offset, $value) {
		if (!is_null($offset)) {
			$this->string[$offset] = $value;
		}
	}
	
	/**
	 * @internal
	 */
	public function offsetExists($offset) {
		return isset($this->string[$offset]);
	}
	
	/**
	 * @internal
	 */
	public function offsetUnset($offset) {
		unset($this->string[$offset]);
	}
	
	/**
	 * @internal
	 */
	public function offsetGet($offset) {
		return isset($this->string[$offset]) ? $this->string[$offset] : null;
	}
	
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