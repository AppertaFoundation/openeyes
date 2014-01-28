<?php

class Intravitreal extends OpenEyesPage
{
    protected $path ="/site/OphTrLaser/Default/create?patient_id={patientId}";

    protected $elements = array (
        //Anaesthetic Right

        'addRightSide' => array('xpath' => "//*[@id='clinical-create']//*[@class='side left eventDetail inactive']//*[contains(text(),'Add right side')]"),
        'removeRightSide' => array('xpath' => "//*[@id='clinical-create']//*[@class='side left eventDetail']//*[@class='removeSide']"),
        'rightAnaestheticTopical' => array('xpath' => "//input[@id='Element_OphTrIntravitrealinjection_Anaesthetic_right_anaesthetictype_id_1']"),
        'rightAnaestheticLA' => array('xpath' => "//input[@id='Element_OphTrIntravitrealinjection_Anaesthetic_right_anaesthetictype_id_3']"),

        'rightDeliveryRetrobulbar' => array('xpath' => "//input[@id='Element_OphTrIntravitrealinjection_Anaesthetic_right_anaestheticdelivery_id_1']"),
        'rightDeliveryPeribulbar' => array('xpath' => "//input[@id='Element_OphTrIntravitrealinjection_Anaesthetic_right_anaestheticdelivery_id_2']"),
        'rightDeliverySubtenons' => array('xpath' => "//input[@id='Element_OphTrIntravitrealinjection_Anaesthetic_right_anaestheticdelivery_id_3']"),
        'rightDeliverySubconjunctival' => array('xpath' => "//input[@id='Element_OphTrIntravitrealinjection_Anaesthetic_right_anaestheticdelivery_id_4']"),
        'rightDeliveryTopical' => array('xpath' => "//input[@id='Element_OphTrIntravitrealinjection_Anaesthetic_right_anaestheticdelivery_id_5']"),
        'rightDeliveryTopicalIntracameral' => array('xpath' => "//input[@id='Element_OphTrIntravitrealinjection_Anaesthetic_right_anaestheticdelivery_id_6']"),
        'rightDeliveryOther' => array('xpath' => "//input[@id='Element_OphTrIntravitrealinjection_Anaesthetic_right_anaestheticdelivery_id_7']"),
        'rightAnaestheticAgent' => array('xpath' => "//*[@id='Element_OphTrIntravitrealinjection_Anaesthetic_right_anaestheticagent_id']"),

        //Anaesthetic Left
        'leftAnaestheticTopical' => array('xpath' => "//*[@id='Element_OphTrIntravitrealinjection_Anaesthetic_left_anaesthetictype_id_1']"),
        'leftAnaestheticLA' => array('xpath' => "//*[@id='Element_OphTrIntravitrealinjection_Anaesthetic_left_anaesthetictype_id_3']"),
        'leftDeliveryRetrobulbar' => array('xpath' => "//*[@id='Element_OphTrIntravitrealinjection_Anaesthetic_left_anaestheticdelivery_id_1']"),
        'leftDeliveryPeribulbar' => array('xpath' => "//*[@id='Element_OphTrIntravitrealinjection_Anaesthetic_left_anaestheticdelivery_id_2']"),
        'leftDeliverySubtenons' => array('xpath' => "//*[@id='Element_OphTrIntravitrealinjection_Anaesthetic_left_anaestheticdelivery_id_3']"),
        'leftDeliverySubconjunctival' => array('xpath' => "//*[@id='Element_OphTrIntravitrealinjection_Anaesthetic_left_anaestheticdelivery_id_4']"),
        'leftDeliveryTopical' => array('xpath' => "//*[@id='Element_OphTrIntravitrealinjection_Anaesthetic_left_anaestheticdelivery_id_5']"),
        'leftDeliveryTopicalIntracameral' => array('xpath' => "//*[@id='Element_OphTrIntravitrealinjection_Anaesthetic_left_anaestheticdelivery_id_6']"),
        'leftDeliveryOther' => array('xpath' => "//*[@id='Element_OphTrIntravitrealinjection_Anaesthetic_left_anaestheticdelivery_id_7']"),
        'leftAnaestheticAgent' => array('xpath' => "//*[@id='Element_OphTrIntravitrealinjection_Anaesthetic_left_anaestheticagent_id']"),

        //Right Treatment
        'rightPreInjectionAntiseptic' => array('xpath' => "//*[@id='Element_OphTrIntravitrealinjection_Treatment_right_pre_antisept_drug_id']"),
        'rightPreInjectionSkinCleanser' => array('xpath' => "//*[@id='Element_OphTrIntravitrealinjection_Treatment_right_pre_skin_drug_id']"),
        'rightPreInjectionIOPTickbox' => array('xpath' => "//*[@id='Element_OphTrIntravitrealinjection_Treatment_right_pre_ioplowering_required']"),
        'rightPerInjectionIOPDrops' => array('xpath' => "//select[@id='Element_OphTrIntravitrealinjection_Treatment[right_pre_ioploweringdrugs]']"),
        'rightDrug' => array('xpath' => "//*[@id='Element_OphTrIntravitrealinjection_Treatment_right_drug_id']"),
        'rightNumberOfInjections' => array('xpath' => "//*[@id='Element_OphTrIntravitrealinjection_Treatment_right_number']"),
        'rightBatchNumber' => array('xpath' => "//*[@id='Element_OphTrIntravitrealinjection_Treatment_right_batch_number']"),
        'rightBatchExpiryDate' => array('xpath' => "//*[@id='Element_OphTrIntravitrealinjection_Treatment_right_batch_expiry_date_0']"),
        'rightInjectionGivenBy' => array('xpath' => "//*[@id='Element_OphTrIntravitrealinjection_Treatment_right_injection_given_by_id']"),
        'rightInjectionTime' => array('xpath' => "//*[@id='Element_OphTrIntravitrealinjection_Treatment_right_injection_time']"),
        'rightPostInjectionIOPTickbox' => array('xpath' => "//*[@id='Element_OphTrIntravitrealinjection_Treatment_right_post_ioplowering_required']"),
        'rightPostInjectionIOPDrops' => array('xpath' => "//*[@id='Element_OphTrIntravitrealinjection_Treatment_right_post_ioplowering_id']"),

        //Left Treatment
        'leftPreInjectionAntiseptic' => array('xpath' => "//*[@id='Element_OphTrIntravitrealinjection_Treatment_left_pre_antisept_drug_id']"),
        'leftPreInjectionSkinCleanser' => array('xpath' => "//*[@id='Element_OphTrIntravitrealinjection_Treatment_left_pre_skin_drug_id']"),
        'leftPreInjectionIOPTickbox' => array('xpath' => "//div[@id='div_Element_OphTrIntravitrealinjection_Treatment_left_pre_ioplowering_required']/div[2]/input[2]"),
        'leftPerInjectionIOPDrops' => array('xpath' => "//select[@id='Element_OphTrIntravitrealinjection_Treatment[left_pre_ioploweringdrugs]']"),
        'leftDrug' => array('xpath' => "//*[@id='Element_OphTrIntravitrealinjection_Treatment_left_drug_id']"),
        'leftNumberOfInjections' => array('xpath' => "//*[@id='Element_OphTrIntravitrealinjection_Treatment_left_number']"),
        'leftBatchNumber' => array('xpath' => "//*[@id='Element_OphTrIntravitrealinjection_Treatment_left_batch_number']"),
        'leftBatchExpiryDate' => array('xpath' => "//*[@id='Element_OphTrIntravitrealinjection_Treatment_left_batch_expiry_date_0']"),
        'leftInjectionGivenBy' => array('xpath' => "//*[@id='Element_OphTrIntravitrealinjection_Treatment_left_injection_given_by_id']"),
        'leftInjectionTime' => array('xpath' => "//*[@id='Element_OphTrIntravitrealinjection_Treatment_left_injection_time']"),
        'leftPostInjectionIOPTickbox' => array('xpath' => "//*[@id='Element_OphTrIntravitrealinjection_Treatment_left_post_ioplowering_required']"),
        'leftPostInjectionIOPDrops' => array('xpath' => "//*[@id='Element_OphTrIntravitrealinjection_Treatment_left_post_ioplowering_id']"),

        //Anterior Segment
        'rightLensStatus' => array('xpath' => "//*[@id='Element_OphTrIntravitrealinjection_AnteriorSegment_right_lens_status_id']"),
        'leftLensStatus' => array('xpath' => "//*[@id='Element_OphTrIntravitrealinjection_AnteriorSegment_left_lens_status_id']"),

        //Right Post Injection Examination
        'rightCountingFingersYes' => array('xpath' => "//*[@id='Element_OphTrIntravitrealinjection_PostInjectionExamination_right_finger_count_1']"),
        'rightCountingFingersNo' => array('xpath' => "//*[@id='Element_OphTrIntravitrealinjection_PostInjectionExamination_right_finger_count_0']"),
        'rightIOPCheckYes' => array('xpath' => "//*[@id='Element_OphTrIntravitrealinjection_PostInjectionExamination_right_iop_check_1']"),
        'rightIOPCheckNo' => array('xpath' => "//*[@id='Element_OphTrIntravitrealinjection_PostInjectionExamination_right_iop_check_0']"),
        'rightPostInjectionDrops' => array('xpath' => "//*[@id='Element_OphTrIntravitrealinjection_PostInjectionExamination_right_drops_id']"),

        //Left Post Injection Examination
        'leftCountingFingersYes' => array('xpath' => "//*[@id='Element_OphTrIntravitrealinjection_PostInjectionExamination_left_finger_count_1']"),
        'leftCountingFingersNo' => array('xpath' => "//*[@id='Element_OphTrIntravitrealinjection_PostInjectionExamination_left_finger_count_0']"),
        'leftIOPCheckYes' => array('xpath' => "//*[@id='Element_OphTrIntravitrealinjection_PostInjectionExamination_left_iop_check_1']"),
        'leftIOPCheckNo' => array('xpath' => "//*[@id='Element_OphTrIntravitrealinjection_PostInjectionExamination_left_iop_check_0']"),
        'leftPostInjectionDrops' => array('xpath' => "//*[@id='Element_OphTrIntravitrealinjection_PostInjectionExamination_left_drops_id']"),

        //Complications
        'rightComplicationsDropdown' => array('xpath' => "//select[@id='Element_OphTrIntravitrealinjection_Complications[right_complications]']"),
        'leftComplicationsDropdown' => array('xpath' => "//select[@id='Element_OphTrIntravitrealinjection_Complications[left_complications]']"),
        'saveIntravitrealInjection' => array('xpath' => "//*[@id='et_save']"),

        'existingAllergyCheck' => array ('xpath' => "//*[contains(text(),'Patient is allergic to: Tetracycline')]"),
    );

        protected function isRightSideOpen()
        {
        return (bool) $this->find('xpath', $this->getElement('addRightSide')->getXpath());
        }

         public function addRightSide ()
         {
             if ($this->isRightSideOpen())
             {
             $this->getElement('addRightSide')->click();
             }
         }

        protected function doesAllergyWarningExist()
        {
            return (bool) $this->find('xpath', $this->getElement('existingAllergyCheck')->getXpath());
        }

        public function confirmAllergyWarning ($allergy)
        {
            if ($this->doesAllergyWarningExist()){
                print "Patient is allergic to: Tetracycline";
            }
            elseif (print "NO Tetracycline or other Allergy warning!!!");
        }

         public function rightTypeTopical ()
         {
             $this->getElement('rightAnaestheticTopical')->click();
         }

         public function rightTypeLA ()
         {
             $this->getElement('rightAnaestheticLA')->click();
         }

         public function rightDeliveryRetrobulbar ()
         {
             $this->getElement('rightDeliveryRetrobulbar')->click();
         }

         public function rightDeliveryPeribulbar ()
         {
         $this->getElement('rightDeliveryPeribulbar')->click();
         }

         public function rightDeliverySubtenons ()
         {
         $this->getElement('rightDeliverySubtenons')->click();
         }

         public function rightDeliverySubconjunctival ()
         {
         $this->getElement('rightDeliverySubconjunctival')->click();
         }

         public function rightDeliveryTopical ()
         {
         $this->getElement('rightDeliveryTopical')->click();
         }

         public function rightDeliveryTopicalIntracameral ()
         {
         $this->getElement('rightDeliveryTopicalIntracameral')->click();
         }

         public function rightDeliveryOther ()
         {
         $this->getElement('rightDeliveryOther')->click();
         }

         public function rightAnaestheticAgent ($agent)
         {
         $this->getElement('rightAnaestheticAgent')->setValue($agent);
         }

        //Left
        public function leftTypeTopical ()
        {
            $this->getElement('leftAnaestheticTopical')->click();
        }

        public function leftTypeLA ()
        {
            $this->getElement('leftAnaestheticLA')->click();
        }

        public function leftDeliveryRetrobulbar ()
        {
            $this->getElement('leftDeliveryRetrobulbar')->click();
        }

        public function leftDeliveryPeribulbar ()
        {
            $this->getElement('leftDeliveryPeribulbar')->click();
        }

        public function leftDeliverySubtenons ()
        {
            $this->getElement('leftDeliverySubtenons')->click();
        }

        public function leftDeliverySubconjunctival ()
        {
            $this->getElement('leftDeliverySubconjunctival')->click();
        }

        public function leftDeliveryTopical ()
        {
            $this->getElement('leftDeliveryTopical')->click();
        }

        public function leftDeliveryTopicalIntracameral ()
        {
            $this->getElement('leftDeliveryTopicalIntracameral')->click();
        }

        public function leftDeliveryOther ()
        {
            $this->getElement('leftDeliveryOther')->click();
        }

        public function leftAnaestheticAgent ($agent)
        {
            $this->getElement('leftAnaestheticAgent')->setValue($agent);
        }

        public function rightPreInjectionAntiseptic ($antiseptic)
        {
            $this->getElement('rightPreInjectionAntiseptic')->selectOption($antiseptic);
        }

        public function rightPreInjectionSkinCleanser ($skin)
        {
            $this->getElement('rightPreInjectionSkinCleanser')->selectOption($skin);
        }

        public function rightPreInjectionIOPDropsCheckbox ()
        {
            $this->getElement('rightPreInjectionIOPTickbox')->check();
            $this->getSession()->wait(5000);
        }

        public function rightPreInjectionIOPDropsLoweringDrops ($drops)
        {
            $this->getElement('rightPerInjectionIOPDrops')->selectOption($drops);
        }

        public function rightDrug ($drug)
        {
            $this->getElement('rightDrug')->selectOption($drug);
        }

        public function rightInjections ($injections)
        {
            $this->getElement('rightNumberOfInjections')->setValue($injections);
        }

        public function rightBatchNumber ($batch)
        {
            $this->getElement('rightBatchNumber')->setValue($batch);
        }

        public function rightInjectionGivenBy ($injection)
        {
            $this->getElement('rightInjectionGivenBy')->selectOption($injection);
        }

        public function rightInjectionTime ($time)
        {
            $this->getElement('rightInjectionTime')->setValue($time);
        }

        public function rightLensStatus ($lens)
        {
            $this->getElement('rightLensStatus')->selectOption($lens);
        }

        public function leftPreInjectionAntiseptic ($antiseptic)
        {
            $this->getElement('leftPreInjectionAntiseptic')->selectOption($antiseptic);
        }
    
        public function leftPreInjectionSkinCleanser ($skin)
        {
            $this->getElement('leftPreInjectionSkinCleanser')->selectOption($skin);
        }
    
        public function leftPreInjectionIOPDropsCheckbox ()
        {
            $this->getElement('leftPreInjectionIOPTickbox')->check();
            $this->getSession()->wait(5000);
        }
    
        public function leftPreInjectionIOPDropsLoweringDrops ($drops)
        {
            $this->getElement('leftPerInjectionIOPDrops')->selectOption($drops);
        }
    
        public function leftDrug ($drug)
        {
            $this->getElement('leftDrug')->selectOption($drug);
        }
    
        public function leftInjections ($injections)
        {
            $this->getElement('leftNumberOfInjections')->setValue($injections);
        }
    
        public function leftBatchNumber ($batch)
        {
            $this->getElement('leftBatchNumber')->setValue($batch);
        }
    
        public function leftInjectionGivenBy ($injection)
        {
            $this->getElement('leftInjectionGivenBy')->selectOption($injection);
        }
    
        public function leftInjectionTime ($time)
        {
            $this->getElement('leftInjectionTime')->setValue($time);
        }

        public function leftLensStatus ($lens)
        {
            $this->getElement('leftLensStatus')->selectOption($lens);
        }

        public function rightCountingFingersYes ()
        {
            $this->getElement('rightCountingFingersYes')->check();
        }

        public function rightCountingFingersNo ()
        {
            $this->getElement('rightCountingFingersNo')->check();
        }

        public function rightIOPNeedsToBeCheckedYes ()
        {
            $this->getElement('rightIOPCheckYes')->check();
        }

        public function rightIOPNeedsToBeCheckedNo ()
        {
            $this->getElement('rightIOPCheckNo')->check();
        }

        public function rightPostInjectionDrops ($drops)
        {
            $this->getElement('rightPostInjectionDrops')->selectOption($drops);
        }

        public function leftCountingFingersYes ()
        {
            $this->getElement('leftCountingFingersYes')->click();
        }
    
        public function leftCountingFingersNo ()
        {
            $this->getElement('leftCountingFingersNo')->check();
        }
    
        public function leftIOPNeedsToBeCheckedYes ()
        {
            $this->getElement('leftIOPCheckYes')->check();
        }
    
        public function leftIOPNeedsToBeCheckedNo ()
        {
            $this->getElement('leftIOPCheckNo')->check();
        }
    
        public function leftPostInjectionDrops ($drops)
        {
            $this->getElement('leftPostInjectionDrops')->selectOption($drops);
        }

        public function rightComplications ($complication)
        {
            $this->getElement('rightComplicationsDropdown')->selectOption($complication);
        }

        public function leftComplications ($complication)
        {
            $this->getElement('leftComplicationsDropdown')->selectOption($complication);
        }

        public function saveIntravitrealInjection ()
        {
            $this->getElement('saveIntravitrealInjection')->click();
            $this->getSession()->wait(10000);
        }
}
