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

trait RemovePart {
	/**
	 * Removes one or more elements from the array
	 *
	 * @param mixed ...$elements
	 *
	 * @return $this
	 */
	public function remove(...$elements): self {
		foreach ($elements as $element) {
			$index = array_search($element, $this->array, true);
			if ($index !== false) {
				unset($this->array[$index]);
			}
		}

		return $this;
	}
}
