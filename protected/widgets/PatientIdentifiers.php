<?php


class PatientIdentifiers extends BaseCWidget
{
    public $patient;
    public $current_showing_identifier;
    public $identifiers = [];
    public $deleted_identifiers = [];
    public $tooltip_size;
    public $show_all = false;
    public $show_global = false;
    public $widget_enabled = true;

    public function init()
    {
        $this->widget_enabled = \SettingMetadata::model()->getSetting("enable_patient_identifier_tooltip") === 'on';

        if ($this->widget_enabled) {
            if ($this->patient->id) {
                $this->deleted_identifiers = PatientIdentifier::model()->resetScope(true)->findAll('deleted = 1 AND patient_id = ?', [$this->patient->id]);
            }

            $this->patient->refresh();
            if ($this->show_all) {
                $this->identifiers = $this->patient->identifiers;
            } else {
                foreach ($this->patient->identifiers as $identifier) {
                    if ($identifier != $this->current_showing_identifier &&
                        ($identifier->patientIdentifierType->usage_type != "GLOBAL" || $this->show_global)) {
                        $this->identifiers[] = $identifier;
                    }
                }
            }
        }
    }
}
