<?php

use SensioLabs\Behat\PageObjectExtension\PageObject\Page;

class Intravitreal extends Page
{
    protected $elements = array (
        //Anaesthetic Right
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
        'rightPerInjectionIOPDrops' => array('xpath' => "//*[@id='Element_OphTrIntravitrealinjection_Treatment_right_pre_ioplowering_id']"),
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
        'leftPreInjectionIOPTickbox' => array('xpath' => "//*[@id='Element_OphTrIntravitrealinjection_Treatment_left_pre_ioplowering_required']"),
        'leftPerInjectionIOPDrops' => array('xpath' => "//*[@id='Element_OphTrIntravitrealinjection_Treatment_left_pre_ioplowering_id']"),
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
        'rightComplicationsDropdown' => array('xpath' => "//*[@id='Element_OphTrIntravitrealinjection_Complications_right_complications']/div[2]/select"),
        'leftComplicationsDropdown' => array('xpath' => "//*[@id='Element_OphTrIntravitrealinjection_Complications_left_complications']/div[2]/select"),
    );

    //Use $saveExamination to Save Intravitreal injection
}
