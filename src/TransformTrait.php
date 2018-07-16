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
	public function toPlural() {
		$pluralizer = new EnglishPluralizer();

		return new Text($pluralizer->getPluralForm($this->string));
	}

	/**
	 * Get the singular form of the Text object.
	 *
	 * @return Text
	 */
	public function toSingular() {
		$pluralizer = new EnglishPluralizer();

		return new Text($pluralizer->getSingularForm($this->string));
	}

	/**
	 * Convert a string from underscore or kebab-case to camel case.
	 * E.g. my_own_variable => myOwnVariable
	 *
	 * @return Text
	 */
	public function toCamelCase() {
		return $this->toStudlyCase()->toLowerCaseFirst();
	}

	/**
	 * Convert a string from camel case or kebab-case to underscore.
	 * E.g. myOwnVariable => my_own_variable.
	 *
	 * Numbers are considered as part of its previous piece:
	 * E.g. myTest3Variable => my_test3_variable
	 *
	 * @return Text
	 */
	public function toSnakeCase() {
		if ($this->contains('-')) {
			return $this->replace('-', '_');
		}

		return new Text(strtolower(preg_replace('/([a-z0-9])([A-Z])/', '$1_$2', $this->string)));
	}

	/**
	 * Convert a string from underscore or kebab-case to camel case, with upper-case first letter.
	 * This function is useful while writing getter and setter method names.
	 * E.g. my_own_variable => MyOwnVariable
	 *
	 * @return Text
	 */
	public function toStudlyCase() {
		$separator = '_';

		if ($this->contains('-')) {
			$separator = '-';
		}

		return new Text(implode('', array_map('ucfirst', explode($separator, $this->string))));
	}

	/**
	 * Convert a string from camel case orsnake case to kebab-case.
	 * E.g. myOwnVariable => my-own-variable.
	 *
	 * Numbers are considered as part of its previous piece:
	 * E.g. myTest3Variable => my_test3_variable
	 *
	 * @return Text
	 */
	public function toKebabCase() {
		if ($this->contains('_')) {
			return $this->replace('_', '-');
		}

		return new Text(strtolower(preg_replace('/([a-z0-9])([A-Z])/', '$1-$2', $this->string)));
	}
}
