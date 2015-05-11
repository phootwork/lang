<?php
namespace phootwork\lang;

interface Comparable {

	/**
	 * Compares to another object
	 *
	 * @param mixed $comparison
	 * @return int
	 * 		Return Values:
	 * 		< 0 if the object is less than comparison
	 * 		> 0 if the object is greater than comparison
	 * 		0 if they are equal.
	 */
	public function compareTo($comparison);

}
