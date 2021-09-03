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

use OEModule\OphCiExamination\widgets\StrabismusManagement as StrabismusManagementWidget;

/**
 * Class StrabismusManagement
 *
 * @package OEModule\OphCiExamination\models
 * @property int $id
 * @property StrabismusManagement_Entry[] $entries
 * @property string $comments
 */
class StrabismusManagement extends \BaseEventTypeElement
{
    use traits\CustomOrdering;

    protected $auto_update_relations = true;
    protected $auto_validate_relations = true;
    protected $widgetClass = StrabismusManagementWidget::class;

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'et_ophciexamination_strabismusmanagement';
    }

    public function rules()
    {
        return [
            ['event_id, entries, comments', 'safe'],
            ['entries', \OERequiredIfOtherAttributesEmptyValidator::class, 'other_attributes' => ['comments']],
        ];
    }

    /**
     * @return array
     */
    public function relations()
    {
        return [
            'event' => [self::BELONGS_TO, \Event::class, 'event_id'],
            'user' => [self::BELONGS_TO, \User::class, 'created_user_id'],
            'usermodified' => [self::BELONGS_TO, \User::class, 'last_modified_user_id'],
            'entries' => [self::HAS_MANY, StrabismusManagement_Entry::class, 'element_id']
        ];
    }

    public function canCopy()
    {
        return true;
    }

    public function getLetter_string()
    {
        $comments_string = strlen(trim($this->comments)) ? " " . \OELinebreakReplacer::plainTextReplace($this->comments) : "";

        return sprintf(
            "%s: %s%s",
            $this->getElementTypeName(),
            count($this->entries) > 0 ? implode(", ", $this->entries) : "No entries",
            $comments_string
        );
    }
}
