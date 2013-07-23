<?php

class Intravitreal

{

//Anaesthetic Right
public $rightAnaestheticTopical = "//input[@id='Element_OphTrIntravitrealinjection_Anaesthetic_right_anaesthetictype_id_1']";
public $rightAnaestheticLA = "//input[@id='Element_OphTrIntravitrealinjection_Anaesthetic_right_anaesthetictype_id_3']";

public $rightDeliveryRetrobulbar = "//input[@id='Element_OphTrIntravitrealinjection_Anaesthetic_right_anaestheticdelivery_id_1']";
public $rightDeliveryPeribulbar = "//input[@id='Element_OphTrIntravitrealinjection_Anaesthetic_right_anaestheticdelivery_id_2']";
public $rightDeliverySubtenons = "//input[@id='Element_OphTrIntravitrealinjection_Anaesthetic_right_anaestheticdelivery_id_3']";
public $rightDeliverySubconjunctival = "//input[@id='Element_OphTrIntravitrealinjection_Anaesthetic_right_anaestheticdelivery_id_4']";
public $rightDeliveryTopical = "//input[@id='Element_OphTrIntravitrealinjection_Anaesthetic_right_anaestheticdelivery_id_5']";
public $rightDeliveryTopicalIntracameral = "//input[@id='Element_OphTrIntravitrealinjection_Anaesthetic_right_anaestheticdelivery_id_6']";
public $rightDeliveryOther = "//input[@id='Element_OphTrIntravitrealinjection_Anaesthetic_right_anaestheticdelivery_id_7']";
public $rightAnaestheticAgent = "//*[@id='Element_OphTrIntravitrealinjection_Anaesthetic_right_anaestheticagent_id']";

//Anaesthetic Left
public $leftAnaestheticTopical = "//*[@id='Element_OphTrIntravitrealinjection_Anaesthetic_left_anaesthetictype_id_1']";
public $leftAnaestheticLA = "//*[@id='Element_OphTrIntravitrealinjection_Anaesthetic_left_anaesthetictype_id_3']";

public $leftDeliveryRetrobulbar = "//*[@id='Element_OphTrIntravitrealinjection_Anaesthetic_left_anaestheticdelivery_id_1']";
public $leftDeliveryPeribulbar = "//*[@id='Element_OphTrIntravitrealinjection_Anaesthetic_left_anaestheticdelivery_id_2']";
public $leftDeliverySubtenons = "//*[@id='Element_OphTrIntravitrealinjection_Anaesthetic_left_anaestheticdelivery_id_3']";
public $leftDeliverySubconjunctival = "//*[@id='Element_OphTrIntravitrealinjection_Anaesthetic_left_anaestheticdelivery_id_4']";
public $leftDeliveryTopical = "//*[@id='Element_OphTrIntravitrealinjection_Anaesthetic_left_anaestheticdelivery_id_5']";
public $leftDeliveryTopicalIntracameral = "//*[@id='Element_OphTrIntravitrealinjection_Anaesthetic_left_anaestheticdelivery_id_6']";
public $leftDeliveryOther = "//*[@id='Element_OphTrIntravitrealinjection_Anaesthetic_left_anaestheticdelivery_id_7']";
public $leftAnaestheticAgent = "//*[@id='Element_OphTrIntravitrealinjection_Anaesthetic_left_anaestheticagent_id']";

//Right Treatment
public $rightPreInjectionAntiseptic = "//*[@id='Element_OphTrIntravitrealinjection_Treatment_right_pre_antisept_drug_id']";
public $rightPreInjectionSkinCleanser = "//*[@id='Element_OphTrIntravitrealinjection_Treatment_right_pre_skin_drug_id']";
public $rightPreInjectionIOPTickbox = "//*[@id='Element_OphTrIntravitrealinjection_Treatment_right_pre_ioplowering_required']";
public $rightPerInjectionIOPDrops = "//*[@id='Element_OphTrIntravitrealinjection_Treatment_right_pre_ioplowering_id']";
public $rightDrug = "//*[@id='Element_OphTrIntravitrealinjection_Treatment_right_drug_id']";
public $rightNumberOfInjections = "//*[@id='Element_OphTrIntravitrealinjection_Treatment_right_number']";
public $rightBatchNumber = "//*[@id='Element_OphTrIntravitrealinjection_Treatment_right_batch_number']";
public $rightBatchExpiryDate = "//*[@id='Element_OphTrIntravitrealinjection_Treatment_right_batch_expiry_date_0']";
public $rightInjectionGivenBy = "//*[@id='Element_OphTrIntravitrealinjection_Treatment_right_injection_given_by_id']";
public $rightInjectionTime = "//*[@id='Element_OphTrIntravitrealinjection_Treatment_right_injection_time']";
public $rightPostInjectionIOPTickbox = "//*[@id='Element_OphTrIntravitrealinjection_Treatment_right_post_ioplowering_required']";
public $rightPostInjectionIOPDrops = "//*[@id='Element_OphTrIntravitrealinjection_Treatment_right_post_ioplowering_id']";

//Left Treatment
public $leftPreInjectionAntiseptic = "//*[@id='Element_OphTrIntravitrealinjection_Treatment_left_pre_antisept_drug_id']";
public $leftPreInjectionSkinCleanser = "//*[@id='Element_OphTrIntravitrealinjection_Treatment_left_pre_skin_drug_id']";
public $leftPreInjectionIOPTickbox = "//*[@id='Element_OphTrIntravitrealinjection_Treatment_left_pre_ioplowering_required']";
public $leftPerInjectionIOPDrops = "//*[@id='Element_OphTrIntravitrealinjection_Treatment_left_pre_ioplowering_id']";
public $leftDrug = "//*[@id='Element_OphTrIntravitrealinjection_Treatment_left_drug_id']";
public $leftNumberOfInjections = "//*[@id='Element_OphTrIntravitrealinjection_Treatment_left_number']";
public $leftBatchNumber = "//*[@id='Element_OphTrIntravitrealinjection_Treatment_left_batch_number']";
public $leftBatchExpiryDate = "//*[@id='Element_OphTrIntravitrealinjection_Treatment_left_batch_expiry_date_0']";
public $leftInjectionGivenBy = "//*[@id='Element_OphTrIntravitrealinjection_Treatment_left_injection_given_by_id']";
public $leftInjectionTime = "//*[@id='Element_OphTrIntravitrealinjection_Treatment_left_injection_time']";
public $leftPostInjectionIOPTickbox = "//*[@id='Element_OphTrIntravitrealinjection_Treatment_left_post_ioplowering_required']";
public $leftPostInjectionIOPDrops = "//*[@id='Element_OphTrIntravitrealinjection_Treatment_left_post_ioplowering_id']";

//Anterior Segment
public $rightLensStatus = "//*[@id='Element_OphTrIntravitrealinjection_AnteriorSegment_right_lens_status_id']";
public $leftLensStatus = "//*[@id='Element_OphTrIntravitrealinjection_AnteriorSegment_left_lens_status_id']";

//Right Post Injection Examination
public $rightCountingFingersYes = "//*[@id='Element_OphTrIntravitrealinjection_PostInjectionExamination_right_finger_count_1']";
public $rightCountingFingersNo = "//*[@id='Element_OphTrIntravitrealinjection_PostInjectionExamination_right_finger_count_0']";
public $rightIOPCheckYes = "//*[@id='Element_OphTrIntravitrealinjection_PostInjectionExamination_right_iop_check_1']";
public $rightIOPCheckNo = "//*[@id='Element_OphTrIntravitrealinjection_PostInjectionExamination_right_iop_check_0']";
public $rightPostInjectionDrops = "//*[@id='Element_OphTrIntravitrealinjection_PostInjectionExamination_right_drops_id']";

//Left Post Injection Examination
public $leftCountingFingersYes = "//*[@id='Element_OphTrIntravitrealinjection_PostInjectionExamination_left_finger_count_1']";
public $leftCountingFingersNo = "//*[@id='Element_OphTrIntravitrealinjection_PostInjectionExamination_left_finger_count_0']";
public $leftIOPCheckYes = "//*[@id='Element_OphTrIntravitrealinjection_PostInjectionExamination_left_iop_check_1']";
public $leftIOPCheckNo = "//*[@id='Element_OphTrIntravitrealinjection_PostInjectionExamination_left_iop_check_0']";
public $leftPostInjectionDrops = "//*[@id='Element_OphTrIntravitrealinjection_PostInjectionExamination_left_drops_id']";

//Complications
public $rightComplicationsDropdown = "//*[@id='Element_OphTrIntravitrealinjection_Complications_right_complications']/div[2]/select";
public $leftComplicationsDropdown = "//*[@id='Element_OphTrIntravitrealinjection_Complications_left_complications']/div[2]/select";

//Use $saveExamination to Save Intravitreal injection
}
