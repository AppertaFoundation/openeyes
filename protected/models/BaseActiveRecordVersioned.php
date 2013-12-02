<?php
/**
 * OpenEyes
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

/**
 * A class that all OpenEyes active record classes should extend.
 *
 * Currently its only purpose is to remove all html tags to
 * prevent XSS.
 */
class BaseActiveRecordVersioned extends BaseActiveRecord
{
	private $enable_archive = true;

	/* Disable archiving on save() */

	public function noArchive()
	{
		$this->enable_archive = false;

		return $this;
	}

	/* Re-enable archiving on save() */

	public function withArchive()
	{
		$this->enable_archive = true;

		return $this;
	}

	/* Test if the current model instance is an archived (previous) row */

	public function isArchived()
	{
		return preg_match('/Archive$/',get_class($this));
	}

	/* Get the archive model for the current non-archived model */

	public function archiveModel()
	{
		$archive_model = get_class($this).'Archive';

		return $archive_model::model();
	}

	/* Returns a new archive model object for the current non-archived model */

	public function newArchiveModel()
	{
		$archive_model = get_class($this).'Archive';

		return new $archive_model;
	}

	/* Return the version prior to the current one, or NULL if there isn't one */

	public function getPreviousVersion()
	{
		if (!$this->isArchived()) {
			return $this->archiveModel()->find(array(
				'condition' => 'rid = :rid',
				'params' => array(
					':rid' => $this->id
				),
				'limit' => 1,
				'offset' => 1,
				'order' => 'id desc',
			));
		}

		return $this->model()->find(array(
			'condition' => 'rid = :rid and id < :id',
			'params' => array(
				':rid' => $this->rid,
				':id' => $this->id,
			),
			'order' => 'id desc',
		));
	}

	/* Return all previous versions ordered by most recent */

	public function getPreviousVersions()
	{
		if (!$this->isArchived()) {
			return $this->archiveModel()->findAll(array(
				'condition' => 'rid = :rid',
				'params' => array(
					':rid' => $this->id,
				),
				'offset' => 1,
				'limit' => 99999999999, /* this is ugly but limit is required to use offset */
				'order' => 'id desc',
			));
		}

		return $this->model()->findAll(array(
			'condition' => 'rid = :rid and id < :id',
			'params' => array(
				':rid' => $this->rid,
				':id' => $this->id,
			),
			'order' => 'id desc',
		));
	}

	public function save($runValidation=true, $attributes=null, $allow_overriding=false, $save_archive=false)
	{
		if (preg_match('/Archive$/',get_class($this))) {
			if ($save_archive) {
				return parent::save($runValidation, $attributes, $allow_overriding);
			}

			throw new Exception("save() should not be called on archive models here: ".get_class($this));
		}

		if ($this->enable_archive) {
			$archive = $this->newArchiveModel();

			foreach ($this as $key => $value) {
				if ($key == 'id') {
					$key = 'rid';
				}

				$archive->{$key} = $value;
			}

			if (!$archive->save(true, null, true, true)) {
				throw new Exception("Unable to save archive model ".get_class($archive_model).": ".print_r($archive_model->getErrors(),true));
			}
		}

		return parent::save($runValidation, $attributes, $allow_overriding);
	}
}
