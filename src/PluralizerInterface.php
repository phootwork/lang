<?php

namespace phootwork\lang;

/**
 * The generic interface to create a plural form of a name.
 *
 * @author Hans Lellelid <hans@xmpl.org>
 * @author Cristiano Cinotti <cristianocinotti@gmail.com>
 */
interface PluralizerInterface {
	/**
	 * Generate a plural name based on the passed in root.
	 *
	 * @param  string $root The root that needs to be pluralized (e.g. Author)
	 * @return string The plural form of $root.
	 */
	public function getPluralForm($root);

	/**
	 * Generate a singular name based on the passed in root.
	 *
	 * @param  string $root The root that needs to be singularized (e.g. Authors)
	 * @return string The singular form of $root.
	 */
	public function getSingularForm($root);
}
