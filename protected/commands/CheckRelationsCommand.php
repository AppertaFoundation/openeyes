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

class CheckRelationsCommand extends CConsoleCommand
{
	public function run($args)
	{
		if(isset($args[0]) &&  $args[0] == 'models'){
			$this->checkModelsRelations();
		}
		else{//
			if(isset($args[0]) &&  $args[0] == 'savefile'){
				$this->checkSqlConstraints(true);
			}
			else{
				$this->checkSqlConstraints();
			}
		}
	}

	private function checkSqlConstraints($saveFile = false){
		$foreignKeysSql="SELECT  ke.referenced_table_name parent,  ke.table_name child,  ke.constraint_name,
  		ke.column_name,  ke.referenced_column_name parent_column,  c.`IS_NULLABLE`
		FROM
		  information_schema.KEY_COLUMN_USAGE ke join information_schema.COLUMNS c on
		  c.`COLUMN_NAME` = ke.`COLUMN_NAME` and c.`TABLE_NAME` = ke.`TABLE_NAME`
		WHERE
		  ke.referenced_table_name IS NOT NULL and ke.table_name not like '%_version'
		  and ke.`CONSTRAINT_SCHEMA`='openeyes' and c.TABLE_SCHEMA='openeyes'
		ORDER BY
		  ke.table_name;";

		$dbConn = Yii::app()->getDb();

		$saveString ='';

		$foreignKeys = $dbConn->createCommand($foreignKeysSql)->queryAll();
		foreach($foreignKeys as  $foreignKey){
			$corruptEcho ='';
			$constraintCheck = "select c.*, p." . $foreignKey['parent_column']
				. " as 'fk-" . $foreignKey['parent_column'] .  "' from " .
				$foreignKey['child'] . " c left join " . $foreignKey['parent'] . " p on c." .
				$foreignKey['column_name'] . " = p." . $foreignKey['parent_column'] .
				" where p." . $foreignKey['parent_column'] . "  is null";

			if($foreignKey['IS_NULLABLE'] =='YES'){
				$constraintCheck =  $constraintCheck .
					" and c." . $foreignKey['column_name'] . "  is not null";
				$corruptEcho .= "\nSkipping null, nullable ". $foreignKey['column_name'] . " table: " . $foreignKey['child'];
			}

			try{
				$corruptRows = $dbConn->createCommand($constraintCheck)->queryAll();
			}
			catch(CDbException $e){
				echo "\n\n ERR executing check sql: " . $dbConn->createCommand($constraintCheck)->getText() . " Msg: " .
					$e->getMessage() . " \n Table: " . $foreignKey['child'] . " Column: "  .
					$foreignKey['column_name']  . " Parent table: " . $foreignKey['parent'] .
					" Parent column " . $foreignKey['parent_column'];
			}

			if(count($corruptRows) > 0 ){
				$corruptEcho .= "\nCorrupt Relation: " . $foreignKey['constraint_name'] . " table/fk: " . $foreignKey['child'] .
					"." . $foreignKey['column_name'] . " -> " . $foreignKey['parent'] . "." . $foreignKey['parent_column'];

				$corruptEcho .= "\n" .  implode(',' , array_keys( $corruptRows[0] ) );
				foreach($corruptRows as  $corruptRow){
					$corruptEcho .= "\n" . implode(',' , $corruptRow);
				}
				echo $corruptEcho ;
				if($saveFile){
					$saveString .= $corruptEcho;
				}
			}
		}

		if($saveFile){
			$this->saveFile($saveString);
		}
		//else{
		//	echo $saveString;
		//}
	}

	private function saveFile($cnt){
		$fileName = "dbCheckReport_" . date("Y-m-d_H:i:s"). '.txt';
		$savePath = Yii::getPathOfAlias('application.runtime');
		$file = fopen($savePath . DIRECTORY_SEPARATOR . $fileName , 'w');
		fwrite($file, $cnt);
		fclose($file);
	}

	private function checkModelsRelations(){
		$cFileHelper = new CFileHelper();
		$models = $cFileHelper->findFiles( Yii::getPathOfAlias('application.models'), array('fileTypes' => array('php')) );
		foreach($models as $model){
			$modelName = substr($model,strrpos($model, DIRECTORY_SEPARATOR)+1);
			$modelName = substr($modelName , 0, strpos($modelName, '.php'));

			if(strpos($modelName , 'Base' ) === 0 || !method_exists($modelName, 'model')){
				//echo "\nSkipping $modelName as not Contains base or has not model method";
				continue;
			}

			try{
				$thisModel = $modelName::model();
				if(!$thisModel instanceof CActiveRecord){
					//echo "\nSkipping $modelName as not CactiveRecord";
					continue;
				}

			}
			catch(CdbException $e){
				echo "\n skipping : " . $modelName;
				continue;
			}
			catch(Exception $e){
				echo "\nSomething wrong happened: " . $modelName . " Code: " . $e->getCode()
					. " Message: " . $e->getMessage() . " Trace: " . $e->getTraceAsString();
			}

			$rels = $thisModel->relations();
			echo "\nChecking Model name : " . $modelName . " rels: " . var_export($rels, true);
			foreach($rels as $rel => $relProps){
				if($relProps[0] == 'CBelongsToRelation'){
					foreach($thisModel->findAll() as $thisRecords){
						$findBelongs = $thisModel->with($rel)->findByPk($thisRecords->id);
						if(count($findBelongs) > 0 ){
							//echo "\n$modelName Relation $rel found, tot:" . count($findBelongs);
						}
						else{
							echo "\n$modelName belong Relation $rel  missing \n\n";
						}
					}
				}
			}
		}
	}
}