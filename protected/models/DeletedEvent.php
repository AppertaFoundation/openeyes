<?php
/**
 * (C) OpenEyes Foundation, 2021
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2021, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

/**
 * This is the model class for table "event".
 *
 * The followings are the available columns in table 'event':
 *
 * @property string $id
 * @property string $episode_id
 * @property string $user_id
 * @property string $event_type_id
 * @property string $info
 * @property boolean $deleted
 * @property string $delete_reason
 * @property boolean $is_automated
 * @property array $automated_source - json structure
 * @property string $event_date
 * @property string $created_date
 * @property string $last_modified_date
 * @property string $worklist_patient_id
 * @property int $firm_id
 *
 * The followings are the available model relations:
 * @property Episode $episode
 * @property User $user
 * @property EventType $eventType
 * @property Institution $institution
 */
class DeletedEvent extends Event
{
    protected $event_view_path = '/default/removed';
    /**
     * Returns the static model of the specified AR class.
     *
     * @param string $className
     *
     * @return DeletedEvent the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function getEventDate(){
        return 'Deleted';
    }
    public function getEventLiCss(){
        return array_merge(parent::getEventLiCss(), array('deleted'));
    }

    /**
     * In deleted event, the issue will always return false
     *
     * @param string $type
     * @return bool false
     */
    public function hasIssue($type = null)
    {
        $ret = true;
        if($type){
            $ret = false;
        }
        return $ret;
    }
    public function getIssueText()
    {
        return 'Deleted';
    }
    public function getDetailedIssueText($event_icon_class, $event_issue_text, $event_issue_class){
        return array(
            'event_icon_class' => $event_icon_class,
            'event_issue_class' => $event_issue_class,
            'event_issue_text' => $event_issue_text,
        );
    }
}
