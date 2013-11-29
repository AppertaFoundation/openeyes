<?php

class m131128_113809_table_versioning extends CDbMigration
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

		$this->delete('proc_opcs_assignment',"proc_id not in (".implode(',',$proc_ids).")");

		foreach (Yii::app()->db->getSchema()->getTables() as $table) {
			if (!in_array($table->name,$exclude) && !preg_match('/et_/',$table->name) && !preg_match('/^oph/',$table->name)) {
				$this->createArchiveTable($table);
			}
		}
	}

	public function createArchiveTable($table)
	{
		$a = Yii::app()->db->createCommand("show create table $table->name;")->queryRow();

		$create = $a['Create Table'];

		$create = preg_replace('/CREATE TABLE `(.*?)`/',"CREATE TABLE `{$table->name}_archive`",$create);

		preg_match_all('/  KEY `(.*?)`/',$create,$m);

		foreach ($m[1] as $key) {
			$_key = $key;

			if (strlen($_key) <= 60) {
				$_key = 'acv_'.$_key;
			} else {
				$_key[0] = 'a';
				$_key[1] = 'c';
				$_key[2] = 'v';
				$_key[3] = '_';
			}

			$create = preg_replace("/KEY `{$key}`/","KEY `$_key`",$create);
		}

		preg_match_all('/CONSTRAINT `(.*?)`/',$create,$m);

		foreach ($m[1] as $key) {
			$_key = $key;

			if (strlen($_key) <= 60) {
				$_key = 'acv_'.$_key;
			} else {
				$_key[0] = 'a';
				$_key[1] = 'c';
				$_key[2] = 'v';
				$_key[3] = '_';
			}

			$create = preg_replace("/CONSTRAINT `{$key}`/","CONSTRAINT `$_key`",$create);
		}

		Yii::app()->db->createCommand($create)->query();

		$this->addColumn("{$table->name}_archive","rid","int(10) unsigned NOT NULL");
		$this->createIndex("{$table->name}_archive_rid_fk","{$table->name}_archive","rid");
		$this->addForeignKey("{$table->name}_archive_rid_fk","{$table->name}_archive","rid",$table->name,"id");

		foreach (Yii::app()->db->createCommand()->select("*")->from($table->name)->order("id asc")->queryAll() as $row) {
			$row['rid'] = $row['id'];
			unset($row['id']);
			$this->insert("{$table->name}_archive",$row);
		}
	}

	public function findForeignKey($table, $field)
	{
		foreach ($this->getKeys($table) as $name => $data) {
			if ($data['field'] == $field && isset($data['remote_table'])) {
				return $name;
			}
		}

		throw new Exception("Can't find foreign key for $table: $field");
	}

	public function findIndex($table, $field)
	{
		foreach ($this->getKeys($table) as $name => $data) {
			if ($data['field'] == $field && !isset($data['remote_table'])) {
				return $name;
			}
		}

		throw new Exception("Can't find index for $table: $field");
	}

	public function getKeys($table) {
		$a = Yii::app()->db->createCommand("show create table `$table`;")->queryRow();

		$keys = array();

		foreach (explode(chr(10),trim($a['Create Table'])) as $line) {
			if (preg_match('/ KEY `(.*?)` \(`(.*?)`\)/',$line,$m)) {
				$keys[$m[1]] = array(
					'field' => $m[2],
				);
			}

			if (preg_match('/ CONSTRAINT `(.*?)` FOREIGN KEY \(`(.*?)`\) REFERENCES `(.*?)` \(`(.*?)`\)/',$line,$m)) {
				if (isset($keys[$m[1]]['field']) && $keys[$m[1]]['field'] != $m[2]) {
					throw new Exception("Key mismatch for table $table: {$keys[$m[1]]['field']} != {$m[2]}");
				}

				$keys[$m[1]]['field'] = $m[2];
				$keys[$m[1]]['remote_table'] = $m[3];
				$keys[$m[1]]['remote_field'] = $m[4];
			}
		}

		return $keys;
	}
}
