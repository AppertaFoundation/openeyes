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

namespace OEModule\OphCiExamination\controllers;

use Yii;

class OEScapeDataController extends \BaseController
{
    public function accessRules()
    {
        return array(
            array('allow',
                'actions' => array('SetPreferredChartSize'),
                'users' => array('@'),
            ),
            array('allow',
                'actions' => array('dataset', 'datasetva', 'datasetmd', 'loadimage', 'loadallimages', 'getoperations', 'getimage', 'getmedications'),
                'expression' => 'Yii::app()->user->isSurgeon()',
            ),
            array('allow',
                'actions' => array('dataset', 'datasetva', 'datasetmd', 'loadimage', 'loadallimages', 'getoperations', 'getimage', 'getmedications'),
                'roles' => array('admin'),
            ),
        );
    }

    protected function queryData($patient, $side)
    {
        $command = Yii::app()->db->createCommand()->select('event_date, oiv.eye_id, reading_time, value')
            ->from('episode ep')
            ->join('event ev', 'ev.episode_id = ep.id')
            ->join('et_ophciexamination_intraocularpressure eoi', 'eoi.event_id=ev.id')
            ->join('ophciexamination_intraocularpressure_value oiv', 'oiv.element_id = eoi.id')
            ->join('ophciexamination_intraocularpressure_reading oir', ' oiv.reading_id = oir.id')
            ->where('patient_id = :patient', array('patient' => $patient))
            ->andWhere('oiv.eye_id = :side', array('side' => $side))
            ->andWhere('ev.deleted <> 1')
            ->order('event_date');

        return $command->queryAll();
    }

    protected function queryDataVA($patient, $side)
    {
        $command = Yii::app()->db->createCommand()->select('event_date, ovauv.value as value')
            ->from('ophciexamination_visualacuity_reading ovr')
            ->join('et_ophciexamination_visualacuity eov', 'eov.id=ovr.element_id')
            ->join('event ev', 'ev.id=eov.event_id')
            ->join('episode ep', 'ev.episode_id=ep.id')
            ->join('ophciexamination_visual_acuity_unit_value ovauv', 'ovauv.unit_id=10 and ovr.value=ovauv.base_value')
            ->where('patient_id = :patient', array('patient' => $patient))
            ->andWhere('ovr.side = :side', array('side' => $side - 1))
            ->andWhere('ev.deleted <> 1')
            ->order('event_date');

        return $command->queryAll();
    }

    protected function queryOperationData($patient)
    {
        $command = Yii::app()->db->createCommand()->select('event_date, eye.name as eye, term, eopp.eye_id as eye_id')
            ->from('ophtroperationnote_procedurelist_procedure_assignment oppa')
            ->join('et_ophtroperationnote_procedurelist eopp', 'oppa.procedurelist_id = eopp.id')
            ->join('event e', 'eopp.event_id = e.id')
            ->join('episode ep', 'e.episode_id = ep.id')
            ->join('proc p', 'oppa.proc_id=p.id')
            ->join('eye', 'eopp.eye_id=eye.id')
            ->where('patient_id = :patient', array('patient' => $patient))
            ->andWhere('e.deleted <> 1')
            ->order('event_date');

        return $command->queryAll();
    }

    protected function queryDataMD($patient, $side)
    {
        $command = Yii::app()->db->createCommand()->select('event_date, mean_deviation')
            ->from('media_data md')
            ->where('patient_id = :patient', array('patient' => $patient))
            ->andWhere('mean_deviation is not null')
            ->andWhere('eye_id= :side', array('side' => $side))
            ->order('event_date');

        return $command->queryAll();
    }

    /**
     * VA is better if the value is closer to 0.
     *
     * @return bool
     */
    protected function isVAbetter($current, $new)
    {
        if ($current < 0) {
            if ($new > $current) {
                return true;
            }
        } elseif ($current > 0) {
            if ($new < $current) {
                return true;
            }
        }

        return false;
    }

    protected function isVAinArray($key, $vaArray)
    {
        foreach ($vaArray as $index => $row) {
            if ($row[0] == $key) {
                return $index;
            }
        }

        return false;
    }

    protected function sortMedications($medArray)
    {
        $outArray = array();
        foreach ($medArray as $med) {
            $outArray[(int) strtotime($med->start_date)] = $med;
        }
        ksort($outArray);

        return $outArray;
    }

    /**
     * @return array
     */
    public function actionDataSet($id, $side)
    {
        $data = $this->queryData($id, $side);

        $output = array();
        foreach ($data as $row) {
            $output[] = array(strtotime($row['event_date']) * 1000, (int) $row['value']);
        }

        $this->renderJSON($output);
    }

    public function actionDataSetVA($id, $side)
    {
        $data = $this->queryDataVA($id, $side);

        $output = array();
        foreach ($data as $row) {
            $key = strtotime($row['event_date']) * 1000;
            if ($currentBest = $this->isVAinArray($key, $output)) {
                if ($row['value'] < $currentBest) {
                    $output[$currentBest] = array($key, (float) $row['value']);
                }
            } else {
                $output[] = array($key, (float) $row['value']);
            }
        }

        $this->renderJSON($output);
    }

    public function actionDataSetMD($id, $side)
    {
        $data = $this->queryDataMD($id, $side);

        $output = array();
        foreach ($data as $row) {
            $output[] = array(strtotime($row['event_date']) * 1000, (float) $row['mean_deviation']);
        }

        $this->renderJSON($output);
    }

    public function actionGetOperations($id)
    {
        $data = $this->queryOperationData($id);

        $output = array();
        foreach ($data as $row) {
            $output[] = array(strtotime($row['event_date']) * 1000, $row['term'], (int) $row['eye_id']);
        }

        $this->renderJSON($output);
    }

    public function actionGetMedications($id)
    {
        $patient = \Patient::model()->findByPk($id);

        $medications = array_merge($patient->get_previous_medications(), $patient->get_medications());
        //$medications = $this->sortMedications($medications);
        $output = array();

        foreach ($medications as $medication) {
            $output[] = array((int) strtotime($medication->start_date) * 1000, (int) strtotime($medication->end_date) * 1000, (int) $medication->option_id, explode(' ', $medication->getDrugLabel())[0]);
        }

        $this->renderJSON($output);
    }

    public function actionLoadImage($id, $eventDate, $side, $eventType, $mediaType)
    {
        // get the closest VF event and image based on the eventDate
        $command = Yii::app()->db->createCommand()->select('max(id) as fileid')
            ->from('media_data')
            ->where('patient_id = :patient', array('patient' => $id))
            ->andWhere('event_date <= :eventDate', array('eventDate' => $eventDate))
            ->andWhere('eye_id = :side', array('side' => $side))
            ->andWhere('event_type_id = (SELECT id FROM event_type WHERE class_name= :eventType)', array('eventType' => $eventType))
            ->andWhere('media_type_id = (SELECT id FROM media_type WHERE type_name =:mediaType)', array('mediaType' => $mediaType));

        if ($row = $command->queryRow()) {
            echo $this->renderPartial('//oescape/vfgreyscale_side', array('fileid' => $row['fileid']));
        }
        //echo $fileId;
    }

    public function actionLoadAllImages($id, $eventType, $mediaType)
    {
        $command = Yii::app()->db->createCommand()->select('md.id as fileid, eye_id, event_date, plot_values')
            ->from('media_data md')
            ->where('patient_id = :patient', array('patient' => $id))
            ->andWhere('event_type_id = (SELECT id FROM event_type WHERE class_name= :eventType)', array('eventType' => $eventType))
            ->andWhere('media_type_id = (SELECT id FROM media_type WHERE type_name =:mediaType)', array('mediaType' => $mediaType))
            ->order('event_date');

        $allData = $command->queryAll();

        foreach ($allData as $row) {
            $output[strtotime($row['event_date'])][$row['eye_id']] = array($row['fileid'], $row['plot_values']);
        }
        $this->renderJSON($output);
    }

    public function actionGetImage($id)
    {
        if (!$file = \MediaData::model()->findByPk($id)) {
            throw new CHttpException(404, 'File not found');
        }
        $filepath = $file->getPath();
        if (!file_exists($file->getPath())) {
            throw new CException('File not found on filesystem: '.$file->getPath());
        }
        //var_dump($file);
        //die;
        header('Content-Type: '.\MediaType::model()->findByPk($file->media_type_id)->type_mime);
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Content-Length: '.$file->original_file_size);
        ob_clean();
        flush();
        readfile($filepath);
    }

    /**
     * @param $chart_size string (small|medium|large\full)
     */
    public function actionSetPreferredChartSize(){
        $_SESSION['oescape_chart_size'] = $_POST['chart_size'];
    }
}
