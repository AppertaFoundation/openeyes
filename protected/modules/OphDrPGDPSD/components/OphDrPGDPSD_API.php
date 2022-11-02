<?php
class OphDrPGDPSD_API extends \BaseAPI
{
    public $createOprn = 'OprnCreateEvent';
    public $createOprnArgs = array('firm', 'episode', 'event_type', 'has_pgdpsd_assignments');

    public function setMedEventEntry($med, $element)
    {
        $event_entry = new $element::$entry_class;
        $event_entry_safe_attrs = $event_entry->getSafeAttributeNames();
        foreach ($med->attributes as $key => $val) {
            if (in_array($key, $event_entry_safe_attrs)) {
                $event_entry->$key = $val;
            }
        }
        $event_entry->start_date = $med->administered_time;
        $event_entry->end_date = $med->administered_time;
        $event_entry->usage_type = $event_entry::getUsagetype();
        $event_entry->usage_subtype = $event_entry::getUsageSubtype();
        $event_entry->event_id = $element->event_id;
        $event_entry->setStopReasonTo('Single administration');
        $event_entry->save();
        $med->administered_id = $event_entry->id;
        return $med;
    }

    public function getPatientLatestDAelement($patient, $worklist_patient_id, $event_type)
    {
        $latest_element = null;
        $episode = $patient->getEpisodeForCurrentSubspecialty();
        $da_event_type_id = $event_type->id;
        $da_class_name = $event_type->elementTypes[0]->class_name;
        if ($episode) {
            $criteria = new CDbCriteria();
            $criteria->compare('event.event_type_id', $da_event_type_id);
            $criteria->compare('event.episode_id', $episode->id);
            $criteria->compare('event.deleted', 0);
            $criteria->compare('event.worklist_patient_id', $worklist_patient_id);
            $selected_institution_id = Yii::app()->session->get('selected_institution_id');
            if ($selected_institution_id) {
                $criteria->compare('event.institution_id', $selected_institution_id);
            }
            $criteria->order = 'event.event_date DESC';
            $latest_element = $da_class_name::model()->with('event')->find($criteria);
        }
        return $latest_element;
    }

    protected function processMedsData($medication)
    {
        $med_info_widget = $this->getWidget('MedicationInfoBox', array('medication_id' => $medication['id']));
        $medication['prepended_markup'] = $med_info_widget->getHTML();
        $medication['preferred_term'] = $medication['label'];
        return $medication;
    }
    public function getMedicationOptions()
    {
        $common_systemic = \Medication::model()->listCommonSystemicMedications(true, true);
        $firm_id = $this->yii->session->get('selected_firm_id');
        $site_id = $this->yii->session->get('selected_site_id');
        if ($firm_id) {
            /** @var Firm $firm */
            $firm = $firm_id ? \Firm::model()->findByPk($firm_id) : null;
            $subspecialty_id = $firm->getSubspecialtyID();
            $common_ophthalmic = \Medication::model()->listBySubspecialtyWithCommonMedications($subspecialty_id, true, $site_id, true);
        } else {
            $common_ophthalmic = array();
        }
        $common_drops = \Medication::model()->listCommonDrops($subspecialty_id, true, true);
        $common_oral = \Medication::model()->listCommonOralMedications(true, true);

        $common_systemic = array_map(function ($comm_sys) {
            $comm_sys['category'] = 'systemic';
            return $comm_sys;
        }, $common_systemic);
        $common_ophthalmic = array_map(function ($comm_oph) {
            $comm_oph['category'] = 'ophthalmic';
            return $comm_oph;
        }, $common_ophthalmic);
        $common_drops = array_map(function ($comm_drops) {
            $comm_drops['category'] = 'drops';
            return $comm_drops;
        }, $common_drops);
        $common_oral = array_map(function ($comm_oral) {
            $comm_oral['category'] = 'oral';
            return $comm_oral;
        }, $common_oral);
        $medications = array_merge(
            $common_systemic,
            $common_ophthalmic,
            $common_drops,
            $common_oral,
        );
        foreach ($medications as &$medication) {
            if (isset($medication['prepended_markup'])) {
                continue;
            }
            $medication = $this->processMedsData($medication);
        }
        return $medications;
    }

    protected function getWidget($class_name, $data)
    {
        $widget = $this->yii->getWidgetFactory()->createWidget($this, $class_name, $data);
        $widget->init();
        return $widget;
    }

    public function getInstitutionUserAuth($require_active = true, $user_ids = array())
    {
        $criteria = new \CDbCriteria();
        if ($require_active) {
            $criteria->compare('t.active', 1);
        }
        if ($user_ids) {
            $criteria->addInCondition('t.user_id', $user_ids);
        }
        $selected_institution_id = Yii::app()->session->get('selected_institution_id');
        if ($selected_institution_id) {
            $criteria->with = ['institutionAuthentication', 'institutionAuthentication.institution'];
            $criteria->compare('institution.id', $selected_institution_id);
        }
        $user_auth_objs = \UserAuthentication::model()->findAll($criteria);
        return $user_auth_objs;
    }
}
