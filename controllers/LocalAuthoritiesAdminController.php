<?php
/**
 * Created by PhpStorm.
 * User: veta
 * Date: 09/08/2016
 * Time: 14:38
 */

namespace OEModule\OphCoCvi\controllers;


class LocalAuthoritiesAdminController extends \AdminController
{
    public $layout = 'clerical_admin';

    public function accessRules()
    {
        return array_merge(
            array(
                array('allow',
                    'actions'=>array('list', 'editCommissioningBodyService', 'addCommissioningBodyService', 'verifyDeleteCommissioningBodyServices', 'deleteCommissioningBodyServices'),
                    'roles'=>array('OprnEditClericalCvi'),
                ),
            ),
            parent::accessRules()
        );
    }

    /**
     * Lists local authorities from Commissioning Body Service
     *
     * @throws CHttpException
     */
    public function actionList()
    {
        \Audit::add('admin-CommissioningBodyService', 'list');

        $commissioningBody = \CommissioningBody::model()->findByAttributes(array('code' => 'eCVILA'));
        $serviceType = \CommissioningBodyServiceType::model()->findByAttributes(array('shortname' => 'SSD'));

        $data["commissioningBodyId"] = $commissioningBody->id;
        $data["serviceTypeId"] = $serviceType->id;
        $data["returnUrl"] = '/OphCoCvi/localAuthoritiesAdmin/list';

        $this->render('//admin/commissioning_body_services', array("data" => $data));
    }
}