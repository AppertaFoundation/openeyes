<?php
/**
 * Created by PhpStorm.
 * User: veta
 * Date: 06/05/15
 * Time: 11:30
 */
namespace OEModule\OphCoCvi\controllers;


class LocalAuthoritiesAdminController extends \BaseAdminController
{
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

