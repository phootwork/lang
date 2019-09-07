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

trait AddAllPart {
	abstract public function add($element);

	/**
	 * Adds all elements to the array
	 *
	 * @param array|\Iterator $array
	 *
	 * @return $this
	 */
	public function addAll($array): self {
		foreach ($array as $element) {
			$this->add($element);
		}

		return $this;
	}
}
