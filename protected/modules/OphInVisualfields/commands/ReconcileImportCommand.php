<?php
/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2014
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2014, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
class ReconcileImportCommand  extends CConsoleCommand
{
    public function getName()
    {
        return 'Reconcile Visual Field Measurements against External Processing DB';
    }

    public function getHelp()
    {
        return <<<EOH
Will compare imports in the openeyes DB with the records in Vern's DB that has pre-processed the Visual Fields data.
EOH;
    }

    public $interval = '1d';
    public $defaultAction = 'reconcile';

    public function actionReconcile($args)
    {
        $hfa_cmd = Yii::app()->db_visualfields->createCommand();
        $hfa_cmd->select('imagelocation, Test_Date, TestTime, patkey');
        $hfa_cmd->from('hfa_data');
        $hfa_cmd->where('test_date >= :test_date', array(':test_date' => '2014-07-15'));

        $hfa_records = $hfa_cmd->queryAll();
        $matched = 0;
        foreach ($hfa_records as $hfa) {
            $patient = Patient::model()->noPas()->findByAttributes(array('pas_key' => $hfa['patkey']));
            $error = null;
            if (!$patient) {
                $error = 'Patient not found';
            } else {
                /*
                $vf = OphInVisualfields_Field_Measurement::model()->with('patient_measurement')->findAllByAttributes(array(
                                'patient_measurement.patient_id' => $patient->id,
                                't.study_datetime' => $hfa['Test_Date'] . ' ' . $hfa['TestTime']));

                */
                $vf_cmd = Yii::app()->db->createCommand()
                    ->select('count(*) as ct')
                    ->from('ophinvisualfields_field_measurement')
                    ->join('patient_measurement', 'patient_measurement.id = ophinvisualfields_field_measurement.patient_measurement_id')
                    ->where('patient_measurement.patient_id = :patient_id AND ophinvisualfields_field_measurement.study_datetime = :dt',
                        array(':patient_id' => $patient->id, ':dt' => $hfa['Test_Date'].' '.$hfa['TestTime']));
                $vf_ct = $vf_cmd->queryRow();
                if ($vf_ct['ct'] == 0) {
                    $error = 'Missing VF';
                } elseif ($vf_ct['ct'] > 1) {
                    $error = 'Duplicate '.$vf_ct['ct'].'VF';
                } else {
                    ++$matched;
                }
            }
            if ($error) {
                echo "{$error}: hosnum: {$hfa['patkey']} at ".$hfa['Test_Date'].' '.$hfa['TestTime'].'. File: '.$hfa['imagelocation']."\n";
            }
        }

        echo 'MATCHED:'.$matched."\n";
    }
}
