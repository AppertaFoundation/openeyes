<?php

/**
 * Class HealthCheckController
 *
 * Performs simple health check functions to be used by upstream systems (e.g, docker) to determine if the node is healthy
 * and functioning
 */
class HealthCheckController extends BaseController
{

    /**
     * @return array action filters
     */
    public function filters()
    {
        return array(
            'accessControl',
        );
    }

    /**
     * Specifies the access control rules.
     * This method is used by the 'accessControl' filter.
     * @return array access control rules
     */
    public function accessRules()
    {
        return array(
            array(
                'allow',
                'actions' => array(
                    'index',
                ),
                'users' => array('*'),
            ),
        );
    }

    /**
     * Performs some simple checks to make sure that the web service is running and is able to contact the database
     */
    public function actionIndex()
    {
        $dbconn = Yii::app()->db;

        $result = $dbconn->createCommand('select `key` from setting_metadata limit 1')->queryAll();

        if (!empty($result)) {
            echo "OK";
        } else {
            echo "FAILED";
        }
    }
}
