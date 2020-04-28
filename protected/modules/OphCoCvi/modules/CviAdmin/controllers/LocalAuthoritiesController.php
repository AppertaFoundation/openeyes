<?php
/**
 * (C) OpenEyes Foundation, 2018
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

/**
 * Class LocalAuthoritiesController
 *
 * @package OEModule\OphCoCvi\CviAdmin\controllers
 */
class LocalAuthoritiesController extends \AdminController
{
    public $layout = '//layouts/admin';
    public $items_per_page = 30;
    public $group = 'CVI';

    /**
     * @return array
     */
    public function accessRules()
    {
        return array_merge(
            array(
                array(
                    'allow',
                    'actions' => array(
                        'list',
                        'editCommissioningBodyService',
                        'addCommissioningBodyService',
                        'verifyDeleteCommissioningBodyServices',
                        'deleteCommissioningBodyServices',
                    ),
                    'roles' => array('OprnEditClericalCvi'),
                ),
            ),
            parent::accessRules()
        );
    }

    /**
     * Lists local authorities from Commissioning Body Service
     *
     * @throws \CHttpException
     */
    public function actionList()
    {

        \Audit::add('admin-CommissioningBodyService', 'list');

        $commissioning_bt = \CommissioningBodyType::model()->findByAttributes(array('shortname' => 'LA'));
        if (!$commissioning_bt) {
            throw new \CHttpException(500, 'Local Authority Commissioning Body Type is not configured.');
        }

        $service_type = \CommissioningBodyServiceType::model()->findByAttributes(array('shortname' => 'SSD'));

        $data['title'] = 'CVI Social Services Depts.';
        $data['commissioning_bt'] = $commissioning_bt;
        $data['service_type'] = $service_type;
        $data['return_url'] = '/OphCoCvi/admin/localAuthorities/list';
        $data['base_data_url'] = 'OphCoCvi/admin/localAuthorities/';

        $this->render('//admin/commissioning_body_services/index', $data);
    }
}
