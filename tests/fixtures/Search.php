<?php declare(strict_types=1);

namespace phootwork\lang\tests\fixtures;

use phootwork\lang\Arrayable;

class Search implements Arrayable
{
	public function toArray(): array
	{
		return [' it', 'go'];
	}
}
