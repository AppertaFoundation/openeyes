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

namespace services;

/**
 * Not to be confused with a CommissioningBodyService
 */
class CommissioningBodyService extends ModelService
{
	static protected $operations = array(self::OP_READ, self::OP_UPDATE, self::OP_CREATE, self::OP_SEARCH);

	static protected $search_params = array(
		'id' => self::TYPE_TOKEN,
		'identifier' => self::TYPE_TOKEN,
	);

	static protected $primary_model = 'CommissioningBody';

	public function search(array $params)
	{
		$model = $this->getSearchModel();
		if (isset($params['id'])) $model->id = $id;
		if (isset($params['identifier'])) $model->code = $params['identifier'];

		return $this->getResourcesFromDataProvider($model->search());
	}

	public function modelToResource($cb)
	{
		$res = parent::modelToResource($cb);
		$res->code = $cb->code;
		$res->name = $cb->name;
		if ($cb->contact->address) $res->address = Address::fromModel($cb->contact->address);
		return $res;
	}

	public function resourceToModel($res, $cb)
	{
		$cb->code = $res->code;
		$cb->name = $res->name;

		// Hard-coded for now
		$type = \CommissioningBodyType::model()->findByAttributes(array('shortname' => 'CCG'));
		if (!$type) {
			throw new \Exception("Failed to find commissioning body type 'CCG'");
		}
		$cb->commissioning_body_type_id = $type->id;

		$this->saveModel($cb);

		if ($res->address) {
			if (!($address = $cb->contact->address)) {
				$address = new \Address;
				$address->contact_id = $cb->contact->id;
			}

			$res->address->toModel($address);
			$this->saveModel($address);
		}

		// Associate with any services already in the db
		$crit = new \CDbCriteria;
		$crit->compare('code', $res->code);
		\CommissioningBodyService::model()->updateAll(array('commissioning_body_id' => $cb->id), $crit);
	}
}
