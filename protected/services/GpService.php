<?php
/**
 * (C) OpenEyes Foundation, 2014
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2014, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

namespace services;

class GpService extends ModelService
{
    protected static $operations = array(self::OP_READ, self::OP_UPDATE, self::OP_DELETE, self::OP_CREATE, self::OP_SEARCH);

    protected static $search_params = array(
        'id' => self::TYPE_TOKEN,
        'identifier' => self::TYPE_TOKEN,
    );

    protected static $primary_model = 'Gp';

    public function search(array $params)
    {
        $model = $this->getSearchModel();
        if (isset($params['id'])) {
            $model->id = $params['id'];
        }
        if (isset($params['identifier'])) {
            $model->nat_id = $params['identifier'];
        }

        return $this->getResourcesFromDataProvider($model->search());
    }

    public function modelToResource($gp)
    {
        $res = parent::modelToResource($gp);
        $res->gnc = $gp->nat_id;
        $res->title = $gp->contact->title;
        $res->family_name = $gp->contact->last_name;
        $res->given_name = $gp->contact->first_name;
        $res->primary_phone = $gp->contact->primary_phone ?: null;
        if ($gp->contact->address) {
            $resouce->address = Address::fromModel($gp->contact->address);
        }

        return $res;
    }

    public function resourceToModel($res, $gp)
    {
        $gp->nat_id = $res->gnc;
        $this->saveModel($gp);

        $contact = $gp->contact;
        $contact->title = $res->title;
        $contact->last_name = $res->family_name;
        $contact->first_name = $res->given_name;
        $contact->primary_phone = $res->primary_phone;
        $this->saveModel($contact);

        if ($res->address) {
            if (!($address = $contact->address)) {
                $address = new \Address();
                $address->contact_id = $contact->id;
            }

            $res->address->toModel($address);
            $this->saveModel($address);
        }
    }

    /**
     * Delete the specified GP record, first unassociating it from any patients.
     *
     * @param int $id
     */
    public function delete($id)
    {
        if (!($gp = $this->model->findByPk($id))) {
            throw new NotFound(\SettingMetadata::model()->getSetting('gp_label')." with ID '$id' not found");
        }

        $crit = new \CDbCriteria();
        $crit->compare('gp_id', $id);
        \Patient::model()->updateAll(array('gp_id' => null), $crit);

        $gp->delete();
    }
}
