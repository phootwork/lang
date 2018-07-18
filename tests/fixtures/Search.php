<?php

namespace phootwork\lang\tests\fixtures;

use phootwork\lang\Arrayable;

class Search implements Arrayable
{
	public function toArray()
	{
		return [' it', 'go'];
	}
}
