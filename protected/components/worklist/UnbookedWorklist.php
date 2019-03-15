<?php
/**
 * (C) OpenEyes Foundation, 2019
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */


class UnbookedWorklist extends CComponent
{
    public $worklist_manager;

    public function __construct(){
        $this->worklist_manager = new \WorklistManager();
    }

    public function getWorklist(DateTime $date, $site_id, $subspecialty_id, $firm_id = null)
    {
        $definition = $this->getDefinition($site_id, $subspecialty_id, $firm_id);

        if (!$definition){
            $definition = $this->createWorklistDefinition($site_id, $subspecialty_id, $firm_id);
        }

        $criteria = new \CDbCriteria();
        $criteria->addCondition('worklist_definition_id = :worklist_definition_id');
        $criteria->addCondition('start >= :start');
        $criteria->addCondition('end < :end');
        $criteria->params[':start'] = $date->modify('today')->format('Y-m-d H:i:s'); // The time is set to 00:00:00
        $criteria->params[':end'] = $date->modify('tomorrow')->format('Y-m-d H:i:s'); // Midnight of tomorrow
        $criteria->params[':worklist_definition_id'] = $definition->id;

       $unbooked_worklist = Worklist::model()->find($criteria);

        if (!$unbooked_worklist) {

            //generate worklist by definition
            $today = new \DateTime();
            return $this->worklist_manager->generateAutomaticWorklists($definition, $today->modify('tomorrow'));
        }

        return null;
    }

    public function getDefinition($site_id, $subspecialty_id, $firm_id = null)
    {
        $criteria = new \CDbCriteria();
        $criteria->with = ['display_contexts', 'mappings.values'];
        $criteria->addCondition('display_contexts.site_id = :site_id');
        $criteria->addCondition('display_contexts.subspecialty_id = :subspecialty_id');

        $criteria->addCondition('mappings.key = "UNBOOKED"');
        $criteria->addCondition('values.mapping_value = "true"');

        $criteria->params[':site_id'] = $site_id;
        $criteria->params[':subspecialty_id'] = $subspecialty_id;

        if ($firm_id) {
            $criteria->addCondition('display_contexts.firm_id = :firm_id');
            $criteria->params[':firm_id'] = $firm_id;
        }

        return WorklistDefinition::model()->find($criteria);
    }


    public function createWorklistDefinition($site_id, $subspecialty_id, $firm_id = null)
    {
        $definition = new \WorklistDefinition();
        $definition->name = 'Unbooked';
        $definition->description = 'Patients for unbooked worklist';
        $definition->worklist_name = null;
        $definition->rrule = 'FREQ=DAILY';
        $definition->start_time = '00:00';
        $definition->end_time = '23:59';

        if ($definition->save()) {
            $context = new \WorklistDefinitionDisplayContext();
            $context->firm_id = $firm_id;
            $context->subspecialty_id = $subspecialty_id;
            $context->site_id = $site_id;
            $context->worklist_definition_id = $definition->id;
            $context->save();

            $mapping = new \WorklistDefinitionMapping();
            $mapping->key = 'UNBOOKED';
            $mapping->worklist_definition_id = $definition->id;

            if ($mapping->save()) {
                $value = new \WorklistDefinitionMappingValue();
                $value->worklist_definition_mapping_id = $mapping->id;
                $value->mapping_value = 'true';

                $value->save();
            }
        }

        return $definition;
    }
}
