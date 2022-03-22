<?php

/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2019
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

namespace OEModule\OphCiExamination\models;

use OEModule\OphCiExamination\widgets\PostOpDiplopiaRisk as PostOpDiplopiaRiskWidget;

/**
 * Class PostOpDiplopiaRisk
 *
 * @package OEModule\OphCiExamination\models
 * @property string $comments
 * @property bool $at_risk
 */
class PostOpDiplopiaRisk extends \BaseEventTypeElement
{
    use traits\CustomOrdering;

    protected $auto_update_relations = true;
    protected $auto_validate_relations = true;

    protected $widgetClass = PostOpDiplopiaRiskWidget::class;

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'et_ophciexamination_postopdiplopiarisk';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return [
            ['event_id, comments, at_risk', 'safe'],
            ['at_risk', 'required'],
            ['at_risk', 'numerical', 'integerOnly' => true, 'min' => 0, 'max' => 1],
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            ['id, event_id, , comments, at_risk',  'safe', 'on' => 'search']
        ];
    }

    /**
     * @return array
     */
    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return [
            'event' => [self::BELONGS_TO, 'Event', 'event_id'],
            'user' => [self::BELONGS_TO, 'User', 'created_user_id'],
            'usermodified' => [self::BELONGS_TO, 'User', 'last_modified_user_id'],
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'comments' => 'Comments',
            'at_risk' => 'At Risk',
        ];
    }

    public function getLetter_string()
    {
        $result = 'PODT: ' . ($this->at_risk === 1 ? 'At risk' : 'NOT at risk');
        if ($this->comments) {
            $result .= ', ' . preg_replace('/[\n\r]+/', ' ', $this->comments);
        }

        return $result;
    }
}
