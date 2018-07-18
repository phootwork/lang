<?php

namespace phootwork\lang\tests\fixtures;


use phootwork\lang\Arrayable;

class Replace implements Arrayable
{
	public function toArray()
	{
		return ["'s", 'run'];
	}
}
