<?php

class m150511_161109_examinationAttributesData extends CDbMigration
{
    /*
     * Current data sets from the live system, as described:
     * "For the set of data to use, extract the contents of the following tables from the MEH database"
     */

    private $ophciexaminationAttributeData = array(
        1 => array('name' => 'history', 'label' => 'History'),
        2 => array('name' => 'severity', 'label' => 'Severity'),
        3 => array('name' => 'onset', 'label' => 'Onset'),
        4 => array('name' => 'eye', 'label' => 'Eye'),
        5 => array('name' => 'duration', 'label' => 'Duration'),
        7 => array('name' => 'investigation', 'label' => 'Add'),
        8 => array('name' => 'conclusion', 'label' => 'Add'),
        9 => array('name' => 'adnexal_comorbidity', 'label' => 'Add'),
        10 => array('name' => 'management', 'label' => 'Add'),
    );

    private $ophciexaminationAttributeElementData = array(
        1 => array('attribute_id' => 1, 'element_type_id' => 311),
        2 => array('attribute_id' => 2, 'element_type_id' => 311),
        3 => array('attribute_id' => 3, 'element_type_id' => 311),
        4 => array('attribute_id' => 4, 'element_type_id' => 311),
        5 => array('attribute_id' => 5, 'element_type_id' => 311),
        6 => array('attribute_id' => 7, 'element_type_id' => 319),
        7 => array('attribute_id' => 8, 'element_type_id' => 320),
        8 => array('attribute_id' => 9, 'element_type_id' => 314),
        9 => array('attribute_id' => 10, 'element_type_id' => 321),
        10 => array('attribute_id' => 10, 'element_type_id' => 357),
    );

    private $elementTypeData = array(
        311 => array('name' => 'History', 'class_name' => 'OEModule\\OphCiExamination\\models\\Element_OphCiExamination_History', 'event_type_id' => 27, 'display_order' => 10, 'default' => 1, 'parent_element_type_id' => null, 'required' => 0),
        314 => array('name' => 'Adnexal Comorbidity', 'class_name' => 'OEModule\\OphCiExamination\\models\\Element_OphCiExamination_AdnexalComorbidity', 'event_type_id' => 27, 'display_order' => 40, 'default' => 1, 'parent_element_type_id' => null, 'required' => 0),
        319 => array('name' => 'Investigation', 'class_name' => 'OEModule\\OphCiExamination\\models\\Element_OphCiExamination_Investigation', 'event_type_id' => 27, 'display_order' => 90, 'default' => 1, 'parent_element_type_id' => null, 'required' => 0),
        320 => array('name' => 'Conclusion', 'class_name' => 'OEModule\\OphCiExamination\\models\\Element_OphCiExamination_Conclusion', 'event_type_id' => 27, 'display_order' => 100, 'default' => 1, 'parent_element_type_id' => null, 'required' => 0),
        321 => array('name' => 'Cataract Surgical Management', 'class_name' => 'OEModule\\OphCiExamination\\models\\Element_OphCiExamination_CataractSurgicalManagement', 'event_type_id' => 27, 'display_order' => 10, 'default' => 1, 'parent_element_type_id' => 357, 'required' => 0),
        357 => array('name' => 'Clinical Management', 'class_name' => 'OEModule\\OphCiExamination\\models\\Element_OphCiExamination_Management', 'event_type_id' => 27, 'display_order' => 95, 'default' => 1, 'parent_element_type_id' => null, 'required' => 0),
    );

    private $subspecialtyData = array(
        1 => array('name' => 'Accident & Emergency'),
        2 => array('name' => 'Adnexal'),
        3 => array('name' => 'Anaesthetics'),
        4 => array('name' => 'Cataract'),
        5 => array('name' => 'Cornea'),
        6 => array('name' => 'External'),
        7 => array('name' => 'Glaucoma'),
        8 => array('name' => 'Medical Retinal'),
        9 => array('name' => 'Neuro-ophthalmology'),
        10 => array('name' => 'Oncology'),
        11 => array('name' => 'Paediatrics'),
        12 => array('name' => 'General Ophthalmology'),
        13 => array('name' => 'Refractive'),
        14 => array('name' => 'Strabismus'),
        15 => array('name' => 'Uveitis'),
        16 => array('name' => 'Vitreoretinal'),
    );

    private $ophciexaminationAttributeOptionData = array(
        1 => array('value' => 'amblyopia', 'delimiter' => ',', 'subspecialty_id' => '4', 'attribute_element_id' => '1', 'display_order' => '0'),
        2 => array('value' => 'pseudophakia', 'delimiter' => ',', 'subspecialty_id' => 'NULL', 'attribute_element_id' => '1', 'display_order' => '0'),
        3 => array('value' => 'blurred vision', 'delimiter' => ',', 'subspecialty_id' => 'NULL', 'attribute_element_id' => '1', 'display_order' => '0'),
        4 => array('value' => 'difficulty with night driving', 'delimiter' => ',', 'subspecialty_id' => '4', 'attribute_element_id' => '1', 'display_order' => '0'),
        5 => array('value' => 'difficulty with reading', 'delimiter' => ',', 'subspecialty_id' => 'NULL', 'attribute_element_id' => '1', 'display_order' => '0'),
        6 => array('value' => 'difficulty with road signs', 'delimiter' => ',', 'subspecialty_id' => '4', 'attribute_element_id' => '1', 'display_order' => '0'),
        7 => array('value' => 'first post-op cataract follow up', 'delimiter' => ',', 'subspecialty_id' => '4', 'attribute_element_id' => '1', 'display_order' => '0'),
        8 => array('value' => 'follow up visit', 'delimiter' => ',', 'subspecialty_id' => 'NULL', 'attribute_element_id' => '1', 'display_order' => '0'),
        9 => array('value' => 'glare', 'delimiter' => ',', 'subspecialty_id' => 'NULL', 'attribute_element_id' => '1', 'display_order' => '0'),
        10 => array('value' => 'glare with headlights', 'delimiter' => ',', 'subspecialty_id' => '4', 'attribute_element_id' => '1', 'display_order' => '0'),
        11 => array('value' => 'gradual deterioration of vision', 'delimiter' => ',', 'subspecialty_id' => 'NULL', 'attribute_element_id' => '1', 'display_order' => '0'),
        12 => array('value' => 'history of Strabismus', 'delimiter' => ',', 'subspecialty_id' => '4', 'attribute_element_id' => '1', 'display_order' => '0'),
        13 => array('value' => 'new referral to cataract service', 'delimiter' => ',', 'subspecialty_id' => '4', 'attribute_element_id' => '1', 'display_order' => '0'),
        14 => array('value' => 'trauma', 'delimiter' => ',', 'subspecialty_id' => '4', 'attribute_element_id' => '1', 'display_order' => '0'),
        15 => array('value' => 'mild', 'delimiter' => ',', 'subspecialty_id' => 'NULL', 'attribute_element_id' => '2', 'display_order' => '0'),
        16 => array('value' => 'moderate', 'delimiter' => ',', 'subspecialty_id' => 'NULL', 'attribute_element_id' => '2', 'display_order' => '0'),
        17 => array('value' => 'severe', 'delimiter' => ',', 'subspecialty_id' => 'NULL', 'attribute_element_id' => '2', 'display_order' => '0'),
        18 => array('value' => 'detected by diabetic screening', 'delimiter' => ',', 'subspecialty_id' => '4', 'attribute_element_id' => '3', 'display_order' => '0'),
        19 => array('value' => 'gradual onset', 'delimiter' => ',', 'subspecialty_id' => 'NULL', 'attribute_element_id' => '3', 'display_order' => '0'),
        20 => array('value' => 'noticed by optometrist', 'delimiter' => ',', 'subspecialty_id' => 'NULL', 'attribute_element_id' => '3', 'display_order' => '0'),
        21 => array('value' => 'noticed by parent', 'delimiter' => ',', 'subspecialty_id' => 'NULL', 'attribute_element_id' => '3', 'display_order' => '0'),
        22 => array('value' => 'sudden onset', 'delimiter' => ',', 'subspecialty_id' => 'NULL', 'attribute_element_id' => '3', 'display_order' => '0'),
        23 => array('value' => 'both eyes', 'delimiter' => ',', 'subspecialty_id' => 'NULL', 'attribute_element_id' => '4', 'display_order' => '0'),
        24 => array('value' => 'left eye', 'delimiter' => ',', 'subspecialty_id' => 'NULL', 'attribute_element_id' => '4', 'display_order' => '0'),
        25 => array('value' => 'left more than right', 'delimiter' => ',', 'subspecialty_id' => 'NULL', 'attribute_element_id' => '4', 'display_order' => '0'),
        26 => array('value' => 'right eye', 'delimiter' => ',', 'subspecialty_id' => 'NULL', 'attribute_element_id' => '4', 'display_order' => '0'),
        27 => array('value' => 'right more than left', 'delimiter' => ',', 'subspecialty_id' => 'NULL', 'attribute_element_id' => '4', 'display_order' => '0'),
        28 => array('value' => '1 day', 'delimiter' => ',', 'subspecialty_id' => 'NULL', 'attribute_element_id' => '5', 'display_order' => '0'),
        29 => array('value' => '1 month', 'delimiter' => ',', 'subspecialty_id' => 'NULL', 'attribute_element_id' => '5', 'display_order' => '0'),
        30 => array('value' => '1 week', 'delimiter' => ',', 'subspecialty_id' => 'NULL', 'attribute_element_id' => '5', 'display_order' => '0'),
        31 => array('value' => '2 weeks', 'delimiter' => ',', 'subspecialty_id' => 'NULL', 'attribute_element_id' => '5', 'display_order' => '0'),
        32 => array('value' => '2-3 days', 'delimiter' => ',', 'subspecialty_id' => 'NULL', 'attribute_element_id' => '5', 'display_order' => '0'),
        33 => array('value' => '6 months', 'delimiter' => ',', 'subspecialty_id' => 'NULL', 'attribute_element_id' => '5', 'display_order' => '0'),
        34 => array('value' => 'Fluorescein angiography', 'delimiter' => ',', 'subspecialty_id' => 'NULL', 'attribute_element_id' => '6', 'display_order' => '0'),
        35 => array('value' => 'OCT', 'delimiter' => ',', 'subspecialty_id' => 'NULL', 'attribute_element_id' => '6', 'display_order' => '0'),
        36 => array('value' => 'field test', 'delimiter' => ',', 'subspecialty_id' => 'NULL', 'attribute_element_id' => '6', 'display_order' => '0'),
        37 => array('value' => 'refraction', 'delimiter' => ',', 'subspecialty_id' => 'NULL', 'attribute_element_id' => '6', 'display_order' => '0'),
        38 => array('value' => 'ultrasound', 'delimiter' => ',', 'subspecialty_id' => 'NULL', 'attribute_element_id' => '6', 'display_order' => '0'),
        39 => array('value' => 'Cataract: Corneal disease limiting postoperative outcome', 'delimiter' => ',', 'subspecialty_id' => '4', 'attribute_element_id' => '7', 'display_order' => '0'),
        40 => array('value' => 'Cataract: Early cataract, not for surgery at present', 'delimiter' => ',', 'subspecialty_id' => '4', 'attribute_element_id' => '7', 'display_order' => '0'),
        41 => array('value' => 'Cataract: Glaucoma disease limiting postoperative outcome', 'delimiter' => ',', 'subspecialty_id' => '4', 'attribute_element_id' => '7', 'display_order' => '0'),
        42 => array('value' => 'Cataract: Macular disease limiting postoperative outcome', 'delimiter' => ',', 'subspecialty_id' => '4', 'attribute_element_id' => '7', 'display_order' => '0'),
        43 => array('value' => 'Cataract: No significant cataract, surgery not required', 'delimiter' => ',', 'subspecialty_id' => '4', 'attribute_element_id' => '7', 'display_order' => '0'),
        44 => array('value' => 'Optician: Review refraction with own optician', 'delimiter' => ',', 'subspecialty_id' => 'NULL', 'attribute_element_id' => '7', 'display_order' => '0'),
        45 => array('value' => 'YAG laser capsulotomy was performed with no complications', 'delimiter' => ',', 'subspecialty_id' => '4', 'attribute_element_id' => '7', 'display_order' => '0'),
        46 => array('value' => 'booked for first eye', 'delimiter' => ',', 'subspecialty_id' => '4', 'attribute_element_id' => '7', 'display_order' => '0'),
        47 => array('value' => 'booked for second eye', 'delimiter' => ',', 'subspecialty_id' => '4', 'attribute_element_id' => '7', 'display_order' => '0'),
        48 => array('value' => 'booked for surgery', 'delimiter' => ',', 'subspecialty_id' => 'NULL', 'attribute_element_id' => '7', 'display_order' => '0'),
        49 => array('value' => 'copy of clinical details provided for Optician', 'delimiter' => ',', 'subspecialty_id' => 'NULL', 'attribute_element_id' => '7', 'display_order' => '0'),
        50 => array('value' => 'discharge', 'delimiter' => ',', 'subspecialty_id' => 'NULL', 'attribute_element_id' => '7', 'display_order' => '0'),
        51 => array('value' => 'discharge, to be reviewed as necessary via A&E or the GP', 'delimiter' => ',', 'subspecialty_id' => 'NULL', 'attribute_element_id' => '7', 'display_order' => '0'),
        52 => array('value' => 'glasses prescribed', 'delimiter' => ',', 'subspecialty_id' => 'NULL', 'attribute_element_id' => '7', 'display_order' => '0'),
        53 => array('value' => 'good outcome from surgery listed for second eye', 'delimiter' => ',', 'subspecialty_id' => '4', 'attribute_element_id' => '7', 'display_order' => '0'),
        54 => array('value' => 'offer of surgery declined by patient', 'delimiter' => ',', 'subspecialty_id' => 'NULL', 'attribute_element_id' => '7', 'display_order' => '0'),
        55 => array('value' => 'personal information leaflet provided to patient', 'delimiter' => ',', 'subspecialty_id' => 'NULL', 'attribute_element_id' => '7', 'display_order' => '0'),
        56 => array('value' => 'removal of suture is done', 'delimiter' => ',', 'subspecialty_id' => '4', 'attribute_element_id' => '7', 'display_order' => '0'),
        57 => array('value' => 'satisfactory post operative progress', 'delimiter' => ',', 'subspecialty_id' => 'NULL', 'attribute_element_id' => '7', 'display_order' => '0'),
        58 => array('value' => 'wean off topical medication', 'delimiter' => ',', 'subspecialty_id' => 'NULL', 'attribute_element_id' => '7', 'display_order' => '0'),
        59 => array('value' => 'blepharitis', 'delimiter' => ',', 'subspecialty_id' => 'NULL', 'attribute_element_id' => '8', 'display_order' => '0'),
        60 => array('value' => 'blepharospasm', 'delimiter' => ',', 'subspecialty_id' => 'NULL', 'attribute_element_id' => '8', 'display_order' => '0'),
        61 => array('value' => 'conjunctivitis', 'delimiter' => ',', 'subspecialty_id' => 'NULL', 'attribute_element_id' => '8', 'display_order' => '0'),
        62 => array('value' => 'crusting of lashes', 'delimiter' => ',', 'subspecialty_id' => 'NULL', 'attribute_element_id' => '8', 'display_order' => '0'),
        63 => array('value' => 'difficult access', 'delimiter' => ',', 'subspecialty_id' => 'NULL', 'attribute_element_id' => '8', 'display_order' => '0'),
        64 => array('value' => 'discharge', 'delimiter' => ',', 'subspecialty_id' => 'NULL', 'attribute_element_id' => '8', 'display_order' => '0'),
        65 => array('value' => 'dry eyes', 'delimiter' => ',', 'subspecialty_id' => 'NULL', 'attribute_element_id' => '8', 'display_order' => '0'),
        66 => array('value' => 'ectropion', 'delimiter' => ',', 'subspecialty_id' => 'NULL', 'attribute_element_id' => '8', 'display_order' => '0'),
        67 => array('value' => 'entropion', 'delimiter' => ',', 'subspecialty_id' => 'NULL', 'attribute_element_id' => '8', 'display_order' => '0'),
        68 => array('value' => 'injected lid margins', 'delimiter' => ',', 'subspecialty_id' => 'NULL', 'attribute_element_id' => '8', 'display_order' => '0'),
        69 => array('value' => 'lower lid ectropion', 'delimiter' => ',', 'subspecialty_id' => 'NULL', 'attribute_element_id' => '8', 'display_order' => '0'),
        70 => array('value' => 'poor tear film', 'delimiter' => ',', 'subspecialty_id' => 'NULL', 'attribute_element_id' => '8', 'display_order' => '0'),
        71 => array('value' => 'punctal ectropian', 'delimiter' => ',', 'subspecialty_id' => 'NULL', 'attribute_element_id' => '8', 'display_order' => '0'),
        72 => array('value' => 'squint', 'delimiter' => ',', 'subspecialty_id' => 'NULL', 'attribute_element_id' => '8', 'display_order' => '0'),
        73 => array('value' => 'discharged and glasses not required', 'delimiter' => ',', 'subspecialty_id' => 'NULL', 'attribute_element_id' => '9', 'display_order' => '0'),
        74 => array('value' => 'discharged with glasses', 'delimiter' => ',', 'subspecialty_id' => 'NULL', 'attribute_element_id' => '9', 'display_order' => '0'),
        75 => array('value' => 'discharged with prescription for glasses', 'delimiter' => ',', 'subspecialty_id' => 'NULL', 'attribute_element_id' => '9', 'display_order' => '0'),
        76 => array('value' => 'good outcome from the first eye surgery and is booked for second eye', 'delimiter' => ',', 'subspecialty_id' => 'NULL', 'attribute_element_id' => '9', 'display_order' => '0'),
        77 => array('value' => 'listed for left cataract surgery under LA', 'delimiter' => ',', 'subspecialty_id' => 'NULL', 'attribute_element_id' => '9', 'display_order' => '0'),
        78 => array('value' => 'listed for right cataract surgery under LA', 'delimiter' => ',', 'subspecialty_id' => 'NULL', 'attribute_element_id' => '9', 'display_order' => '0'),
        79 => array('value' => 'new glasses prescribed', 'delimiter' => ',', 'subspecialty_id' => 'NULL', 'attribute_element_id' => '9', 'display_order' => '0'),
        80 => array('value' => 'patient declined surgery', 'delimiter' => ',', 'subspecialty_id' => 'NULL', 'attribute_element_id' => '9', 'display_order' => '0'),
        81 => array('value' => 'patient managing well and not keen for surgery', 'delimiter' => ',', 'subspecialty_id' => 'NULL', 'attribute_element_id' => '9', 'display_order' => '0'),
        82 => array('value' => 'removal of suture at next visit', 'delimiter' => ',', 'subspecialty_id' => 'NULL', 'attribute_element_id' => '9', 'display_order' => '0'),
        83 => array('value' => 'wean off the medication', 'delimiter' => ',', 'subspecialty_id' => 'NULL', 'attribute_element_id' => '9', 'display_order' => '0'),
        84 => array('value' => 'none', 'delimiter' => ',', 'subspecialty_id' => 'NULL', 'attribute_element_id' => '8', 'display_order' => '0'),
        85 => array('value' => 'asymptomatic - optometrist noted elevated IOP on routine visit', 'delimiter' => ',', 'subspecialty_id' => '7', 'attribute_element_id' => '1', 'display_order' => '0'),
        86 => array('value' => 'asymptomatic - optometrist noted visual field defect on routine visit', 'delimiter' => ',', 'subspecialty_id' => '7', 'attribute_element_id' => '1', 'display_order' => '0'),
        87 => array('value' => 'asymptomatic - optometrist noted suspicious disc appearance on routine visit', 'delimiter' => ',', 'subspecialty_id' => '7', 'attribute_element_id' => '1', 'display_order' => '0'),
        88 => array('value' => 'loss of vision', 'delimiter' => ',', 'subspecialty_id' => '7', 'attribute_element_id' => '1', 'display_order' => '0'),
        89 => array('value' => 'peripheral visual field loss', 'delimiter' => ',', 'subspecialty_id' => '7', 'attribute_element_id' => '1', 'display_order' => '0'),
        90 => array('value' => 'visual disturbance', 'delimiter' => ',', 'subspecialty_id' => '7', 'attribute_element_id' => '1', 'display_order' => '0'),
        91 => array('value' => 'ocular pain', 'delimiter' => ',', 'subspecialty_id' => '7', 'attribute_element_id' => '1', 'display_order' => '0'),
        92 => array('value' => 'corneal clouding / photophobia or globe enlargement noted by parents', 'delimiter' => ',', 'subspecialty_id' => '7', 'attribute_element_id' => '1', 'display_order' => '0'),
        93 => array('value' => 'more than 6 months', 'delimiter' => ',', 'subspecialty_id' => 'NULL', 'attribute_element_id' => '5', 'display_order' => '0'),
        94 => array('value' => 'discharge', 'delimiter' => ',', 'subspecialty_id' => 'NULL', 'attribute_element_id' => '10', 'display_order' => '0'),
        95 => array('value' => 'listed for cataract surgery', 'delimiter' => ',', 'subspecialty_id' => '7', 'attribute_element_id' => '10', 'display_order' => '0'),
        96 => array('value' => 'listed for glaucoma surgery', 'delimiter' => ',', 'subspecialty_id' => '7', 'attribute_element_id' => '10', 'display_order' => '0'),
        97 => array('value' => 'referred to optometrist for annual examination', 'delimiter' => ',', 'subspecialty_id' => 'NULL', 'attribute_element_id' => '10', 'display_order' => '0'),
        98 => array('value' => 'requires change in medication', 'delimiter' => ',', 'subspecialty_id' => 'NULL', 'attribute_element_id' => '10', 'display_order' => '0'),
        99 => array('value' => 'stable – continue with current medication', 'delimiter' => ',', 'subspecialty_id' => 'NULL', 'attribute_element_id' => '10', 'display_order' => '0'),
        100 => array('value' => 'medication changed / initiated', 'delimiter' => ',', 'subspecialty_id' => '7', 'attribute_element_id' => '10', 'display_order' => '0'),
        101 => array('value' => 'stable; continue observation', 'delimiter' => ',', 'subspecialty_id' => '7', 'attribute_element_id' => '10', 'display_order' => '0'),
        102 => array('value' => 'listed for laser treatment', 'delimiter' => ',', 'subspecialty_id' => '7', 'attribute_element_id' => '10', 'display_order' => '0'),
        103 => array('value' => 'booked for left eye', 'delimiter' => ',', 'subspecialty_id' => '4', 'attribute_element_id' => '7', 'display_order' => '0'),
        104 => array('value' => 'booked for right eye', 'delimiter' => ',', 'subspecialty_id' => '4', 'attribute_element_id' => '7', 'display_order' => '0'),
        105 => array('value' => 'electrodiagnostic tests', 'delimiter' => ',', 'subspecialty_id' => 'NULL', 'attribute_element_id' => '6', 'display_order' => '0'),
        106 => array('value' => 'Indocyanine green', 'delimiter' => ',', 'subspecialty_id' => 'NULL', 'attribute_element_id' => '6', 'display_order' => '0'),
        107 => array('value' => 'low visual aid', 'delimiter' => ',', 'subspecialty_id' => 'NULL', 'attribute_element_id' => '6', 'display_order' => '0'),
        108 => array('value' => 'Humphrey visual field test', 'delimiter' => ',', 'subspecialty_id' => 'NULL', 'attribute_element_id' => '6', 'display_order' => '0'),
        109 => array('value' => 'kinetic visual field test', 'delimiter' => ',', 'subspecialty_id' => 'NULL', 'attribute_element_id' => '6', 'display_order' => '0'),
        110 => array('value' => 'refer to other Consultant', 'delimiter' => ',', 'subspecialty_id' => 'NULL', 'attribute_element_id' => '7', 'display_order' => '0'),
        111 => array('value' => 'discharge to DRSS', 'delimiter' => ',', 'subspecialty_id' => 'NULL', 'attribute_element_id' => '7', 'display_order' => '0'),
        112 => array('value' => 'no changes', 'delimiter' => ',', 'subspecialty_id' => 'NULL', 'attribute_element_id' => '1', 'display_order' => '0'),
        113 => array('value' => 'Cataract: Corneal disease limiting postoperative outcome', 'delimiter' => ',', 'subspecialty_id' => '4', 'attribute_element_id' => '10', 'display_order' => '0'),
        114 => array('value' => 'Cataract: Early cataract, not for surgery at present', 'delimiter' => ',', 'subspecialty_id' => '4', 'attribute_element_id' => '10', 'display_order' => '0'),
        115 => array('value' => 'Cataract: Glaucoma disease limiting postoperative outcome', 'delimiter' => ',', 'subspecialty_id' => '4', 'attribute_element_id' => '10', 'display_order' => '0'),
        116 => array('value' => 'Cataract: Macular disease limiting postoperative outcome', 'delimiter' => ',', 'subspecialty_id' => '4', 'attribute_element_id' => '10', 'display_order' => '0'),
        117 => array('value' => 'Cataract: No significant cataract, surgery not required', 'delimiter' => ',', 'subspecialty_id' => '4', 'attribute_element_id' => '10', 'display_order' => '0'),
        118 => array('value' => 'YAG laser capsulotomy was performed with no complications', 'delimiter' => ',', 'subspecialty_id' => '4', 'attribute_element_id' => '10', 'display_order' => '0'),
        119 => array('value' => 'booked for first eye', 'delimiter' => ',', 'subspecialty_id' => '4', 'attribute_element_id' => '10', 'display_order' => '0'),
        120 => array('value' => 'booked for second eye', 'delimiter' => ',', 'subspecialty_id' => '4', 'attribute_element_id' => '10', 'display_order' => '0'),
        121 => array('value' => 'good outcome from surgery listed for second eye', 'delimiter' => ',', 'subspecialty_id' => '4', 'attribute_element_id' => '10', 'display_order' => '0'),
        122 => array('value' => 'removal of suture is done', 'delimiter' => ',', 'subspecialty_id' => '4', 'attribute_element_id' => '10', 'display_order' => '0'),
        123 => array('value' => 'booked for left eye', 'delimiter' => ',', 'subspecialty_id' => '4', 'attribute_element_id' => '10', 'display_order' => '0'),
        124 => array('value' => 'booked for right eye', 'delimiter' => ',', 'subspecialty_id' => '4', 'attribute_element_id' => '10', 'display_order' => '0'),
    );

    public function up()
    {
        // 1. load ophciexamination_attribute data
        foreach ($this->ophciexaminationAttributeData as $ophciexaminationAttribute) {
            // check if data currently exists
            $current_data = $this->dbConnection->createCommand()->select('*')->from('ophciexamination_attribute')->where('name = :name and label = :label', array(':name' => $ophciexaminationAttribute['name'], ':label' => $ophciexaminationAttribute['label']))->queryRow();
            if (!$current_data) {
                $this->insert('ophciexamination_attribute', $ophciexaminationAttribute);
            }
        }

        //2. load ophciexamination_attribute_element data
        foreach ($this->ophciexaminationAttributeElementData as $ophciexaminationAttributeElement) {
            // we need an id for the attribute and the element_type
            unset($attributeData);
            unset($elementData);
            // searching for attribute id data
            $attributeData = $this->dbConnection->createCommand()->select('*')->from('ophciexamination_attribute')->where(
                'name = :name and label = :label',
                array(':name' => $this->ophciexaminationAttributeData[$ophciexaminationAttributeElement['attribute_id']]['name'],
                ':label' => $this->ophciexaminationAttributeData[$ophciexaminationAttributeElement['attribute_id']]['label'],
                )
            )->queryRow();
            if ($attributeData) {
                // searching for element type id data
                $elementData = $this->dbConnection->createCommand()->select('*')->from('element_type')->where(
                    'name = :name and class_name = :class_name',
                    array(':name' => $this->elementTypeData[$ophciexaminationAttributeElement['element_type_id']]['name'],
                    ':class_name' => $this->elementTypeData[$ophciexaminationAttributeElement['element_type_id']]['class_name'],
                    )
                )->queryRow();
                if (!$elementData) {
                    // TODO: ask - we need to insert the element?? (I think that it's not a good idea)
                    echo 'Missing element type: '.$this->elementTypeData[$ophciexaminationAttributeElement['element_type_id']]['class_name']."\n";
                } else {
                    // we can check if the attribute element already exists
                    $currentAttributeElement = $this->dbConnection->createCommand()->select('*')
                                                            ->from('ophciexamination_attribute_element')
                                                            ->where(
                                                                'attribute_id = :attribute_id and element_type_id = :element_type_id',
                                                                array(':attribute_id' => $attributeData['id'],
                                                                ':element_type_id' => $elementData['id'],
                                                                )
                                                            )->queryRow();
                    if (!$currentAttributeElement) {
                        $this->insert('ophciexamination_attribute_element', array('attribute_id' => $attributeData['id'], 'element_type_id' => $elementData['id']));
                    }
                }
            }
        }

        // 3. load ophciexamination_attribute_option data
        foreach ($this->ophciexaminationAttributeOptionData as $ophciexaminationAttributeOption) {
            // we need an id for the attribute and the element_type
            unset($attributeData);
            unset($subspecialtyData);
            unset($attributeElementData);
            // searching for attribute id data
            $attributeData = $this->dbConnection->createCommand()->select('*')->from('ophciexamination_attribute')->where(
                'name = :name and label = :label',
                array(':name' => $this->ophciexaminationAttributeData[$this->ophciexaminationAttributeElementData[$ophciexaminationAttributeOption['attribute_element_id']]['attribute_id']]['name'],
                ':label' => $this->ophciexaminationAttributeData[$this->ophciexaminationAttributeElementData[$ophciexaminationAttributeOption['attribute_element_id']]['attribute_id']]['label'],
                )
            )->queryRow();
            if ($attributeData) {
                // searching for element id data
                $elementData = $this->dbConnection->createCommand()->select('*')->from('element_type')->where(
                    'name = :name and class_name = :class_name',
                    array(':name' => $this->elementTypeData[$this->ophciexaminationAttributeElementData[$ophciexaminationAttributeOption['attribute_element_id']]['element_type_id']]['name'],
                    ':class_name' => $this->elementTypeData[$this->ophciexaminationAttributeElementData[$ophciexaminationAttributeOption['attribute_element_id']]['element_type_id']]['class_name'],
                    )
                )->queryRow();
                if (!$elementData) {
                    echo 'Missing element type: '.$this->elementTypeData[$this->ophciexaminationAttributeElementData[$ophciexaminationAttributeOption['attribute_element_id']]['element_type_id']]['class_name']."\n";
                } else {
                    // searching for attribute element id data
                    $attributeElementData = $subspecialtyData = $this->dbConnection->createCommand()->select('*')->from('ophciexamination_attribute_element')->where(
                        'attribute_id = :attribute_id and element_type_id = :element_type_id',
                        array(':attribute_id' => $attributeData['id'],
                        ':element_type_id' => $elementData['id'],
                        )
                    )->queryRow();

                    if ($ophciexaminationAttributeOption['subspecialty_id'] > 0) {
                        $subspecialtyData = $this->dbConnection->createCommand()->select('*')->from('subspecialty')->where(
                            'name = :name',
                            array(':name' => $this->subspecialtyData[$ophciexaminationAttributeOption['subspecialty_id']]['name'])
                        )->queryRow();

                        $currentAttributeOption = $this->dbConnection->createCommand()->select('*')
                            ->from('ophciexamination_attribute_option')
                            ->where(
                                'value = :value and subspecialty_id = :subspecialty_id and attribute_element_id = :attribute_element_id',
                                array(
                                    ':value' => $ophciexaminationAttributeOption['value'],
                                    ':subspecialty_id' => $subspecialtyData['id'],
                                    ':attribute_element_id' => $attributeElementData['id'],
                                )
                            )->queryRow();
                    } else {
                        $subspecialtyData['id'] = null;
                        $currentAttributeOption = $this->dbConnection->createCommand()->select('*')
                            ->from('ophciexamination_attribute_option')
                            ->where(
                                'value = :value and attribute_element_id = :attribute_element_id',
                                array(
                                    ':value' => $ophciexaminationAttributeOption['value'],
                                    ':attribute_element_id' => $attributeElementData['id'],
                                )
                            )->queryRow();
                    }

                    if (!$currentAttributeOption) {
                        $this->insert('ophciexamination_attribute_option', array('value' => $ophciexaminationAttributeOption['value'],
                                                                                    'delimiter' => $ophciexaminationAttributeOption['delimiter'],
                                                                                    'subspecialty_id' => $subspecialtyData['id'],
                                                                                    'attribute_element_id' => $attributeElementData['id'],
                                                                                    'display_order' => $ophciexaminationAttributeOption['display_order'],
                                                                                    ));
                    }
                }
            }
        }
    }

    public function down()
    {
        echo "We do not delete the data here.\n";

        return true;
    }

    /*
    // Use safeUp/safeDown to do migration with transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}
