<?php

/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2012
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2012, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */
class InternalReferralDeliveryCommand extends CConsoleCommand
{

    private $path;

    private $event;

    private $csv_format = 'OEGPLetterReport_%s.csv';

    private $generate_xml = false;

    private $generate_csv = false;

    // The integrated service
    public $integration = 'Windipintegration';

    /**
     * InternalReferralDelivery constructor.
     */
    public function __construct()
    {
        //checking the integration
        if( !Yii::app()->internalReferralIntegration){
            throw new Exception("No Internal Referral integration found");
        }

        $this->path = Yii::app()->params['OphCoCorrespondence_Internalreferral']['export_dir'];
        $this->generate_xml = !isset(Yii::app()->params['OphCoCorrespondence_Internalreferral']['xml_format']) || Yii::app()->params['OphCoCorrespondence_Internalreferral']['xml_format'] !== 'none';
        $this->generate_csv = Yii::app()->params['OphCoCorrespondence_Internalreferral']['generate_csv'];

        // check if directory exists
        if (!is_dir($this->path)) {
            mkdir($this->path);
            echo "ALERT! Directory " . $this->path . " has been created!";
        }
        parent::__construct(null, null);
    }

    /**
    * Run the command.
    */
    public function run($args)
    {
        $pending_documents = $this->getPendingDocuments();
        foreach ($pending_documents as $document) {
            echo 'Processing event ' . $document->document_target->document_instance->correspondence_event_id . PHP_EOL;

            $data = Yii::app()->internalReferralIntegration->constructRequestData($document->document_target->document_instance->event);

            $xml = $this->savePDFFile($data);

            //$data = Yii::app()->internalReferralIntegration->generateXmlRequest($document->document_target->document_instance->event);

            echo "<pre>" . print_r($xml, true) . "</pre>";
        }
    }

    /**
     * @return CActiveRecord[]
     */
    private function getPendingDocuments()
    {
        $criteria = new CDbCriteria();
        $criteria->join = "JOIN `document_target` tr ON t.`document_target_id` = tr.id";
        $criteria->join .= " JOIN `document_instance` i ON tr.`document_instance_id` = i.`id`";
        $criteria->join .= " JOIN event e ON i.`correspondence_event_id` = e.id";
        $criteria->addCondition("e.deleted = 0");
        $criteria->addCondition("t.`output_status` = 'PENDING' AND t.`output_type`= 'Internalreferral'");

        return DocumentOutput::model()->findAll($criteria);
    }

    private function savePDFFile($data)
    {
        $path = \Yii::getPathOfAlias('application.modules.OphCoCorrespondence.views.windipintegration').'/request_xml.php';
        if(!file_exists($path)){
            throw new \Exception('Template '.$path.' does not exist.');
        }
        return $this->renderFile($path, $data, true);

    }

}