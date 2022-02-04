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

class WaitingListController extends BaseModuleController
{
    public $renderPatientPanel = false;
    private $pdf_output;

    public function accessRules()
    {
        return array(
            array('allow',
                'actions' => array('index', 'search', 'filterFirms', 'filterSetFirm', 'filterSetStatus', 'filterSetSiteId', 'filterSetHosNum', 'setBooked'),
                'roles' => array('OprnViewClinical'),
            ),
            array('allow',
                'actions' => array('printLetters', 'printLettersPdf'),
                'roles' => array('OprnPrint'),
            ),
            array('allow',
                'actions' => array('confirmPrinted'),
                'roles' => array('OprnConfirmBookingLetterPrinted'),
            ),
        );
    }

    /**
     * @return array
     *               (non-phpdoc)
     *
     * @see parent::printActions()
     */
    public function printActions()
    {
        return array(
            'printLetters',
            'printLettersPdf'
        );
    }

    /**
     * Lists all models.
     */
    public function actionIndex()
    {
        if (empty($_POST)) {
            if (($data = YiiSession::get('waitinglist_searchoptions'))) {
                $_POST = $data;
            } else {
                $_POST = array(
                    'firm-id' => YiiSession::get('selected_firm_id'),
                    'subspecialty-id' => Firm::Model()->findByPk(YiiSession::get('selected_firm_id'))->getSubspecialtyID(),
                );
            }

            Audit::add('waiting list', 'view');
        } else {
            Audit::add('waiting list', 'search');
        }

        $this->pageTitle = 'Partial Bookings Waiting List';
        $this->render('index');
    }

    /**
     * Carry out a search on the waiting list.
     */
    public function actionSearch()
    {
        Audit::add('waiting list', 'search');

        $subspecialty_id = \Yii::app()->request->getParam('subspecialty-id');
        $firm_id = !empty($_POST['firm-id']) ? $_POST['firm-id'] : null;
        $status = !empty($_POST['status']) ? $_POST['status'] : null;
        $patient_identifier_value = \Yii::app()->request->getParam('patient_identifier_value');
        $site_id = !empty($_POST['site_id']) ? $_POST['site_id'] : false;
                    $booking_status =  \Yii::app()->request->getParam('booking_status', '');

        YiiSession::set('waitinglist_searchoptions', array(
                'subspecialty-id' => $subspecialty_id,
                'firm-id' => $firm_id,
                'status' => $status,
                'patient_identifier_value' => $patient_identifier_value,
                'site_id' => $site_id,
                'booking_status' => $booking_status
        ));

        $dataProvider = $this->getWaitingList($firm_id, $subspecialty_id, $status, $patient_identifier_value, $site_id, $booking_status);

        $this->renderPartial('_list', array('dataProvider' => $dataProvider, 'assetPath' => $this->assetPath), false, true);
    }

    /**
     * Get the operations for the waiting list criteria provided.
     *
     * @param $firm_id
     * @param $subspecialty_id
     * @param $status
     * @param bool $hos_num
     * @param bool $site_id
     *
     * @return Element_OphTrOperationbooking_Operation[]
     * @throws Exception
     */
    public function getWaitingList($firm_id, $subspecialty_id, $status, $patient_identifier_value = false, $site_id = false, $booking_status)
    {
        $criteria = new CDbCriteria();
        // common joins
        $criteria_with = [
            'event.episode.patient.contact',
            'event.episode.patient.practice',
            'event.episode.patient.contact.correspondAddress',
            'eye',
            'priority',
            'status',
            'date_letter_sent',
            'procedures'
        ];

        if ($firm_id) {
            $criteria_with[] = 'event.episode.firm';
            $criteria->addCondition('firm.id = :firm_id');
            $criteria->params[':firm_id'] = $firm_id;
        } elseif (!empty($subspecialty_id)) {
            $criteria_with[] = 'event.episode.firm.serviceSubspecialtyAssignment';
            $criteria->addCondition('serviceSubspecialtyAssignment.subspecialty_id = :subspecialty_id');
            $criteria->params[':subspecialty_id'] = $subspecialty_id;
        }

        $patient_search = new \PatientSearch();
        $patient_search_details = $patient_search->prepareSearch($patient_identifier_value);
        $terms_with_types = $patient_search_details['terms_with_types'] ?? [];

        if ($patient_search_details['original_term']) {
            $id_condition = [];
            foreach ($terms_with_types as $pi_key => $item) {
                $type = $item['patient_identifier_type'];
                $id_condition[] = "(value = :{$pi_key}_value AND patient_identifier_type_id = :{$pi_key}_type_id)";

                $criteria->params[":{$pi_key}_value"] = $item['term'];
                $criteria->params[":{$pi_key}_type_id"] = $type->id;
            }

            if ($id_condition) {
                $criteria_with['event.episode.patient.identifiers'] = array('together' => true);
                $criteria->addCondition('(' . implode(' OR ', $id_condition) . ')');
            } else {
                // means 'terms_with_types' didn't return anything so we have no patient_identifier_type to search in
                // if no values in $patient_search_details['patient_identifier_value'] means the input field was empty
                // so we do not have to restrict this part
                $criteria->addCondition('1 = 0');
            }
        }

        if ($site_id && ctype_digit($site_id)) {
            $criteria->addCondition('t.site_id = :site_id');
            $criteria->params[':site_id'] = $site_id;
        } else {
            $criteria_with[] = 'site';
            $criteria->addCondition('site.institution_id = :institution_id');
            $criteria->params[':institution_id'] = Institution::model()->getCurrent()->id;
        }

        if ($booking_status) {
            $criteria->addCondition('t.status_id = :status_id');
            $criteria->params[':status_id'] = $booking_status;
        } else {
            $booking_status_ids = Yii::app()->db->createCommand()->select('id')->from('ophtroperationbooking_operation_status')
                ->where(['in','name', ['On-Hold', 'Requires scheduling', 'Requires rescheduling', ]])->queryColumn();
            $booking_status_ids = "(" . implode(',', $booking_status_ids) . ")";
            $criteria->addCondition("t.status_id IN $booking_status_ids");
        }

        $criteria->with = $criteria_with;
        $criteria->addCondition('event.id IS NOT NULL AND episode.end_date IS NULL');
        $criteria->order = 'decision_date asc';

        // get total record count for pagination
        $items_count = Element_OphTrOperationbooking_Operation::model()->count($criteria);

        // instantiate pagination
        $pagination = new CPagination($items_count);
        $pagination->pageSize = 15;
        $pagination->applyLimit($criteria);

        Yii::app()->event->dispatch('start_batch_mode');
        $dataProvider = new CActiveDataProvider('Element_OphTrOperationbooking_Operation', [
            'criteria' => $criteria,
            'pagination' => $pagination,
        ]);
        Yii::app()->event->dispatch('end_batch_mode');

        return $dataProvider;
    }

    /**
     * Generates a firm list based on a subspecialty id provided via POST
     * echoes form option tags for display.
     */
    public function actionFilterFirms()
    {
        YiiSession::set('waitinglist_searchoptions', 'subspecialty-id', $_POST['subspecialty_id']);

        echo CHtml::tag('option', array('value' => ''), CHtml::encode("All ".Yii::app()->params['service_firm_label']."s"), true);

        $firms = $this->getFilteredFirms($_POST['subspecialty_id']);

        foreach ($firms as $id => $name) {
            echo CHtml::tag('option', array('value' => $id), CHtml::encode($name), true);
        }
    }

    /**
     * Store the filter item in the user session.
     *
     * @param $field
     * @param $value
     */
    public function setFilter($field, $value)
    {
        YiiSession::set('waitinglist_searchoptions', $field, $value);
    }

    /**
     * Ajax action to set the firm filter.
     */
    public function actionFilterSetFirm()
    {
        $this->setFilter('firm-id', $_POST['firm_id']);
    }

    /**
     * Ajax action to set the status filter.
     */
    public function actionFilterSetStatus()
    {
        $this->setFilter('status', $_POST['status']);
    }

    /**
     * Ajax action to the site id filter.
     */
    public function actionFilterSetSiteId()
    {
        $this->setFilter('site_id', $_POST['site_id']);
    }

    /**
     * Ajax action to set the hosnum filter.
     */
    public function actionFilterSetHosNum()
    {
        $this->setFilter('patient_identifier_value', \Yii::app()->request->getParam('patient_identifier_value'));
    }
    /**
     * Helper method to fetch firms by subspecialty ID.
     *
     * @param int $subspecialtyId
     *
     * @return array
     */
    protected function getFilteredFirms($subspecialtyId)
    {
        $criteria = new CDbCriteria();
        if ($subspecialtyId > 0) {
            $criteria->addCondition('subspecialty_id = :subspecialtyId');
            $criteria->params[':subspecialtyId'] = $subspecialtyId;
        }
        $criteria->addCondition('can_own_an_episode = 1');
        if (!isset(Yii::app()->session['selected_institution_id'])) {
            $criteria->addCondition('institution_id = :institutionId');
            $criteria->params[':institutionId'] = Institution::model()->getCurrent()->id;
        }
        $criteria->order = '`t`.name asc';

        return CHtml::listData(Firm::model()
            ->active()
            ->with(array('serviceSubspecialtyAssignment'))
            ->findAll($criteria), 'id', 'name');
    }

    /**
     * Prints next pending letter type for requested operations
     * Operation IDs are passed as an array (operations[]) via GET or POST
     * Invalid operation IDs are ignored.
     *
     * @throws CHttpException
     */
    public function actionPrintLetters()
    {
        Audit::add('waiting list', (@$_REQUEST['all'] == 'true' ? 'print all' : 'print selected'), serialize($_POST));
        if (isset($_REQUEST['event_id'])) {
            $operations = Element_OphTrOperationbooking_Operation::model()->findAll('event_id=?', array($_REQUEST['event_id']));
            $auto_confirm = true;
        } else {
            $operation_ids = (isset($_REQUEST['operations'])) ? $_REQUEST['operations'] : null;
            $auto_confirm = (isset($_REQUEST['confirm']) && $_REQUEST['confirm'] == 1);
            if (!is_array($operation_ids)) {
                throw new CHttpException('400', 'Invalid operation list');
            }
            $operations = Element_OphTrOperationbooking_Operation::model()->findAllByPk($operation_ids);
        }

        $this->layout = '//layouts/print';

        $html = '';

        foreach ($operations as $operation) {
            set_time_limit(3);
            $html .= $this->printLetter($operation, $auto_confirm);
        }

        echo $html;
    }

        /**
     * Merges a PDF file to the end of the output
     *
     * @param $pdf_path
     * @throws Exception
     */
    private function addPDFToOutput($pdf_path)
    {
        if (file_exists($pdf_path)) {
            $pagecount = $this->pdf_output->setSourceFile($pdf_path);
            for ($i = 1; $i <= $pagecount; $i++) {
                $this->pdf_output->AddPage('P');
                $tplidx = $this->pdf_output->ImportPage($i);
                $this->pdf_output->useTemplate($tplidx);
            }
        } else {
            $this->pdf_output->AddPage('P');
            $this->pdf_output->SetFont('Arial', 'B', 16);
            $this->pdf_output->SetY(($this->pdf_output->GetPageHeight() / 2) - 10);
            $this->pdf_output->Cell(0, 10, 'Attachment unavailable -', 0, 2, 'C');
            $this->pdf_output->Cell(0, 10, 'please try re-printing the event to re-generate attachments', 0, 2, 'C');
        }
    }

    function generatePdf($operation, $documents, $docrefs, $barcodes, $patients, $directory)
    {
        $url = "http://localhost/OphTrOperationbooking/waitingList/printLetters?"
        ."operations[]=".$operation;

        set_time_limit(60);

        $pdf_suffix = 'waitingList_'.Yii::app()->user->id.'_'.rand();

        $wk = Yii::app()->puppeteer;
        $wk->setDocuments($documents);
        $wk->setDocrefs($docrefs);
        $wk->setBarcodes($barcodes);
        $wk->setPatients($patients);

        $wk->savePageToPDF($directory, $pdf_suffix."_".$operation, '', $url);

        $pdf = $directory."/".$pdf_suffix."_".$operation.".pdf";
        return $pdf;
    }

    function actionPrintLettersPdf()
    {
        Audit::add('waiting list', (@$_REQUEST['all'] == 'true' ? 'print all' : 'print selected'), serialize($_POST));
        if (isset($_REQUEST['event_id'])) {
            $operations = Element_OphTrOperationbooking_Operation::model()->findAll('event_id=?', array($_REQUEST['event_id']));
            $auto_confirm = true;
        } else {
            $operation_ids = (isset($_REQUEST['operations'])) ? $_REQUEST['operations'] : null;
            $auto_confirm = (isset($_REQUEST['confirm']) && $_REQUEST['confirm'] == 1);
            if (!is_array($operation_ids)) {
                throw new CHttpException('400', 'Invalid operation list');
            }
            $operations = Element_OphTrOperationbooking_Operation::model()->findAllByPk($operation_ids);
        }

        $this->layout = '//layouts/print';

        $cmd = Yii::app()->db->createCommand('SELECT GET_LOCK(?, 1)');

        while (!$cmd->queryScalar(array('waitingListPrint'))) {
        }

        $directory = Yii::app()->assetManager->basePath.'/waitingList';

        Yii::app()->db->createCommand('SELECT RELEASE_LOCK(?)')->execute(array('waitingListPrint'));

        $documents = 0;

        $pdf = array();
        $this->pdf_output = new PDF_JavaScript();

        // FIXME: provide a means by which progress can be reported back to the user, possibly via session and parallel polling?
        foreach ($operations as $operation) {
            $docrefs = array();
            $barcodes = array();
            $patients = array();

            $letter_status = $operation->getDueLetter();
            if ($letter_status === null && $operation->getLastLetter() == Element_OphTrOperationbooking_Operation::LETTER_GP) {
                $letter_status = Element_OphTrOperationbooking_Operation::LETTER_GP;
            }

            set_time_limit(60);

            $docrefs[] = "E:{$operation->event->id}/".strtoupper(base_convert(time().sprintf('%04d', Yii::app()->user->getId()), 10, 32)).'/{{PAGE}}';
            $barcodes[] = $operation->event->barcodeSVG;
            $patients[] = $operation->event->episode->patient;

            ++$documents;

            $pdf[] = $this->generatePdf($operation->id, 1, $docrefs, $barcodes, $patients, $directory);
            $this->addPDFToOutput($pdf[$documents-1]);

            if ($letter_status == Element_OphTrOperationbooking_Operation::LETTER_GP) {
                // Patient letter is another document
                $docrefs[] = "E:{$operation->event->id}/".strtoupper(base_convert(time().sprintf('%04d', Yii::app()->user->getId()), 10, 32)).'/{{PAGE}}';
                $barcodes[] = $operation->event->barcodeSVG;
                $patients[] = $operation->event->episode->patient;

                ++$documents;
                $pdf[] = $this->generatePdf($operation->id, 1, $docrefs, $barcodes, $patients, $directory);
                $this->addPDFToOutput($pdf[$documents-1]);
            }
        }

        header('Content-Type: application/pdf');

        $this->pdf_output->Output('I');

        foreach ($pdf as $pdffile) {
            @unlink($pdffile);
        }
    }

    /**
     * Print the next letter for an operation.
     *
     * @param OEPDFPrint                              $pdf_print
     * @param Element_OphTrOperationbooking_Operation $operation
     * @param bool                                    $auto_confirm
     *
     * @throws CException
     */
    protected function printLetter($operation, $auto_confirm = false)
    {
        $patient = $operation->event->episode->patient;
        $letter_status = $operation->getDueLetter();
        if ($letter_status === null && $operation->getLastLetter() == Element_OphTrOperationbooking_Operation::LETTER_GP) {
            $letter_status = Element_OphTrOperationbooking_Operation::LETTER_GP;
        }
        $letter_templates = array(
                Element_OphTrOperationbooking_Operation::LETTER_INVITE => 'invitation_letter',
                Element_OphTrOperationbooking_Operation::LETTER_REMINDER_1 => 'reminder_letter',
                Element_OphTrOperationbooking_Operation::LETTER_REMINDER_2 => 'reminder_letter',
                Element_OphTrOperationbooking_Operation::LETTER_GP => 'gp_letter',
                Element_OphTrOperationbooking_Operation::LETTER_REMOVAL => false,
        );
        $letter_template = (isset($letter_templates[$letter_status])) ? $letter_templates[$letter_status] : false;

        if ($letter_template) {
            $firm = $operation->event->episode->firm;
            $site = $operation->site;
            $waitingListContact = $operation->waitingListContact;

            // Don't print GP letter if practice address is not defined
            if ($letter_status != Element_OphTrOperationbooking_Operation::LETTER_GP || ($patient->practice && $patient->practice->contact->address)) {
                Yii::log('Printing letter: '.$letter_template, 'trace');

                $html = call_user_func(array($this, 'print_'.$letter_template), $operation);

                if ($auto_confirm) {
                    $operation->confirmLetterPrinted();
                }

                return $html;
            } else {
                Yii::log('Patient has no practice address, printing letter supressed: '.$patient->id, 'trace');
            }
        } elseif ($letter_status === null) {
            Yii::log('No letter is due: '.$patient->id, 'trace');
        } else {
            throw new CException('Undefined letter status');
        }
    }

    /**
     * Get letter from address for letter.
     *
     * @param Element_OphTrOperationbooking_Operation $operation
     *
     * @return string
     */
    protected function getFromAddress($operation)
    {
        $from_address = $operation->site->getLetterAddress(array(
            'include_name' => true,
            'delimiter' => "\n",
        ));
        $from_address .= "\nTel: ".$operation->site->telephone;
        if ($operation->site->fax) {
            $from_address .= "\nFax: ".$operation->site->fax;
        }

        return $from_address;
    }

    /**
     * @param Element_OphTrOperationbooking_Operation $operation
     */
    protected function print_invitation_letter($operation)
    {
        $patient = $operation->event->episode->patient;
        $to_address = $patient->getLetterAddress(array(
            'include_name' => true,
            'delimiter' => "\n",
        ));

        return $this->render('../letters/invitation_letter', array(
            'to' => $patient->salutationname,
            'consultantName' => isset($operation->event->episode->firm->consultant->fullName) ? $operation->event->episode->firm->consultant->fullName : "the eye service",
            'overnightStay' => $operation->overnight_stay,
            'patient' => $patient,
            'changeContact' => $operation->waitingListContact,
            'toAddress' => $to_address,
            'site' => $operation->site,
        ), true);
    }

    /**
     * @param Element_OphTrOperationbooking_Operation $operation
     */
    protected function print_reminder_letter($operation)
    {
        $patient = $operation->event->episode->patient;
        $to_address = $patient->getLetterAddress(array(
            'include_name' => true,
            'delimiter' => "\n",
        ));

        return $this->render('../letters/reminder_letter', array(
                'to' => $patient->salutationname,
                'consultantName' => isset($operation->event->episode->firm->consultant) ? $operation->event->episode->firm->consultant->fullName : "the eye service",
                'overnightStay' => $operation->overnight_stay,
                'patient' => $patient,
                'changeContact' => $operation->waitingListContact,
                'toAddress' => $to_address,
                'site' => $operation->site,
        ), true);
    }

    /**
     * @param Element_OphTrOperationbooking_Operation $operation
     *
     * @throws CException
     */
    protected function print_gp_letter($operation)
    {
        $patient = $operation->event->episode->patient;

        // GP Letter
        if ($patient->practice && $patient->practice->contact->address) {
            $to_address = $patient->practice->getLetterAddress(array(
                    'patient' => $patient,
                    'include_name' => false,
                    'delimiter' => "\n",
                ));
        } else {
            throw new CException('Patient has no practice address');
        }

        if ($gp = $patient->gp) {
            $to_name = $gp->contact->fullname;
        } else {
            $to_name = Gp::UNKNOWN_NAME;
        }

        $to_address = $to_name."\n".$to_address;
        $consultantName = $operation->event->episode->firm->consultant ? $operation->event->episode->firm->consultant->fullName : null;

        $html = $this->render('../letters/gp_letter', array(
                'to' => $to_name,
                'patient' => $patient,
                'consultantName' => $consultantName,
                'toAddress' => $to_address,
                'site' => $operation->site,
        ), true);

        return $html.$this->render('../letters/gp_letter_patient', array(
                'to' => $patient->salutationname,
                'patient' => $patient,
                'consultantName' => $consultantName,
                'toAddress' => $patient->getLetterAddress(array(
                    'include_name' => true,
                    'delimiter' => "\n",
                )),
                'site' => $operation->site,
        ), true);
    }

    /**
     * Set operations printed letter state.
     */
    public function actionConfirmPrinted()
    {
        Audit::add('waiting list', 'confirm');

        foreach ($_POST['operations'] as $operation_id) {
            if ($operation = Element_OphTrOperationbooking_Operation::Model()->findByPk($operation_id)) {
                if (Yii::app()->user->checkAccess('OprnConfirmBookingLetterPrinted') && (isset($_POST['adminconfirmto'])) && ($_POST['adminconfirmto'] != 'OFF') && ($_POST['adminconfirmto'] != '')) {
                    $operation->confirmLetterPrinted($_POST['adminconfirmto'], $_POST['adminconfirmdate']);
                } else {
                    $operation->confirmLetterPrinted();
                }
            }
        }
    }

    /**
     * @param $event_id
     *
     * Marks an Operation Booking "booked"
     */

    public function actionSetBooked($event_id)
    {
        $success = true;

        if (!$element = Element_OphTrOperationbooking_Operation::model()->find("event_id = :event_id", array(":event_id" => $event_id))) {
            $this->renderJSON(array('success'=>false, 'This event could not be found.'));
            exit;
        }

        $transaction = \Yii::app()->db->beginTransaction();

        try {
            $element->status_id = 2; //@TODO: change hardcoded id to a query
            $element->save();
            $message = '';

            $event = Event::model()->find("id = :event_id", array(":event_id"=>$event_id));
            $event->deleteIssue("Operation requires scheduling");

            $listed_episode_status_id = Yii::app()->db->createCommand()
                ->select('id')
                ->from('episode_status')->where('name=:name', array(':name' => 'Listed/booked'))
                ->queryScalar();

            $event->episode->episode_status_id = $listed_episode_status_id;

            $event->episode->save();

            $transaction->commit();
        } catch (\Exception $e) {
            $message = $e->getMessage();

            \OELog::log($message);
            $transaction->rollback();
            $success = false;
        }

        $this->renderJSON(array('success' => $success, 'message' => $message));
        exit;
    }
}
