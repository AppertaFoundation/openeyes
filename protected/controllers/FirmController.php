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
class FirmController extends BaseController
{
    public function accessRules()
    {
        return array(
            array('allow',
                'actions' => array('getFirmsBySubspecialty'),
                'users' => array('@'),
            ),
        );
    }

    /**
     * Returns the consultants by subspecialty
     * @param null $subspecialty_id
     */
    public function actionGetFirmsBySubspecialty($subspecialty_id = null, $runtime_selectable = null)
    {
        $institution_id = Yii::app()->session['selected_institution_id'];
        $firms = \Firm::model()->getList($institution_id, $subspecialty_id, null, $runtime_selectable);
        echo \CJSON::encode($firms);

        \Yii::app()->end();
    }
}
