<?php

namespace OEModule\PASAPI\resources;

use CommissioningBodyType;

/**
 * OpenEyes.
 *
 * (C) OpenEyes Foundation, 2019
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 *
 * @property Contact $Contact
 */
class CommissioningBody extends BaseResource
{
    protected static $resource_type = 'CommissioningBody';
    protected static $model_class = 'CommissioningBody';
    public $isNewResource;

    /**
     * @return bool
     */
    public function shouldValidateRequired()
    {
        return $this->isNewResource || !$this->partial_record;
    }

    /**
     * As a primary resource (i.e. mapped to external resource) we need to ensure we have an id for tracking
     * the resource in the system.
     *
     * @return bool
     */
    public function validate()
    {
        if (!$this->id) {
            $this->addError('Resource ID required');
        }

        return parent::validate();
    }

    public function save()
    {
        $assignment = $this->getAssignment();
        $model = $assignment->getInternal(false, 'code');
        $this->isNewResource = $model->isNewRecord;

        if (!$this->validate()) {
            return false;
        }

        $transaction = $this->startTransaction();

        if (($this->isNewResource && $this->update_only) || (!$this->isNewResource && $this->create_only)) {
            return false;
        }

        try {
            if ($this->saveModel($model)) {
                $assignment->internal_id = $model->id;
                $assignment->save();
                $assignment->unlock();

                $this->audit($this->isNewResource ? 'create' : 'update', array('commissioning_body_id' => $model->id));

                if ($transaction) {
                    $transaction->commit();
                }

                return $model->id;
            } elseif ($transaction) {
                $transaction->rollback();
            }
        } catch (\Exception $e) {
            if ($transaction) {
                $transaction->rollback();
            }

            throw $e;
        }
        return false;
    }

    public function saveModel(?\CommissioningBody $model)
    {
        $model->code = $this->getAssignedProperty('Code');
        $model->name = $this->getAssignedProperty('Name');
        $type = CommissioningBodyType::model()->findByAttributes(['shortname' => 'CCG']);
        $model->commissioning_body_type_id = $type->id;

        if (!$model->validate()) {
            $this->addModelErrors($model->getErrors());

            return;
        }

        $model->save();

        if (isset($this->Contact)) {
            $contact = $model->contact;
            $this->Contact->saveModel($contact);
        }

        if (!$this->errors) {
            return true;
        }
        return false;
    }
}
