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
		$this->assertTrue($str->startsWith(new String('let')));
		$this->assertFalse($str->startsWith('go'));
		$this->assertFalse($str->startsWith(new String('go')));
		
		$this->assertTrue($str->endsWith('go'));
		$this->assertTrue($str->endsWith(new String('go')));
		$this->assertFalse($str->endsWith('let'));
		$this->assertFalse($str->endsWith(new String('let')));
		
		$this->assertTrue($str->contains('it'));
		$this->assertTrue($str->contains(new String('it')));
		$this->assertFalse($str->contains('Hulk'));
		$this->assertFalse($str->contains(new String('Hulk')));
		
		$this->assertTrue($str->equals('let it go'));
		$this->assertTrue($str->equals(new String('let it go')));
		$this->assertFalse($str->equals('Let It Go'));
		$this->assertTrue($str->equalsIgnoreCase('Let It Go'));
		$this->assertTrue($str->equalsIgnoreCase(new String('Let It Go')));
		
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
		$this->assertEquals('let it', $str->prepend(new String('let ')));
		$this->assertEquals('it go', $str->append(' go'));
		$this->assertEquals('it go', $str->append(new String(' go')));
	}
	
	public function testTrimming() {
		$str = new String('  let it go  ');
		$this->assertEquals('let it go  ', $str->trimLeft());
		$this->assertEquals('  let it go', $str->trimRight());
		$this->assertEquals('let it go', $str->trim());
	}
	
	public function testPadding() {
		$str = new String('let it go');
		$this->assertEquals('-=let it go', $str->padLeft(11, '-='));
		$this->assertEquals('-=let it go', $str->padLeft(11, new String('-=')));
		$this->assertEquals('let it go=-', $str->padRight(11, '=-'));
		$this->assertEquals('let it go=-', $str->padRight(11, new String('=-')));
	}
	
	public function testIndexSearch() {
		$str = new String('let it go');
		$this->assertEquals(4, $str->indexOf('it'));
		$this->assertEquals(4, $str->indexOf(new String('it')));
	}
}
