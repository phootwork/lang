<?php declare(strict_types=1);

namespace phootwork\lang\tests\fixtures;


use phootwork\lang\Arrayable;

class Replace implements Arrayable
{
	public function toArray(): array
	{
		return ["'s", 'run'];
	}
}
