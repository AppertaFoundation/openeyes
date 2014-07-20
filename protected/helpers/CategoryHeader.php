<?php
/**
 * (C) OpenEyes Foundation, 2014
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2014, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

/**
 * Implements http://tools.ietf.org/html/draft-johnston-http-category-header-02
 */
class CategoryHeader
{
	static public function load()
	{
		return self::parse(@$_SERVER['HTTP_CATEGORY']);
	}

	/**
	 * @param string $header
	 */
	static public function parse($header)
	{
		$categories = array();

		if (!empty($header)) {
			foreach (preg_split('/,(?=(?:[^"]*"[^"]*")*[^"]*$)/', $header) as $category) {
				$bits = preg_split('/;(?=(?:[^"]*"[^"]*")*[^"]*$)/', $category);

				$term = trim(array_shift($bits));
				$scheme = "";

				foreach ($bits as $bit) {
					if (preg_match('/scheme=(.*)/', $bit, $m)) {
						$scheme = trim($m[1], '"');
					}
				}

				$categories[$scheme][] = $term;
			}
		}

		return new self($categories);
	}

	protected $categories;

	/**
	 * @param array $categories
	 */
	public function __construct(array $categories)
	{
		$this->categories = $categories;
	}

	/**
	 * @param string $scheme
	 * @return array
	 */
	public function get($scheme = "")
	{
		return isset($this->categories[$scheme]) ? $this->categories[$scheme] : array();
	}
}
