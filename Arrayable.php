<?php declare(strict_types=1);
/**
 * This file is part of the Phootwork package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license MIT License
 * @copyright Thomas Gossmann
 */
namespace phootwork\lang;

interface Arrayable {

	/**
	 * Array representation of the object
	 * 
	 * @return array
	 */
    public function toArray(): array;
}
