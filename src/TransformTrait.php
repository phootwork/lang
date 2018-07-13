<?php
namespace phootwork\lang;


use Propel\Pluralizer\EnglishPluralizer;

trait TransformTrait
{
	/**
	 * Transforms the string to lowercase
	 *
	 * @return Text
	 */
	public function toLowerCase() {
		return new Text(strtolower($this->string));
	}

	/**
	 * Transforms the string to first character lowercased
	 *
	 * @return Text
	 */
	public function toLowerCaseFirst() {
		return new Text(lcfirst($this->string));
	}

	/**
	 * Transforms the string to uppercase
	 *
	 * @return Text
	 */
	public function toUpperCase() {
		return new Text(strtoupper($this->string));
	}

	/**
	 * Transforms the string to first character uppercased
	 *
	 * @return Text
	 */
	public function toUpperCaseFirst() {
		return new Text(ucfirst($this->string));
	}

	/**
	 * Transforms the string to first character of each word uppercased
	 *
	 * @return Text
	 */
	public function toUpperCaseWords() {
		return new Text(ucwords($this->string));
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
		return $this->toLowerCase()->toUpperCaseWords();
	}

	/**
	 * Get the plural form of the Text object.
	 *
	 * @return Text
	 */
	public function toPlural()
	{
		$pluralizer = new EnglishPluralizer();

		return new Text($pluralizer->getPluralForm($this->string));
	}

	/**
	 * Get the singular form of the Text object.
	 *
	 * @return Text
	 */
	public function toSingular()
	{
		$pluralizer = new EnglishPluralizer();

		return new Text($pluralizer->getSingularForm($this->string));
	}
}
