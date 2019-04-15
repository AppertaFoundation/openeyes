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
            $criteria->join .= " join address ad on ad.contact_id = t.id";
            $params = array();
            if (isset($_GET['term']) && $term = $_GET['term']) {

                $criteria->addCondition(array(
                    'LOWER(first_name) LIKE :term',
                    'LOWER(last_name) LIKE :term',
                    'LOWER(cl.name) LIKE :term',
                    'LOWER(t.national_code) LIKE :term',
                    'LOWER(ad.address1) LIKE :term',
                    'LOWER(ad.address2) LIKE :term',
                    'LOWER(ad.postcode) LIKE :term',
                    'LOWER(last_name) LIKE :term'), 'OR');
                $criteria->addCondition(array('cl.is_private = 0'));
                $criteria->addCondition(array('t.active = 1'));
                $params[':term'] = '%' . strtolower(strtr($term, array('%' => '\%'))) . '%';
            }
            if(isset($_GET['filter']) && $contact_label_id = $_GET['filter']){
                $contact_label = \ContactLabel::model()->findByPk($contact_label_id);
                $criteria->addCondition(array(
                        'cl.name = ' . '"'. $contact_label->name . '"'
                    )
                );
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
            'label' => $contact->getFullName() .
                (isset($contact->label) && isset($contact->label->name) ?
                    " (" . $contact->label->name . ")" : "")
                . (isset($contact->national_code) ? "(" . $contact->national_code . ")" : "") .
                " " . ($contact->address ? $contact->address->getLetterLine() : ""),
            'id' => $contact['id'],
            'name' => $contact->getFullName(),
            'email' => $contact->address ? $contact->address->email : "",
            'phone' => $contact->primary_phone,
            'address' => $contact->address ? $contact->address->getLetterLine() : "",
            'contact_label' => $contact->label ? $contact->label->name : "",
        );
    }

    public function actionContactPage()
    {
        $this->renderPartial('//contacts/add_new_contact_assignment', array(), false, true);
    }

    public function actionSaveNewContact()
    {
        if (\Yii::app()->request->isAjaxRequest) {
            if (isset($_POST['data'])) {
                $transaction = \Yii::app()->db->beginTransaction();

                $data = json_decode($_POST['data']);
                $contact = new \Contact();
                $contact->first_name = $data->first_name;
                $contact->last_name = $data->last_name;
                $contact->primary_phone = $data->primary_phone;
                $contact->contact_label_id = $data->contact_label_id;
                $contact->active = $data->active;

                $address = new \Address();
                $address->address1 = $data->address1;
                $address->address2 = $data->address2;
                $address->city = $data->city;
                $address->email = $data->email;
                $address->postcode = $data->postcode;
                $address->country_id = $data->country;
                $address->address_type_id = 3;


                $contact->save();
                $address->contact_id = $contact->id;
                $address->save();

                $transaction->commit();
            }
            echo CJSON::encode($this->contactStructure($contact));
        }
    }
}