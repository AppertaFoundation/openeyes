<?php
/**
 * OpenEyes
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

class DicomLogViewerController extends BaseController
{
    /**
     * @var string the default layout for the views
     */
    public $layout='//layouts/main';

    public $items_per_page = 100;

    public function accessRules()
    {
        return array(
            array('allow',
                'roles' => array('OprnViewClinical'),
            ),
        );
    }

    public function beforeAction($action)
    {
        $userid = Yii::app()->session['user']->id;
        //if (($userid != 2103)and($userid != 122)and($userid != 613)and($userid != 1330)and($userid != 1)) return false;
        return parent::beforeAction($action);
    }

    /**
     *
     */
    public function actionLog(){
        $data  = DicomFileLog::model()->findAll((array('order'=>'id desc')));
        $this->render('//dicomlogviewer/dicom_file_log_viewer', array( 'data' => $data));
    }

    /**
     *
     */
    public function actionIndex(){
        $data  = DicomFileLog::model()->findAll((array('order'=>'id desc')));
        $this->render('//dicomlogviewer/dicom_file_log_viewer', array( 'data' => $data));
    }
}