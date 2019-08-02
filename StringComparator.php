<?php
namespace phootwork\lang;

/**
 * String comparison
 */
class StringComparator implements Comparator {
	
	public function compare($a, $b) {
		return strcmp($a, $b);
	}
	
}
