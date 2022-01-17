<?php

/**
 * (C) Apperta Foundation, 2020
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2020, Apperta Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

namespace OEModule\OphCiExamination\models;

use OEModule\OphCiExamination\widgets\PrismFusionRange as PrismFusionRangeWidget;

class PrismFusionRange extends \BaseEventTypeElement
{
    use traits\CustomOrdering;
    use traits\HasChildrenWithEventScopeValidation;

    protected $auto_update_relations = true;
    protected $auto_validate_relations = true;
    protected $widgetClass = PrismFusionRangeWidget::class;

    protected const EVENT_SCOPED_CHILDREN = [
        'entries' => 'with_head_posture'
    ];

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'et_ophciexamination_prismfusionrange';
    }

    public function rules()
    {
        return [
            ['event_id, entries, comments', 'safe'],
            ['entries', 'required'],
        ];
    }

    public function relations()
    {
        return [
            'event' => [self::BELONGS_TO, \Event::class, 'event_id'],
            'user' => [self::BELONGS_TO, \User::class, 'created_user_id'],
            'usermodified' => [self::BELONGS_TO, \User::class, 'last_modified_user_id'],
            'entries' => [self::HAS_MANY, PrismFusionRange_Entry::class, 'element_id']
        ];
    }

    public function getLetter_string()
    {

        return \Yii::app()->getController()->renderPartial(
            'application.modules.OphCiExamination.views.default.letter.PrismFusionRange',
            [
                'element' => $this
            ],
            true
        );
    }

    public function afterValidate()
    {
        parent::afterValidate();
        foreach($this->entries as $key => $entry){
            $field_prefix = \CHtml::modelName($this) . "[entries][{$key}]";
            foreach($entry->getErrors() as $err_attr => $err){
                $this->setFrontEndError("{$field_prefix}_{$err_attr}");
            }
        }
    }
}
