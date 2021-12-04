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
class ContactBehavior extends CActiveRecordBehavior
{
    public function getLetterAddress($params = array(), $contact = null)
    {
        if (@$params['contact']) {
            $contactRelation = @$params['contact'];
            $contact = $this->owner->$contactRelation;
        } else {
            $contact = isset($this->owner->contact) ? $this->owner->contact : $this->owner;
        }
        $address = isset($contact->correspondAddress) ? $contact->correspondAddress : $contact->address;

        return $this->formatLetterAddress($contact, $address, $params);
    }

    protected function formatLetterAddress($contact, $address, $params = array())
    {
        if ($address) {
            if (method_exists($this->owner, 'getLetterArray')) {
                $address = $this->owner->getLetterArray(@$params['include_country']);
            } else {
                $address = $address->getLetterArray(@$params['include_country']);
            }

            if (@$params['include_label'] && $contact->label) {
                $address = array_merge(array($contact->label->name), $address);
            }

            if (@$params['include_name']) {
                if (method_exists($this->owner, 'getCorrespondenceName')) {
                    $correspondenceName = $this->owner->correspondenceName;
                    if (!is_array($correspondenceName)) {
                        $correspondenceName = array($correspondenceName);
                    }
                    $address = array_merge($correspondenceName, $address);
                } else {
                    $address = array_merge(array($contact->fullName), $address);
                }
            }

            if (@$params['include_telephone'] && isset($this->owner->telephone) && $this->owner->telephone) {
                $address[] = "Tel: {$this->owner->telephone}";
            }

            if (@$params['include_fax'] && isset($this->owner->fax) && $this->owner->fax) {
                $address[] = "Fax: {$this->owner->fax}";
            }

            if (@$params['delimiter']) {
                $address = implode($params['delimiter'], $address);
            }

            if (@$params['include_prefix']) {
                if ($this->owner->prefix) {
                    return $this->owner->prefix.': '.$address;
                }
            }

            return $address;
        }

        return false;
    }

    public function getLetterIntroduction($params = array())
    {
        if (@$params['nickname'] && $this->owner->contact->nick_name) {
            return 'Dear '.$this->owner->contact->nick_name.',';
        }

        if ($this->owner->getSalutationName()) {
            return 'Dear '.$this->owner->getSalutationName().',';
        }

        return 'Dear Sir/Madam,';
    }

    public function getFullName()
    {
        return $this->owner->contact->getFullName();
    }

    public function getReversedFullName()
    {
        return $this->owner->contact->getReversedFullName();
    }

    public function getSalutationName()
    {
        return $this->owner->contact->getSalutationName();
    }

    public function isDeceased()
    {
        if (isset($this->owner->is_deceased) && $this->owner->is_deceased) {
            return true;
        }
    }

    /* this can be overridden by models that use this behavior */
    public function getPrefix()
    {
    }

    public function getMetadata($key)
    {
        if ($cm = ContactMetadata::model()->find('contact_id=? and `key`=?', array($this->owner->contact_id, $key))) {
            return $cm->value;
        }

        return false;
    }

    public function setMetadata($key, $value)
    {
        if (!$cm = ContactMetadata::model()->find('contact_id=? and `key`=?', array($this->owner->contact_id, $key))) {
            $cm = new ContactMetadata();
            $cm->contact_id = $this->owner->contact_id;
            $cm->key = $key;
        }
        $cm->value = $value;

        if (!$cm->save()) {
            throw new Exception('Unable to save contact metadata: '.print_r($cm->getErrors(), true));
        }
    }

    public function addAddress($address)
    {
        if ($this->owner->isNewRecord) {
            throw new Exception('Cannot add address to unsaved contact object');
        }
        $address->contact_id = $this->owner->contact->id;
        $address->save();
    }

    public function beforeSave($event)
    {
        if ($this->owner->isNewRecord) {
            // create a base contact object
            if ($this->owner->hasAttribute('contact_id') && !$this->owner->contact_id) {
                $contact = new Contact();
                $contact->created_institution_id = Yii::app()->session['selected_institution_id'];
                if (!$contact->email) {
                    $contact->email = null;
                }
                if (!$contact->save(false)) {
                    throw new Exception('cannot save contact: '.print_r($contact->getErrors(), true));
                }
                $this->owner->contact_id = $contact->id;
            }
        }
    }

    public function afterDelete($event)
    {
        if ($this->owner->contact_id && get_class($this->owner) != 'ContactLocation') {
            Address::model()->deleteAll('contact_id = :contact_id', array(':contact_id' => $this->owner->contact_id));
            Contact::model()->deleteByPk($this->owner->contact_id);
        }

        parent::afterDelete($event);
    }
}
