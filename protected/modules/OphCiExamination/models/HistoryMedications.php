<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2017
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2017, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
namespace OEModule\OphCiExamination\models;
/**
 * Class HistoryMedications
 * @package OEModule\OphCiExamination\models
 *
 * @property \Event $event
 * @property \User $user
 * @property \User $usermodified
 * @property \EventMedicationUse[] $entries
 * @property HistoryMedicationsEntry[] $orderedEntries
 * @property HistoryMedicationsEntry[] $currentOrderedEntries
 * @property HistoryMedicationsEntry[] $stoppedOrderedEntries
 */
class HistoryMedications extends BaseMedicationElement
{
    public $widgetClass = 'OEModule\OphCiExamination\widgets\HistoryMedications';
    public $new_entries = array();
    public function tableName()
    {
        return 'et_ophciexamination_history_medications';
    }
    protected function errorAttributeException($attribute, $message)
    {
        if ($attribute === \CHtml::modelName($this) . '_entries') {
            if (preg_match('/^(\d+)/', $message, $match) === 1) {
                return \CHtml::modelName($this) . '_entry_table tbody tr:eq(' . ($match[1]-1) . ')';
            }
        }
        return parent::errorAttributeException($attribute, $message);
    }
    /**
     * @param HistoryMedications $element
     */
    public function loadFromExisting($element)
    {
        $entries = array();
        foreach ($element->entries as $entry) {
            $new = new \EventMedicationUse();
            $new->loadFromExisting($entry);
            $entries[] = $new;
        }
        $this->entries = $entries;
        $this->assortEntries();
        $this->originalAttributes = $this->getAttributes();
    }
    
    public function getTileSize($action)
    {
        return $action === 'view' ? 2 : null;
    }
    public function isIndividual($action)
    {
        return $action !=='view';
    }
    public function getDisplayOrder($action, $as_parent = false)
    {
        if ($action=='view'){
            return 25;
        }
        else{
            return parent::getDisplayOrder($action, $as_parent);
        }
    }
    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return MedicationManagement the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }
}
