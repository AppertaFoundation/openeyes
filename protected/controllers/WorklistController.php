<?php

/**
 * OpenEyes.
 *
 * (C) OpenEyes Foundation, 2019
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
class WorklistController extends BaseController
{
    public $layout = 'worklist';
    /**
     * @var WorklistManager
     */
    protected $manager;

    public function accessRules()
    {
        return array(array('allow', 'roles' => array('OprnWorklist')));
    }

    protected function beforeAction($action)
    {
        Yii::app()->assetManager->registerCssFile('components/font-awesome/css/font-awesome.css', null, 10);
        if ($action->getId() === "print") {
            $newblue_path = 'application.assets.newblue';
            Yii::app()->assetManager->registerCssFile('css/style_oe3_print.min.css', $newblue_path, null);
        }

        $this->manager = new WorklistManager();

        return parent::beforeAction($action);
    }

    public function actionView()
    {
        $this->layout = 'main';
        $date_from = Yii::app()->request->getQuery('date_from');
        $date_to = Yii::app()->request->getQuery('date_to');
        $redirect = false;

        if (!isset(Yii::app()->session['worklist'])) {
            Yii::app()->session['worklist'] = [];
        }

        if ($date_from || $date_to) {
            foreach (['date_from', 'date_to'] as $date) {
                ${$date} = is_numeric(str_replace([" ", "/"], "", ${$date})) ? str_replace([" ", "/"], "-", ${$date}) : str_replace(['/'], " ", ${$date});
            }
            Yii::app()->session['worklist'] = ['date_from' => $date_from, 'date_to' => $date_to];
        }

        if (count(Yii::app()->session['worklist']) > 0) {
            foreach (['date_from', 'date_to'] as $date) {
                if (Yii::app()->session['worklist'][$date] && !${$date}) {
                    ${$date} = str_replace(" ", "+", Yii::app()->session['worklist'][$date]);
                    $redirect = true;
                }
            }
        }

        Yii::app()->clientScript->registerScriptFile(Yii::app()->assetManager->createUrl('js/OpenEyes.UI.InputFieldValidation.js'), ClientScript::POS_END);

        if ($redirect) {
            return $this->redirect(array('/worklist/view?date_from='.$date_from.'&date_to='.$date_to));
        }

        $worklists = $this->manager->getCurrentAutomaticWorklistsForUser(null, $date_from ? new DateTime($date_from) : null, $date_to ? new DateTime($date_to) : null);
        $this->render('index', array('worklists' => $worklists));
    }

    /**
     * Redirect to a suitable worklist default action.
     */
    public function actionIndex()
    {
        return $this->redirect(array('/worklist/manual'));
    }

    /**
     * Manage User's manual worklists.
     */
    public function actionManual()
    {
        $current_worklists = $this->manager->getCurrentManualWorklistsForUser(Yii::app()->user);
        $available_worklists = $this->manager->getAvailableManualWorklistsForUser(Yii::app()->user);

        $this->render('//worklist/manual/index', array(
            'current_worklists' => $current_worklists,
            'available_worklists' => $available_worklists,
        ));
    }

    public function actionManualAdd()
    {
        $worklist = new Worklist();

        if (!empty($_POST)) {
            $worklist->attributes = $_POST['Worklist'];
            if ($this->manager->createWorklistForUser($worklist)) {
                Audit::add('Manual-Worklist', 'add', $worklist->id);
                $this->redirect('/worklist/manual');
            } else {
                $errors = $worklist->getErrors();
            }
        }

        $this->render('//worklist/manual/add', array(
            'worklist' => $worklist,
            'errors' => @$errors,
        ));
    }

    /**
     * Update the worklist display order for the current user based on the submitted ids.
     */
    public function actionManualUpdateDisplayOrder()
    {
        $worklist_ids = @$_POST['item_ids'] ? explode(',', $_POST['item_ids']) : array();

        if (!$this->manager->setWorklistDisplayOrderForUser(Yii::app()->user, $worklist_ids)) {
            OELog::log(print_r($this->manager->getErrors(), true));
            throw new Exception('Unable to save new display order for worklists');
        }

        $this->redirect('/worklist/manual');
    }

    public function actionPrint($date_from = null, $date_to = null, $list_id = null)
    {
        $this->layout = '//layouts/print';
        $worklists = $this->manager->getCurrentAutomaticWorklistsForUser(null, $date_from ? new DateTime($date_from) : null, $date_to ? new DateTime($date_to) : null);
        if ($list_id) {
            $worklists = array_filter($worklists, function ($e) use ($list_id) {
                return (int)$e->id === (int)$list_id;
            });
        }


        $this->render('//worklist/print', array('worklists' => $worklists));
    }

    public function actionClearDates()
    {
        Yii::app()->session->remove('worklist');
        return $this->redirect(array('/worklist/view'));
    }
    public function actionRenderPopups()
    {
        if (isset($_POST['worklistId'])) {
            $worklist = $this->manager->getWorklist($_POST["worklistId"]);
            $dataProvider = $this->manager->getPatientsForWorklist($worklist);
            foreach ($dataProvider->getData() as $dataProvider) {
                $this->renderPartial('application.widgets.views.PatientIcons', array('data' => ($dataProvider->patient), 'page' => 'worklist'));
            }
        }
    }
}
