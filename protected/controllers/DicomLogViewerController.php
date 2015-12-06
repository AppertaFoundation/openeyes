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

    public $items_per_page = 200;

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
        $data  = DicomFileLog::model()->with('dicom_file_id')->findAll();
        $this->render('//dicomlogviewer/dicom_file_log_viewer', array( 'data' => $data));
    }

    /**
     *
     */
    public function actionIndex(){
        $data  = DicomFileLog::model()->findAll((array('order'=>'id desc')));
        $this->render('//dicomlogviewer/dicom_file_log_viewer', array( 'data' => $data));
    }

    public  function actionList()
    {
        $this->render('index');
    }
    /////////////

    public function actionSearch()
    {
        if (isset($_POST['page'])) {
            $data = $this->getData($_POST['page']);
        } else {
            $data = $this->getData();
        }

        Yii::app()->assetManager->registerScriptFile('js/audit.js');
        $this->renderPartial('_list', array('data' => $data), false, true);
        echo "<!-------------------------->";
        $this->renderPartial('_pagination', array('data' => $data), false, true);
    }

    public function criteria($count=false)
    {
        $criteria = new CDbCriteria;

        if ($count) {
            $criteria->select = 'count(*) as count';
        }

        if (@$_REQUEST['site_id']) {
            $criteria->addCondition('site_id = :site_id');
            $criteria->params[':site_id'] = $_REQUEST['site_id'];
        }

        if (@$_REQUEST['firm_id']) {
            $firm = Firm::model()->findByPk($_REQUEST['firm_id']);
            $firm_ids = array();
            foreach (Firm::model()->findAll('name=?',array($firm->name)) as $firm) {
                $firm_ids[] = $firm->id;
            }
            if (!empty($firm_ids)) {
                $criteria->addInCondition('firm_id',$firm_ids);
            }
        }

        if (@$_REQUEST['user']) {
            $user_ids = array();

            $criteria2 = new CDbCriteria;
            $criteria2->addCondition(array("active = :active"));
            $criteria2->addCondition(array("LOWER(concat_ws(' ',first_name,last_name)) = :term"));

            $params[':active'] = 1;
            $params[':term'] = strtolower($_REQUEST['user']);

            $criteria2->params = $params;

            foreach (User::model()->findAll($criteria2) as $user) {
                $user_ids[] = $user->id;
            }

            $criteria->addInCondition('user_id',$user_ids);
        }

        if (@$_REQUEST['action']) {
            $criteria->addCondition("action_id=:action_id");
            $criteria->params[':action_id'] = $_REQUEST['action'];
        }

        if (@$_REQUEST['target_type']) {
            $criteria->addCondition("type_id=:type_id");
            $criteria->params[':type_id'] = $_REQUEST['target_type'];
        }

        if (@$_REQUEST['event_type_id']) {
            $criteria->addCondition('event_type_id=:event_type_id');
            $criteria->params[':event_type_id'] = $_REQUEST['event_type_id'];
        }

        if (@$_REQUEST['date_from']) {
            $date_from = Helper::convertNHS2MySQL($_REQUEST['date_from']).' 00:00:00';
            $criteria->addCondition("`t`.created_date >= :date_from");
            $criteria->params[':date_from'] = $date_from;
        }

        if (@$_REQUEST['date_to']) {
            $date_to = Helper::convertNHS2MySQL($_REQUEST['date_to']).' 23:59:59';
            $criteria->addCondition("`t`.created_date <= :date_to");
            $criteria->params[':date_to'] = $date_to;
        }

        if (@$_REQUEST['hos_num']) {
            if ($patient = Patient::model()->find('hos_num=?',array($_REQUEST['hos_num']))) {
                $criteria->addCondition('patient_id='.$patient->id);
            } else {
                if ($patient = Patient::model()->find('hos_num=?',array(str_pad($_REQUEST['hos_num'],7,'0',STR_PAD_LEFT)))) {
                    $criteria->addCondition('patient_id='.$patient->id);
                } else {
                    $criteria->addCondition('patient_id=0');
                }
            }
        }

        !($count) && $criteria->join = 'left join event on t.event_id = event.id left join event_type on event.event_type_id = event_type.id';

        return $criteria;
    }

    public function getData($page=1, $id=false)
    {
        $data = array();

        if ($_data = Audit::model()->with('event')->find($this->criteria(true))) {
            $data['total_items'] = $_data->count;
        } else {
            $data['total_items'] = 0;
        }

        $criteria = $this->criteria();

        $criteria->order = 't.id desc';
        $criteria->limit = $this->items_per_page;
        if ($id) {
            $criteria->addCondition('t.id > '.(integer) $id);
        } else {
            $criteria->offset = (($page-1) * $this->items_per_page);
        }

        $data['items'] = Audit::model()->findAll($criteria);
        $data['pages'] = ceil($data['total_items'] / $this->items_per_page);
        if ($data['pages'] <1) {
            $data['pages'] = 1;
        }
        if ($page > $data['pages']) {
            $page = $data['pages'];
        }
        if (!$id) {
            $data['page'] = $page;
        }
        $data['files_data'] = $this->getDicomFiles($page);
        //$data['files_data1'] = $this->getDicomFiles1($page);
        return $data;
    }

    public function actionUpdateList()
    {
        if (!$audit = Audit::model()->findByPk(@$_GET['last_id'])) {
            throw new Exception('Log entry not found: '.@$_GET['last_id']);
        }

        $this->renderPartial('_list_update', array('data' => $this->getData(null,$audit->id)), false, true);
    }

    ///////////////

    protected function getDicomFiles1($page, $sc='import_datetime', $so='asc')
    {
        return Yii::app()->db->createCommand()
            ->select('df.id, df.filename, dil.id as did, dil.import_datetime, dil.study_datetime, dil.study_instance_id, dil.station_id, dil.study_location, dil.report_type, dil.patient_number, dil.status,dil.comment')
            ->from('dicom_files as df')
            ->join('dicom_import_log as dil', 'df.id = dil.dicom_file_id')
          //  ->where('ep.patient_id=:pid and e.deleted=:del', array(':pid' => $patientId, ':del' => 0))
            ->order($sc.' '.$so)
            ->limit( $this->items_per_page)
            ->offset(($page-1)*$this->items_per_page)
            ->queryAll();
            //->getText();
    }

    protected function getDicomFiles($page, $sc='import_datetime', $so='asc')
    {
        $data =  Yii::app()->db->createCommand()
            ->select('df.id, df.filename,df.processor_id, dil.id as did, dil.import_datetime, dil.study_datetime, dil.study_instance_id, dil.station_id, dil.study_location, dil.report_type, dil.patient_number, dil.status,dil.comment,
            dil.raw_importer_output,dil.machine_manufacturer,dil.machine_model, dil.machine_software_version
            ')
            ->from('dicom_files as df')
            ->leftJoin('dicom_import_log as dil', 'df.id = dil.dicom_file_id')
            //  ->where('ep.patient_id=:pid and e.deleted=:del', array(':pid' => $patientId, ':del' => 0))
            ->order($sc.' '.$so)
            ->limit( $this->items_per_page)
            ->offset(($page-1)*$this->items_per_page)
            ->queryAll();
     //   ->getText(); echo $data; die;
        foreach ($data as $k =>$y ){
            $data[$k] = $y;
            $data[$k]['watcher_log'] = $this->getFileWatcherLog($y['id']);
        }
        return($data);


    }
    protected function getFileWatcherLog($file_id){
        //select * from dicom_file_log where dicom_file_id=1;

        return Yii::app()->db->createCommand()
            ->select('dfl.*')
            ->from('dicom_file_log as dfl')
            //->join('dicom_import_log as dil', 'df.id = dil.dicom_file_id')
            ->where('dfl.dicom_file_id=:fid', array(':fid' => $file_id))
            ->order('dfl.event_date_time')
            //->limit( $this->items_per_page)
            //->offset(($page-1)*$this->items_per_page)
            ->queryAll();
        //->getText();

    }
};