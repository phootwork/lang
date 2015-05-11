<?php
namespace phootwork\lang;

class ComparableComparator implements Comparator {

	public function compare($a, $b) {
		return $a->compareTo($b);
	}

}
