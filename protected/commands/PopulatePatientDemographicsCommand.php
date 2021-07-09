<?php

use OEModule\PASAPI\components\XmlHelper;
use OEModule\PASAPI\models\PasApiAssignment;

class PopulatePatientDemographicsCommand extends CConsoleCommand
{
    private const DELAY_SECONDS = 5;
    /**
     * @throws Exception
     */
    public function actionIndex($block_size = 100)
    {
        $offset = 0;
        $query = Yii::app()->db->createCommand()
            ->selectDistinct('p.nhs_num')
            ->from('patient p')
            ->leftJoin('pasapi_assignment pa', 'pa.internal_id = p.id')
            ->where('pa.id IS NULL AND LOWER(p.hos_num) = \'unknown\'
            AND p.nhs_num IN (SELECT p1.nhs_num FROM patient p1 GROUP BY p1.nhs_num HAVING COUNT(*) = 1)');

        $total_patients = Yii::app()->db->createCommand()
            ->select('COUNT(*)')
            ->from('patient p')
            ->leftJoin('pasapi_assignment pa', 'pa.internal_id = p.id')
            ->where('pa.id IS NULL AND LOWER(p.hos_num) = \'unknown\'
            AND p.nhs_num IN (SELECT p1.nhs_num FROM patient p1 GROUP BY p1.nhs_num HAVING COUNT(*) = 1)')
            ->queryScalar();

        if ($total_patients === 0) {
            echo "No patients with missing demographics found. Exiting...\n";
            Yii::app()->end();
        }

        echo $total_patients . " patient records with missing demographics found. Commencing demographic lookup...\n";
        $block_count = 1;
        $patients_to_update = array();

        do {
            $unaltered_patients = array();
            $transaction = Yii::app()->db->beginTransaction();
            try {
                $query = $query->limit($block_size, $offset);
                $patients_to_update = $query->queryColumn();
                echo "Processing patient record block $block_count. Block size: " . count($patients_to_update) . "...";
                foreach ($patients_to_update as $patient) {
                    $result = $this->pasRequest($patient);
                    $xml_helper = new XmlHelper($result);
                    $xml_handler = $xml_helper->getHandler();

                    while ($xml_handler->read() && $xml_handler->name !== 'Patient');

                    // now that we're at the right depth, hop to the next <patient/> until the end of the tree
                    while ($xml_handler->name === 'Patient') {
                        $node = new SimpleXMLElement($xml_handler->readOuterXML());

                        $patient_record = Patient::model()->find('nhs_num = :nhs AND hos_num = \'unknown\'', [':nhs' => $patient]);

                        if (!$patient_record) {
                            throw new CDbException("Unable to find patient record with NHS: $patient");
                        }

                        $patient_record->hos_num = $node->HospitalNumber;

                        if (!$patient_record->validate()) {
                            // If the patient record is invalid (most likely due to an invalid CRN from the MPI),
                            // don't make any changes to it and report that it hasn't been saved.
                            $unaltered_patients[] = $patient_record->id;
                        } else {
                            if (!$patient_record->save()) {
                                throw new CDbException("Unable to save demographic changes for patient ID $patient_record->id: " . print_r($patient_record->getErrors(), true));
                            }

                            $assignment = new PasApiAssignment();
                            $assignment->resource_id = $node->HospitalNumber;
                            $assignment->resource_type = 'Patient';
                            $assignment->internal_type = '\\Patient';
                            $assignment->internal_id = $patient_record->id;

                            if (!$assignment->save()) {
                                throw new CDbException("Unable to save demographic changes for patient: $assignment->resource_id" . print_r($assignment->getErrors(), true));
                            }
                        }

                        $xml_handler->next('Patient');
                    }
                    sleep(self::DELAY_SECONDS);
                }
                $transaction->commit();
                if (count($unaltered_patients) > 0) {
                    OELog::log(
                        "PopulatePatientDemographics: Patients with the following IDs could not be saved: ["
                        . implode(', ', $unaltered_patients)
                        . ']'
                    );
                    $total_patients -= count($unaltered_patients);
                    echo "Done. " . count($unaltered_patients) . " patients were not updated. Consult the application logs for further information.\n";
                } else {
                    echo "Done.\n";
                }
                $block_count++;
                $offset += count($patients_to_update) - 1;
            } catch (Exception $e) {
                $transaction->rollback();
                echo "PopulatePatientDemographics: " . $e->getMessage();
                OELog::log("PopulatePatientDemographics: " . $e->getMessage());
                Yii::app()->end(1);
            }
        } while (!empty($patients_to_update));

        echo $total_patients . " patient demographic records updated successfully.\n";
        OELog::log("PopulatePatientDemographics: " . $total_patients . " patient demographic records updated successfully.");
    }

    /**
     * Building the query string and making a GET call to the API
     * @param $nhs_num
     * @return bool|string
     * @throws Exception
     */
    private function pasRequest($nhs_num)
    {
        $curl = new Curl();
        $xml = false;

        $url = Yii::app()->params['pasapi']['url'];

        $query = array();
        $query['nhsnum'] = $nhs_num;

        $error = '';
        if (!empty($query)) {
            $xml = $curl->get($url . '?' . http_build_query($query));
            $ch = $curl->curl;

            if (curl_errno($ch)) {
                $error = 'PopulatePatientDemographics cURL error occurred on API request. Request error: ' . curl_error($ch) . " ";
                OELog::log($error);
            }
        }

        Audit::add('PopulatePatientDemographics', 'GET request', $error . $xml);

        return $xml;
    }
}
