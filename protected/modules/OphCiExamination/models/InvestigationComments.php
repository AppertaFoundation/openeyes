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

/**
 * Class InvestigationComments
 *
 * @package OEModule\OphCiExamination\models
 * @property int $id
 * @property int $investigation_code
 * @property string $comments
 * @property OphCiExamination_Investigation_Codes $options
 */
class InvestigationComments extends \BaseActiveRecord
{
    const SELECTION_LABEL_FIELD = 'comments';

    public function tableName()
    {
        return 'ophciexamination_investigation_comments';
    }

    public function rules()
    {
        return [
            ['comments', 'required'],
            ['id, investigation_code, comments', 'safe'],
        ];
    }

    public function relations()
    {
        return [
            'investigation_codes' => [self::BELONGS_TO, OphCiExamination_Investigation_Codes::class, 'investigation_code']
        ];
    }

    public function attributeLabels()
    {
        return array(
            'investigation_code' => 'Investigation Code',
            'comments' => 'Comments',
        );
    }
}
