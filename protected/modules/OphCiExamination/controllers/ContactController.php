<?php


namespace OEModule\OphCiExamination\controllers;


use CDbCriteria;
use CJSON;
use Disorder;

class ContactController extends \BaseController
{
    public function accessRules()
    {
        return array(
            array('allow', 'users' => array('@')),
        );
    }

    /**
     * Lists all disorders for a given search term.
     */
    public function actionAutocomplete()
    {
        if (\Yii::app()->request->isAjaxRequest) {
            $criteria = new CDbCriteria();
            $criteria->join = "join contact_label cl on cl.id = t.contact_label_id";
            $params = array();
            if (isset($_GET['term']) && $term = $_GET['term']) {
                $criteria->addCondition(array(
                    'LOWER(first_name) LIKE :term',
                    'LOWER(last_name) LIKE :term',
                    'LOWER(cl.name) LIKE :term',
                    'LOWER(last_name) LIKE :term'), 'OR');
                $criteria->addCondition(array('cl.is_private = 0'));
                $params[':term'] = '%' . strtolower(strtr($term, array('%' => '\%'))) . '%';
            }
            $criteria->order = 'cl.name';

            // Limit results
            $criteria->limit = '200';

            $criteria->params = $params;

            $contacts = \Contact::model()->findAll($criteria);
            $return = array();
            foreach ($contacts as $contact) {
                $return[] = $this->contactStructure($contact);
            }
            echo CJSON::encode($return);
        }
    }

    /**
     * Generate array structure of disorder for JSON structure return
     *
     * @param Disorder $disorder
     * @return array
     */
    protected function contactStructure(\Contact $contact)
    {
        return array(
            'label' => $contact['first_name'] . " (" . $contact->label->name . ")",
            'id' => $contact['id'],
            'name' => $contact->getFullName(),
            'email' => $contact->address ? $contact->address->email : "",
            'phone' => $contact->primary_phone,
            'address' => $contact->address ? $contact->address->getLetterLine() : "",
            'contact_label' => $contact->label ? $contact->label->name : "",
        );
    }
}