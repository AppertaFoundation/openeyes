<?php
class SetupPathwayStepPickerBehavior extends CBehavior
{
    public function setupPicker(){
        $steps = OEModule\OphCiExamination\models\OphCiExamination_Workflow_Rule::model()->findWorkflowSteps(
            Yii::app()->session['selected_institution_id'],
            null
        );
        $custom_booking_step_sql = Yii::app()->db->createCommand()
            ->select('pstpa.custom_pathway_step_type_id, pstpa.site_id, pstpa.subspecialty_id, pstpa.firm_id, pstpa.preset_id')
            ->from('pathway_step_type_preset_assignment pstpa')
            ->join('pathway_step_type pst', 'pst.id = pstpa.custom_pathway_step_type_id')
            ->join('pathway_step_type_institution psti', 'psti.pathway_step_type_id = pst.id')
            ->where('pstpa.preset_short_name = "Book Apt." AND psti.institution_id = :institution_id')
            ->group('pstpa.custom_pathway_step_type_id, pstpa.site_id, pstpa.subspecialty_id, pstpa.firm_id, pstpa.preset_id')
            ->bindValue(':institution_id', Yii::app()->session['selected_institution_id'])
            ->queryAll();
        $custom_booking_steps = array_map(
            static function ($item) {
                return [
                    'id' => $item['custom_pathway_step_type_id'],
                    'site_id' => $item['site_id'],
                    'subspecialty_id' => $item['subspecialty_id'],
                    'firm_id' => $item['firm_id'],
                    'duration_value' => ((int)$item['preset_id'])%100,
                    'duration_period' => $item['preset_id'] ? PathwayStepTypePresetAssignment::$duration_period[((int)$item['preset_id'])/100] : null,
                ];
            },
            $custom_booking_step_sql
        );
        $preset_criteria = new CDbCriteria();
        $preset_criteria->compare('LOWER(type)', 'psd');
        $preset_criteria->compare('active', true);
        $presets = OphDrPGDPSD_PGDPSD::model()->findAll($preset_criteria) ? : array();
        $psds = array_map(
            static function ($item) {
                return array('id' => $item->id, 'name' => 'preset_order', 'label' => $item->name);
            },
            $presets
        );
        $vf_presets = VisualFieldTestPreset::model()->findAllAtLevel(ReferenceData::LEVEL_INSTITUTION);
        $vf_test_types = VisualFieldTestType::model()->findAll();
        $vf_test_options = VisualFieldTestOption::model()->findAll();

        $vf_preset_json = array_map(
            static function ($item) {
                return array('id' => $item->id, 'name' => 'preset_id', 'label' => $item->name);
            },
            $vf_presets
        );
        $vf_test_type_json = array_map(
            static function ($item) {
                return array('id' => $item->id, 'name' => 'test_type_id', 'label' => $item->short_name);
            },
            $vf_test_types
        );
        $vf_test_option_json = array_map(
            static function ($item) {
                return array('id' => $item->id, 'name' => 'option_id', 'label' => $item->short_name);
            },
            $vf_test_options
        );

        $letter_macros = array_map(
            static function ($item) {
                return ['id' => $item->id, 'name' => $item->name];
            },
            LetterMacro::model()->findAllAtLevel(ReferenceData::LEVEL_INSTITUTION)
        );
        array_unshift($letter_macros, ['id' => '', 'name' => 'None']);

        $sites = array_map(
            static function ($item) {
                return ['id' => $item->id, 'name' => 'site', 'label' => $item->name];
            },
            Institution::model()->getCurrent()->sites
        );

        $services = array_map(
            static function ($item) {
                return ['id' => $item->id, 'name' => 'subspecialty', 'label' => $item->name];
            },
            Subspecialty::model()->with(['serviceSubspecialtyAssignment' => ['with' => 'firms']])->findAll('firms.active = 1')
        );

        // key names need to be consistent to OpenEyes.UI.PathwayStepPicker part of default options
        return json_encode(array(
            // Can't use the much faster json_encode here because the workflow step list contains a list of active records,
            // which can't be serialised by json_encode.
            'workflows' => CJSON::encode($steps),
            'custom_booking_steps' => $custom_booking_steps,
            'letter_macros' => $letter_macros,
            'vf_test_presets' => $vf_preset_json,
            'vf_test_types' => $vf_test_type_json,
            'vf_test_options' => $vf_test_option_json,
            'psds' => $psds,
            'sites' => $sites,
            'services' => $services,
            'subspecialties' => NewEventDialogHelper::structureAllSubspecialties(),
        ), JSON_THROW_ON_ERROR);
    }

    public function getPathwayStepTypesRequirePicker(){
        $psd_step_type_id = Yii::app()->db->createCommand()
            ->select('id')
            ->from('pathway_step_type')
            ->where('short_name = \'drug admin\'')
            ->queryScalar();
        $exam_step_type_id = Yii::app()->db->createCommand()
            ->select('id')
            ->from('pathway_step_type')
            ->where('short_name = \'Exam\'')
            ->queryScalar();
        $vf_step_type_id = Yii::app()->db->createCommand()
            ->select('id')
            ->from('pathway_step_type')
            ->where('short_name = \'Fields\'')
            ->queryScalar();
        $letter_step_type_id = Yii::app()->db->createCommand()
            ->select('id')
            ->from('pathway_step_type')
            ->where('short_name = \'Letter\'')
            ->queryScalar();
        $generic_step_type_id = Yii::app()->db->createCommand()
            ->select('id')
            ->from('pathway_step_type')
            ->where('short_name = \'Task\'')
            ->queryScalar();
        $onhold_step_type_id = Yii::app()->db->createCommand()
            ->select('id')
            ->from('pathway_step_type')
            ->where('short_name = \'onhold\'')
            ->queryScalar();
        $booking_step_type_id = Yii::app()->db->createCommand()
            ->select('id')
            ->from('pathway_step_type')
            ->where('short_name = \'Book Apt.\'')
            ->queryScalar();
        $current_firm = Firm::model()->findByPk(Yii::app()->session['selected_firm_id']);

        // key names need to be consistent to OpenEyes.UI.PathwayStepPicker part of default options
        return array(
            'psd_step_type_id' => (int)$psd_step_type_id,
            'exam_step_type_id' => (int)$exam_step_type_id,
            'vf_step_type_id' => (int)$vf_step_type_id,
            'letter_step_type_id' => (int)$letter_step_type_id,
            'generic_step_type_id' => (int)$generic_step_type_id,
            'onhold_step_type_id' => (int)$onhold_step_type_id,
            'booking_step_type_id' => (int)$booking_step_type_id,
            'current_firm_id' => (int)$current_firm->id,
            'current_subspecialty_id' => (int)$current_firm->getSubspecialtyID()
        );
    }
}
