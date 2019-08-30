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
	 * Removes an element from the array
	 *
	 * @param mixed $element
	 * @return $this
	 */
	public function remove($element): self {
		$index = array_search($element, $this->array, true);
		if ($index !== false) {
			unset($this->array[$index]);
		}

		return $this;
	}

	/**
	 * Removes all elements from the array
	 *
	 * @param array|\Iterator $array
	 * @return $this
	 */
	public function removeAll($array): self {
		foreach ($array as $element) {
			$this->remove($element);
		}

		return $this;
	}
}
