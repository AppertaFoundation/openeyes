<?php

/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2012
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2012, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
class ImportLegacyVFCommand extends CConsoleCommand
{
    public $importDir;
    public $archiveDir;
    public $errorDir;
    public $dupDir;
    public $interval;

    public function getHelp()
    {
        return "Usage: importlegacyvf import --interval=<time> --importDir=<dir> --archiveDir=<dir> --errorDir=<dir> --dupDir=<dir>\n\n"
        ."Import Humphrey visual fields into OpenEyes from the given import directory.\n"
        ."Successfully imported files are moved to the given archive directory;\n"
        ."likewise, errored files and duplicate files (already within OE) are moved to\n"
        ."the respective directory. --interval is used to check for tests within\n"
        ."the specified time limit, so PT10M looks for files within 10 minutes of the other to\n"
        ."bind to an existing field.\n\n"
        ."The import expects to find .XML files in the given directory, two\n"
        ."for each field test, and each file is expected to be in a format\n"
        ."acceptable by OpenEyes (specifically they must conform to the API).\n"
        ."For each pair of files, the first is a patient measurement, the\n"
        ."second a humphrey field test reading.\n"
        ."\n";
    }

    public function actionImport($importDir, $archiveDir, $errorDir, $dupDir, $interval = 'PT45M', $pasImport = false)
    {
        $this->importDir = $this->checkSeparator($importDir);
        $this->archiveDir = $this->checkSeparator($archiveDir);
        $this->errorDir = $this->checkSeparator($errorDir);
        $this->dupDir = $this->checkSeparator($dupDir);
        $this->interval = $interval;
        $fhirMarshal = Yii::app()->fhirMarshal;
        $eventType = EventType::model()->find('class_name=:class_name', array(':class_name' => 'OphInVisualfields'));
        if (!$eventType) {
            echo 'Cannot find OphInVisualfields event type, cannot continue'.PHP_EOL;
            die();
        }
        echo 'Processing FMES files...'.PHP_EOL;
        $filenames = glob($this->importDir.'/*.fmes');
        echo count($filenames)." files to process\n";

        foreach ($filenames as $file) {
            try {
                $basename = basename($file);
                echo $basename.PHP_EOL;

                // First check the file has not already been imported:
                $field = file_get_contents($file);
                $fieldObject = $fhirMarshal->parseXml($field);

                if ($protected_file = ProtectedFile::model()->find('name=:name', array(':name' => $fieldObject->file_reference))) {
                    echo '- ProtectedFile exists ('.$protected_file->id.')'.PHP_EOL;
                    $this->move($this->dupDir, $file);
                    continue;
                }

                // Extract the patient number
                $matches = array();
                preg_match('/__OE_PATIENT_ID_([0-9]*)__/', $field, $matches);
                if (count($matches) < 2) {
                    echo '- Failed to extract patient ID'.PHP_EOL;
                    $this->move($this->errorDir, $file);
                    continue;
                }
                $match = str_pad($matches[1], 7, '0', STR_PAD_LEFT);

                // Fetch the patient
                if ($pasImport) {
                    $model = new Patient(null);
                    $model->hos_num = $match;
                    $results = $model->search()->getData();
                    $patient = reset($results);
                } else {
                    $patient = Patient::model()->find('hos_num=:hos_num', array(':hos_num' => $match));
                }

                if (!$patient) {
                    echo "- Failed to find patient ($match)".PHP_EOL;
                    $this->move($this->errorDir, $file);
                    continue;
                }
                $pid = $patient->id;
                $field = preg_replace('/__OE_PATIENT_ID_([0-9]*)__/', $pid, $field);

                // Convert to measurement
                $resource_type = 'MeasurementVisualFieldHumphrey';
                $service = Yii::app()->service->getService($resource_type);
                $fieldObject = $fhirMarshal->parseXml($field);
                $tx = Yii::app()->db->beginTransaction();
                $ref = $service->fhirCreate($fieldObject);
                $tx->commit();
                $refId = $ref->getId();
                $measurement = OphInVisualfields_Field_Measurement::model()->findByPk($refId);
                $study_datetime = $measurement->study_datetime;

                // Check for existing legacy events
                if (!$episode = Episode::model()->find('legacy = 1 AND patient_id = :patient_id', array(':patient_id' => $pid))) {
                    echo '- No legacy episode found, creating...';
                    $episode = new Episode();
                    $episode->legacy = 1;
                    $episode->patient_id = $pid;
                    $episode->save();
                    echo 'done'.PHP_EOL;

                    // As there are no previous legacy events, we can create a new event
                    $this->newEvent($episode, $eventType, $measurement);
                    $this->move($this->archiveDir, $file);
                } else {
                    // There is a legacy episode, so there may be unmatched legacy field events

                    $criteria = new CdbCriteria();
                    $criteria->condition = 'event_type_id = :event_type_id and t.deleted = 0 and ep.deleted = 0 and ep.legacy = 1 and ep.patient_id = :patient_id';
                    $criteria->join = 'join episode ep on ep.id = t.episode_id';
                    $criteria->order = 't.event_date desc';
                    $criteria->params = array(':patient_id' => $pid, ':event_type_id' => $eventType->id);
                    if ($this->interval) {
                        // we're looking for all events that are bound to a legacy episode,
                        // for the given patient, looking for the last created test -
                        // this accounts for multiple tests per eye - the implication
                        // being that the newest test overrides the last test for the same eye
                        // (e.g. when a mistake is made and the test is re-ran):
                        // Base time on interval defined by user, a narrow time slot that the test falls within
                        $startCreatedTime = new DateTime($study_datetime);
                        $endCreatedTime = new DateTime($study_datetime);
                        $startCreatedTime->sub(new DateInterval($this->interval));
                        $endCreatedTime->add(new DateInterval($this->interval));
                        $criteria->condition .= ' AND t.event_date >= STR_TO_DATE("'.$startCreatedTime->format('Y-m-d H:i:s')
                            .'", "%Y-%m-%d %H:%i:%s") AND t.event_date <= STR_TO_DATE("'.$endCreatedTime->format('Y-m-d H:i:s')
                            .'", "%Y-%m-%d %H:%i:%s")';
                    }
                    // Of events, there can only be one or none:
                    // FIXME: This can return multiple events, so how do we choose?
                    $events = Event::model()->findAll($criteria);
                    if (count($events) == 1) {
                        echo '- Found existing event ('.$events[0]->id.')'.PHP_EOL;
                        $element = Element_OphInVisualfields_Image::model()->find('event_id = :event_id', array(':event_id' => $events[0]->id));

                        $side = strtolower($measurement->eye->name);

                        if (($existing = $element->{"{$side}_field"})) {
                            if ($measurement->study_datetime > $existing->study_datetime) {
                                echo "Newer than existing measurement on {$side}, overwriting\n";
                                $element->{"{$side}_field_id"} = $measurement->id;
                                $unattached = $existing;
                            } else {
                                echo "Older than existing measurement on {$side}, ignoring\n";
                                $unattached = $measurement;
                            }
                            // Add dummy reference for the unattached measurement
                            $ref = new MeasurementReference();
                            $ref->patient_measurement_id = $unattached->getPatientMeasurement()->id;
                            $ref->save();
                        } else {
                            echo "No existing measurement on {$side}, adding\n";
                            $element->{"{$side}_field_id"} = $measurement->id;
                        }

                        $element->save();
                        $this->move($this->archiveDir, $file);
                    } elseif (count($events) > 1) {
                        echo '- Found more than one matching event, cannot attach'.PHP_EOL;
                        $this->move($this->errorDir, $file);
                    } else {
                        // No events in match window, so we create a new one
                        $this->newEvent($episode, $eventType, $measurement);
                        $this->move($this->archiveDir, $file);
                    }
                }
            } catch (Exception $ex) {
                echo $ex.PHP_EOL;
                if (@$tx && $tx->active) {
                    echo '- rolling back tx'.PHP_EOL;
                    $tx->rollback();
                }
                $this->move($this->errorDir, $file);
            }
        }
    }

    /**
     * @param $episode Episode
     * @param $eventType EventType
     * @param $measurement OphInVisualfields_Field_Measurement
     */
    private function newEvent($episode, $eventType, $measurement)
    {
        // now bind a new event to the new legacy episode:
        echo '- Creating new event...';
        $event = new Event();
        $event->episode_id = $episode->id;
        $event->event_type_id = $eventType->id;
        $event->created_user_id = 1;
        $event->event_date = $measurement->study_datetime;
        $event->save(true, null, true);

        $image = new Element_OphInVisualfields_Image();
        $image->event_id = $event->id;
        if ($measurement->eye->name == 'Left') {
            $image->left_field_id = $measurement->id;
        } else {
            $image->right_field_id = $measurement->id;
        }
        $image->save();
        echo 'done'.PHP_EOL;
    }

    /**
     * Moves both the .pmes and .fmes file.
     *
     * @param $toDir string
     * @param $file string
     */
    private function move($toDir, $file)
    {
        $file = basename($file);
        echo "- Moving to $toDir...";
        rename($this->importDir.$file, $toDir.$file);
        echo 'done'.PHP_EOL;
    }

    /**
     * @param $file string
     *
     * @return string
     */
    private function checkSeparator($file)
    {
        if (substr($file, -1) != DIRECTORY_SEPARATOR) {
            $file = $file.DIRECTORY_SEPARATOR;
        }

        return $file;
    }
}
