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

/**
 * This is the model class for table "anaesthetic_agent".
 *
 * The followings are the available columns in table 'anaesthetic_agent':
 *
 * @property int $id
 * @property string $name
 * @property int $display_order
 */
class AnaestheticAgent extends BaseActiveRecordVersioned
{
    /**
     * Returns the static model of the specified AR class.
     *
     * @return AnaestheticAgent the static model class
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
        return 'anaesthetic_agent';
    }

    public function defaultScope()
    {
        return array('order' => $this->getTableAlias(true, false).'.display_order');
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('name', 'required'),
            array(
                'name',
                'ext.validators.NotContainedInValidator',
                'model' => 'OphTrOperationnote_OperationAnaestheticAgent',
                'message' => $this->getCleanAttribute('name').' has been used in existing operation notes and cannot be edited',
            ),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        return array(
            'siteSubspecialtyAssignments' => array(self::HAS_MANY, 'SiteSubspecialtyAnaestheticAgent', 'anaesthetic_agent_id'),
            'siteSubspecialtyAssignmentDefaults' => array(self::HAS_MANY, 'SiteSubspecialtyAnaestheticAgentDefault', 'anaesthetic_agent_id'),
        );
    }

    public function behaviors()
    {
        return array(
            'LookupTable' => 'LookupTable',
        );
    }
}
