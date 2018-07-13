<?php
namespace phootwork\lang\tests;

use phootwork\lang\ArrayObject;
use phootwork\lang\tests\fixtures\Replace;
use phootwork\lang\tests\fixtures\Search;
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
		
 		$this->assertEquals('let', $str->slice(0, 3));
 		$this->assertEquals('it', $str->slice(4, 2));
 		$this->assertEquals(new Text(''), $str->slice(5, 0));
 		$this->assertEquals('it go', $str->slice(4));
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

	public function testIndexSearchNullString() {
		$str = new Text('let it go');
		$this->assertEquals(0, $str->indexOf(''));
		$this->assertEquals(0, $str->indexOf(new Text('')));
	}

	public function testToLowerCase() {
		$str = new Text('LET IT GO');
		$lower = $str->toLowerCase();
		$this->assertInstanceOf(Text::class, $lower);
		$this->assertEquals('let it go', $lower->toString());
		$this->assertEquals('=let it go', '=' . $str->toLowerCase());
	}

	public function testToLowerCaseFirst() {
		$str = new Text('LET IT GO');
		$lower = $str->toLowerCaseFirst();
		$this->assertInstanceOf(Text::class, $lower);
		$this->assertEquals('lET IT GO', $lower->toString());
		$this->assertEquals('=lET IT GO', '=' . $str->toLowerCaseFirst());
	}

	public function testToUpperCase() {
		$str = new Text('let it go');
		$upper = $str->toUpperCase();
		$this->assertInstanceOf(Text::class, $upper);
		$this->assertEquals('LET IT GO', $upper->toString());
		$this->assertEquals('=LET IT GO', '=' . $str->toUpperCase());
	}

	public function testToUpperCaseFirst() {
		$str = new Text('let it go');
		$upper = $str->toUpperCaseFirst();
		$this->assertInstanceOf(Text::class, $upper);
		$this->assertEquals('Let it go', $upper->toString());
		$this->assertEquals('=Let it go', '=' . $str->toUpperCaseFirst());
	}

	public function testToUpperCaseWords() {
		$str = new Text('let it go');
		$upper = $str->toUpperCaseWords();
		$this->assertInstanceOf(Text::class, $upper);
		$this->assertEquals('Let It Go', $upper->toString());
		$this->assertEquals('=Let It Go', '=' . $str->toUpperCaseWords());
	}

	public function testToCapitalCase()	{
		$str = new Text('let it go');
		$upper = $str->toCapitalCase();
		$this->assertInstanceOf(Text::class, $upper);
		$this->assertEquals('Let it go', $upper->toString());
		$this->assertEquals('=Let it go', '=' . $str->toCapitalCase());
	}

	public function testToCapitalCaseWords() {
		$str = new Text('let it go');
		$upper = $str->toCapitalCaseWords();
		$this->assertInstanceOf(Text::class, $upper);
		$this->assertEquals('Let It Go', $upper->toString());
		$this->assertEquals('=Let It Go', '=' . $str->toCapitalCaseWords());
	}

	public function testReplace() {
		$str = new Text('let it go');

		// string
		$repl = $str->replace(' it', '\'s');
		$this->assertEquals('let\'s go', $repl);
		$this->assertInstanceOf(Text::class, $repl);

		// Text objects
		$repl = $str->replace(new Text(" it"), new Text("'s"));
		$this->assertEquals('let\'s go', $repl);
		$this->assertInstanceOf(Text::class, $repl);

		// array
		$search = [' it', 'go'];
		$replace = ["'s", 'run'];

		$repl = $str->replace($search, $replace);
		$this->assertEquals('let\'s run', $repl);
		$this->assertInstanceOf(Text::class, $repl);

		// Arrayable
		$repl = $str->replace(new Search(), new Replace());
		$this->assertEquals('let\'s run', $repl);
		$this->assertInstanceOf(Text::class, $repl);
	}

	public function testSupplant() {
		$str = new Text('let it go');
		$search = [' it' => "'s", 'go' => 'run'];

		$repl = $str->supplant($search);
		$this->assertEquals('let\'s run', $repl);
		$this->assertInstanceOf(Text::class, $repl);
	}

	public function testSplice() {
		$str = new Text('Text to splice');

		$repl = $str->splice('', 4);
		$this->assertInstanceOf(Text::class, $repl);
		$this->assertEquals('Text', $repl);

		$repl = $str->splice('', -4);
		$this->assertInstanceOf(Text::class, $repl);
		$this->assertEquals('Text to sp', $repl);

		$repl = $str->splice('beautifull ', 5, 0);
		$this->assertInstanceOf(Text::class, $repl);
		$this->assertEquals('Text beautifull to splice', $repl);

		$repl = $str->splice(' you can', 4, 3);
		$this->assertInstanceOf(Text::class, $repl);
		$this->assertEquals('Text you can splice', $repl);

		$repl = $str->splice('replace', -6, 6);
		$this->assertInstanceOf(Text::class, $repl);
		$this->assertEquals('Text to replace', $repl);

		$repl = $str->splice('replace and ', 8, -6);
		$this->assertInstanceOf(Text::class, $repl);
		$this->assertEquals('Text to replace and splice', $repl);

		$repl = $str->splice(new Text(' you can'), 4, 3);
		$this->assertInstanceOf(Text::class, $repl);
		$this->assertEquals('Text you can splice', $repl);
	}

	/**
	 * @expectedException \InvalidArgumentException
	 * @expectedExceptionMessage Offset must be in range [-len, len]
	 */
	public function testSpliceWrongOffsetThrowsException() {
		$str = new Text('Text to splice');
		$str->splice('', 25);
	}

	/**
	 * @expectedException \InvalidArgumentException
	 * @expectedExceptionMessage Offset must be in range [-len, len]
	 */
	public function testSpliceWrongNegativeOffsetThrowsException() {
		$str = new Text('Text to splice');
		$str->splice('', -25);
	}

	/**
	 * @expectedException \InvalidArgumentException
	 * @expectedExceptionMessage Length too large
	 */
	public function testSpliceWrongLengthThrowsException() {
		$str = new Text('Text to splice');
		$str->splice('test', 4, 20);
	}

	/**
	 * @expectedException \InvalidArgumentException
	 * @expectedExceptionMessage Length too small
	 */
	public function testSpliceLengthSmallThrowsException() {
		$str = new Text('Text to splice');
		$str->splice('test', -4, -12);
	}

	public function testCharAt() {
		$str = new Text('Text to splice');
		$pos = $str->charAt(5);
		$this->assertSame('t', $pos);
	}

	public function testLastIndexOf() {
		$str = new Text('Text to test');
		$this->assertEquals(5, $str->lastIndexOf('to'));
		$this->assertEquals(12, $str->lastIndexOf(''));
		$this->assertEquals(3, $str->lastIndexOf('', 3));
	}

	public function testCount() {
		$str = new Text('Text to count total occurrencies');
		$this->assertEquals(2, $str->count('to'));
		$this->assertEquals(5, $str->count(new Text('t')));
		$this->assertEquals(1, $str->count('to', 10));
		$this->assertEquals(0, $str->count('to', 10, 5));
		$this->assertEquals(1, $str->count('to', 10, 10));
		$this->assertEquals(1, $str->count('to', 10, -2));
		$this->assertEquals(1, $str->count('rr', -10, 5));
		$this->assertEquals(1, $str->count('rr', -10, 5));
		$this->assertEquals(33, $str->count(''));
	}

	/**
	 * @expectedException \InvalidArgumentException
	 * @expectedExceptionMessage Offset must be in range [-len, len]
	 */
	public function testCountWrongOffsetThrowsException() {
		$str = new Text('Text to count');
		$str->splice('', 25);
	}

	public function testMatch() {
		$str = new Text('Text to search');
		$this->assertSame(true, $str->match('/to/'));
	}

	public function testToPlural() {
		$str = new Text('Book');
		$plural = $str->toPlural();
		$this->assertEquals('Books', $plural);
		$this->assertInstanceOf(Text::class, $plural);
	}

	public function testToSingular() {
		$str = new Text('teeth');
		$singular = $str->toSingular();
		$this->assertEquals('tooth', $singular);
		$this->assertInstanceOf(Text::class, $singular);
	}

	public function testToPluralManyWords() {
		$str = new Text('The book is on the table');
		$plural = $str->toPlural();
		$this->assertEquals('The book is on the tables', $plural);
	}

	public function testWrapWords() {
		$text = new Text(file_get_contents(__DIR__ . '/fixtures/lorem.txt'));
		$wrapped = $text->wrapWords();
		$this->assertInstanceOf(Text::class, $wrapped);

		$expected = "Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod
tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim
veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea
commodo consequat. Duis aute irure dolor in reprehenderit in voluptate
velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat
cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id
est laborum.";
		$this->assertEquals($expected, $wrapped);
	}

	public function testWrapWordsCut() {
		$text = new Text(file_get_contents(__DIR__ . '/fixtures/lorem.txt'));
		$wrapped = $text->wrapWords(20, "\n", true);
		$expected = "Lorem ipsum dolor
sit amet,
consectetur
adipiscing elit, sed
do eiusmod tempor
incididunt ut labore
et dolore magna
aliqua. Ut enim ad
minim veniam, quis
nostrud exercitation
ullamco laboris nisi
ut aliquip ex ea
commodo consequat.
Duis aute irure
dolor in
reprehenderit in
voluptate velit esse
cillum dolore eu
fugiat nulla
pariatur. Excepteur
sint occaecat
cupidatat non
proident, sunt in
culpa qui officia
deserunt mollit anim
id est laborum.";
		$this->assertEquals($expected, $wrapped);
	}

	public function testWrapWordsCustomBreak() {
		$text = new Text(file_get_contents(__DIR__ . '/fixtures/lorem.txt'));
		$wrapped = $text->wrapWords(75, "**", true);
		$expected = "Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod**tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim**veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea**commodo consequat. Duis aute irure dolor in reprehenderit in voluptate**velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat**cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id**est laborum.";
		$this->assertEquals($expected, $wrapped);
	}

	public function testRepeat() {
		$str = new Text('repeat');
		$rep = $str->repeat(4);
		$this->assertInstanceOf(Text::class, $rep);
		$this->assertEquals('repeatrepeatrepeatrepeat', $rep);
		$this->assertEquals('', $str->repeat(0));
	}

	/**
	 * @expectedException \InvalidArgumentException
	 * @expectedExceptionMessage Number of repetitions can not be negative
	 */
	public function testRepeatNegativeTimesThrowsException() {
		$str = new Text('repeat');
		$str->repeat(-2);
	}

	public function testReverse() {
		$str = new Text("Hello world!");
		$rev = $str->reverse();
		$this->assertInstanceOf(Text::class, $rev);
		$this->assertEquals("!dlrow olleH", $rev);
	}

	public function testChunk() {
		$str = new Text('Let it go');
		$splitted = $str->chunk();
		$this->assertInstanceOf(ArrayObject::class, $splitted);
		$this->assertEquals(['L', 'e', 't', ' ', 'i', 't', ' ', 'g', 'o'], $splitted->toArray());

		$splitted = $str->chunk(3);
		$this->assertEquals(['Let', ' it', ' go'], $splitted->toArray());

		$splitted = $str->chunk(30);
		$this->assertEquals(['Let it go'], $splitted->toArray());
	}

	/**
	 * @expectedException \InvalidArgumentException
	 * @expectedExceptionMessage The chunk length has to be positive
	 */
	public function testChunkNegativeLengthThrowsException() {
		$str = new Text('Let it go');
		$str->chunk(-1);
	}

	/**
	 * @expectedException \InvalidArgumentException
	 * @expectedExceptionMessage The chunk length has to be positive
	 */
	public function testChunkZeroLengthThrowsException() {
		$str = new Text('Let it go');
		$str->chunk(0);
	}

	public function testIsAlphanumeric()
	{
		$this->assertTrue((new Text('AbCd1zyZ9'))->isAlphanumeric());
		$this->assertFalse((new Text('AbC?d1z#yZ$9'))->isAlphanumeric());
		$this->assertFalse((new Text(''))->isAlphanumeric(), 'Null string is not alphanumeric');
	}

	public function testIsAlphabetic()
	{
		$this->assertTrue((new Text('AbCdzyZ'))->isAlphabetic());
		$this->assertFalse((new Text('AbC?d1z#yZ$9'))->isAlphabetic());
		$this->assertFalse((new Text('123456789'))->isAlphabetic());
		$this->assertFalse((new Text(''))->isAlphabetic(), 'Null string is not alphabetic');
	}

	public function testIsNumeric()
	{
		$this->assertTrue((new Text('125874698'))->isNumeric());
		$this->assertFalse((new Text('AbC?d1z#yZ$9'))->isNumeric());
		$this->assertFalse((new Text('qwerty'))->isNumeric());
		$this->assertFalse((new Text(''))->isNumeric(), 'Null string is not numeric');
	}

	public function testIsPunctuation()
	{
		$this->assertTrue((new Text('#@[{}?^&%.;'))->isPunctuation());
		$this->assertFalse((new Text('AbC?d1z#yZ$9'))->isPunctuation());
		$this->assertFalse((new Text(''))->isPunctuation(), 'Null string is not punctuation');
	}

	public function testIsSpace()
	{
		$this->assertTrue((new Text('  '))->isSpace());
		$this->assertFalse((new Text(' 9 '))->isSpace());
		$this->assertFalse((new Text(''))->isSpace(), 'Null string is not space');
	}

	public function testIsLowercase()
	{
		$this->assertTrue((new Text('lowercase'))->isLowerCase());
		$this->assertFalse((new Text('The show must go on'))->isLowerCase());
		$this->assertFalse((new Text(''))->isLowerCase(), 'Null string is not lowercase');

		//@todo is it a desirable behavior? Spaces are considered non-lowercase characters
		$this->assertFalse((new Text('lowercase string'))->isLowerCase());
	}

	public function testIsUpperCase()
	{
		$this->assertTrue((new Text('UPPERCASE'))->isUpperCase());
		$this->assertFalse((new Text('nONUPPERCASE'))->isUpperCase());
		$this->assertFalse((new Text(''))->isUpperCase(), 'Null string is not uppercase');

		//@todo is it a desirable behavior? Spaces are considered non-uppercase characters
		$this->assertFalse((new Text('UPPERCASE STRING'))->isUpperCase());
	}

	public function testIsSingular()
	{
		$this->assertTrue((new Text('chair'))->isSingular());
		$this->assertFalse((new Text('tables'))->isSingular());
	}

	public function testIsPlural()
	{
		$this->assertFalse((new Text('chair'))->isPlural());
		$this->assertTrue((new Text('tables'))->isPlural());
	}
}
