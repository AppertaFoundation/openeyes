<?php

namespace OEModule\OphCiExamination\controllers;

use Yii;
use OEModule\OphCiExamination\modules;
use OEModule\OphCiExamination\components;

//class OptomFeedbackController extends \BaseController
class OptomFeedbackController extends \BaseEventTypeController
{
    const ACTION_TYPE_LIST = 'List';
    protected static $FILTER_LIST_KEY = 'OptomFeedbackManager_list_filter';
    private $is_list_filtered = false;
    protected static $action_types = array(
        'list' => self::ACTION_TYPE_FORM,
    );

    public function accessRules()
    {
        return array(
            array('allow',
                'actions' => array('list'),
                'roles' => array('Optom co-ordinator')
            ),
        );
    }

    protected function beforeAction($action)
    {
        $this->jsVars['OE_MODEL_PREFIX'] = 'OEModule_OphCiExamination_models_';
        return parent::beforeAction($action);
    }

    /**
     *  Optom Feedback manager list screen
     */
    public function actionList()
    {
        $this->layout = '//layouts/main';
        $this->renderPatientPanel = false;

        $filter = $this->getListFilter();
        $examinationLog = new \AutomaticExaminationEventLog();
        $dp = $examinationLog->optomSearch( $filter );

        $this->render(
            '/optom/list',
            array(
                'list_filter'   => $filter,
                'model'         => $examinationLog,
                'dp'            => $dp
            )
        );
    }

    public function actionEdit( $id )
    {
        var_dump($id);
    }

    /**
     *  Get and set post data value to search fields
     */
    protected function getListFilter()
    {
        $filter = array();

        // if POST, then a new filter is to be applied, otherwise retrieve from the session
        if ($this->request->isPostRequest) {
            foreach (array('date_from', 'date_to', 'status_id', 'patient_number','optometrist_name', 'optometrist_goc_code' ) as $key) {
                $val = $this->request->getPost($key, null);
                $filter[$key] = $val;
            }
        } else {
            if ($session_filter = $this->getApp()->session[static::$FILTER_LIST_KEY]) {
                $filter = $session_filter;
            }
        }

        // set the is filtered flag for the controller
        foreach ($filter as $val) {
            if ($val) {
                $this->is_list_filtered = true;
                break;
            }
        }

        // store filter for later use
        $this->getApp()->session[static::$FILTER_LIST_KEY] = $filter;

        return $filter;
    }

    public function isListFiltered()
    {
        return $this->is_list_filtered;
    }
}