<?php
namespace phootwork\lang\tests;

use phootwork\lang\String;

class StringTest extends \PHPUnit_Framework_TestCase {
	
	public function testToString() {
		$str = new String('bla');
		$this->assertEquals('bla', ''.$str);
		
		$str = String::create('bla');
		$this->assertEquals('bla', ''.$str);
	}
	
	public function testOccurences() {
		$str = new String('let it go');
		
		$this->assertTrue($str->startsWith('let'));
		$this->assertFalse($str->startsWith('go'));
		
		$this->assertTrue($str->endsWith('go'));
		$this->assertFalse($str->endsWith('let'));
		
		$this->assertTrue($str->contains('it'));
		$this->assertFalse($str->contains('Hulk'));
		
		$this->assertTrue($str->equals('let it go'));
		$this->assertFalse($str->equals('Let It Go'));
		$this->assertTrue($str->equalsIgnoreCase('Let It Go'));
		
		$this->assertFalse($str->isEmpty());
	}
	
	public function testSlicing() {
		$str = new String('let it go');
		
// 		$this->assertEquals('let', $str->slice(0, 3));
// 		$this->assertEquals('it', $str->slice(4, 2));
// 		$this->assertEquals(new String(''), $str->slice(5, 0));
// 		$this->assertEquals('it go', $str->slice(4));
		// TODO: Negative values for slice - what behavior should it be?
		
		$this->assertEquals('it go', $str->substring(4));
		$this->assertEquals('let', $str->substring(0, 3));
		$this->assertEquals('it', $str->substring(4, 6));
		$this->assertEquals('et it g', $str->substring(1, -1));
		$this->assertEquals('g', $str->substring(7, -1));
	}
	
	public function testMutators() {
		$str = new String('it');
		
		$this->assertEquals('let it', $str->prepend('let '));
		$this->assertEquals('it go', $str->append(' go'));
	}
	
	
}
