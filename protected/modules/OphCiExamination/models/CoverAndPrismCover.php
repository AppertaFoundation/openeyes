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

use OEModule\OphCiExamination\models\traits\HasChildrenWithEventScopeValidation;
use OEModule\OphCiExamination\widgets\CoverAndPrismCover as CoverAndPrismCoverWidget;

class CoverAndPrismCover extends \BaseEventTypeElement
{
    use traits\CustomOrdering;
    use HasChildrenWithEventScopeValidation;
    use \LoadFromExistingWithRelation;

    public $widgetClass = CoverAndPrismCoverWidget::class;
    protected $auto_update_relations = true;
    protected $auto_validate_relations = true;
    protected const EVENT_SCOPED_CHILDREN = ['entries' => 'with_head_posture'];

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'et_ophciexamination_coverandprismcover';
    }

    public function rules()
    {
        return [
            ['event_id, entries, comments', 'safe'],
            ['entries', \OERequiredIfOtherAttributesEmptyValidator::class,
                'other_attributes' => ['comments'],
                'message' => '{attribute} cannot be blank without comment'],
            ['comments', 'length', 'min' => 5]
        ];
    }

    /**
     * @return array
     */
    public function relations()
    {
        return [
            'event' => [self::BELONGS_TO, 'Event', 'event_id'],
            'user' => [self::BELONGS_TO, 'User', 'created_user_id'],
            'usermodified' => [self::BELONGS_TO, 'User', 'last_modified_user_id'],
            'entries' => [self::HAS_MANY, CoverAndPrismCover_Entry::class, 'element_id']
        ];
    }

    public function canCopy()
    {
        return true;
    }

    public function attributeLabels()
    {
        return [
            'comments' => 'Comments',
        ];
    }

    public function getLetter_string()
    {

        return \Yii::app()->controller->renderPartial(
            'application.modules.OphCiExamination.views.default.letter.CoverAndPrismCover',
            [
                'element' => $this
            ],
            true
        );
    }
}
