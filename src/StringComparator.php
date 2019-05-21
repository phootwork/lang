<?php declare(strict_types=1);
namespace phootwork\lang;

/**
 * String comparison
 */
class StringComparator implements Comparator {
	
	public function compare($a, $b): int {
		return strcmp($a, $b);
	}
	
}
