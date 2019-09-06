<?php

/**
 * Created by PhpStorm.
 * User: petergallagher
 * Date: 18/03/15
 * Time: 11:43.
 */
class UniqueSiteFirmValidator extends CValidator
{
    /**
     * @param CModel $object
     * @param string $attribute
     */
    protected function validateAttribute($object, $attribute)
    {
        if (!array_key_exists('site_id', $object->attributes) || !array_key_exists('firm_id', $object->attributes)) {
            $this->addError($object, $attribute, get_class($object).' is missing site_id or firm_id');

            return;
        }

        if ($this->message === null) {
            $this->message = 'Site and Firm must be unique for each object';
        }

        $siteId = $object->site_id;
        $firmId = $object->firm_id;

        $finder = CActiveRecord::model(get_class($object));
        $criteria = new CDbCriteria();
        $criteria->condition = 'site_id = :siteId AND firm_id = :firmId';
        $criteria->params = array('siteId' => $siteId, 'firmId' => $firmId);
        $found = $finder->findAll($criteria);

        if (count($found) === 0) {
            //No items found, so must be unique
            return;
        }

        if (count($found) > 1) {
            //More than one item found, definitely not unique
            $this->addError($object, $attribute, $this->message);

            return;
        }

        $foundObject = array_shift($found);
        if ($foundObject->id !== $object->id) {
            //Object exists and is not the one currently be validated so not unique
            $this->addError($object, $attribute, $this->message);

            return;
        }
    }
}
