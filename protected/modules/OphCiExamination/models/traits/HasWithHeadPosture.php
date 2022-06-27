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

namespace OEModule\OphCiExamination\models\traits;

use OEModule\OphCiExamination\models\HeadPosture;

/**
 * Trait HasWithHeadPosture
 *
 * Common model properties for tracking when head posture usage should be recorded on an element.
 *
 * @package OEModule\OphCiExamination\models\traits
 * @property $with_head_posture
 * @property string $display_with_head_posture
 */
trait HasWithHeadPosture
{
    public static $WITH_HEAD_POSTURE = '1';
    public static $DISPLAY_WITH_HEAD_POSTURE = 'Used';
    public static $WITHOUT_HEAD_POSTURE = '0';
    public static $DISPLAY_WITHOUT_HEAD_POSTURE = 'Not Used';

    public function getWith_head_posture_options()
    {
        return [
            ['id' => self::$WITH_HEAD_POSTURE, 'name' => self::$DISPLAY_WITH_HEAD_POSTURE],
            ['id' => self::$WITHOUT_HEAD_POSTURE, 'name' => self::$DISPLAY_WITHOUT_HEAD_POSTURE],
        ];
    }

    public function eventScopeValidation($elements)
    {
        if ($this->with_head_posture !== static::$WITH_HEAD_POSTURE || $this->with_head_posture !== static::$WITHOUT_HEAD_POSTURE) {
            return;
        }

        if (!$this->headPostureInElements($elements)) {
            $this->addError('with_head_posture', 'CHP has not been recorded in the examination.');
        }
    }

    public function getDisplay_with_head_posture()
    {
        return $this->convertWithHeadPostureRecordToDisplay($this->with_head_posture) ?? '-';
    }

    public function getDisplay_labelled_with_head_posture()
    {
        return $this->withHeadPostureRecorded() ?
            sprintf("%s %s", $this->getAttributeLabel('with_head_posture'), $this->display_with_head_posture) :
            '';
    }

    /**
     * Common rules for the correction type attribute(s)
     * @return array
     */
    protected function rulesForWithHeadPosture()
    {
        return [
            [
                'with_head_posture', 'in',
                'range' => [self::$WITH_HEAD_POSTURE, self::$WITHOUT_HEAD_POSTURE],
                'message' => '{attribute} is invalid'
            ],
            [
                'with_head_posture', 'safe'
            ],
        ];
    }

    protected function convertWithHeadPostureRecordToDisplay($value)
    {
        return [
            self::$WITH_HEAD_POSTURE => self::$DISPLAY_WITH_HEAD_POSTURE,
            self::$WITHOUT_HEAD_POSTURE => self::$DISPLAY_WITHOUT_HEAD_POSTURE
        ][$value] ?? null;
    }

    protected function headPostureInElements($elements)
    {
        return in_array(
            HeadPosture::class,
            array_map('get_class', $elements)
        );
    }

    protected function withHeadPostureRecorded()
    {
        return in_array($this->with_head_posture, [static::$WITHOUT_HEAD_POSTURE, static::$WITH_HEAD_POSTURE]);
    }
}
