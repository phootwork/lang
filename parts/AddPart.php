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

trait AddPart {
	/**
	 * Adds an element to that array
	 *
	 * @param mixed $element
	 * @param int $index
	 *
	 * @return $this
	 */
	public function add($element, ?int $index = null): self {
		if ($index === null) {
			$this->array[count($this->array)] = $element;
		} else {
			array_splice($this->array, $index, 0, $element);
		}

		return $this;
	}
}
