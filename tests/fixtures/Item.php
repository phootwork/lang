<?php declare(strict_types=1);
namespace phootwork\lang\tests\fixtures;

use phootwork\lang\Comparable;

class Item implements Comparable {
	
	private $content;
	
	public function __construct(string $content = '') {
		$this->content = $content;
	}
	
	public function compareTo($comparison): int {
		return strcmp($this->content, $comparison->getContent());
	}
	
	/**
	 * @return mixed
	 */
	public function getContent() {
		return $this->content;
	}
	
	/**
	 *
	 * @param mixed $content        	
	 */
	public function setContent($content): self {
		$this->content = $content;
		return $this;
	}
	
}