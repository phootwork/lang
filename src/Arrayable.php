<?php declare(strict_types=1);
namespace phootwork\lang;

interface Arrayable {
	
	/**
	 * Array representation of the object
	 * 
	 * @return array
	 */
	public function toArray(): array;
	
}
