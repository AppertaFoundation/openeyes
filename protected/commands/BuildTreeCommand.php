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

class BuildTreeCommand extends CConsoleCommand {

	const CONCEPT_TABLE = 'snomed_concepts';
	const DESC_TABLE    = 'snomed_descriptions';
	const REL_TABLE     = 'snomed_relationships';
	const ISA_RELTYPE   = '116680003';
	
	private $model_cls;
	
	public function getName() {
		return 'Build Disorder Tree Command.';
	}

	public function getHelp() {
		return "yiic buildtree <model_class> <parent_snomeds>\n\n" .
		"Build up the nested set data relationships on the <model_class> for specified <parent_snomeds> codes.\n".
		"\t <parent_someds> is a comma separated list of snomed codes that trees should be imported for.\n\n".
		"Requires the snomed data to be imported:\n\n" .
		"concepts table: " . self::CONCEPT_TABLE . "\n" .
		"descriptions table: " . self::DESC_TABLE . "\n" . 
		"relationships_table: " . self::REL_TABLE . "\n\n" .
		"Please refer to full OpenEyes SNOMED documentation for column details in these tables\n";
		
	}

	/**
	 * Parse csv files from Google Docs, process them into the right format for MySQL import, and import them
	 */
	public function run($args) {
		if (count($args) != 2) {
			echo "wrong arguments\n\n";
			echo $this->getHelp();
			exit();
		}
		
		$kls = $args[0];
		$snomeds = explode(",", $args[1]);

		// check the model class is valid
		$test_class = new $kls();
		
		try {
			$behaviour = $test_class->treeStart();
		} catch (Exception $e) {
			echo "class '$kls' does not implement 'treeBehaviour', exiting ...\n";
			exit();
		}
		
		$this->model_cls = $kls;
		
		// Initialise db
		$db = Yii::app()->db;
		
		$command  = $db->createCommand("ALTER TABLE " . $test_class->treeTable() . " DISABLE KEYS")->execute();
		
		// empty the object tree table
		
		$query = "DELETE FROM disorder_tree";
		$db->createCommand($query)->execute();
		
		foreach($snomeds as $snomed) {
			$this->initialiseSnomed($snomed);
		}
		
		$command  = $db->createCommand("ALTER TABLE " . $test_class->treeTable() . " ENABLE KEYS")->execute();
	}

	/*
	 * checks valid snomed, gets tree data and manages the insert
	 * 
	 */
	protected function initialiseSnomed($snomed) {
		$kls = $this->model_cls;
		// might want this to be more sophisticated for multiple specialty installations, but this will do for now.
		$obj = $kls::model()->findByPk($snomed);
		echo "Processing " . $obj->term . " (" . $snomed . ")\n";
		
		$index = $obj->treeStart();

		list($results, $ignore) = $this->buildTree($snomed, $index);
		
		echo "found " . count($results) . " entries including root\n";
		
		$this->insertBlock('disorder_tree', array("id", "lft", "rght"), $results);
	}
	
	/*
	 * recursive function to calculate nested set values
	 */
	protected function buildTree($snomed, $index) {
		$db = Yii::app()->db;
		$kls= $this->model_cls;
		
		// This join is done purely for reporting/debug purposes, and is unnecessary for the tree building
		$query = "SELECT r.ConceptId1 as child_id, d.term FROM " . self::REL_TABLE . " r LEFT JOIN " . $kls::model()->tableName() . 
			" d ON (r.ConceptId1 = d.id) WHERE r.ConceptId2 = :pid AND r.RelationshipType = :reltype ORDER BY d.term";
		$comm = $db->createCommand($query);
		$comm->params = array(":pid" => $snomed, ":reltype" => self::ISA_RELTYPE);
		
		$rows = $comm->query();
		
		$result = array(array($snomed, $index));
		
		foreach ($rows as $r) {
			if ($r['term'] == null) {
				echo $kls . " with id '" . $r['child_id'] . "' not present, skipping ...\n";
				continue;
			}	
			list($children, $index) = $this->buildTree($r['child_id'], $index+1);
			foreach ($children as $child) {
				$result[] = $child;
			}
		}
		$result[0][2] = ++$index;
		return array($result, $index);
	}
	
	/**
	 * Insert a block of records into a table
	 * @param string $table
	 * @param array $columns
	 * @param array $records
	 */
	protected function insertBlock($table, $columns, $records) {
		$db = Yii::app()->db;
		foreach($columns as &$column) {
			$column = $db->quoteColumnName($column);
		}
		$insert = array();
		foreach($records as $record) {
			foreach($record as &$field) {
				if($field != 'NULL') {
					$field = $db->quoteValue($field);
				}
			}
			$insert[] = '('.implode(',', $record).')';
		}
		$query = "INSERT INTO ".$db->quoteTableName($table)." (".implode(',',$columns).") VALUES ".implode(',', $insert);
		//echo "$query\n";
		$db->createCommand($query)->execute();
	}

}
