<?php
/**
 * OpenEyes
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2012
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2012, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

class ReportsCommand extends CConsoleCommand {
	public function run($args) {
		Yii::import('application.modules.Reports.models.*');

		if (!$query_type_events = ReportQueryType::model()->find('name=?',array('Events'))) {
			$query_type_events = new ReportQueryType;
			$query_type_events->name = 'Events';
			$query_type_events->display_order = 1;
			if (!$query_type_events->save()) {
				print_r($query_type_events->getErrors(),true);
				exit;
			}
		}

		if (!$query_type_patients = ReportQueryType::model()->find('name=?',array('Patients'))) {
			$query_type_patients = new ReportQueryType;
			$query_type_patients->name = 'Patients';
			$query_type_patients->display_order = 1;
			if (!$query_type_patients->save()) {
				print_r($query_type_patients->getErrors(),true);
				exit;
			}
		}

		/* input data types */

		$ridt_number = ReportInputDataType::add('number',1);
		$ridt_dropdown_from_table = ReportInputDataType::add('dropdown_from_table',2);
		$ridt_date = ReportInputDataType::add('date',3);
		$ridt_diagnoses = ReportInputDataType::add('diagnoses',4);
		$ridt_checkbox = ReportInputDataType::add('checkbox',5);
		$ridt_checkbox_optional_match = ReportInputDataType::add('checkbox_optional_match',6);

		/* report item data types */

		$rimt_total = ReportItemDataType::add('total');
		$rimt_mean_and_range = ReportItemDataType::add('mean_and_range');
		$rimt_number_and_percentage = ReportItemDataType::add('number_and_percentage');
		$rimt_number_and_percentage_pair = ReportItemDataType::add('number_and_percentage_pair');
		$rimt_list = ReportItemDataType::add('list');
		$rimt_string = ReportItemDataType::add('string');
		$rimt_date = ReportItemDataType::add('date');
		$rimt_nhsdate = ReportItemDataType::add('NHSDate');
		$rimt_conditional = ReportItemDataType::add('conditional');
		$rimt_list_from_element_relation = ReportItemDataType::add('list_from_element_relation');
		$rimt_element_relation = ReportItemDataType::add('element_relation');
		$rimt_number = ReportItemDataType::add('number');

		/* rule types */

		$rule_one_of = ReportValidationRuleType::add('One of');

		/* Cataract Outcomes */

		Yii::import('application.modules.OphTrOperationnote.models.*');

		$opnote = EventType::model()->find('class_name=?',array('OphTrOperationnote'));
		$element_proclist = ElementType::model()->find('event_type_id=? and class_name=?',array($opnote->id,'ElementProcedureList'));
		$element_surgeon = ElementType::model()->find('event_type_id=? and class_name=?',array($opnote->id,'ElementSurgeon'));
		$element_cataract = ElementType::model()->find('event_type_id=? and class_name=?',array($opnote->id,'ElementCataract'));

		$report = Report::add(array(
			'query_type_id' => $query_type_events->id,
			'subspecialty_id' => 4,
			'name' => 'Cataract outcomes',
			'description' => 'Cataract outcomes report',
			'icon' => 'treatment_operation',
			'display_order' => 1,
			'can_print' => 1,
			'can_download' => 1,
		));

			$dataset1 = $report->addDataset('dataset1');
				$el_proclist = $dataset1->addElement($element_proclist->id);
					$el_proclist->addField('eye_id');
				$el_surgeon = $dataset1->addElement($element_surgeon->id);
					$el_surgeon->addField('surgeon_id');
					$el_surgeon->addField('assistant_id');
					$el_surgeon->addField('supervising_surgeon_id');
				$el_cataract = $dataset1->addElement($element_cataract->id);

			$dataset1->addInput(array(
				'data_type_id' => $ridt_dropdown_from_table->id,
				'data_type_param1' => 'Firm',
				'data_type_param2' => 'getCataractList',
				'name' => 'firm_id',
				'description' => 'Firm',
				'display_order' => 1,
			));

			$dataset1->addInput(array(
				'data_type_id' => $ridt_dropdown_from_table->id,
				'data_type_param1' => 'User',
				'data_type_param2' => 'getListSurgeons',
				'name' => 'surgeon_id',
				'description' => 'Surgeon',
				'display_order' => 2,
				'or_id' => 1,
			));

			$dataset1->addInput(array(
				'data_type_id' => $ridt_dropdown_from_table->id,
				'data_type_param1' => 'User',
				'data_type_param2' => 'getListSurgeons',
				'name' => 'assistant_id',
				'description' => 'Assistant surgeon',
				'display_order' => 3,
				'or_id' => 1,
			));

			$dataset1->addInput(array(
				'data_type_id' => $ridt_dropdown_from_table->id,
				'data_type_param1' => 'User',
				'data_type_param2' => 'getListSurgeons',
				'name' => 'supervising_surgeon_id',
				'description' => 'Supervising surgeon',
				'display_order' => 4,
				'or_id' => 1,
			));

			$dataset1->addInput(array(
				'data_type_id' => $ridt_date->id,
				'name' => 'date_from',
				'description' => 'Date from',
				'default_value' => '-12 months',
				'display_order' => 5,
				'required' => 1,
			));

			$dataset1->addInput(array(
				'data_type_id' => $ridt_date->id,
				'name' => 'date_to',
				'description' => 'Date to',
				'default_value' => 'now',
				'display_order' => 6,
				'required' => 1,
			));

			$dataset1->addItem(array(
				'data_type_id' => $rimt_total->id,
				'name' => 'Cataracts',
				'data_field' => 'cataracts',
				'subtitle' => 'Number of cataracts performed',
				'display_order' => 1,
			));

			$dataset1->addItem(array(
				'data_type_id' => $rimt_mean_and_range->id,
				'name' => 'Age',
				'data_field' => 'age',
				'data_input_field' => 'age',
				'subtitle' => 'Age of patients',
				'display_order' => 2,
			));

			$item = $dataset1->addItem(array(
				'data_type_id' => $rimt_number_and_percentage_pair->id,
				'name' => 'Eyes',
				'data_field' => 'eyes',
				'subtitle' => 'Eyes',
				'display_order' => 3,
			));

				$item->addPairField(array(
					'name' => 'left',
					'field' => 'eye_id',
					'value' => '1',
				));

				$item->addPairField(array(
					'name' => 'right',
					'field' => 'eye_id',
					'value' => '2',
				));

			$dataset1->addItem(array(
				'data_type_id' => $rimt_mean_and_range->id,
				'name' => 'Final visual acuity',
				'data_field' => 'final_visual_acuity',
				'subtitle' => 'Final visual acuity',
				'display_order' => 4,
			));

			$pc_rupture = CataractComplications::model()->find('name=?',array('PC rupture'));

			$pc_ruptures = $dataset1->addItem(array(
				'data_type_id' => $rimt_number_and_percentage->id,
				'name' => 'PC ruptures',
				'data_field' => 'pc_ruptures',
				'subtitle' => 'PC ruptures',
				'display_order' => 5,
				'element_id' => $el_cataract->id,
				'element_relation' => 'complications',
				'element_relation_field' => 'complication_id',
				'element_relation_value' => $pc_rupture->id,
			));

			$complications = $dataset1->addItem(array(
				'data_type_id' => $rimt_number_and_percentage->id,
				'name' => 'Complications',
				'data_field' => 'complications',
				'subtitle' => 'All complications',
				'display_order' => 7,
				'element_id' => $el_cataract->id,
				'element_relation' => 'complications',
			));

			$dataset2 = $report->addDataset('dataset2');
				$el_cataract = $dataset2->addElement($element_cataract->id);

			$avg_pc_ruptures = $dataset2->addItem(array(
				'data_type_id' => $rimt_number_and_percentage->id,
				'name' => 'Average',
				'data_field' => 'pc_rupture_average',
				'subtitle' => 'Average',
				'display_order' => 6,
				'element_id' => $el_cataract->id,
				'element_relation' => 'complications',
				'element_relation_field' => 'complication_id',
				'element_relation_value' => $pc_rupture->id,
				'display' => 0,
			));

			$avg_complications = $dataset2->addItem(array(
				'data_type_id' => $rimt_number_and_percentage->id,
				'name' => 'Average',
				'data_field' => 'complication_average',
				'subtitle' => 'Average',
				'display_order' => 8,
				'element_id' => $el_cataract->id,
				'element_relation' => 'complications',
				'display' => 0,
			));

		$graph = $report->addGraph('Cataract complication rate',1);

			$graph->addItem(array(
				'report_item_id' => $pc_ruptures->id,
				'name' => 'PC rupture rate',
				'subtitle' => 'percentage',
				'range' => 10,
				'display_order' => 1,
				'show_scale' => 0,
			));

			$graph->addItem(array(
				'report_item_id' => $avg_pc_ruptures->id,
				'name' => 'Average rate',
				'subtitle' => 'institution average',
				'range' => 10,
				'display_order' => 2,
			));

			$graph->addItem(array(
				'report_item_id' => $complications->id,
				'name' => 'Complication rate',
				'subtitle' => 'percentage',
				'range' => 10,
				'display_order' => 3,
				'show_scale' => 0,
			));
			
			$graph->addItem(array(
				'report_item_id' => $avg_complications->id,
				'name' => 'Average rate',
				'subtitle' => 'institution average',
				'range' => 10,
				'display_order' => 4,
			));


		/* Operations */

		$report = Report::add(array(
			'query_type_id' => $query_type_events->id,
			'subspecialty_id' => null,
			'name' => 'Operations',
			'description' => 'Operations',
			'icon' => 'treatment_operation',
			'display_order' => 3,
			'can_print' => 1,
			'can_download' => 1,
		));

		$dataset1 = $report->addDataset('dataset1');
			$el_proclist = $dataset1->addElement($element_proclist->id);
				$el_proclist->addField('eye_id');
				$el_proclist->addJoin(array(
					'join_table' => 'eye',
					'join_clause' => 'eye_id = eye.id',
					'join_select' => 'eye.name as eye',
				));
			$el_surgeon = $dataset1->addElement($element_surgeon->id);
				$el_surgeon->addField('surgeon_id');
				$el_surgeon->addField('assistant_id');
				$el_surgeon->addField('supervising_surgeon_id');
			$el_cataract = $dataset1->addElement($element_cataract->id, 1);

		$surgeon_id = $dataset1->addInput(array(
			'data_type_id' => $ridt_dropdown_from_table->id,
			'data_type_param1' => 'User',
			'data_type_param2' => 'getListSurgeons',
			'name' => 'surgeon_id',
			'description' => 'Surgeon',
			'display_order' => 1,
			'required' => 1,
			'include' => 0,
		));

		$dataset1->addInput(array(
			'data_type_id' => $ridt_checkbox_optional_match->id,
			'name' => 'match_surgeon',
			'default_value' => 1,
			'description' => 'Match surgeon',
			'display_order' => 2,
			'data_type_param1' => 'surgeon_id',
			'data_type_param2' => 'surgeon_id',
			'or_id' => 1,
		));

		$dataset1->addInput(array(
			'data_type_id' => $ridt_checkbox_optional_match->id,
			'name' => 'match_assistant_surgeon',
			'default_value' => 1,
			'description' => 'Match assistant surgeon',
			'display_order' => 3,
			'data_type_param1' => 'surgeon_id',
			'data_type_param2' => 'assistant_id',
			'or_id' => 1,
		));

		$dataset1->addInput(array(
			'data_type_id' => $ridt_checkbox_optional_match->id,
			'name' => 'match_supervising_surgeon',
			'default_value' => 1,
			'description' => 'Match supervising surgeon',
			'display_order' => 4,
			'data_type_param1' => 'surgeon_id',
			'data_type_param2' => 'supervising_surgeon_id',
			'or_id' => 1,
		));

		$dataset1->addInput(array(
			'data_type_id' => $ridt_date->id,
			'name' => 'date_from',
			'description' => 'Date from',
			'default_value' => '-12 months',
			'display_order' => 5,
			'required' => 1,
		));

		$dataset1->addInput(array(
			'data_type_id' => $ridt_date->id,
			'name' => 'date_to',
			'description' => 'Date to',
			'default_value' => 'now',
			'display_order' => 6,
			'required' => 1,
		));

		$operations = $dataset1->addItem(array(
			'data_type_id' => $rimt_list->id,
			'name' => 'Operations',
			'data_field' => 'operations',
			'subtitle' => 'Operations',
			'display_order' => 1,
		));

			$operations->addListItem(array(
				'data_type_id' => $rimt_number->id,
				'name' => 'Patient ID',
				'data_field' => 'patient_id',
				'subtitle' => 'Patient ID',
				'display' => 0,
			));

			$operations->addListItem(array(
				'data_type_id' => $rimt_nhsdate->id,
				'name' => 'Date',
				'data_field' => 'datetime',
				'subtitle' => 'Date',
				'display_order' => 1,
			));

			$operations->addListItem(array(
				'data_type_id' => $rimt_string->id,
				'name' => 'Hospital no',
				'data_field' => 'hos_num',
				'subtitle' => 'Patient hospital number',
				'display_order' => 2,
				'link' => '/patient/episodes/{patient_id}',
			));
	
			$operations->addListItem(array(
				'data_type_id' => $rimt_string->id,
				'name' => 'First name',
				'data_field' => 'first_name',
				'subtitle' => 'Patient first name',
				'display_order' => 3,
			));
	
			$operations->addListItem(array(
				'data_type_id' => $rimt_string->id,
				'name' => 'Last name',
				'data_field' => 'last_name',
				'subtitle' => 'Patient last name',
				'display_order' => 4,
			));
	
			$procedures = $operations->addListItem(array(
				'data_type_id' => $rimt_list_from_element_relation->id,
				'name' => 'Procedures',
				'data_field' => 'procedures',
				'subtitle' => 'Procedures',
				'display_order' => 5,
				'element_id' => $el_proclist->id,
				'element_relation' => 'procedures',
			));
	
				$procedures->addListItem(array(
					'data_type_id' => $rimt_element_relation->id,
					'name' => 'procedure',
					'data_field' => 'term',
				));

				$procedures->addListItem(array(
					'data_type_id' => $rimt_element_relation->id,
					'name' => 'procedure',
					'data_field' => 'term',
				));

			$complications = $operations->addListItem(array(
				'data_type_id' => $rimt_list_from_element_relation->id,
				'name' => 'Complications',
				'data_field' => 'complications',
				'subtitle' => 'Complications',
				'display_order' => 6,
				'element_id' => $el_cataract->id,
				'element_relation' => 'complications',
			));

				$complications->addListItem(array(
					'data_type_id' => $rimt_element_relation->id,
					'name' => 'name',
					'data_field' => 'name',
				));

			$role = $operations->addListItem(array(
				'data_type_id' => $rimt_conditional->id,
				'name' => 'Role',
				'data_field' => 'role',
				'subtitle' => 'Role',
				'display_order' => 7,
			));

				$role->addConditional(array(
					'input_id' => $surgeon_id->id,
					'match_field' => 'surgeon_id',
					'result' => 'Surgeon',
					'display_order' => 1,
				));

				$role->addConditional(array(
					'input_id' => $surgeon_id->id,
					'match_field' => 'assistant_id',
					'result' => 'Assistant surgeon',
					'display_order' => 2,
				));

				$role->addConditional(array(
					'input_id' => $surgeon_id->id,
					'match_field' => 'supervising_surgeon_id',
					'result' => 'Supervising surgeon',
					'display_order' => 3,
				));

		$report->addRule(array(
			'rule_type_id' => $rule_one_of->id,
			'rule' => 'match_surgeon,match_assistant_surgeon,match_supervising_surgeon',
			'message' => 'At least one of the surgeon checkboxes must be selected',
		));

		/* Patient diagnoses */

		$report = Report::add(array(
			'query_type_id' => $query_type_patients->id,
			'name' => 'Patient diagnoses',
			'description' => 'Patient diagnoses report',
			'icon' => 'treatment_operation',
			'display_order' => 2,
			'can_print' => 1,
			'can_download' => 0,
		));

		$dataset1 = $report->addDataset('dataset1');
			$disorders = $dataset1->addRelatedEntity('diagnoses');
				$principal = $disorders->addRelatedEntityType('principal');
				$secondary = $disorders->addRelatedEntityType('secondary');

				$episode = $disorders->addRelatedEntityTable(array(
					'entity_type_id' => $principal->id,
					'table_name' => 'episode',
					'table_related_field' => 'patient_id',
					'table_query_field' => 'disorder_id',
					'table_date_field' => 'last_modified_date',
				));

					$episode->addRelation(array(
						'local_field' => 'disorder_id',
						'related_table' => 'disorder',
						'select_field' => 'term',
						'select_field_as' => 'diagnosis',
					));

					$episode->addRelation(array(
						'local_field' => 'eye_id',
						'related_table' => 'eye',
						'select_field' => 'name',
						'select_field_as' => 'eye',
					));

				$secondary_diagnosis = $disorders->addRelatedEntityTable(array(
					'entity_type_id' => $secondary->id,
					'table_name' => 'secondary_diagnosis',
					'table_related_field' => 'patient_id',
					'table_query_field' => 'disorder_id',
					'table_date_field' => 'last_modified_date',
				));

					$secondary_diagnosis->addRelation(array(
						'local_field' => 'disorder_id',
						'related_table' => 'disorder',
						'select_field' => 'term',
						'select_field_as' => 'diagnosis',
					));

					$secondary_diagnosis->addRelation(array(
						'local_field' => 'eye_id',
						'related_table' => 'eye',
						'select_field' => 'name',
						'select_field_as' => 'eye',
					));

			$dataset1->addInput(array(
				'data_type_id' => $ridt_date->id,
				'name' => 'date_from',
				'description' => 'Start date',
				'default_value' => '-12 months',
				'display_order' => 1,
				'required' => 1,
			));

			$dataset1->addInput(array(
				'data_type_id' => $ridt_date->id,
				'name' => 'date_to',
				'description' => 'End date',
				'default_value' => 'now',
				'display_order' => 2,
				'required' => 1,
			));

			$dataset1->addInput(array(
				'data_type_id' => $ridt_diagnoses->id,
				'related_entity_id' => $disorders->id,
				'name' => 'diagnoses',
				'description' => 'Diagnoses',
				'display_order' => 3,
			));

			$patients = $dataset1->addItem(array(
				'data_type_id' => $rimt_list->id,
				'name' => 'Patients',
				'data_field' => 'patients',
				'subtitle' => 'Patient diagnoses',
				'display_order' => 1,
			));

				$patients->addListItem(array(
					'data_type_id' => $rimt_nhsdate->id,
					'name' => 'Date',
					'data_field' => 'date',
					'subtitle' => 'Date',
					'display_order' => 1,
				));

				$patients->addListItem(array(
					'data_type_id' => $rimt_string->id,
					'name' => 'Hospital no',
					'data_field' => 'hos_num',
					'subtitle' => 'Patient hospital number',
					'display_order' => 2,
					'link' => '/patient/episodes/{patient_id}',
				));

				$patients->addListItem(array(
					'data_type_id' => $rimt_string->id,
					'name' => 'First name',
					'data_field' => 'first_name',
					'subtitle' => 'Patient first name',
					'display_order' => 3,
				));

				$patients->addListItem(array(
					'data_type_id' => $rimt_string->id,
					'name' => 'Last name',
					'data_field' => 'last_name',
					'subtitle' => 'Patient last name',
					'display_order' => 4,
				));

				$patients->addListItem(array(
					'data_type_id' => $rimt_number->id,
					'name' => 'Patient ID',
					'data_field' => 'patient_id',
					'subtitle' => 'Patient ID',
					'display' => 0,
				));

				$diagnoses = $patients->addListItem(array(
					'data_type_id' => $rimt_list->id,
					'name' => 'Diagnoses',
					'data_field' => 'diagnoses',
					'subtitle' => 'Diagnoses',
					'display_order' => 5,
				));

					$diagnoses->addListItem(array(
						'data_type_id' => $rimt_string->id,
						'name' => 'Eye',
						'data_field' => 'eye',
						'subtitle' => 'Eye',
						'display_order' => 1,
					));

					$diagnoses->addListItem(array(
						'data_type_id' => $rimt_string->id,
						'name' => 'Diagnosis',
						'data_field' => 'diagnosis',
						'subtitle' => 'Diagnosis',
						'display_order' => 2,
					));

	}
}
