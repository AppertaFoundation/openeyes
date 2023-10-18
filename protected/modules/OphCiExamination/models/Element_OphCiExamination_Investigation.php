<?php
/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

namespace OEModule\OphCiExamination\models;

use OEModule\OphCiExamination\widgets\Investigations as InvestigationsWidget;

/**
 * This is the model class for table "et_ophciexamination_investigation".
 *
 * The followings are the available columns in table:
 *
 * @property string $id
 * @property int $event_id
 * @property string $description
 * @property OphCiExamination_Investigation_Entry[] $entries
 *
 * The followings are the available model relations:
 */
class Element_OphCiExamination_Investigation extends \BaseEventTypeElement
{
    use traits\CustomOrdering;

    protected $widgetClass = InvestigationsWidget::class;
    public $service;
    const ELEMENT_CHILDREN = [
        'Element_OphCiExamination_OCT',
        'Element_OphCiExamination_Keratometry'
    ];

    /**
     * Returns the static model of the specified AR class.
     *
     * @return the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'et_ophciexamination_investigation';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('event_id, description, entries', 'safe'),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('description, id, event_id ', 'safe', 'on' => 'search'),
        );
    }

    public function eventScopeValidation($elements)
    {
        $element_names = array_map(
            function ($element) {
                return \Helper::getNSShortname($element);
            },
            $elements
        );

        foreach ($this->entries as $entry) {
            if (!$this->validateTime($entry->time)) {
                $this->addError('time', 'Incorrect time format ' . $entry->time . '.');
            }
            if (!$entry->validate(['date'])) {
                $this->addError('date', 'Incorrect date: ' . $entry->date);
            }
        }
    }

    public function validateTime($time)
    {
        if (!preg_match('/^(([01]?[0-9])|(2[0-3])):[0-5][0-9]$/', $time)) {
            if (!preg_match('/^(([01]?[0-9])|(2[0-3])):[0-5][0-9]:[0-5][0-9]$/', $time)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'event' => array(self::BELONGS_TO, 'Event', 'event_id'),
            'user' => array(self::BELONGS_TO, 'User', 'created_user_id'),
            'usermodified' => array(self::BELONGS_TO, 'User', 'last_modified_user_id'),
            'entries' => array(self::HAS_MANY, 'OEModule\OphCiExamination\models\OphCiExamination_Investigation_Entry', 'element_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'event_id' => 'Event',
            'description' => 'Description',
        );
    }

    public function canCopy()
    {
        return true;
    }

    protected function beforeSave()
    {
        if ($this->id !== null) {
            foreach (OphCiExamination_Investigation_Entry::model()->findAll('element_id=?', array($this->id)) as $investigation_entry) {
                $investigation_entry->delete();
            }
        }

        return parent::beforeSave();
    }

    protected function afterSave()
    {
        foreach ($this->entries as $entry) {
            $entry->element_id = $this->id;
            $investigation_entry = new OphCiExamination_Investigation_Entry();
            $investigation_entry->element_id = $entry->element_id;
            $investigation_entry->comments = $entry->comments;
            $investigation_entry->investigation_code = $entry->investigation_code;
            $investigation_entry->date = $entry->date;
            $investigation_entry->time = $entry->time;
            $investigation_entry->save(true);
        }
        return parent::afterSave();
    }
    /**
     * Retrieves a list of models based on the current search/filter conditions.
     *
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search()
    {
        // Warning: Please modify the following code to remove attributes that
        // should not be searched.

        $criteria = new \CDbCriteria();

        $criteria->compare('id', $this->id, true);
        $criteria->compare('event_id', $this->event_id, true);

        $criteria->compare('description', $this->description);

        return new \CActiveDataProvider(get_class($this), array(
            'criteria' => $criteria,
        ));
    }

    /**
     * get the letter strings for this element and the elements in its group
     * method used by correspondence module if installed.
     *
     * @return string
     */
    public function getLetter_string()
    {
        $res = '';

        $has_element_comments = trim($this->description) !== "";

        if (count($this->entries) > 0 || $has_element_comments) {
            $res = '<table class="standard borders"><tbody>';

            foreach ($this->entries as $entry) {
                $comment_text = '';

                if (!is_null($entry->comments) && trim($entry->comments) !== "") {
                    $comment_text = " ({$entry->comments})";
                }

                $res .= "<tr><td>{$entry->investigationCode->name}$comment_text</td></tr>";
            }

            if ($has_element_comments) {
                $res .= "<tr><td>Comments:$this->description</td></tr>";
            }

            $res .= '</tbody></table>';
        }

        return $res;
    }

    protected function beforeDelete()
    {
        foreach ($this->entries as $entry) {
            $entry->delete();
        }

        return parent::beforeDelete();
    }
}
