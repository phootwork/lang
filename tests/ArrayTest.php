<?php
namespace phootwork\lang\tests;

use phootwork\lang\ArrayObject;
use phootwork\lang\Text;
use phootwork\lang\tests\fixtures\Item;
use phootwork\lang\ComparableComparator;
use phootwork\lang\StringComparator;

class ArrayTest extends \PHPUnit_Framework_TestCase {
	
	public function testArray() {
		$base = ['a' => 'b', 'c' => 'd'];
		$arr = new ArrayObject($base);
	
		$this->assertEquals(new ArrayObject(['b', 'd']), $arr->values());
		$this->assertEquals(new ArrayObject(['a', 'c']), $arr->keys());
		$this->assertEquals($base, $arr->toArray());
	
		$new = [];
		foreach ($arr as $k => $v) {
			$new[$k] = $v;
		}
		$this->assertEquals($new, $arr->toArray());
	
		$this->assertEquals(new ArrayObject(['b' => 'a', 'd' => 'c']), $arr->flip());
		
		$arr = new ArrayObject(['these', 'are', 'my', 'items']);
		$this->assertEquals(new Text('these are my items'), $arr->join(' '));
	}
	
	public function testCount() {
		$arr = new ArrayObject(['these', 'are', 'my', 'items']);

		$this->assertEquals(4, $arr->count());
		$this->assertEquals(4, count($arr));

		$arr->merge('a', 'b');

		$this->assertEquals(6, $arr->count());
	}
	
	public function testArrayAccess() {
		$arr = new ArrayObject(['a' => 'b', 'c' => 'd']);
		
		$this->assertEquals('b', $arr['a']);
		$this->assertTrue(isset($arr['c']));
		$this->assertFalse(isset($arr['x']));
		unset($arr['c']);
		$this->assertFalse(isset($arr['c']));
		$arr['a'] = 'x';
		$this->assertEquals('x', $arr['a']);
	}
	
	public function testSerialization() {
		$arr = new ArrayObject(['these', 'are', 'my', 'items']);
		$serialized = $arr->serialize();
		
		$brr = new ArrayObject();
		$brr->unserialize($serialized);
		
		$this->assertEquals($arr, $brr);
	}
	
	public function testReduce() {
		$list = new ArrayObject(range(1, 10));
		$sum = $list->reduce(function($a, $b) {return $a + $b;});
	
		$this->assertEquals(55, $sum);
	}
	
	public function testFilter() {
		$arr = new ArrayObject(['a' => 'a', 'b' => 'b', 'c' => 'c']);
		$arr = $arr->filter(function ($item) {
			return $item != 'b';
		});

		$this->assertSame(['a' => 'a', 'c' => 'c'], $arr->toArray());
	}
	
	public function testMap() {
		$arr = new ArrayObject(['a' => 'a', 'b' => 'b', 'c' => 'c']);
		$arr = $arr->map(function ($item) {
			return $item . 'val';
		});
	
		$this->assertSame(['a' => 'aval', 'b' => 'bval', 'c' => 'cval'], $arr->toArray());
	}
	
	public function testSort() {
		$unsorted = [5, 2, 8, 3, 9, 4, 6, 1, 7, 10];
		$list = new ArrayObject($unsorted);

		$this->assertEquals(range(1, 10), $list->sort()->toArray());
		
		$list = new ArrayObject($unsorted);
		$cmp = function ($a, $b) {
			if ($a == $b) {
				return 0;
			}
			return ($a < $b) ? -1 : 1;
		};
		$this->assertEquals(range(1, 10), $list->sort($cmp)->toArray());
		
		$items = ['x', 'c', 'a', 't', 'm'];
		$list = new ArrayObject();
		foreach ($items as $item) {
			$list->push(new Item($item));
		}
		$list->sort(new ComparableComparator());
		$this->assertEquals(['a', 'c', 'm', 't', 'x'], $list->map(function ($item) {return $item->getContent();})->toArray());
	}
	
	public function testSortAssoc() {
		$arr = new ArrayObject(['b' => 'bval', 'a' => 'aval', 'c' => 'cval']);
		$arr->sortAssoc();
		$this->assertEquals(['a' => 'aval', 'b' => 'bval', 'c' => 'cval'], $arr->toArray());
	
		$arr = new ArrayObject(['b' => 'bval', 'a' => 'aval', 'c' => 'cval']);
		$arr->sortAssoc(function ($a, $b) {
			if ($a == $b) {
				return 0;
			}
			return ($a < $b) ? -1 : 1;
		});
		$this->assertEquals(['a' => 'aval', 'b' => 'bval', 'c' => 'cval'], $arr->toArray());
	
		$arr = new ArrayObject(['b' => new Item('bval'), 'a' => new Item('aval'), 'c' => new Item('cval')]);
		$arr->sortAssoc(new ComparableComparator());
		$this->assertEquals(['a' => 'aval', 'b' => 'bval', 'c' => 'cval'], $arr
				->map(function ($elem) {return $elem->getContent();})
				->toArray());
	}
	
	public function testSortKeys() {
		$arr = new ArrayObject(['b' => 'bval', 'a' => 'aval', 'c' => 'cval']);
		$arr->sortKeys();
		$this->assertEquals(['a' => 'aval', 'b' => 'bval', 'c' => 'cval'], $arr->toArray());
	
		$arr = new ArrayObject(['b' => 'bval', 'a' => 'aval', 'c' => 'cval']);
		$arr->sortKeys(function ($a, $b) {
			if ($a == $b) {
				return 0;
			}
			return ($a < $b) ? -1 : 1;
		});
		$this->assertEquals(['a' => 'aval', 'b' => 'bval', 'c' => 'cval'], $arr->toArray());
	
		$arr = new ArrayObject(['b' => 'bval', 'a' => 'aval', 'c' => 'cval']);
		$arr->sortKeys(new StringComparator());
		$this->assertEquals(['a' => 'aval', 'b' => 'bval', 'c' => 'cval'], $arr->toArray());
	}
	
	public function testMutators() {
		$base = ['b', 'c', 'd'];
		$arr = new ArrayObject($base);
		$arr->push('e', 'f');
		
		$this->assertEquals(['b', 'c', 'd', 'e', 'f'], $arr->toArray());
		$this->assertEquals('f', $arr->pop());
		$this->assertEquals('e', $arr->pop());
		$arr->prepend('a');
		$this->assertEquals(['a', 'b', 'c', 'd'], $arr->toArray());
		$this->assertEquals('a', $arr->shift());
		$this->assertEquals($base, $arr->toArray());
	}
	
}
