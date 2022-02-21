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
class BaseActiveRecordVersioned extends BaseActiveRecord
{
    private $enable_version = true;
    private $fetch_from_version = false;
    public $version_id = null;
    public $version_date = null;
    /* Disable archiving on save() */

    public function noVersion()
    {
        $this->enable_version = false;

        return $this;
    }

    /* Re-enable archiving on save() */

    public function withVersion()
    {
        $this->enable_version = true;

        return $this;
    }

    /* Fetch from version */

    public function fromVersion()
    {
        $this->fetch_from_version = true;

        return $this;
    }

    /* Disable fetch from version */

    public function notFromVersion()
    {
        $this->fetch_from_version = false;

        return $this;
    }

    public function getTableSchema()
    {
        if ($this->fetch_from_version) {
            return $this->getDbConnection()->getSchema()->getTable($this->tableName().'_version', true);
        }

        return parent::getTableSchema();
    }

    public function getPreviousVersion()
    {
        $condition = 'id = :id';
        $params = array(':id' => $this->id);

        if ($this->version_id) {
            $condition .= ' and version_id < :version_id';
            $params[':version_id'] = $this->version_id;
        }

        return $this->model()->fromVersion()->find(array(
            'condition' => $condition,
            'params' => $params,
            'order' => 'version_id desc',
        ));
    }

    /* Return all previous versions ordered by most recent */

    public function getPreviousVersions()
    {
        $condition = 'id = :id';
        $params = array(':id' => $this->id);

        if ($this->version_id) {
            $condition .= ' and version_id = :version_id';
            $params[':version_id'] = $this->version_id;
        }

        return $this->model()->fromVersion()->findAll(array(
            'condition' => $condition,
            'params' => $params,
            'order' => 'version_id desc',
        ));
    }

    /**
     * Return all previous versions based on criteria
     *
     * @param CDbCriteria $criteria custom query criteria
     * @return array return version data
     */
    public function getPreviousVersionsWithCriteria(CDbCriteria $criteria)
    {
        $criteria->addCondition('id = :id');
        $criteria->params[':id'] = $this->id;

        $criteria->order = 'version_id desc';
        return $this->model()->fromVersion()->findAll($criteria);
    }

    public function getPreviousVersionWithCriteria(CDbCriteria $criteria)
    {
        $criteria->addCondition('id = :id');
        $criteria->params[':id'] = $this->id;

        if ($this->version_id) {
            $criteria->addCondition('version_id = :version_id');
            $criteria->params[':version_id'] = $this->version_id;
        }
        $criteria->order = 'version_id desc';
        return $this->model()->fromVersion()->find($criteria);
    }

    public function getVersionTableSchema()
    {
        return $this->getDbConnection()->getSchema()->getTable($this->tableName().'_version', true);
    }

    public function getCommandBuilder()
    {
        return new OECommandBuilder($this->getDbConnection()->getSchema());
    }

    public function updateByPk($pk, $attributes, $condition = '', $params = array())
    {
        $transaction = $this->dbConnection->beginInternalTransaction();
        try {
            $this->versionToTable($this->commandBuilder->createPkCriteria($this->tableName(), $pk, $condition, $params));
            $result = parent::updateByPk($pk, $attributes, $condition, $params);
            $transaction->commit();

            return $result;
        } catch (Exception $e) {
            $transaction->rollback();
            throw $e;
        }
    }

    public function updateAll($attributes, $condition = '', $params = array())
    {
        $transaction = $this->dbConnection->beginInternalTransaction();
        try {
            $this->versionToTable($this->commandBuilder->createCriteria($condition, $params));
            $result = parent::updateAll($attributes, $condition, $params);
            $transaction->commit();

            return $result;
        } catch (Exception $e) {
            $transaction->rollback();
            throw $e;
        }
    }

    public function deleteByPk($pk, $condition = '', $params = array())
    {
        $transaction = $this->dbConnection->beginInternalTransaction();
        try {
            $this->versionToTable($this->commandBuilder->createPkCriteria($this->tableName(), $pk, $condition, $params));
            $result = parent::deleteByPk($pk, $condition, $params);
            $transaction->commit();

            return $result;
        } catch (Exception $e) {
            $transaction->rollback();
            throw $e;
        }
    }

    public function deleteAll($condition = '', $params = array())
    {
        $transaction = $this->dbConnection->beginInternalTransaction();
        try {
            $this->versionToTable($this->commandBuilder->createCriteria($condition, $params));
            $result = parent::deleteAll($condition, $params);
            $transaction->commit();

            return $result;
        } catch (Exception $e) {
            $transaction->rollback();
            throw $e;
        }
    }

    public function deleteAllByAttributes($attributes, $condition = '', $params = array())
    {
        $transaction = $this->dbConnection->beginInternalTransaction();
        try {
            $this->versionToTable($this->commandBuilder->createColumnCriteria($this->tableName(), $attributes, $condition, $params));
            $result = parent::deleteAllByAttributes($attributes, $condition, $params);
            $transaction->commit();

            return $result;
        } catch (Exception $e) {
            $transaction->rollback();
            throw $e;
        }
    }

    public function save($runValidation = true, $attributes = null, $allow_overriding = false)
    {
        if ($this->version_id) {
            throw new Exception('save() should not be called on versiond model instances.');
        }

        return parent::save($runValidation, $attributes, $allow_overriding);
    }

    public function resetScope($resetDefault = true)
    {
        $this->enable_version = true;
        $this->fetch_from_version = false;

        return parent::resetScope($resetDefault);
    }

    protected function versionToTable(CDbCriteria $criteria)
    {
        if ($this->enable_version) {
            $this->getCommandBuilder()->createInsertFromTableCommand(
                $this->getVersionTableSchema(),
                $this->getTableSchema(),
                $criteria
            )->execute();
        }
    }
}
