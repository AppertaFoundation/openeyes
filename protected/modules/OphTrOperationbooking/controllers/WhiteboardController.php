<?php

/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
class WhiteboardController extends BaseDashboardController
{
    protected $headerTemplate = 'header';

    protected $whiteboard;

    /**
     * @param OphTrOperationbooking_Whiteboard $whiteboard
     */
    public function setWhiteboard(OphTrOperationbooking_Whiteboard $whiteboard)
    {
        $this->whiteboard = $whiteboard;
    }

    /**
     * @return null|OphTrOperationbooking_Whiteboard
     */
    public function getWhiteboard()
    {
        return $this->whiteboard;
    }

    /**
     * Define access rules for the controller.
     *
     * @return array
     */
    public function accessRules()
    {
        return array(
            array('allow',
                'actions' => array('view', 'reload', 'confirm', 'saveComment'),
                'roles' => array('OprnViewClinical'),
            ),
        );
    }

    public function init()
    {
        $id = Yii::app()->request->getParam('id');
        $whiteboard = OphTrOperationbooking_Whiteboard::model()->with('booking')->findByAttributes(array('event_id' => $id));
        if($whiteboard){
            $this->setWhiteboard($whiteboard);
        }
    }

    /**
     * Set up the CSS.
     *
     * We need to set up the CSS here, after the parent is called, because the parent class removes the previously
     * registered scripts.
     *
     * @param CAction $action
     *
     * @return bool
     */
    public function beforeAction($action)
    {
        $before = parent::beforeAction($action);
        //core scripts
        $assetPath = Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('application.assets'), false, -1);
        Yii::app()->clientScript->registerScriptFile($assetPath.'/components/dialog-polyfill/dialog-polyfill.js');
        Yii::app()->clientScript->registerCssFile($assetPath.'/components/dialog-polyfill/dialog-polyfill.css');
        Yii::app()->clientScript->registerScriptFile($assetPath.'/components/mustache/mustache.js');
        Yii::app()->clientScript->registerScriptFile($assetPath.'/js/dashboard/OpenEyes.Dialog.js');
        Yii::app()->clientScript->registerScriptFile($assetPath.'/js/dashboard/whiteboard.js');

        $assetPath = Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('application.modules.'.$this->getModule()->name.'.assets'), false, -1);
        Yii::app()->clientScript->registerCssFile($assetPath.'/css/whiteboard.css');

        return $before;
    }

    /**
     * View the whiteboard.
     *
     * View the whiteboard data, if there is no data for this event we will collate and persist it in the model.
     *
     * @param $id
     *
     * @throws CException
     * @throws CHttpException
     */
    public function actionView($id)
    {
        $whiteboard = $this->getWhiteboard();
        if (!$whiteboard) {
            $whiteboard = new OphTrOperationbooking_Whiteboard();
            $whiteboard->loadData($id);
        }
        $this->setWhiteboard($whiteboard);

        if (is_object($whiteboard->booking) && $whiteboard->booking->isEditable() && !$whiteboard->is_confirmed)
        {
            $whiteboard->loadData($id);
        }

        $this->render('view', array('data' => $whiteboard), false, true);
    }

    /**
     * Reload the data for the whiteboard.
     *
     * If the data is wrong we can reload it and update the database.
     * 
     * @param $id
     *
     * @throws CException
     * @throws CHttpException
     */
    public function actionReload($id)
    {
        $whiteboard = $this->getWhiteboard();
        if (!$whiteboard) {
            throw new CHttpException(400, 'No whiteboard found for reload with id '.$id);
        }

        if (!$whiteboard->booking->isEditable()) {
            throw new CHttpException(400, 'Whiteboard is not editable '.$id);
        }

        $whiteboard->loadData($id);

        $this->redirect('/OphTrOperationbooking/whiteboard/view/'.$id);
    }

    /**
     * Confirms the checks.
     *
     * @param $id
     *
     * @throws CHttpException
     */
    public function actionConfirm($id)
    {
        $whiteboard = $this->getWhiteboard();
        if (!$whiteboard) {
            throw new CHttpException(400, 'No whiteboard found for save with id '.$id);
        }

        if (!$whiteboard->booking->isEditable()) {
            throw new CHttpException(400, 'Whiteboard is not editable '.$id);
        }

        $whiteboard->is_confirmed = 1;
        $whiteboard->save();

        $this->redirect('/OphTrOperationbooking/whiteboard/view/'.$id);
    }

    /**
     * @param $id
     *
     * @throws CHttpException
     * @throws Exception
     */
    public function actionSaveComment($id)
    {
        $whiteboard = $this->getWhiteboard();
        if (!$whiteboard) {
            throw new CHttpException(400, 'No whiteboard found for comment save with id '.$id);
        }

        if (!$whiteboard->booking->isEditable()) {
            throw new CHttpException(400, 'Whiteboard is not editable '.$id);
        }

        $savable = array('comments', 'predicted_additional_equipment');
        foreach($savable as $toSave){
            if(Yii::app()->request->getPost($toSave, '')){
                $whiteboard->$toSave = Yii::app()->request->getPost($toSave);
            }
        }

        $whiteboard->save();

        if(Yii::app()->request->isAjaxRequest){
            $this->renderJSON(array('success' => true));
        } else {
            $this->redirect('/OphTrOperationbooking/whiteboard/view/'.$id);
        }
    }
}
