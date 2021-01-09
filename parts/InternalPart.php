<?php declare(strict_types=1);
/**
 * This file is part of the Phootwork package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license MIT License
 * @copyright Thomas Gossmann
 */
namespace phootwork\lang\parts;

use InvalidArgumentException;
use phootwork\lang\Text;
use Stringable;

/**
 * Internal Text methods
 *
 * @author Thomas Gossmann
 * @author Cristiano Cinotti
 */
trait InternalPart {
	abstract public function length(): int;

	/**
	 * @internal
	 *
	 * @param int $offset
	 *
	 * @return int
	 */
	protected function prepareOffset(int $offset): int {
		$len = $this->length();
		if ($offset < -$len || $offset > $len) {
			throw new InvalidArgumentException('Offset must be in range [-len, len]');
		}

		if ($offset < 0) {
			$offset += $len;
		}

		return $offset;
	}

	/**
	 * @param int      $offset
	 * @param int|null $length
	 *
	 * @return int
	 *
	 * @internal
	 *
	 */
	protected function prepareLength(int $offset, ?int $length): int {
		$length = (null === $length) ? ($this->length() - $offset) : (
			($length < 0) ? ($length + $this->length() - $offset) : $length);

		if ($length < 0) {
			throw new InvalidArgumentException('Length too small');
		}

		if ($offset + $length > $this->length()) {
			throw new InvalidArgumentException('Length too large');
		}

		return $length;
	}

	/**
	 * @param string|Stringable $string |Text $string
	 * @param string            $name
	 *
	 * @internal
	 *
	 */
	protected function verifyNotEmpty(string | Stringable $string, string $name): void {
		if (empty($string)) {
			throw new InvalidArgumentException("$name cannot be empty");
		}
	}

	/**
	 * @internal
	 *
	 * @param int $value
	 * @param string $name
	 *
	 * @throws InvalidArgumentException
	 */
	protected function verifyPositive(int $value, string $name): void {
		if ($value <= 0) {
			throw new InvalidArgumentException("$name has to be positive");
		}
	}

	/**
	 * @internal
	 *
	 * @param int $value
	 * @param string $name
	 *
	 * @throws InvalidArgumentException
	 */
	protected function verifyNotNegative(int $value, string $name): void {
		if ($value < 0) {
			throw new InvalidArgumentException("$name can not be negative");
		}
	}
}
