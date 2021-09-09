<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2019
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */


namespace OEModule\OphCoCvi\controllers;

/**
 * Class LocalAuthorityController
 *
 * @package OEModule\OphCoCvi\controllers
 */
class LocalAuthorityController extends \BaseModuleController
{
    /**
     * @return array
     */
    public function accessRules()
    {
        return array(
            array(
                'allow',
                'actions' => array('autoComplete'),
                'roles' => array('OprnViewClinical'),
            ),
        );
    }

    /**
     * Search the local authorities and return both their address and the address of their social security department
     *
     * @param $term
     */
    public function actionAutoComplete($term)
    {
        $crit = new \CDbCriteria();

        // NOTE: have commented out the address eager loading here due to column ambiguity issues with the relation definitions.
        // need to investigate if this can be solved with the cunning use of scopes on the Contact model or not.
        $crit->with = array(
            'contact' => array('alias' => 'service_contact'),
//            'contact.correspondAddress' => array('alias' => 'service_correspondaddress'),
//            'contact.address' => array('alias' => 'service_address'),
            'commissioning_body',
            'commissioning_body.contact',
//            'commissioning_body.contact.address',
//            'commissioning_body.contact.correspondAddress',
            'type' => array('alias' => 'service_type'),
            'commissioning_body.type' => array('alias' => 'body_type'),
        );
        $crit->compare('LOWER(t.name)', strtolower($term), true);
        $crit->compare('LOWER(commissioning_body.name)', strtolower($term), true, 'OR');
        $crit->addColumnCondition(array('service_type.shortname' => 'SSD'));
        $crit->addColumnCondition(array('body_type.shortname' => 'LA'));
        $crit->order = 'commissioning_body.name, t.name';

        $results = array();

        $found_bodies = array();

        foreach (\CommissioningBodyService::model()->findAll($crit) as $cbs) {
            $body = $cbs->commissioning_body;
            $found_bodies[] = $body->id;
            $postcode = explode(" ", \Helper::setPostCodeFormat($cbs->contact->address->postcode));

            $results[] = array(
                'id' => 'service' . $cbs->id,
                'label' => $cbs->name . " ({$body->name})",
                'service' => array(
                    'id' => $cbs->id,
                    'name' => $cbs->name,
                    'address' => $cbs->getLetterAddress(array('delimiter' => ",\n")),
                    'telephone' => $cbs->contact->primary_phone,
                    'postcode_1' => array_key_exists(0, $postcode) ? $postcode[0] : null,
                    'postcode_2' => array_key_exists(1, $postcode) ? $postcode[1] : null
                ),
                'body' => array(
                    'id' => $body->id,
                    'name' => $body->name,
                    'address' => $body->getLetterAddress(array('delimiter' => ",\n")),
                    'telephone' => $body->contact->primary_phone,
                    'postcode_1' => array_key_exists(0, $postcode) ? $postcode[0] : null,
                    'postcode_2' => array_key_exists(1, $postcode) ? $postcode[1] : null,
                    'email' => $body->contact->email,
                ),
            );
        }

        $body_crit = new \CDbCriteria();
        $body_crit->with = array(
            'type',
            'contact',
            'contact.correspondAddress',
        );
        $body_crit->compare('LOWER(t.name)', strtolower($term), true);
        $body_crit->addNotInCondition('t.id', $found_bodies);
        $body_crit->addColumnCondition(array('type.shortname' => 'LA'));

        foreach (\CommissioningBody::model()->findAll($body_crit) as $body) {
            $postcode = explode(" ", \Helper::setPostCodeFormat($cbs->contact->address->postcode));

            $results[] = array(
                'id' => 'body' . $body->id,
                'label' => $body->name,
                'body' => array(
                    'id' => $body->id,
                    'name' => $body->name,
                    'address' => $body->getLetterAddress(array('delimiter' => ",\n")),
                    'telephone' => $body->contact->primary_phone,
                    'postcode_1' => $postcode[0],
                    'postcode_2' => $postcode[1],
                    'email' => isset($body->contact->address) ? $body->contact->email : "",
                ),
            );
        }

        $this->renderJSON($results);
    }
}
