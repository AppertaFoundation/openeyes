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
    /**
     * @var WorklistManager
     */
    public $worklist_manager;

    public function __construct()
    {
        $this->worklist_manager = new \WorklistManager();
    }

    /**
     * Gets the Unbooked Worklist based on date, site and subspeicalty (and firm)
     *
     * @param DateTime $date
     * @param $site_id
     * @param $subspecialty_id
     * @param null $firm_id
     * @return bool|null
     * @throws Exception
     */
    public function createWorklist(DateTime $date, $site_id, $subspecialty_id, $firm_id = null)
    {
        $tomorrow = clone $date;
        $tomorrow->modify('tomorrow');
        $definition = $this->getDefinition($site_id, $subspecialty_id, $firm_id);

        if (!$definition) {
            $definition = $this->createWorklistDefinition($site_id, $subspecialty_id, $firm_id);
        }

        $worklist = $this->getWorklist($date, $definition->id);
        if (!is_null($worklist)) {
            return $worklist;
        } else {
            //generate worklist by definition
            /**
             * Regarding to the worklist.end time, the generateAutomaticWorklists() function strips the seconds
             * of the time so the worklist end time ends up 23:59:00 regardless that in the definition is 23:59:59
             */
            //generate or skipp if no need to generate
            $this->worklist_manager->generateAutomaticWorklists($definition, $tomorrow);
        }

        return $this->getWorklist($date, $definition->id);
    }

    public function getWorklist(DateTime $date, $definition_id)
    {
        $today = clone $date;
        $today->modify('today');  // The time is set to 00:00:00
        $tomorrow = clone $today;
        $tomorrow->modify('tomorrow');

        $criteria = new \CDbCriteria();
        $criteria->addCondition('t.worklist_definition_id = :worklist_definition_id');
        $criteria->addCondition('start >= :start');
        $criteria->addCondition('end < :end');
        $criteria->params[':start'] = $today->format('Y-m-d H:i:s');
        $criteria->params[':end'] = $tomorrow->format('Y-m-d H:i:s'); // Midnight of tomorrow
        $criteria->params[':worklist_definition_id'] = $definition_id;

        return Worklist::model()->find($criteria);
    }

    /**
     * Gets the Unbooked Worklist Definition based on based on date, site and subspeicalty (and firm)
     *
     * @param $site_id
     * @param $subspecialty_id
     * @param null $firm_id
     * @return WorklistDefinition|null
     */
    public function getDefinition($site_id, $subspecialty_id, $firm_id = null)
    {
        $criteria = new \CDbCriteria();
        $criteria->with = ['display_contexts', 'mappings.values'];
        $criteria->addCondition('display_contexts.site_id = :site_id');
        if (\SettingMetadata::model()->getSetting('include_subspecialty_name_in_unbooked_worklists')) {
            $criteria->addCondition('display_contexts.subspecialty_id = :subspecialty_id');
            $criteria->params[':subspecialty_id'] = $subspecialty_id;
        } else {
            $criteria->addCondition('display_contexts.subspecialty_id is null');
        }

        $criteria->addCondition('mappings.key = "UNBOOKED"');
        $criteria->addCondition('values.mapping_value = "true"');

        $criteria->params[':site_id'] = $site_id;

        if ($firm_id) {
            $criteria->addCondition('display_contexts.firm_id = :firm_id');
            $criteria->params[':firm_id'] = $firm_id;
        }

        return WorklistDefinition::model()->find($criteria);
    }


    /**
     * Creates Unbooked Worklist Definition based on date, site and subspeicalty (and firm)
     *
     * @param $site_id
     * @param $subspecialty_id
     * @param null $firm_id
     * @return WorklistDefinition
     * @throws Exception
     */
    public function createWorklistDefinition($site_id, $subspecialty_id, $firm_id = null)
    {
        $include_subspecialty_name_in_unbooked_worklists = \SettingMetadata::model()->getSetting('include_subspecialty_name_in_unbooked_worklists');
        $subspecialty = Subspecialty::model()->findByPk($subspecialty_id);
        $site = Site::model()->findByPk($site_id);
        $today = new \DateTime();
        $definition = new \WorklistDefinition();
        $site_name = CHtml::encode($site->name);
        if ($include_subspecialty_name_in_unbooked_worklists) {
            $subspecialty_name = CHtml::encode($subspecialty->name);
            $definition->name = "Unbooked - {$subspecialty_name} - {$site_name}";
        } else {
            $definition->name = "Unbooked - {$site_name}";
        }
        $definition->description = 'Patients for unbooked worklist';
        $definition->worklist_name = null;
        $definition->rrule = 'FREQ=DAILY';
        $definition->start_time = '00:00:00';
        $definition->end_time = '23:59:59';
        $definition->active_from = $today->modify('midnight')->format('Y-m-d H:i:s');

        $patient_identifier_type = PatientIdentifierHelper::getPatientIdentifierType('LOCAL', $site->institution_id, $site->id) ??
            PatientIdentifierHelper::getPatientIdentifierType('LOCAL', $site->institution_id);
        $definition->patient_identifier_type_id = $patient_identifier_type->id;

        if ($definition->save()) {
            $context = new \WorklistDefinitionDisplayContext();
            $context->firm_id = $firm_id;
            if ($include_subspecialty_name_in_unbooked_worklists) {
                $context->subspecialty_id = $subspecialty_id;
            }
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

            return $definition;

        } else {
            \OELog::log("WorklistDefinition saving error: " . print_r($definition->getErrors(), true));
        }

        return null;
    }
}
