<?php
namespace phootwork\lang;

use Propel\Pluralizer\EnglishPluralizer;

trait CheckerTrait
{
	/**
	 * Compares this string to another
	 *
	 * @param $string
	 * @return mixed
	 *
	 * @see \phootwork\lang\Comparable::compareTo()
	 */
	abstract public function compareTo($string);

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
	abstract public function compareCaseInsensitive($compare);

	public function isEmpty() {
		return empty($this->string);
	}

	/**
	 * Checks whether the string and the given object are equal
	 *
	 * @param mixed $string
	 * @return boolean
	 */
	public function isEqualTo($string) {
		return $this->compareTo($string) === 0;
	}

	/**
	 * Checks whether the string and the given object are equal ignoring the case
	 *
	 * @param mixed $string
	 * @return boolean
	 */
	public function isEqualIgnoreCaseTo($string) {
		return $this->compareCaseInsensitive($string) === 0;
	}

	/**
	 * Check if the string contains only alphanumeric characters.
	 *
	 * @return bool
	 */
	public function isAlphanumeric()
	{
		return ctype_alnum($this->string);
	}

	/**
	 * Check if the string contains only alphanumeric characters.
	 *
	 * @return bool
	 */
	public function isAlphabetic()
	{
		return ctype_alpha($this->string);
	}

	/**
	 * Check if the string contains only numeric characters.
	 *
	 * @return bool
	 */
	public function isNumeric()
	{
		return ctype_digit($this->string);
	}

	/**
	 * Check if the string contains only characters which are not whitespace or an alphanumeric.
	 *
	 * @return bool
	 */
	public function isPunctuation()
	{
		return ctype_punct($this->string);
	}

	/**
	 * Check if the string contains only space characters.
	 *
	 * @return bool
	 */
	public function isSpace()
	{
		return ctype_space($this->string);
	}

	/**
	 * Check if the string contains only lower case characters.
	 * Spaces are considered non-lowercase characters, so lowercase strings with multiple words, separated by spaces,
	 * return false. E.g.:
	 * <code>
	 *      $text = new Text('lowercase multi words string')
	 *      var_dump($text->isLowercase()); //FALSE
	 * </code>
	 *
	 * @return bool
	 */
	public function isLowerCase()
	{
		return ctype_lower($this->string);
	}

	/**
	 * Check if the string contains only upper case characters.
	 * Spaces are considered non-uppercase characters, so uppercase strings with multiple words, separated by spaces,
	 * return false. E.g.:
	 * <code>
	 *      $text = new Text('UPPERCASE MULTI WORDS STRING')
	 *      var_dump($text->isUppercase()); //FALSE
	 * </code>
	 * @return bool
	 */
	public function isUpperCase()
	{
		return ctype_upper($this->string);
	}

	/**
	 * Check if a string is singular form.
	 *
	 * @return bool
	 */
	public function isSingular()
	{
		$pluralizer = new EnglishPluralizer();

		return $pluralizer->isSingular($this->string);
	}

	/**
	 * Check if a string is plural form.
	 *
	 * @return bool
	 */
	public function isPlural()
	{
		$pluralizer = new EnglishPluralizer();

		return $pluralizer->isPlural($this->string);
	}

}