<?php declare(strict_types=1);
/**
 * This file is part of the Phootwork package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license MIT License
 * @copyright Thomas Gossmann
 */

namespace phootwork\lang\text;

trait CheckerTrait {

	/**
	 * Checks if the string is empty
	 *
	 * @return boolean
	 */
	public function isEmpty(): bool {
		return empty($this->string);
	}

	/**
	 * Check if the string contains only alphanumeric characters.
	 *
	 * @return boolean
	 */
	public function isAlphanumeric(): bool {
		return ctype_alnum($this->string);
	}

	/**
	 * Check if the string contains only alphanumeric characters.
	 *
	 * @return boolean
	 */
	public function isAlphabetic(): bool {
		return ctype_alpha($this->string);
	}

	/**
	 * Check if the string contains only numeric characters.
	 *
	 * @return boolean
	 */
	public function isNumeric(): bool {
		return ctype_digit($this->string);
	}

	/**
	 * Check if the string contains only characters which are not whitespace or an alphanumeric.
	 *
	 * @return boolean
	 */
	public function isPunctuation(): bool {
		return ctype_punct($this->string);
	}

	/**
	 * Check if the string contains only space characters.
	 *
	 * @return boolean
	 */
	public function isSpace(): bool {
		return ctype_space($this->string);
	}

	/**
	 * Check if the string contains only lower case characters.
	 *
	 * Spaces are considered non-lowercase characters, so lowercase strings with multiple words, separated by spaces,
	 * return false. E.g.:
	 *
	 * <code>
	 * $text = new Text('lowercase multi words string');<br>
	 * var_dump($text->isLowercase()); // false
	 * </code>
	 *
	 * @return boolean
	 */
	public function isLowerCase(): bool {
		return ctype_lower($this->string);
	}

	/**
	 * Check if the string contains only upper case characters.
	 *
	 * Spaces are considered non-uppercase characters, so uppercase strings with multiple words, separated by spaces,
	 * return false. E.g.:
	 *
	 * <code>
	 * $text = new Text('UPPERCASE MULTI WORDS STRING'); <br>
	 * var_dump($text->isUppercase()); // false
	 * </code>
	 *
	 * @return boolean
	 */
	public function isUpperCase(): bool {
		return ctype_upper($this->string);
	}

	/**
	 * Check if a string is singular form.
	 *
	 * @param Pluralizer $pluralizer
	 *        	A custom pluralizer. Default is the EnglishPluralizer
	 * @return boolean
	 */
	public function isSingular(?Pluralizer $pluralizer = null): bool {
		$pluralizer = $pluralizer ?? new EnglishPluralizer();

		return $pluralizer->isSingular($this->string);
	}

	/**
	 * Check if a string is plural form.
	 *
	 * @param Pluralizer $pluralizer
	 *        	A custom pluralizer. Default is the EnglishPluralizer
	 * @return boolean
	 */
	public function isPlural(?Pluralizer $pluralizer = null): bool {
		$pluralizer = $pluralizer ?? new EnglishPluralizer();

		return $pluralizer->isPlural($this->string);
	}
}
