<?php declare(strict_types=1);
/**
 * This file is part of the Phootwork package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license MIT License
 * @copyright Thomas Gossmann
 */
namespace phootwork\lang\parts;

use phootwork\lang\ArrayObject;
use phootwork\lang\Text;

/**
 * Text searching methods
 *
 * @author ThomasGossmann
 * @author Cristiano Cinotti
 */
trait SearchPart {
	abstract protected function getString(): string;

	abstract public function length(): int;

	abstract protected function prepareOffset(int $offset): int;

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
	 *
	 * @return string the found character
	 */
	public function at(int $index): string {
		return mb_substr($this->getString(), $index, 1, $this->encoding);
	}

	/**
	 * Returns an ArrayObject consisting of the characters in the string.
	 *
	 * @return ArrayObject An ArrayObject of all chars
	 */
	public function chars(): ArrayObject {
		$chars = new ArrayObject();
		for ($i = 0, $l = $this->length(); $i < $l; $i++) {
			$chars->append($this->at($i));
		}

		return $chars;
	}

	/**
	 * Returns the index of a given string, starting at the optional zero-related offset
	 *
	 * @param string|Text $string
	 * @param int $offset zero-related offset
	 *
	 * @return int|null int for the index or null if the given string doesn't occur
	 */
	public function indexOf($string, int $offset = 0): ?int {
		$offset = $this->prepareOffset($offset);
		if ($string == '') {
			return $offset;
		}
		$output = mb_strpos($this->getString(), (string) $string, $offset, $this->encoding);

		return false === $output ? null : $output;
	}

	/**
	 * Returns the last index of a given string, starting at the optional offset
	 *
	 * @param string $string
	 * @param int $offset
	 *
	 * @return int|null int for the index or null if the given string doesn't occur
	 */
	public function lastIndexOf(string $string, ?int $offset = null): ?int {
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
		$output = mb_strrpos($this->getString(), (string) $string, $offset - $this->length(), $this->encoding);

		return false === $output ? null : $output;
	}

	/**
	 * Checks whether the string starts with the given string. Case sensitive!
	 *
	 * @see Text::startsWithIgnoreCase()
	 *
	 * @param string|Text $substring The substring to look for
	 *
	 * @return bool
	 */
	public function startsWith($substring): bool {
		$substringLength = mb_strlen((string) $substring, $this->encoding);
		$startOfStr = mb_substr($this->getString(), 0, $substringLength, $this->encoding);

		return (string) $substring === $startOfStr;
	}

	/**
	 * Checks whether the string starts with the given string. Ignores case.
	 *
	 * @see Text::startsWith()
	 *
	 * @param string|Text $substring The substring to look for
	 *
	 * @return bool
	 */
	public function startsWithIgnoreCase($substring): bool {
		$substring = mb_strtolower((string) $substring, $this->encoding);
		$substringLength = mb_strlen($substring, $this->encoding);
		$startOfStr = mb_strtolower(mb_substr($this->getString(), 0, $substringLength, $this->encoding));

		return (string) $substring === $startOfStr;
	}

	/**
	 * Checks whether the string ends with the given string. Case sensitive!
	 *
	 * @see Text::endsWithIgnoreCase()
	 *
	 * @param string|Text $substring The substring to look for
	 *
	 * @return bool
	 */
	public function endsWith($substring): bool {
		$substringLength = mb_strlen((string) $substring, $this->encoding);
		$endOfStr = mb_substr($this->getString(), $this->length() - $substringLength, $substringLength, $this->encoding);

		return (string) $substring === $endOfStr;
	}

	/**
	 * Checks whether the string ends with the given string. Ingores case.
	 *
	 * @see Text::endsWith()
	 *
	 * @param string|Text $substring The substring to look for
	 *
	 * @return bool
	 */
	public function endsWithIgnoreCase($substring): bool {
		$substring = mb_strtolower((string) $substring, $this->encoding);
		$substringLength = mb_strlen($substring, $this->encoding);
		$endOfStr = mb_strtolower(mb_substr($this->getString(), $this->length() - $substringLength, $substringLength, $this->encoding));

		return (string) $substring === $endOfStr;
	}

	/**
	 * Checks whether the given string occurs
	 *
	 * @param string|Text $string
	 *
	 * @return bool
	 */
	public function contains($string): bool {
		return $this->indexOf($string) !== null;
	}

	/**
	 * Performs a regular expression matching with the given regexp
	 *
	 * @param string $regexp
	 *
	 * @return bool
	 */
	public function match(string $regexp): bool {
		return (bool) preg_match($regexp, $this->getString());
	}
}
