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
class DicomLogViewerController extends BaseController
{
    /**
     * @var string the default layout for the views
     */
    public $layout = 'admin';

    public $items_per_page = 200;

    public $group = 'System';

    public function accessRules()
    {
        return array(
            array('allow',
                'roles' => array('admin'),
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
    public function actionLog()
    {
        $data = DicomFileLog::model()->with('dicom_file_id')->findAll();
        $this->render('//dicomlogviewer/index', array('data' => $data));
    }

    /**
     *
     */
    public function actionIndex()
    {
        $data = DicomFileLog::model()->findAll((array('order' => 'id desc')));
        $this->render('//dicomlogviewer/dicom_file_log_viewer', array('data' => $data));
    }

    public function actionList()
    {
        $this->render('//dicomlogviewer/index');
    }
    /////////////

    public function actionSearch()
    {
        $result = ['data' => null];

        if (isset($_POST['page'])) {
            $data = $this->getData($_POST['page']);
        } else {
            $data = $this->getData();
        }

        if (!empty($data['items'])) {
            $result['data'] = $data['items'];
        }

        $this->renderJSON($result);
        Yii::app()->end();

        Yii::app()->assetManager->registerScriptFile('js/audit.js');
        $this->renderPartial('//dicomlogviewer/_list', array('data' => $data), false, true);
        echo '<!-------------------------->';
        $this->renderPartial('//dicomlogviewer/_pagination', array('data' => $data), false, true);
    }

    public function criteria($count = false)
    {
        $criteria = new CDbCriteria();

        if (@$_REQUEST['hos_num']) {
            $criteria->addCondition('`dil`.`patient_number` = :hos_num');
            $criteria->params[':hos_num'] = $_REQUEST['hos_num'];
        }

        if (@$_REQUEST['file_name']) {
            $criteria->addCondition('file_name = :file_name');
            $criteria->params[':file_name'] = $_REQUEST['file_name'];
        }

        if (@$_REQUEST['firm_id']) {
            $firm = Firm::model()->findByPk($_REQUEST['firm_id']);
            $firm_ids = array();
            foreach (Firm::model()->findAll('name=?', array($firm->name)) as $firm) {
                $firm_ids[] = $firm->id;
            }
            if (!empty($firm_ids)) {
                $criteria->addInCondition('firm_id', $firm_ids);
            }
        }

        if (@$_REQUEST['action']) {
            $criteria->addCondition('action_id=:action_id');
            $criteria->params[':action_id'] = $_REQUEST['action'];
        }

        if (@$_REQUEST['target_type']) {
            $criteria->addCondition('type_id=:type_id');
            $criteria->params[':type_id'] = $_REQUEST['target_type'];
        }

        if (@$_REQUEST['event_type_id']) {
            $criteria->addCondition('event_type_id=:event_type_id');
            $criteria->params[':event_type_id'] = $_REQUEST['event_type_id'];
        }

        if (@$_REQUEST['date_from']) {
            $date_from = Helper::convertNHS2MySQL($_REQUEST['date_from']).' 00:00:00';
            $criteria->addCondition('`t`.created_date >= :date_from');
            $criteria->params[':date_from'] = $date_from;
        }

        if (@$_REQUEST['date_to']) {
            $date_to = Helper::convertNHS2MySQL($_REQUEST['date_to']).' 23:59:59';
            $criteria->addCondition('`t`.created_date <= :date_to');
            $criteria->params[':date_to'] = $date_to;
        }

     //  !($count) && $criteria->join = 'left join event on t.event_id = event.id left join event_type on event.event_type_id = event_type.id';

        return $criteria;
    }

    public function getData($page = 1, $id = false)
    {
        $data = array();

/*        if ($_data = Audit::model()->with('event')->find($this->criteria(true))) {
            $data['total_items'] = $_data->count;
        } else {
            $data['total_items'] = 0;
        }*/

        $data['total_items'] = count($this->getDicomFiles($page));
        $data['pages'] = 1;
        $data['page'] = 1;

        $criteria = $this->criteria();

        $criteria->order = 't.id desc';
        $criteria->limit = $this->items_per_page;
        if ($id) {
            $criteria->addCondition('t.id > '.(integer) $id);
        } else {
            $criteria->offset = (($page - 1) * $this->items_per_page);
        }
       // $data['items'] = Audit::model()->findAll($criteria);
        $data['pages'] = ceil($data['total_items'] / $this->items_per_page);
        if ($data['pages'] < 1) {
            $data['pages'] = 1;
        }
        if ($page > $data['pages']) {
            $page = $data['pages'];
        }
        if (!$id) {
            $data['page'] = $page;
        }

       // print_r($criteria);

        $data['items'] = $data['files_data'] = $this->getDicomFiles($page);

        return $data;
    }

    public function actionUpdateList()
    {
        if (!$audit = Audit::model()->findByPk(@$_GET['last_id'])) {
            throw new Exception('Log entry not found: '.@$_GET['last_id']);
        }

        $this->renderPartial('_list_update', array('data' => $this->getData(null, $audit->id)), false, true);
    }

    ///////////////

    protected function getDicomFiles($page, $sc = 'entry_date_time', $so = 'desc')
    {
        $command = Yii::app()->db->createCommand()
            ->select('df.id, df.filename, df.processor_id, dil.id as did, dil.import_datetime, dil.study_datetime, dil.study_instance_id, dil.station_id, dil.study_location, dil.report_type, dil.patient_number, dil.status, dil.comment,
            dil.raw_importer_output,dil.machine_manufacturer,dil.machine_model, dil.machine_software_version
            ')
            ->from('dicom_files as df')
            ->leftJoin('dicom_import_log as dil', 'df.id = dil.dicom_file_id')
            ->order($sc.' '.$so)
            ->limit($this->items_per_page)
            ->offset(($page - 1) * $this->items_per_page);

        if (isset($_REQUEST['hos_num']) && $_REQUEST['hos_num']) {
            $command->andWhere(['like', 'dil.patient_number', $_REQUEST['hos_num']]);
        }

        if (isset($_REQUEST['file_name']) && $_REQUEST['file_name']) {
            $command->andWhere(['like', 'df.filename', '%'.$_REQUEST['file_name'].'%']);
        }
        if (isset($_REQUEST['study_id']) && $_REQUEST['study_id']) {
            $command->andWhere(['like', 'dil.study_instance_id', '%'.$_REQUEST['study_id'].'%']);
        }
        if (isset($_REQUEST['location']) && $_REQUEST['location']) {
            $command->andWhere(['like', 'dil.study_location', '%'.$_REQUEST['location'].'%']);
        }
        if (isset($_REQUEST['station_id']) && $_REQUEST['station_id']) {
            $command->andWhere(['like', 'dil.station_id', $_REQUEST['station_id']]);
        }

        if (isset($_REQUEST['status']) && $_REQUEST['status']) {
            $command->andWhere(['like', 'dil.status', $_REQUEST['status']]);
        }

        if (isset($_REQUEST['type']) && $_REQUEST['type']) {
            $command->andWhere(['like', 'dil.report_type', $_REQUEST['type']]);
        }

        if (isset($_REQUEST['date_type']) && $_REQUEST['date_type'] == 1) {
            if (isset($_REQUEST['date_from']) && $_REQUEST['date_from'] && isset($_REQUEST['date_to']) && $_REQUEST['date_to']) {
                $command->andWhere('dil.import_datetime > \''.date('Y-m-d H:i:s', strtotime($_REQUEST['date_from'])).'\' AND dil.import_datetime < \''.date('Y-m-d H:i:s', strtotime($_REQUEST['date_to'].' + 24 Hours')).'\'');
            }
        } else {
            if (isset($_REQUEST['date_from']) && $_REQUEST['date_from'] && isset($_REQUEST['date_to']) && $_REQUEST['date_to']) {
                $command->andWhere('dil.study_datetime > \''.date('Y-m-d H:i:s', strtotime($_REQUEST['date_from'])).'\' AND dil.study_datetime < \''.date('Y-m-d H:i:s', strtotime($_REQUEST['date_to'].' + 24 Hours')).'\'');
            }
        }

        try {
            $data = $command->queryAll();
        } catch (CDbException $cdbe) {
            OELog::log($cdbe->getMessage());
            throw new Exception("An error has occurred.");
        }

        foreach ($data as $k => $y) {
            $data[$k] = $y;
            $data[$k]['watcher_log'] = $this->getFileWatcherLog($y['id']);
        }

        return $data;
    }
    protected function getFileWatcherLog($file_id)
    {
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

    public function actionReprocess()
    {
        // check if it's an ajax call

        if (Yii::app()->request->isAjaxRequest) {
            $request = Yii::app()->getRequest();
            $filename = $request->getQuery('filename');
            if ($filename != '') {
                Yii::app()->db->createCommand("update dicom_file_queue set status_id=1 where filename = '".$filename."'")->execute();
            }
        }
    }
};
