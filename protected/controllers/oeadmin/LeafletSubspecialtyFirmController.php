<?php

/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2012
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2012, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
class LeafletSubspecialtyFirmController extends BaseAdminController
{
    public $group = 'Consent';

    /**
     * Render the view for LeafletSubspecialtyFirm controller
     */
    public function actionList()
    {
        // set 'subspecialty-id' based on the default context provided
        @$_POST['subspecialty-id'] = $this->actionGetSubspecialtyByFirm(Yii::app()->session['selected_firm_id']);

        $this->render('/oeadmin/leaflet_subspecialty_firm/index', [
            'firm' => Firm::model()->findByPk(Yii::app()->session['selected_firm_id']),
        ]);
    }

    /**
     * Create table entries from leaflets, specific to a type retrieved from GET
     */
    public function actionGetLeaflets()
    {
        $id = @$_GET['id'];
        $type = @$_GET['type']; //firm
        $types = @$_GET['types']; //firm

        $criteria = new CDbCriteria();
        $criteria->with = array(
            $types => array(
                'select' => false,
                'joinType' => 'INNER JOIN',
                'on' => $types . '.leaflet_id=t.id'
            ),
        );
        $criteria->together = true;

        $criteria->addCondition($types . '.' . $type . '_id = :query');
        $criteria->params[':query'] = $id;

        $leaflets = OphTrConsent_Leaflet::model()->findAll($criteria);

        $html_tbody = '';
        foreach ($leaflets as $leaflet) {
            $delete_button = '<input id="' . $leaflet->id . '-' . $type . '-button" 
                        type="button" value="DELETE" onclick="deleteLeaflet(this);" />';
            if ($type === 'firm' ||
                ($type === 'subspecialty' && $this->checkAccess('admin'))
            ) {
                // Display the delete button if context has been selected or if context has not been selected and the user is an installation admin.
                // Otherwise, hide it so subspecialty-level mappings cannot be deleted by institution admins.
                $html_tbody .=
                "<tr>
                    <td>$leaflet->id</td>
                    <td>$leaflet->name</td>
                    <td>$delete_button</td>
                </tr>";
            } else {
                $html_tbody .=
                "<tr>
                    <td>$leaflet->id</td>
                    <td>$leaflet->name</td>
                    <td></td>
                </tr>";
            }
        }

        echo $html_tbody;
    }

    /**
     * Get the ID of a subspecialty, given the firm ID
     *
     * @param integer firm_id - id of the given firm
     * @return int Id of the corresponding subspecialty
     */
    public function actionGetSubspecialtyByFirm($firm_id)
    {
        $criteria = new CDbCriteria();

        $criteria->addCondition('t.id = :query');
        $criteria->params[':query'] = $firm_id;

        $criteria->with = array(
            'serviceSubspecialtyAssignment' => array(
                'joinType' => 'INNER JOIN',
                'on' => 'serviceSubspecialtyAssignment.id=t.service_subspecialty_assignment_id',
            )
        );
        $criteria->together = true;

        return Firm::model()->find($criteria)->serviceSubspecialtyAssignment->subspecialty_id;
    }

    /**
     * Delete the relation between a leaflet and a Firm or a Subspecialty,
     *  depending on the type provided in the GET variable
     */
    public function actionDelete()
    {
        $leaflet_id = @$_GET['leaflet_id'];
        $type = @$_GET['type'];
        $type_id = @$_GET['type_id'];

        if ($type === 'firm') {
            $model = OphTrConsent_Leaflet_Firm::model();
        } elseif ($type === 'subspecialty' && $this->checkAccess('admin')) {
            // Only installation admins can delete subspecialty-level leaflet mappings.
            $model = OphTrConsent_Leaflet_Subspecialty::model();
        } else {
            echo 'error';
            return;
        }

        $leaflet = $model->findByAttributes(array($type . '_id' => $type_id, 'leaflet_id' => $leaflet_id));

        if (!$leaflet->delete()) {
            echo 'error';
        }
    }

    /**
     * Add a relation between a leaflet and a Firm or a Subspecialty,
     *  depending on the type provided in the GET variable
     * @throws Exception
     */
    public function actionAdd()
    {
        $leaflet_id = @$_GET['leaflet_id'];
        $type = @$_GET['type'];
        $type_id = @$_GET['type_id'];
        $new_leaflet = null;

        if ($type === 'subspecialty') {
            $new_leaflet = new OphTrConsent_Leaflet_Subspecialty();
            $new_leaflet->subspecialty_id = $type_id;
        }
        if ($type === 'firm') {
            $new_leaflet = new OphTrConsent_Leaflet_Firm();
            $new_leaflet->firm_id = $type_id;
        }

        $new_leaflet->leaflet_id = $leaflet_id;

        if (!$new_leaflet->save()) {
            echo 'error';
        }
    }

    /**
     * Return a list with all leaflets.
     */
    public function actionSearch()
    {
        if (Yii::app()->request->isAjaxRequest) {
            $criteria = new CDbCriteria();
            if (isset($_GET['term'])) {
                $term = $_GET['term'];
                $criteria->addCondition(array('LOWER(name) LIKE :term'), 'OR');
                $params[':term'] = '%' . strtolower(strtr($term, array('%' => '\%'))) . '%';
            }
            $criteria->order = 'name';
            $criteria->select = 'id, name';
            $criteria->params = $params;
            $results = OphTrConsent_Leaflet::model()->active()->findAll($criteria);

            $return = array();
            foreach ($results as $resultRow) {
                $return[] = array(
                    'label' => $resultRow->name,
                    'value' => $resultRow->name,
                    'id' => $resultRow->id,
                );
            }
            $this->renderJSON($return);
        }
    }
}
