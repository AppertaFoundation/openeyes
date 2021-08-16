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

    public function actionSignatureList($type = 1, $sortby = 'DESC', $page = 1)
    {
        $this->layout = 'admin';
        Audit::add('admin-SignatureImportLog', 'list');

        $likewhere = '';
        if ($type == SignatureImportLog::TYPE_CVI) {
            $likewhere = 'cvi';
        } elseif ($type == SignatureImportLog::TYPE_CONSENT) {
            $likewhere = 'consent';
        }

        $search = new ModelSearch(SignatureImportLog::model());
        $criteria = new CDbCriteria();
        $criteria->order = "import_datetime ".$sortby;
        $search->setCriteria($criteria);

        if (!$page) {
            $page = $search->initPagination()->currentPage+1;
        }


        $this->render('/dicomlogviewer/signature_import_log', array(
            'pagination' => $search->initPagination(),
            'logs' => $search->retrieveResults(),
            'type' => $type,
            'sortby' => $sortby,
            'current_page' => $page,
        ));
    }

    public function actionSignatureCrop($id, $type = 1, $page = 1)
    {
        $this->layout = 'admin';
        $file = SignatureImportLog::model()->findByPk($id);
        $path = $file->filename;
        $filetype = pathinfo($path, PATHINFO_EXTENSION);
        $data = file_get_contents($path);
        $base64 = 'data:image/' . $filetype . ';base64,' . base64_encode($data);
        $elementy_type_id = ElementType::model()->findByAttributes(array('class_name'=> 'OEModule\OphCoCvi\models\Element_OphCoCvi_ConsentSignature'))->id;

        $this->render('/dicomlogviewer/signature_import_log_crop', array('img' => $base64, 'log_id' => $id, 'type' => $type, 'elementy_type_id' => $elementy_type_id, 'page' => $page));
    }

    /**
     * Lists all disorders for a given search term.
     */
    public function actionSignatureImportLogAutocomplete($term)
    {
        $search = "%{$term}%";
        $where = '(pi.value like :search)';
        //$where = '';
        $cvis = \Yii::app()->db->createCommand()
            ->select('uc.code AS unique_id, DATE_FORMAT(e.event_date, "%d %b %Y") as value, ps.id AS element_id, pi.value AS hos_num, CONCAT(first_name," ", last_name) AS patient_name, e.id AS event_id')
            ->from('event e')
            ->join('episode ep', 'e.episode_id = ep.id')
            ->join('patient p', 'ep.patient_id = p.id')
            ->join('patient_identifier pi', 'pi.patient_id = p.id')
            ->join('unique_codes_mapping ucm', 'e.id = ucm.event_id')
            ->join('unique_codes uc', 'ucm.unique_code_id = uc.id')
            ->join('et_ophcocvi_consentsig ps', 'e.id = ps.event_id')
            ->join('contact c', 'p.contact_id = c.id')
            //->join('et_ophcocvi_eventinfo eoe', 'e.id = eoe.event_id')
            ->where($where, array(
                ':search' => $search,
            ))
            ->order('e.event_date')
            ->queryAll();


        echo json_encode($cvis);
    }

    public function actionSignatureImageView($id)
    {
        $file = SignatureImportLog::model()->findByPk($id);

        $filepath = $file->filename;
        if (!file_exists($filepath)) {
            return false;
        }
        header('Content-Type: image');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        ob_clean();
        flush();
        readfile($filepath);
    }

    public function actionStatusChange()
    {
        if (Yii::app()->request->isPostRequest) {
            $id = Yii::app()->request->getPost("id");
            $status_id = Yii::app()->request->getPost("status_id");
            $event_id = Yii::app()->request->getPost("event_id");

            $log = SignatureImportLog::model()->findByPk($id);
            $log->status_id = $status_id;
            $log->event_id = $event_id;
            $log->save();

            return true;
        }
        return false;
    }

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
            foreach (Firm::model()->findAll('name=? AND institution_id = ?', array($firm->name, Yii::app()->session['selected_institution_id'])) as $firm) {
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

        return $criteria;
    }

    public function getData($page = 1, $id = false)
    {
        $data = array();

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
            ->where('dfl.dicom_file_id=:fid', array(':fid' => $file_id))
            ->order('dfl.event_date_time')
            ->queryAll();
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
