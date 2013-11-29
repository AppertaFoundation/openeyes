<?php

class m131128_113809_table_versioning extends OEMigration
{
	public function up()
	{
		$exclude = array('audit','audit_action','audit_ipaddr','audit_model','audit_module','audit_server','audit_type','audit_useragent','authassignment','authitem','authitemchild','eye','gender','import_source','pas_assignment','pas_patient_merged','report','report_dataset','report_dataset_element','report_dataset_element_field','report_dataset_element_join','report_dataset_related_entity','report_dataset_related_entity_table','report_dataset_related_entity_table_relation','report_dataset_related_entity_type','report_graph','report_graph_item','report_input','report_input_data_type','report_input_option','report_item','report_item_data_type','report_item_list_item','report_item_list_item_conditional','report_item_pair_field','report_query_type','report_validation_rule','report_validation_rule_type','tbl_audit_trail','tbl_migration','user_session');

		$this->update('drug',array('default_frequency_id' => null),"default_frequency_id = 0");
		$this->update('drug',array('default_duration_id' => null),"default_duration_id = 0");
		$this->update('drug',array('default_route_id' => null),"default_route_id = 0");

		$proc_ids = array();
		foreach (Yii::app()->db->createCommand()->select("id")->from("proc")->queryAll() as $row) {
			$proc_ids[] = $row['id'];
		}

		if (!empty($proc_ids)) {
			$this->delete('proc_opcs_assignment',"proc_id not in (".implode(',',$proc_ids).")");
		}

		foreach (Yii::app()->db->getSchema()->getTables() as $table) {
			if (!in_array($table->name,$exclude) && !preg_match('/et_/',$table->name) && !preg_match('/^oph/',$table->name)) {
				$this->createArchiveTable($table);
			}
		}
	}

	public function down()
	{
	}
}
