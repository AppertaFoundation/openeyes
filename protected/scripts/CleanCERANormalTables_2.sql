use openeyes;

/* disable foreigh key check*/
SET FOREIGN_KEY_CHECKS = 0;

SELECT "TRUNCATE TABLE address" AS "";
truncate table address;

SELECT "TRUNCATE TABLE patient" AS "";
truncate table patient;

SELECT "TRUNCATE TABLE audit" AS "";
truncate table audit;

SELECT "TRUNCATE TABLE audit_ipaddr" AS "";
truncate table audit_ipaddr;

SELECT "TRUNCATE TABLE audit_server" AS "";
truncate table audit_server;

SELECT "TRUNCATE TABLE audit_useragent" AS "";
truncate table audit_useragent;

SELECT "TRUNCATE TABLE patient_user_referral" AS "";
TRUNCATE TABLE patient_user_referral;

/*As required, we need keep "admin" and "docman" user.
Which means we need to keep the corresponding contact information as well*/
SELECT "TRUNCATE TABLE contact" AS "";
delete from contact
where id not in (
    select contact_id from user
    where user.username in ('admin', 'docman_user')
);

SELECT "TRUNCATE TABLE contact_location" AS "";
truncate table contact_location;

SELECT "TRUNCATE TABLE dicom_file_log" AS "";
truncate table dicom_file_log;

SELECT "TRUNCATE TABLE dicom_file_queue" AS "";
truncate table dicom_file_queue;


SELECT "TRUNCATE TABLE dicom_files" AS "";
truncate table dicom_files;

SELECT "TRUNCATE TABLE dicom_import_log" AS "";
truncate table dicom_import_log;

SELECT "TRUNCATE TABLE document_instance" AS "";
truncate table document_instance;

SELECT "TRUNCATE TABLE document_instance_data" AS "";
truncate table document_instance_data;

SELECT "TRUNCATE TABLE document_output" AS "";
truncate table document_output;

SELECT "TRUNCATE TABLE document_set" AS "";
truncate table document_set;

SELECT "TRUNCATE TABLE document_target" AS "";
truncate table document_target;

SELECT "TRUNCATE TABLE et_ophciexamination_adnexalcomorbidity" AS "";
truncate table et_ophciexamination_adnexalcomorbidity;

SELECT "TRUNCATE TABLE et_ophciexamination_allergies" AS "";
truncate table et_ophciexamination_allergies;

SELECT "TRUNCATE TABLE et_ophciexamination_allergy" AS "";
truncate table et_ophciexamination_allergy;

SELECT "TRUNCATE TABLE et_ophciexamination_anteriorsegment" AS "";
truncate table et_ophciexamination_anteriorsegment;

SELECT "TRUNCATE TABLE et_ophciexamination_anteriorsegment_cct" AS "";
truncate table et_ophciexamination_anteriorsegment_cct;

SELECT "TRUNCATE TABLE et_ophciexamination_bleb_assessment" AS "";
truncate table et_ophciexamination_bleb_assessment;

SELECT "TRUNCATE TABLE et_ophciexamination_cataractsurgicalmanagement" AS "";
truncate table et_ophciexamination_cataractsurgicalmanagement;


SELECT "TRUNCATE TABLE et_ophciexamination_cataractsurgicalmanagement_surgery_reasons" AS "";
truncate table et_ophciexamination_cataractsurgicalmanagement_surgery_reasons;

SELECT "TRUNCATE TABLE et_ophciexamination_clinicoutcome" AS "";
truncate table et_ophciexamination_clinicoutcome;

SELECT "TRUNCATE TABLE et_ophciexamination_colourvision" AS "";
truncate table et_ophciexamination_colourvision;

SELECT "TRUNCATE TABLE et_ophciexamination_comorbidities" AS "";
truncate table et_ophciexamination_comorbidities;

SELECT "TRUNCATE TABLE et_ophciexamination_conclusion" AS "";
truncate table et_ophciexamination_conclusion;

SELECT "TRUNCATE TABLE et_ophciexamination_currentmanagementplan" AS "";
truncate table et_ophciexamination_currentmanagementplan;

SELECT "TRUNCATE TABLE et_ophciexamination_diagnoses" AS "";
truncate table et_ophciexamination_diagnoses;

SELECT "TRUNCATE TABLE et_ophciexamination_dilation" AS "";
truncate table et_ophciexamination_dilation;

SELECT "TRUNCATE TABLE et_ophciexamination_drgrading" AS "";
truncate table et_ophciexamination_drgrading;

SELECT "TRUNCATE TABLE et_ophciexamination_familyhistory" AS "";
truncate table et_ophciexamination_familyhistory;

SELECT "TRUNCATE TABLE et_ophciexamination_glaucomarisk" AS "";
truncate table et_ophciexamination_glaucomarisk;

SELECT "TRUNCATE TABLE et_ophciexamination_gonioscopy" AS "";
truncate table et_ophciexamination_gonioscopy;

SELECT "TRUNCATE TABLE et_ophciexamination_history" AS "";
truncate table et_ophciexamination_history;

SELECT "TRUNCATE TABLE et_ophciexamination_history_medications" AS "";
truncate table et_ophciexamination_history_medications;

SELECT "TRUNCATE TABLE et_ophciexamination_history_risks" AS "";
truncate table et_ophciexamination_history_risks;

SELECT "TRUNCATE TABLE et_ophciexamination_injectionmanagementcomplex" AS "";
truncate table et_ophciexamination_injectionmanagementcomplex;

SELECT "TRUNCATE TABLE et_ophciexamination_intraocularpressure" AS "";
truncate table et_ophciexamination_intraocularpressure;

SELECT "TRUNCATE TABLE et_ophciexamination_investigation" AS "";
truncate table et_ophciexamination_investigation;

SELECT "TRUNCATE TABLE et_ophciexamination_lasermanagement" AS "";
truncate table et_ophciexamination_lasermanagement;

SELECT "TRUNCATE TABLE et_ophciexamination_management" AS "";
truncate table et_ophciexamination_management;

SELECT "TRUNCATE TABLE et_ophciexamination_nearvisualacuity" AS "";
truncate table et_ophciexamination_nearvisualacuity;

SELECT "TRUNCATE TABLE et_ophciexamination_oct" AS "";
truncate table et_ophciexamination_oct;

SELECT "TRUNCATE TABLE et_ophciexamination_opticdisc" AS "";
truncate table et_ophciexamination_opticdisc;

SELECT "TRUNCATE TABLE et_ophciexamination_optom_comments" AS "";
truncate table et_ophciexamination_optom_comments;

SELECT "TRUNCATE TABLE et_ophciexamination_overallmanagementplan" AS "";
truncate table et_ophciexamination_overallmanagementplan;

SELECT "TRUNCATE TABLE et_ophciexamination_pastsurgery" AS "";
truncate table et_ophciexamination_pastsurgery;

SELECT "TRUNCATE TABLE et_ophciexamination_posteriorpole" AS "";
truncate table et_ophciexamination_posteriorpole;

SELECT "TRUNCATE TABLE et_ophciexamination_postop_complications" AS "";
truncate table et_ophciexamination_postop_complications;

SELECT "TRUNCATE TABLE et_ophciexamination_pupillaryabnormalities" AS "";
truncate table et_ophciexamination_pupillaryabnormalities;

SELECT "TRUNCATE TABLE et_ophciexamination_refraction" AS "";
truncate table et_ophciexamination_refraction;

SELECT "TRUNCATE TABLE et_ophciexamination_risks" AS "";
truncate table et_ophciexamination_risks;

SELECT "TRUNCATE TABLE et_ophciexamination_socialhistory" AS "";
truncate table et_ophciexamination_socialhistory;

SELECT "TRUNCATE TABLE et_ophciexamination_systemic_diagnoses" AS "";
truncate table et_ophciexamination_systemic_diagnoses;

SELECT "TRUNCATE TABLE et_ophciexamination_visualacuity" AS "";
truncate table et_ophciexamination_visualacuity;

SELECT "TRUNCATE TABLE et_ophciphasing_intraocularpressure" AS "";
truncate table et_ophciphasing_intraocularpressure;

SELECT "TRUNCATE TABLE et_ophcocorrespondence_letter" AS "";
truncate table et_ophcocorrespondence_letter;

SELECT "TRUNCATE TABLE et_ophcodocument_document" AS "";
truncate table et_ophcodocument_document;

SELECT "TRUNCATE TABLE et_ophcomessaging_message" AS "";
truncate table et_ophcomessaging_message;

SELECT "TRUNCATE TABLE et_ophcotherapya_mrservicein" AS "";
truncate table et_ophcotherapya_mrservicein;

SELECT "TRUNCATE TABLE et_ophcotherapya_patientsuit" AS "";
truncate table et_ophcotherapya_patientsuit;

SELECT "TRUNCATE TABLE et_ophcotherapya_relativecon" AS "";
truncate table et_ophcotherapya_relativecon;

SELECT "TRUNCATE TABLE et_ophcotherapya_therapydiag" AS "";
truncate table et_ophcotherapya_therapydiag;

SELECT "TRUNCATE TABLE et_ophdrprescription_details" AS "";
truncate table et_ophdrprescription_details;

SELECT "TRUNCATE TABLE et_ophinbiometry_calculation" AS "";
truncate table et_ophinbiometry_calculation;

SELECT "TRUNCATE TABLE et_ophinbiometry_iol_ref_values" AS "";
truncate table et_ophinbiometry_iol_ref_values;

SELECT "TRUNCATE TABLE et_ophinbiometry_measurement" AS "";
truncate table et_ophinbiometry_measurement;

SELECT "TRUNCATE TABLE et_ophinbiometry_selection" AS "";
truncate table et_ophinbiometry_selection;

SELECT "TRUNCATE TABLE et_ophinvisualfields_image" AS "";
truncate table et_ophinvisualfields_image;

SELECT "TRUNCATE TABLE et_ophouanaestheticsataudit_anaesthetis" AS "";
truncate table et_ophouanaestheticsataudit_anaesthetis;

SELECT "TRUNCATE TABLE et_ophouanaestheticsataudit_notes" AS "";
truncate table et_ophouanaestheticsataudit_notes;

SELECT "TRUNCATE TABLE et_ophouanaestheticsataudit_satisfactio" AS "";
truncate table et_ophouanaestheticsataudit_satisfactio;

SELECT "TRUNCATE TABLE et_ophouanaestheticsataudit_vitalsigns" AS "";
truncate table et_ophouanaestheticsataudit_vitalsigns;

SELECT "TRUNCATE TABLE et_ophtrconsent_benfitrisk" AS "";
truncate table et_ophtrconsent_benfitrisk;


SELECT "TRUNCATE TABLE et_ophtrconsent_leaflets" AS "";
truncate table et_ophtrconsent_leaflets;


SELECT "TRUNCATE TABLE et_ophtrconsent_other" AS "";
truncate table et_ophtrconsent_other;


SELECT "TRUNCATE TABLE et_ophtrconsent_permissions" AS "";
truncate table et_ophtrconsent_permissions;

SELECT "TRUNCATE TABLE et_ophtrconsent_procedure" AS "";
truncate table et_ophtrconsent_procedure;


SELECT "TRUNCATE TABLE et_ophtrintravitinjection_anaesthetic" AS "";
truncate table et_ophtrintravitinjection_anaesthetic;


SELECT "TRUNCATE TABLE et_ophtrintravitinjection_anteriorseg" AS "";
truncate table et_ophtrintravitinjection_anteriorseg;


SELECT "TRUNCATE TABLE et_ophtrintravitinjection_complications" AS "";
truncate table et_ophtrintravitinjection_complications;


SELECT "TRUNCATE TABLE et_ophtrintravitinjection_postinject" AS "";
truncate table et_ophtrintravitinjection_postinject;


SELECT "TRUNCATE TABLE et_ophtrintravitinjection_site" AS "";
truncate table et_ophtrintravitinjection_site;


SELECT "TRUNCATE TABLE et_ophtrintravitinjection_treatment" AS "";
truncate table et_ophtrintravitinjection_treatment;


SELECT "TRUNCATE TABLE et_ophtrlaser_anteriorseg" AS "";
truncate table et_ophtrlaser_anteriorseg;


SELECT "TRUNCATE TABLE et_ophtrlaser_comments" AS "";
truncate table et_ophtrlaser_comments;


SELECT "TRUNCATE TABLE et_ophtrlaser_posteriorpo" AS "";
truncate table et_ophtrlaser_posteriorpo;


SELECT "TRUNCATE TABLE et_ophtrlaser_site" AS "";
truncate table et_ophtrlaser_site;


SELECT "TRUNCATE TABLE et_ophtrlaser_treatment" AS "";
truncate table et_ophtrlaser_treatment;


SELECT "TRUNCATE TABLE et_ophtroperationbooking_contact_details" AS "";
truncate table et_ophtroperationbooking_contact_details;


SELECT "TRUNCATE TABLE et_ophtroperationbooking_diagnosis" AS "";
truncate table et_ophtroperationbooking_diagnosis;


SELECT "TRUNCATE TABLE et_ophtroperationbooking_operation" AS "";
truncate table et_ophtroperationbooking_operation;


SELECT "TRUNCATE TABLE et_ophtroperationbooking_scheduleope" AS "";
truncate table et_ophtroperationbooking_scheduleope;


SELECT "TRUNCATE TABLE et_ophtroperationnote_anaesthetic" AS "";
truncate table et_ophtroperationnote_anaesthetic;


SELECT "TRUNCATE TABLE et_ophtroperationnote_cataract" AS "";
truncate table et_ophtroperationnote_cataract;


SELECT "TRUNCATE TABLE et_ophtroperationnote_comments" AS "";
truncate table et_ophtroperationnote_comments;


SELECT "TRUNCATE TABLE et_ophtroperationnote_genericprocedure" AS "";
truncate table et_ophtroperationnote_genericprocedure;


SELECT "TRUNCATE TABLE et_ophtroperationnote_glaucomatube" AS "";
truncate table et_ophtroperationnote_glaucomatube;


SELECT "TRUNCATE TABLE et_ophtroperationnote_membrane_peel" AS "";
truncate table et_ophtroperationnote_membrane_peel;


SELECT "TRUNCATE TABLE et_ophtroperationnote_mmc" AS "";
truncate table et_ophtroperationnote_mmc;


SELECT "TRUNCATE TABLE et_ophtroperationnote_postop_drugs" AS "";
truncate table et_ophtroperationnote_postop_drugs;


SELECT "TRUNCATE TABLE et_ophtroperationnote_procedurelist" AS "";
truncate table et_ophtroperationnote_procedurelist;


SELECT "TRUNCATE TABLE et_ophtroperationnote_site_theatre" AS "";
truncate table et_ophtroperationnote_site_theatre;


SELECT "TRUNCATE TABLE et_ophtroperationnote_surgeon" AS "";
truncate table et_ophtroperationnote_surgeon;


SELECT "TRUNCATE TABLE et_ophtroperationnote_tamponade" AS "";
truncate table et_ophtroperationnote_tamponade;


SELECT "TRUNCATE TABLE et_ophtroperationnote_vitrectomy" AS "";
truncate table et_ophtroperationnote_vitrectomy;


SELECT "TRUNCATE TABLE event" AS "";
truncate table event;


SELECT "TRUNCATE TABLE event_image" AS "";
truncate table event_image;


SELECT "TRUNCATE TABLE event_issue" AS "";
truncate table event_issue;


SELECT "TRUNCATE TABLE gp" AS "";
truncate table gp;


SELECT "TRUNCATE TABLE institution" AS "";
delete from institution
where remote_id not in ('CERA');


SELECT "TRUNCATE TABLE measurement_reference" AS "";
truncate table measurement_reference;

SELECT "TRUNCATE TABLE ophciexamination_allergy_entry" AS "";
truncate table ophciexamination_allergy_entry;

SELECT "TRUNCATE TABLE ophciexamination_colourvision_reading" AS "";
truncate table ophciexamination_colourvision_reading;

SELECT "TRUNCATE TABLE ophciexamination_comorbidities_assignment" AS "";
truncate table ophciexamination_comorbidities_assignment;

SELECT "TRUNCATE TABLE ophciexamination_diagnosis" AS "";
truncate table ophciexamination_diagnosis;

SELECT "TRUNCATE TABLE ophciexamination_dilation_treatment" AS "";
truncate table ophciexamination_dilation_treatment;

SELECT "TRUNCATE TABLE ophciexamination_event_elementset_assignment" AS "";
truncate table ophciexamination_event_elementset_assignment;


SELECT "TRUNCATE TABLE ophciexamination_familyhistory_entry" AS "";
truncate table ophciexamination_familyhistory_entry;

SELECT "TRUNCATE TABLE ophciexamination_history_medications_entry" AS "";
truncate table ophciexamination_history_medications_entry;

SELECT "TRUNCATE TABLE ophciexamination_history_risks_entry" AS "";
truncate table ophciexamination_history_risks_entry;

SELECT "TRUNCATE TABLE ophciexamination_injectmanagecomplex_answer" AS "";
truncate table ophciexamination_injectmanagecomplex_answer;

SELECT "TRUNCATE TABLE ophciexamination_injectmanagecomplex_risk_assignment" AS "";
truncate table ophciexamination_injectmanagecomplex_risk_assignment;

SELECT "TRUNCATE TABLE ophciexamination_intraocularpressure_value" AS "";
truncate table ophciexamination_intraocularpressure_value;


SELECT "TRUNCATE TABLE ophciexamination_nearvisualacuity_reading" AS "";
truncate table ophciexamination_nearvisualacuity_reading;

SELECT "TRUNCATE TABLE ophciexamination_oct_fluidtype_assignment" AS "";
truncate table ophciexamination_oct_fluidtype_assignment;


SELECT "TRUNCATE TABLE ophciexamination_pastsurgery_op" AS "";
truncate table ophciexamination_pastsurgery_op;


SELECT "TRUNCATE TABLE ophciexamination_postop_et_complications" AS "";
truncate table ophciexamination_postop_et_complications;


SELECT "TRUNCATE TABLE ophciexamination_systemic_diagnoses_diagnosis" AS "";
truncate table ophciexamination_systemic_diagnoses_diagnosis;


SELECT "TRUNCATE TABLE ophciexamination_visualacuity_reading" AS "";
truncate table ophciexamination_visualacuity_reading;


SELECT "TRUNCATE TABLE ophcotherapya_therapydisorder" AS "";
truncate table ophcotherapya_therapydisorder;


SELECT "TRUNCATE TABLE ophinbiometry_imported_events" AS "";
truncate table ophinbiometry_imported_events;


SELECT "TRUNCATE TABLE ophtrconsent_procedure_add_procs_add_procs" AS "";
truncate table ophtrconsent_procedure_add_procs_add_procs;


SELECT "TRUNCATE TABLE ophtrconsent_procedure_procedures_procedures" AS "";
truncate table ophtrconsent_procedure_procedures_procedures;


SELECT "TRUNCATE TABLE ophtrintravitinjection_complicat_assignment" AS "";
truncate table ophtrintravitinjection_complicat_assignment;


SELECT "TRUNCATE TABLE ophtrintravitinjection_ioplowering_assign" AS "";
truncate table ophtrintravitinjection_ioplowering_assign;


SELECT "TRUNCATE TABLE ophtroperationbooking_operation_booking" AS "";
truncate table ophtroperationbooking_operation_booking;


SELECT "TRUNCATE TABLE ophtroperationbooking_operation_date_letter_sent" AS "";
truncate table ophtroperationbooking_operation_date_letter_sent;


SELECT "TRUNCATE TABLE ophtroperationbooking_operation_procedures_procedures" AS "";
truncate table ophtroperationbooking_operation_procedures_procedures;

SELECT "TRUNCATE TABLE ophtroperationbooking_operation_sequence" AS "";
truncate table ophtroperationbooking_operation_sequence;


SELECT "TRUNCATE TABLE ophtroperationbooking_scheduleope_patientunavail" AS "";
truncate table ophtroperationbooking_scheduleope_patientunavail;

SELECT "TRUNCATE TABLE ophtroperationnote_anaesthetic_anaesthetic_agent" AS "";
truncate table ophtroperationnote_anaesthetic_anaesthetic_agent;


SELECT "TRUNCATE TABLE ophtroperationnote_anaesthetic_anaesthetic_complication" AS "";
truncate table ophtroperationnote_anaesthetic_anaesthetic_complication;


SELECT "TRUNCATE TABLE ophtroperationnote_anaesthetic_anaesthetic_delivery" AS "";
truncate table ophtroperationnote_anaesthetic_anaesthetic_delivery;


SELECT "TRUNCATE TABLE ophtroperationnote_cataract_complication" AS "";
truncate table ophtroperationnote_cataract_complication;


SELECT "TRUNCATE TABLE ophtroperationnote_cataract_operative_device" AS "";
truncate table ophtroperationnote_cataract_operative_device;


SELECT "TRUNCATE TABLE ophtroperationnote_postop_drugs_drug" AS "";
truncate table ophtroperationnote_postop_drugs_drug;

SELECT "TRUNCATE TABLE ophtroperationnote_postop_site_subspecialty_drug" AS "";
truncate table ophtroperationnote_postop_site_subspecialty_drug;

SELECT "TRUNCATE TABLE ophtroperationnote_procedure_element" AS "";
truncate table ophtroperationnote_procedure_element;

SELECT "TRUNCATE TABLE ophtroperationnote_procedurelist_procedure_assignment" AS "";
truncate table ophtroperationnote_procedurelist_procedure_assignment;

/*NEED FIGUTRE OUT WHERE TO ADD NEW*/
SELECT "TRUNCATE TABLE ophtroperationnote_site_subspecialty_postop_instructions" AS "";
truncate table ophtroperationnote_site_subspecialty_postop_instructions;


SELECT "TRUNCATE TABLE ophtroperationnote_trabeculectomy_difficulties" AS "";
truncate table ophtroperationnote_trabeculectomy_difficulties;


SELECT "TRUNCATE TABLE patient_contact_assignment" AS "";
truncate table patient_contact_assignment;


SELECT "TRUNCATE TABLE patient_identifier" AS "";
truncate table patient_identifier;

SELECT "TRUNCATE TABLE patient_measurement" AS "";
truncate table patient_measurement;


SELECT "TRUNCATE TABLE patientticketing_queuesetuser" AS "";
truncate table patientticketing_queuesetuser;


SELECT "TRUNCATE TABLE patientticketing_ticket" AS "";
truncate table patientticketing_ticket;


SELECT "TRUNCATE TABLE patientticketing_ticketqueue_assignment" AS "";
truncate table patientticketing_ticketqueue_assignment;


SELECT "TRUNCATE TABLE pcr_risk_values" AS "";
truncate table pcr_risk_values;


SELECT "TRUNCATE TABLE practice" AS "";
truncate table practice;


SELECT "TRUNCATE TABLE proc_opcs_assignment" AS "";
truncate table proc_opcs_assignment;


SELECT "TRUNCATE TABLE proc_subspecialty_assignment" AS "";
truncate table proc_subspecialty_assignment;


SELECT "TRUNCATE TABLE proc_subspecialty_subsection_assignment" AS "";
truncate table proc_subspecialty_subsection_assignment;


SELECT "TRUNCATE TABLE protected_file" AS "";
truncate table protected_file;


SELECT "TRUNCATE TABLE referral_episode_assignment" AS "";
truncate table referral_episode_assignment;


SELECT "TRUNCATE TABLE secondary_diagnosis" AS "";
truncate table secondary_diagnosis;


SELECT "TRUNCATE TABLE secondaryto_common_oph_disorder" AS "";
truncate table secondaryto_common_oph_disorder;

SELECT "TRUNCATE TABLE setting_subspecialty" AS "";
truncate table setting_subspecialty;


SELECT "TRUNCATE TABLE setting_user" AS "";
truncate table setting_user;


SELECT "TRUNCATE TABLE site_subspecialty_anaesthetic_agent" AS "";
truncate table site_subspecialty_anaesthetic_agent;


SELECT "TRUNCATE TABLE site_subspecialty_anaesthetic_agent_default" AS "";
truncate table site_subspecialty_anaesthetic_agent_default;


SELECT "TRUNCATE TABLE site_subspecialty_drug" AS "";
truncate table site_subspecialty_drug;


SELECT "TRUNCATE TABLE site_subspecialty_operative_device" AS "";
truncate table site_subspecialty_operative_device;


SELECT "TRUNCATE TABLE trial" AS "";
truncate table trial;


SELECT "TRUNCATE TABLE trial_patient" AS "";
truncate table trial_patient;


SELECT "TRUNCATE TABLE user" AS "";
delete from user
where username not in ('admin', 'docman_user');


SELECT "TRUNCATE TABLE user_firm_preference" AS "";
truncate table user_firm_preference;


SELECT "TRUNCATE TABLE user_session" AS "";
truncate table user_session;


SELECT "TRUNCATE TABLE user_trial_assignment" AS "";
truncate table user_trial_assignment;


SELECT "TRUNCATE TABLE worklist" AS "";
truncate table worklist;


SELECT "TRUNCATE TABLE worklist_definition" AS "";
truncate table worklist_definition;


SELECT "TRUNCATE TABLE worklist_patient" AS "";
truncate table worklist_patient;

SELECT "TRUNCATE TABLE user_hotlist_item" AS "";
truncate table user_hotlist_item;

SELECT "TRUNCATE TABLE archive_patient_allergy_assignment" AS "";
truncate table archive_patient_allergy_assignment;

SELECT "TRUNCATE TABLE archive_patient_risk_assignment" AS "";
truncate table archive_patient_risk_assignment;

SELECT "TRUNCATE TABLE commissioning_body_patient_assignment" AS "";
truncate table commissioning_body_patient_assignment;

SELECT "TRUNCATE TABLE ophcotherapya_patientsuit_decisiontreenoderesponse" AS "";
truncate table ophcotherapya_patientsuit_decisiontreenoderesponse;

SELECT "TRUNCATE TABLE patient_merge_request" AS "";
truncate table patient_merge_request;

SELECT "TRUNCATE TABLE patient_oph_info" AS "";
truncate table patient_oph_info;

SELECT "TRUNCATE TABLE patient_referral" AS "";
truncate table patient_referral;

SELECT "TRUNCATE TABLE worklist_patient_attribute" AS "";
truncate table worklist_patient_attribute;




/*enable foreign key check again*/
SET FOREIGN_KEY_CHECKS = 1;
