<?php
namespace phootwork\lang\tests;

use phootwork\lang\Text;

class TextTest extends \PHPUnit_Framework_TestCase {
	
	public function testToText() {
		$str = new Text('bla');
		$this->assertEquals('bla', ''.$str);
		
		$str = Text::create('bla');
		$this->assertEquals('bla', ''.$str);
	}
	
	public function testOccurences() {
		$str = new Text('let it go');
		
		$this->assertTrue($str->startsWith('let'));
		$this->assertTrue($str->startsWith(new Text('let')));
		$this->assertFalse($str->startsWith('go'));
		$this->assertFalse($str->startsWith(new Text('go')));
		
		$this->assertTrue($str->endsWith('go'));
		$this->assertTrue($str->endsWith(new Text('go')));
		$this->assertFalse($str->endsWith('let'));
		$this->assertFalse($str->endsWith(new Text('let')));
		
		$this->assertTrue($str->contains('it'));
		$this->assertTrue($str->contains(new Text('it')));
		$this->assertFalse($str->contains('Hulk'));
		$this->assertFalse($str->contains(new Text('Hulk')));
		
		$this->assertTrue($str->equals('let it go'));
		$this->assertTrue($str->equals(new Text('let it go')));
		$this->assertFalse($str->equals('Let It Go'));
		$this->assertTrue($str->equalsIgnoreCase('Let It Go'));
		$this->assertTrue($str->equalsIgnoreCase(new Text('Let It Go')));
		
		$this->assertFalse($str->isEmpty());
	}

	public function testSlicing() {
		$str = new Text('let it go');
		
// 		$this->assertEquals('let', $str->slice(0, 3));
// 		$this->assertEquals('it', $str->slice(4, 2));
// 		$this->assertEquals(new Text(''), $str->slice(5, 0));
// 		$this->assertEquals('it go', $str->slice(4));
		// TODO: Negative values for slice - what behavior should it be?

		$this->assertEquals('it go', $str->subString(4));
		$this->assertEquals('let', $str->subString(0, 3));
		$this->assertEquals('it', $str->subString(4, 6));
		$this->assertEquals('et it g', $str->subString(1, -1));
		$this->assertEquals('g', $str->subString(7, -1));
	}

	public function testMutators() {
		$str = new Text('it');
		
		$this->assertEquals('let it', $str->prepend('let '));
		$this->assertEquals('let it', $str->prepend(new Text('let ')));
		$this->assertEquals('it go', $str->append(' go'));
		$this->assertEquals('it go', $str->append(new Text(' go')));
	}
	
	public function testTrimming() {
		$str = new Text('  let it go  ');
		$this->assertEquals('let it go  ', $str->trimLeft());
		$this->assertEquals('  let it go', $str->trimRight());
		$this->assertEquals('let it go', $str->trim());
	}
	
	public function testPadding() {
		$str = new Text('let it go');
		$this->assertEquals('-=let it go', $str->padLeft(11, '-='));
		$this->assertEquals('-=let it go', $str->padLeft(11, new Text('-=')));
		$this->assertEquals('let it go=-', $str->padRight(11, '=-'));
		$this->assertEquals('let it go=-', $str->padRight(11, new Text('=-')));
	}
	
	public function testIndexSearch() {
		$str = new Text('let it go');
		$this->assertEquals(4, $str->indexOf('it'));
		$this->assertEquals(4, $str->indexOf(new Text('it')));
	}
}
