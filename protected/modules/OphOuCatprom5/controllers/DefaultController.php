<?php
namespace OEModule\OphOuCatprom5\controllers;

use mysql_xdevapi\Exception;
use OEModule\OphOuCatprom5\components;
use OEModule\OphOuCatprom5\models;

class DefaultController extends \BaseEventTypeController
{

    public function actionCreate()
    {
        $errors = array();
//			parent::actionCreate();

        if (!empty($_POST)) {
          // form has been submitted
          if (isset($_POST['cancel'])) {
            $this->redirectToPatientLandingPage();
          }

          if (empty($errors)) {
            $transaction = Yii::app()->db->beginTransaction();

            try {
            	$success = $this->saveEvent($_POST);
            	if ($success) {

							}
            } catch (Exception $e) {
              $transaction->rollback();
              throw $e;
            }
          }


          \Yii::log(var_export($_POST, true));
        } else {
          parent::actionCreate();
        }
    }

    public function actionIndex()
    {
        $this->render('index');
    }
}