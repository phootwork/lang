<?php
namespace phootwork\lang\tests;

use phootwork\lang\ArrayObject;

class ArrayTest extends \PHPUnit_Framework_TestCase {
	
	public function testCount() {
		$arr = new ArrayObject(['these', 'are', 'my', 'items']);
		
		$this->assertEquals(4, $arr->count());
		$this->assertEquals(4, count($arr));
	}
	
}
