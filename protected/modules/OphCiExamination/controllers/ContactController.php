<?php


namespace OEModule\OphCiExamination\controllers;

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
            $criteria = new \CDbCriteria();
            $criteria->join = "left join contact_label cl on cl.id = t.contact_label_id ";
            $criteria->join .= "left join address ad on ad.contact_id = t.id";
            if (isset($_GET['term']) && $term = $_GET['term']) {

                $criteria->addSearchCondition('last_name', $term, true, 'OR');
                $criteria->addSearchCondition('first_name', $term, true, 'OR');
                $criteria->addSearchCondition('cl.name', $term, true, 'OR');
                $criteria->addSearchCondition('t.national_code', $term, true, 'OR');
                $criteria->addSearchCondition('ad.address1', $term, true, 'OR');
                $criteria->addSearchCondition('ad.address2', $term, true, 'OR');
                $criteria->addSearchCondition('ad.postcode', $term, true, 'OR');
                $criteria->addSearchCondition('last_name', $term, true, 'OR');
                $criteria->addCondition(array('cl.is_private = 0'));
                $criteria->addCondition(array('t.active = 1'));
            }
            if (isset($_GET['filter'])) {
                $contact_label_id = $_GET['filter'];
                if ($contact_label_id != 'false') {
                    $contact_label = \ContactLabel::model()->findByPk($contact_label_id);
                    $criteria->addCondition(array(
                            'cl.name = ' . '"' . $contact_label->name . '"'
                        )
                    );
                }
            }
            $criteria->order = 'cl.name';

            // Limit results
            $criteria->limit = '200';

            $contacts = \Contact::model()->findAll($criteria);
            $return = array();
            foreach ($contacts as $contact) {
                $return[] = $this->contactStructure($contact);
            }
            echo \CJSON::encode($return);
        }
    }

    /**
     * Generate array structure of disorder for JSON structure return
     *
     * @param \Contact $contact
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
        $selected_contact_type = null;
        if (isset($_GET['selected_contact_type'])) {
            $selected_contact_type = $_GET['selected_contact_type'];
        }
        $this->renderPartial('//contacts/add_new_contact_assignment',
            array('selected_contact_type' => $selected_contact_type),
            false,
            true
        );
    }

    public function actionSaveNewContact()
    {
        if (\Yii::app()->request->isAjaxRequest) {
            if (isset($_POST['data'])) {
                $transaction = \Yii::app()->db->beginTransaction();
                $errors = [];

                $data = json_decode($_POST['data']);
                $contact = new \Contact();
                $contact->scenario = 'self_register';
                $contact->first_name = $data->first_name;
                $contact->last_name = $data->last_name;
                $contact->primary_phone = $data->primary_phone;
                $contact->contact_label_id = $data->contact_label_id;
                $contact->active = 1;

                $address = new \Address();
                $address->address1 = $data->address1;
                $address->address2 = $data->address2;
                $address->city = $data->city;
                $address->email = $data->email;
                $address->postcode = $data->postcode;
                $address->country_id = $data->country;
                $address->address_type_id = 3;


                if (!$contact->save()) {
                    $errors = $contact->getErrors();
                }
                $address->contact_id = $contact->id;
                if (!$address->save()) {
                    $errors = array_merge($errors, $address->getErrors());
                }

                if($data->contact_label_id == ""){
                    $errors['missing_contact_label'] = "Please select a Contact Type";
                }

                if (trim($data->address1) == "" && trim($data->primary_phone) == "" && trim($data->email) == "") {
                    $errors['missing_address'] = "Address or phone number or an email should be provided";
                }
                if (!empty($errors)) {
                    $transaction->rollback();
                    echo \CJSON::encode(['errors' => $errors]);
                } else {
                    $transaction->commit();
                    echo \CJSON::encode($this->contactStructure($contact));
                }
            }

        }
    }
}