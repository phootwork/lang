<?php
namespace phootwork\lang\tests;

use PHPUnit\Framework\TestCase;
use phootwork\lang\text\EnglishPluralizer;

/**
 * Tests for the StandardEnglishPluralizer class
 *
 */
class EnglishPluralizerTest extends TestCase {
	public function getPluralFormDataProvider() {
		return [
			['', 's'],
			['user', 'users'],
			['User', 'Users'],
			['sheep', 'sheep'],
			['Sheep', 'Sheep'],
			['wife', 'wives'],
			['Wife', 'Wives'],
			['country', 'countries'],
			['Country', 'Countries'],
			['Video', 'Videos'],
			['video', 'videos'],
			['Photo', 'Photos'],
			['photo', 'photos'],
			['Tomato', 'Tomatoes'],
			['tomato', 'tomatoes'],
			['Buffalo', 'Buffaloes'],
			['buffalo', 'buffaloes'],
			['typo', 'typos'],
			['Typo', 'Typos'],
			['apple', 'apples'],
			['Apple', 'Apples'],
			['Man', 'Men'],
			['man', 'men'],
			['numen', 'numina'],
			['Numen', 'Numina'],
			['bus', 'buses'],
			['Bus', 'Buses'],
			['news', 'news'],
			['News', 'News'],
			['food_menu', 'food_menus'],
			['Food_menu', 'Food_menus'],
			['quiz', 'quizzes'],
			['Quiz', 'Quizzes'],
			['alumnus', 'alumni'],
			['Alumnus', 'Alumni'],
			['vertex', 'vertices'],
			['Vertex', 'Vertices'],
			['matrix', 'matrices'],
			['Matrix', 'Matrices'],
			['index', 'indices'],
			['Index', 'Indices'],
			['alias', 'aliases'],
			['Alias', 'Aliases'],
			['bacillus', 'bacilli'],
			['Bacillus', 'Bacilli'],
			['cactus', 'cacti'],
			['Cactus', 'Cacti'],
			['focus', 'foci'],
			['Focus', 'Foci'],
			['fungus', 'fungi'],
			['Fungus', 'Fungi'],
			['nucleus', 'nuclei'],
			['Nucleus', 'Nuclei'],
			['radius', 'radii'],
			['Radius', 'Radii'],
			['people', 'people'],
			['People', 'People'],
			['glove', 'gloves'],
			['Glove', 'Gloves'],
			['crisis', 'crises'],
			['Crisis', 'Crises'],
			['tax', 'taxes'],
			['Tax', 'Taxes'],
			['Tooth', 'Teeth'],
			['tooth', 'teeth'],
			['Foot', 'Feet'],
		];
	}

	public function providerForWrongType() {
		return [
			[null],
			[[1, 2, 3]],
			[245],
			[['apple' => 'fruit', 'tomato' => 'vegetables']],
			[new \StdClass()],
			[true],
			[false]
		];
	}

	/**
	 * @dataProvider getPluralFormDataProvider
	 */
	public function testPluralForm($input, $output) {
		$pluralizer = new EnglishPluralizer();
		$this->assertEquals($output, $pluralizer->getPluralForm($input));
	}

	/**
	 * @dataProvider providerForWrongType
	 * @expectedException \InvalidArgumentException
	 */
	public function testWrongTypeToPluralizeThrowsException($wrong) {
		$pluralizer = new EnglishPluralizer();
		$pluralizer->getPluralForm($wrong);
	}

	/**
	 * @dataProvider getPluralFormDataProvider
	 */
	public function testSingularForm($output, $input) {
		$pluralizer = new EnglishPluralizer();
		$this->assertEquals($output, $pluralizer->getSingularForm($input));
	}

	/**
	 * @dataProvider providerForWrongType
	 * @expectedException \InvalidArgumentException
	 */
	public function testWrongTypeToSingularizeThrowsException($wrong) {
		$pluralizer = new EnglishPluralizer();
		$pluralizer->getSingularForm($wrong);
	}

	public function testSingularizeSingularForm() {
		$pluralizer = new EnglishPluralizer();
		$this->assertEquals('book', $pluralizer->getSingularForm('book'), '`book` is already singular.');
		$this->assertEquals('Book', $pluralizer->getSingularForm('Book'), '`Book` is already singular.');
		$this->assertEquals('foot', $pluralizer->getSingularForm('foot'), '`foot` is already singular.');
		$this->assertEquals('people', $pluralizer->getSingularForm('people'), '`peolple` is uncountable, so it cannot be singularized.');
		$this->assertEquals('food_menu', $pluralizer->getSingularForm('food_menu'), '`food_menu` is already singular.');
	}

	public function testPluralizePluralForm() {
		$pluralizer = new EnglishPluralizer();
		$this->assertEquals('books', $pluralizer->getPluralForm('books'), '`books` is already plural.');
		$this->assertEquals('Books', $pluralizer->getPluralForm('Books'), '`Books` is already plural.');
		$this->assertEquals('feet', $pluralizer->getPluralForm('feet'), '`feet` is already plural.');
		$this->assertEquals('people', $pluralizer->getPluralForm('people'), '`peolple` is uncountable, so it cannot be pluralized.');
		$this->assertEquals('food_menus', $pluralizer->getPluralForm('food_menus'), '`food_menus` is already plural.');
	}

	/**
	 * @dataProvider getPluralFormDataProvider
	 */
	public function testIsPlural($singular, $plural) {
		$pluralizer = new EnglishPluralizer();
		$this->assertTrue($pluralizer->isPlural($plural));
	}

	/**
	 * @dataProvider getPluralFormDataProvider
	 */
	public function testIsSingular($singular, $plural) {
		$pluralizer = new EnglishPluralizer();
		$this->assertTrue($pluralizer->isSingular($singular));
	}
}
