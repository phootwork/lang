<?php
namespace phootwork\lang;

interface Comparator {
	
	/**
	 * Compares two objects
	 *
	 * @param mixed $a
	 * @param mixed $b
	 * @return int
	 * 		Return Values:
	 * 		< 0 if the $a is less than $b
	 * 		> 0 if the $a is greater than $b
	 * 		0 if they are equal.
	 */
	public function compare($a, $b);
	
}
