<?php declare(strict_types=1);
namespace phootwork\lang;

class ComparableComparator implements Comparator {

	public function compare($a, $b): int {
		return $a->compareTo($b);
	}

}
