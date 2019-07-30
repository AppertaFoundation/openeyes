<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2019
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the Affero GNU General Public
 * License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later
 * version. OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the
 * implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more
 * details. You should have received a copy of the Affero GNU General Public License along with OpenEyes in a file titled
 * COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU General Public License V3.0
 */

/**
 * Class UserFeatureTourState
 *
 * Simple model to leverage basic updates for tracking the state of any given feature tour
 * for a user. A tour can be marked as complete, or can be slept until a given date.
 *
 * @property $user_id
 * @property $tour_id
 * @property boolean $completed
 * @property date $sleep_until
 */
class UserFeatureTourState extends CActiveRecord
{
    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return Tag the static model class
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
        return 'user_feature_tour_state';
    }

    /**
     * @param $user_id
     * @param $tour_id
     *
     * @return static
     */
    public function findOrCreate($user_id, $tour_id)
    {
        if (!$instance = $this->findByAttributes(array('user_id' => $user_id, 'tour_id' => $tour_id))) {
            $instance = new static;
            $instance->user_id = $user_id;
            $instance->tour_id = $tour_id;
        };
        return $instance;
    }
}
