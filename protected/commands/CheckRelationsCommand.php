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
