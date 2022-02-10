<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2019
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

class BaseDeliveryCommand extends CConsoleCommand
{
    // if export path provided it will overwrite the $path
    public $export_path = null;
    public $xml_template = '';
    public $template_path = '/protected/modules/OphCoCorrespondence/views/templates/xml/docman/';

    public $path;
    public $event;

    public ?bool $generate_xml = null;
    public bool $with_print;

    public bool $generate_csv;
    public $csv_file_options = [
        'file_name' => null,
        'format' => 'OEGPLetterReport_%s.csv',
        'header' => [
            'hos_num',
            'clinician_name',
            'letter_type',
            'letter_finalised_date',
            'letter_created_date',
            'last_significant_event_date',
            'letter_sent_date',
        ]
    ];

    /**
     * Whether Internal referral tags generated into the xml, also processes XML for only Internal referrals as well
     *
     * BUT, it will not generate 3rd part (like WinDip) XML, to generate specific
     *
     * @var bool
     */
    private bool $with_internal_referral;

    /**
     * DocManDeliveryCommand constructor.
     */
    public function __construct()
    {
        $this->path = $this->export_path ?: Yii::app()->params['docman_export_dir'];

        $this->template_path = dirname(Yii::app()->basePath) . $this->template_path;
        if (!$this->xml_template) {
            $template_name = \Yii::app()->params['docman_xml_template'] ?? 'default';
            $this->xml_template = $this->template_path . $template_name . '.php';
        }

        if ($this->generate_xml === null ) {
            $this->generate_xml = isset(\Yii::app()->params['docman_generate_xml']) && \Yii::app()->params['docman_generate_xml'];
        }

        $this->with_internal_referral = !isset(Yii::app()->params['docman_with_internal_referral']) || Yii::app()->params['docman_with_internal_referral'] !== false;
        $this->with_print = isset(\Yii::app()->params['docman_with_print']) && \Yii::app()->params['docman_with_print'];

        $this->checkPath($this->path);

        if ($this->generate_csv = Yii::app()->params['docman_generate_csv']) {
            $this->csv_file_options['file_name'] = implode(DIRECTORY_SEPARATOR, array($this->path, sprintf($this->csv_file_options['format'], date('Ymd'))));
            $this->createCSVFile();
        }

        parent::__construct(null, null);
    }

    private function createCSVFile()
    {
        //if file doesn't exist we create one and put the header
        if (!file_exists($this->csv_file_options['file_name'])) {
            try {
                $fp = fopen($this->csv_file_options['file_name'], 'ab');
                fputcsv($fp, $this->csv_file_options['header']);
                fclose($fp);
            } catch (\Exception $exception) {
                \OELog::log($exception->getMessage());
            }
        }
    }

    private function writeCSVFile($data)
    {
        $fp = fopen($this->csv_file_options['file_name'], 'ab');
        fputcsv($fp, $data);
        fclose($fp);
    }

    /**
     * Create directory if not exist
     * @param $path
     */
    private function checkPath($path)
    {
        if (!is_dir($path)) {
            mkdir($path, 0777, true);
            echo "ALERT! Directory " . $this->path . " has been created!";
        }
    }


    /**
     * Run the command.
     * @throws Exception
     */
    public function actionIndex()
    {
        throw new Exception("Function not implemented");
    }

    /**
     * @param $data
     */
    public function logData($data)
    {
        if ($this->generate_csv) {
            $doc_log = new DocumentLog();
            $doc_log->attributes = $data;
            $doc_log->save();

            $this->writeCSVFile($data);
        }
    }

    public function getLastSignificantEventDate(Event $event)
    {
        $correspondence_date = $event->event_date;

        $event_type = EventType::model()->find('class_name=?', array('OphTrOperationnote'));
        $event_type_id = $event_type->id;

        $criteria = new CDbCriteria();
        $criteria->condition = "episode_id = '" . $event->episode->id
            . "' AND event_date <= '$correspondence_date' AND deleted = 0 AND event_type_id = '$event_type_id'";
        $criteria->order = 'event_date desc, created_date desc';

        $last_opnote_date = '';
        if ($op_note = Event::model()->find($criteria)) {
            $last_opnote_date = $op_note->event_date;
        }

        $event_type = EventType::model()->find('class_name=?', array('OphCiExamination'));
        $event_type_id = $event_type->id;

        $criteria = new CDbCriteria();
        $criteria->condition = "episode_id = '" . $event->episode->id
            . "' AND event_date <= '$correspondence_date' AND deleted = 0 AND event_type_id = '$event_type_id'";
        $criteria->order = 'event_date desc, created_date desc';

        $last_exam_date = '';
        if ($examEvent = Event::model()->find($criteria)) {
            $last_exam_date = $examEvent->event_date;
        }

        $last_significant_event_date = '';
        if (!$last_exam_date && $last_opnote_date) {
            $last_significant_event_date = $last_opnote_date;
        }
        if ($last_exam_date && !$last_opnote_date) {
            $last_significant_event_date = $last_exam_date;
        }
        if (!$last_exam_date && !$last_opnote_date) {
            $last_significant_event_date = null;
        }
        if ($last_exam_date && $last_opnote_date) {
            $diff = date_diff(date_create($last_exam_date), date_create($last_opnote_date));
            if ($diff->days >= 0) {
                $last_significant_event_date = $last_opnote_date;
            } else {
                $last_significant_event_date = $last_exam_date;
            }
        }

        return $last_significant_event_date;
    }

    public function getGeneralDataForTemplate($filename, \Event $event): array
    {
        $patient = $event->episode->patient;
        $contact = $patient->contact;
        $sub_obj = $this->event->episode->firm->serviceSubspecialtyAssignment->subspecialty ?? null;

        $local_identifier_value = PatientIdentifierHelper::getIdentifierValue(PatientIdentifierHelper::getIdentifierForPatient(
            'LOCAL',
            $patient->id,
            $event->institution_id, $event->site_id
        ));
        $global_identifier_value = PatientIdentifierHelper::getIdentifierValue(PatientIdentifierHelper::getIdentifierForPatient(
            'GLOBAL',
            $patient->id,
            $event->institution_id, $event->site_id
        ));

        return [
            'hos_num' => $local_identifier_value,
            'nhs_num' => $global_identifier_value,
            'full_name' => $contact->getFullName(),
            'last_name' => $contact->last_name,
            'first_name' => $contact->first_name,
            'patient_title' => $contact->title,
            'second_forename' => '',
            'title' => $contact->title,
            'dob' => $patient->dob,
            'date_of_death' => $patient->date_of_death,
            'gender' => $patient->gender,
            'address' => isset($contact->address) ? $contact->address->getLetterArray() : [],
            'address1' => isset($contact->address) ? ($contact->address->address1) : '',
            'city' => isset($contact->address) ? ($contact->address->city) : '',
            'county' => isset($contact->address) ? ($contact->address->county) : '',
            'post_code' => isset($contact->address) ? ($contact->address->postcode) : '',

            'gp_nat_id' => $patient->gp->nat_id ?? null,
            'gp_name' => isset($patient->gp->contact) ? $patient->gp->contact->getFullName() : null,
            'gp_first_name' => isset($patient->gp->contact) ? $patient->gp->contact->first_name : null,
            'gp_last_name' => isset($patient->gp->contact) ? $patient->gp->contact->last_name : null,
            'gp_title' => isset($patient->gp->contact) ? $patient->gp->contact->title : null,

            'practice_code' => $patient->practice->code ?? '',
            'event_id' => $event->id,
            'event_date' => $event->event_date,
            'subspeciality' => $sub_obj->ref_spec ?? 'SS',
            'subspeciality_name' => $sub_obj->name ?? 'Support Services',
            'document_links' => [$filename],
        ];
    }

    /**
     * Special function to sanitize XML
     *
     * @param string $xml
     * @return string
     */
    public function cleanXML($xml)
    {
        return str_replace("&", "and", $xml);
    }
}
