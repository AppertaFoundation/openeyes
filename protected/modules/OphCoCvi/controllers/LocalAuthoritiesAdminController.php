<?php
/**
 * Created by PhpStorm.
 * User: veta
 * Date: 09/08/2016
 * Time: 14:38
 */

namespace OEModule\OphCoCvi\controllers;

/**
 * Class LocalAuthoritiesAdminController
 *
 * @package OEModule\OphCoCvi\controllers
 */
class LocalAuthoritiesAdminController extends \AdminController
{
    public $layout = '//layouts/admin';

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

        if (!$commissioning_bt = \CommissioningBodyType::model()->findByAttributes(array('shortname' => 'LA'))) {
            throw new \CHttpException(500, 'Local Authority Commissioning Body Type is not configured.');
        }

        $service_type = \CommissioningBodyServiceType::model()->findByAttributes(array('shortname' => 'SSD'));

        $data['title'] = 'CVI Social Services Depts.';
        $data['commissioning_bt'] = $commissioning_bt;
        $data['service_type'] = $service_type;
        $data['return_url'] = '/OphCoCvi/localAuthoritiesAdmin/list';
        $data['base_data_url'] = 'OphCoCvi/localAuthoritiesAdmin/';

        $this->render('//admin/commissioning_body_services/index', $data);
    }
}