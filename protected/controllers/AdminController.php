<?php
/**
 * OpenEyes
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2012
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2012, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

class AdminController extends BaseAdminController
{
	public $layout = 'admin';
	public $items_per_page = 30;

	public function actionIndex()
	{
		$this->redirect(array('/admin/users'));
	}

	public function actionDrugs()
	{
		$pagination = $this->initPagination(Drug::model());

		$this->render('/admin/drugs',array(
				'drugs' => $this->getItems(array(
						'model' => 'Drug',
						'page' => $pagination->currentPage ,
					)),
				'pagination' => $pagination,
			));
	}

	public function actionAddDrug()
	{
		$drug=new Drug('create');

		if (!empty($_POST)) {

			$drug->attributes = $_POST['Drug'];

			if (!$drug->validate()) {
				$errors = $drug->getErrors();
			} else {
				if (!$drug->save()) {
					throw new Exception("Unable to save drug: ".print_r($drug->getErrors(),true));
				}

				if(isset($_POST['allergies']))
				{
					$posted_allergy_ids = $_POST['allergies'];

					//add new allergy mappings
					foreach($posted_allergy_ids as $asign){
						$allergy_assignment = new DrugAllergyAssignment();
						$allergy_assignment->drug_id=$drug->id;
						$allergy_assignment->allergy_id=$asign;
						$allergy_assignment->save();
					}
				}

				$this->redirect('/admin/drugs/'.ceil($drug->id/$this->items_per_page));
			}
		}

		$this->render('/admin/adddrug',array(
				'drug' => $drug,
				'errors' => @$errors,
			));
	}

	public function actionEditDrug($id)
	{
		if (!$drug = Drug::model()->findByPk($id)) {
			throw new Exception("Drug not found: $id");
		}
		$drug->scenario = 'update';

		if (!empty($_POST)) {

			$drug->attributes = $_POST['Drug'];

			if (!$drug->validate()) {
				$errors = $drug->getErrors();
			} else {
				if (!$drug->save()) {
					throw new Exception("Unable to save drug: ".print_r($drug->getErrors(),true));
				}

				$posted_allergy_ids = array();

				if(isset($_POST['allergies'])){
					$posted_allergy_ids = $_POST['allergies'];
				}

				$criteria=new CDbCriteria;
				$criteria->compare('drug_id',$drug->id);
				$allergy_assignments = DrugAllergyAssignment::model()->findAll($criteria);

				$allergy_assignment_ids = array();
				foreach($allergy_assignments as $allergy_assignment){
					$allergy_assignment_ids[]=$allergy_assignment->allergy_id;
				}

				$allergy_assignment_ids_to_delete = array_diff($allergy_assignment_ids,$posted_allergy_ids);
				$posted_allergy_ids_to_assign =  array_diff($posted_allergy_ids , $allergy_assignment_ids);

				//add new allergy mappings
				foreach($posted_allergy_ids_to_assign as $asign){
					$allergy_assignment = new DrugAllergyAssignment();
					$allergy_assignment->drug_id=$drug->id;
					$allergy_assignment->allergy_id=$asign;
					$allergy_assignment->save();
				}

				//delete redundant allergy mappings
				foreach($allergy_assignments as $asigned){
					if(in_array($asigned->allergy_id,$allergy_assignment_ids_to_delete)){
						$asigned->delete();
					}
				}

				$this->redirect('/admin/drugs/'.ceil($drug->id/$this->items_per_page));
			}
		}

		$this->render('/admin/editdrug',array(
				'drug' => $drug,
				'errors' => @$errors,
			));
	}

	public function actionUsers($id=false)
	{
		Audit::add('admin-User','list');
		$pagination = $this->initPagination(User::model());

		$this->render('/admin/users',array(
			'users' => $this->getItems(array(
				'model' => 'User',
				'page' => $pagination->currentPage ,
			)),
			'pagination' => $pagination,
		));
	}

	public function actionAddUser()
	{
		$user = new User;

		if (!empty($_POST)) {
			$user->attributes = $_POST['User'];

			if (!$user->validate()) {
				$errors = $user->getErrors();
			} else {
				if (!$user->save()) {
					throw new Exception("Unable to save user: ".print_r($user->getErrors(),true));
				}

				$user->saveRoles($_POST['User']['roles']);

				Audit::add('admin-User','add',serialize($_POST));
				$this->redirect('/admin/users/'.ceil($user->id/$this->items_per_page));
			}
		}

		$user->password = '';

		$this->render('/admin/adduser',array(
			'user' => $user,
			'errors' => @$errors,
		));
	}


	public function actionEditUser($id)
	{
		if (!$user = User::model()->findByPk($id)) {
			throw new Exception("User not found: $id");
		}

		if (!empty($_POST)) {
			if (!$_POST['User']['password']) {
				unset($_POST['User']['password']);
			}

			$user->attributes = $_POST['User'];

			if (!$user->validate()) {
				$errors = $user->getErrors();
			} else {
				if (!$user->save()) {
					throw new Exception("Unable to save user: ".print_r($user->getErrors(),true));
				}

				if (!$contact = $user->contact) {
					$contact = new Contact;
				}

				$contact->title = $user->title;
				$contact->first_name = $user->first_name;
				$contact->last_name = $user->last_name;
				$contact->qualifications = $user->qualifications;

				if (!$contact->save()) {
					throw new Exception("Unable to save user contact: ".print_r($contact->getErrors(),true));
				}

				if (!$user->contact) {
					$user->contact_id = $contact->id;
					if (!$user->save()) {
						throw new Exception("Unable to save user: ".print_r($user->getErrors(),true));
					}
				}

				$user->saveRoles($_POST['User']['roles']);

				Audit::add('admin-User','edit',serialize(array_merge(array('id'=>$id),$_POST)));

				$this->redirect('/admin/users/'.ceil($user->id/$this->items_per_page));
			}
		} else {
			Audit::add('admin-User','view',$id);
		}

		$user->password = '';

		$this->render('/admin/edituser',array(
			'user' => $user,
			'errors' => @$errors,
		));
	}

	public function actionDeleteUsers() {
		$result = 1;

		if (!empty($_POST['users'])) {
			foreach (User::model()->findAllByPk($_POST['users']) as $user) {
				try {
					if (!$user->delete()) {
						$result = 0;
					}
				} catch (Exception $e) {
					$result = 0;
				}

				if ($result) {
					Audit::add('admin-User','delete',serialize($_POST));
				}
			}
		}

		echo $result;
	}

	public function actionFirms($id=false)
	{
		Audit::add('admin-Firm','list');

		$pagination = $this->initPagination(Firm::model());

		$this->render('/admin/firms',array(
			'firms' => $this->getItems(array(
				'model' => 'Firm',
				'page' => $pagination->currentPage,
			)),
			'pagination' => $pagination,
		));
	}

	public function actionAddFirm()
	{
		$firm = new Firm;

		if (!empty($_POST)) {
			$firm->attributes = $_POST['Firm'];

			if (!$firm->validate()) {
				$errors = $firm->getErrors();
			} else {
				if (!$firm->save()) {
					throw new Exception("Unable to save firm: ".print_r($firm->getErrors(),true));
				}
				Audit::add('admin-Firm','add',serialize($_POST));
				$this->redirect('/admin/firms/'.ceil($firm->id/$this->items_per_page));
			}
		}

		$this->render('/admin/editfirm',array(
			'firm' => $firm,
			'errors' => @$errors,
		));
	}

	public function actionEditFirm($id)
	{
		if (!$firm= Firm::model()->findByPk($id)) {
			throw new Exception("Firm not found: $id");
		}

		if (!empty($_POST)) {
			$firm->attributes = $_POST['Firm'];

			if (!$firm->validate()) {
				$errors = $firm->getErrors();
			} else {
				if (!$firm->save()) {
					throw new Exception("Unable to save firm: ".print_r($firm->getErrors(),true));
				}
				Audit::add('admin-Firm','edit',serialize(array_merge(array('id'=>$id),$_POST)));
				$this->redirect('/admin/firms/'.ceil($firm->id/$this->items_per_page));
			}
		} else {
			Audit::add('admin-Firm','view',$id);
		}

		$this->render('/admin/editfirm',array(
			'firm' => $firm,
			'errors' => @$errors,
		));
	}

	public function getItems($params)
	{
		$model = $params['model']::model();
		$page = $params['page'];

		$criteria = new CDbCriteria;
		if (isset($params['order'])) {
			$criteria->order = $params['order'];
		} else {
			$criteria->order = 'id asc';
		}
		$criteria->offset = $page * $this->items_per_page;
		$criteria->limit = $this->items_per_page;

		if (!empty($_REQUEST['search'])) {
			if($params['model']=='User'){
				$criteria->addSearchCondition("username",$_REQUEST['search'],true,'OR');
				$criteria->addSearchCondition("first_name",$_REQUEST['search'],true,'OR');
				$criteria->addSearchCondition("last_name",$_REQUEST['search'],true,'OR');
			}
			else if($params['model']=='Drug'){
				$criteria->addSearchCondition("name",$_REQUEST['search'],true,'OR');
			}
		}
		return array(
			'items' => $model->findAll($criteria),
		);
	}

	public function actionLookupUser()
	{
		Yii::app()->event->dispatch('lookup_user', array('username' => $_GET['username']));

		if ($user = User::model()->find('username=?',array($_GET['username']))) {
			echo $user->id;
		} else {
			echo "NOTFOUND";
		}
	}

	public function actionContacts($id=false)
	{
		$contacts = $this->searchContacts();
		Audit::add('admin-Contact','list');

		$this->render('/admin/contacts',array('contacts'=>@$contacts));
	}

	public function actionContactlabels($id=false)
	{
		Audit::add('admin-ContactLabel','list');
		$pagination = $this->initPagination(ContactLabel::model());

		$this->render('/admin/contactlabels',array(
			'contactlabels' => $this->getItems(array(
				'model' => 'ContactLabel',
				'order' => 'name asc',
				'page' => $pagination->currentPage,
			)),
			'pagination' => $pagination
		));
	}

	public function searchContacts()
	{
		$criteria = new CDbCriteria;
		Audit::add('admin-Contact','search',@$_GET['q']);

		$ex = explode(' ',@$_GET['q']);

		if (empty($ex)) {
			throw new Exception("Empty search query string, this shouldn't happen");
		}

		if (count($ex) == 1) {
			$criteria->addSearchCondition("lower(`t`.first_name)",strtolower(@$_GET['q']),false);
			$criteria->addSearchCondition("lower(`t`.last_name)",strtolower(@$_GET['q']),false,'OR');
		} elseif (count($ex) == 2) {
			$criteria->addSearchCondition("lower(`t`.first_name)",strtolower(@$ex[0]),false);
			$criteria->addSearchCondition("lower(`t`.last_name)",strtolower(@$ex[1]),false);
		} elseif (count($ex) >= 3) {
			$criteria->addSearchCondition("lower(`t`.title)",strtolower(@$ex[0]),false);
			$criteria->addSearchCondition("lower(`t`.first_name)",strtolower(@$ex[1]),false);
			$criteria->addSearchCondition("lower(`t`.last_name)",strtolower(@$ex[2]),false);
		}

		if (@$_GET['label']) {
			$criteria->compare('contact_label_id',@$_GET['label']);
		}

		$criteria->order = 'title, first_name, last_name';
		$pagination = $this->initPagination(Contact::model() , $criteria);

		$contacts = Contact::model()->findAll($criteria);

		if (count($contacts) == 1) {
			foreach ($contacts as $contact) {}
			$this->redirect(array('/admin/editContact?contact_id='.$contact->id));
			return;
		}

		return array(
			'contacts' => $contacts,
			'pagination' =>$pagination
		);
	}

	public function actionEditContact()
	{
		if (!$contact = Contact::model()->findByPk(@$_GET['contact_id'])) {
			throw new Exception("Contact not found: ".@$_GET['contact_id']);
		}

		if (!empty($_POST)) {
			$contact->attributes = $_POST['Contact'];

			if (!$contact->validate()) {
				$errors = $contact->getErrors();
			} else {
				if (!$contact->save()) {
					throw new Exception("Unable to save contact: ".print_r($contact->getErrors(),true));
				}
				Audit::add('admin-Contact','edit',serialize(array_merge(array('id'=>@$_GET['contact_id']),$_POST)));
				$this->redirect('/admin/contacts?q='.$contact->fullName);
			}
		} else {
			Audit::add('admin-Contact','view',@$_GET['contact_id']);
		}

		$this->render('/admin/editcontact',array(
			'contact' => $contact,
			'errors' => @$errors,
		));
	}

	public function actionContactLocation()
	{
		if (!$cl = ContactLocation::model()->findByPk(@$_GET['location_id'])) {
			throw new Exception("ContactLocation not found: ".@$_GET['location_id']);
		}

		Audit::add('admin-ContactLocation','view',@$_GET['location_id']);

		$this->render('/admin/contactlocation',array(
			'location' => $cl,
		));
	}

	public function actionRemoveLocation()
	{
		if (!$cl = ContactLocation::model()->findByPk(@$_POST['location_id'])) {
			throw new Exception("ContactLocation not found: ".@$_POST['location_id']);
		}

		if (count($cl->patients) >0) {
			echo "0";
			return;
		}

		if (!$cl->delete()) {
			echo "-1";
			return;
		}

		Audit::add('admin-ContactLocation','delete',@$_POST['location_id']);

		return "1";
	}

	public function actionAddContactLocation()
	{
		if (!$contact = Contact::model()->findByPk(@$_GET['contact_id'])) {
			throw new Exception("Contact not found: ".@$_GET['contact_id']);
		}

		$errors = array();
		$sites = array();

		if (!empty($_POST)) {
			if (!$institution = Institution::model()->findByPk(@$_POST['institution_id'])) {
				$errors['institution_id'] = array("Please select an institution");
			} else {
				$criteria = new CDbCriteria;
				$criteria->compare('institution_id',@$_POST['institution_id']);
				$criteria->order = 'name asc';
				$sites = CHtml::listData(Site::model()->findAll($criteria),'id','name');
			}

			if (empty($errors)) {
				$cl = new ContactLocation;
				$cl->contact_id = $contact->id;

				if ($site = Site::model()->findByPk(@$_POST['site_id'])) {
					$cl->site_id = $site->id;
				} else {
					$cl->institution_id = $institution->id;
				}

				if (!$cl->save()) {
					$errors = array_merge($errors,$cl->getErrors());
				} else {
					Audit::add('admin-ContactLocation','add',serialize($_POST));
					$this->redirect(array('/admin/editContact?contact_id='.$contact->id));
				}
			}
		}

		$this->render('/admin/addcontactlocation',array(
			'contact' => $contact,
			'errors' => $errors,
			'sites' => $sites,
		));
	}

	public function actionGetInstitutionSites()
	{
		if (!$institution = Institution::model()->findByPk(@$_GET['institution_id'])) {
			throw new Exception("Institution not found: ".@$_GET['institution_id']);
		}

		Audit::add('admin-Institution>Site','view',@$_GET['institution_id']);

		echo json_encode(CHtml::listData($institution->sites,'id','name'));
	}

	public function actionInstitutions($id=false)
	{
		Audit::add('admin-Institution','list');
		$pagination = $this->initPagination(Institution::model());

		$this->render('/admin/institutions',array(
			'institutions' => $this->getItems(array(
				'model' => 'Institution',
				'order' => 'name asc',
				'page' => $pagination->currentPage,
			)),
			'pagination' => $pagination
		));
	}

	public function actionAddInstitution()
	{
		$institution = new Institution();
		$address = new Address();

		$errors = array();

		if (!empty($_POST)) {
			$institution->attributes = $_POST['Institution'];

			if (!$institution->validate()) {
				$errors = $institution->getErrors();
			}

			$address->attributes = $_POST['Address'];

			if ($address->validate()) {
				$errors = array_merge($errors, $address->getErrors());
			}

			if (empty($errors)) {
				if (!$institution->save()) {
					throw new Exception("Unable to save institution: ".print_r($institution->getErrors(),true));
				}
				if (!$address->save()) {
					throw new Exception("Unable to save institution address: ".print_r($address->getErrors(),true));
				}
				$institution->addAddress($address);

				if (!$institution->contact->save()) {
					throw new Exception("Institution contact could not be saved: " . print_r($institution->contact->getErrors(), true));
				}

				Audit::add('admin-Institution','add',serialize($_POST));

				$this->redirect(array('/admin/editInstitution?institution_id='.$institution->id));
			}
		}

		$this->render('/admin/addinstitution',array(
				'institution' => $institution,
				'address' => $address,
				'errors' => @$errors,
		));
	}

	public function actionEditInstitution()
	{
		if (!$institution = Institution::model()->findByPk(@$_GET['institution_id'])) {
			throw new Exception("Institution not found: ".@$_GET['institution_id']);
		}

		$errors = array();
		$address = $institution->contact->address;
		if (!$address) {
			$address = new Address();
		}
		if (!empty($_POST)) {
			$institution->attributes = $_POST['Institution'];

			if (!$institution->validate()) {
				$errors = $institution->getErrors();
			}

			$address = $institution->contact->address;

			$address->attributes = $_POST['Address'];

			if (!$address->validate()) {
				$errors = array_merge(@$errors, $address->getErrors());
			}

			if (empty($errors)) {
				if (!$institution->save()) {
					throw new Exception("Unable to save institution: ".print_r($institution->getErrors(),true));
				}
				if (!$address->save()) {
					throw new Exception("Unable to save institution address: ".print_r($address->getErrors(),true));
				}

				Audit::add('admin-Institution','edit',serialize(array_merge(array('id'=>@$_GET['institution_id']),$_POST)));

				$this->redirect('/admin/institutions');
			}
		} else {
			Audit::add('admin-Institution','view',@$_GET['institution_id']);
		}

		$this->render('/admin/editinstitution',array(
			'institution' => $institution,
			'address' => $address,
			'errors' => $errors,
		));
	}

	public function actionSites($id=false)
	{
		Audit::add('admin-Site','list');
		$pagination = $this->initPagination(Site::model());

		$this->render('/admin/sites',array(
			'sites' => $this->getItems(array(
				'model' => 'Site',
				'order' => 'name asc',
				'page' => $pagination->currentPage,
			)),
			'pagination' => $pagination
		));
	}

	public function actionEditsite()
	{
		if (!$site = Site::model()->findByPk(@$_GET['site_id'])) {
			throw new Exception("Site not found: ".@$_GET['site_id']);
		}

		$errors = array();

		if (!empty($_POST)) {
			$site->attributes = $_POST['Site'];

			if (!$site->validate()) {
				$errors = $site->getErrors();
			}

			$address = $site->contact->address;

			$address->attributes = $_POST['Address'];

			if (!$address->validate()) {
				$errors = array_merge($errors, $address->getErrors());
			}

			if (empty($errors)) {
				if (!$site->save()) {
					throw new Exception("Unable to save site: ".print_r($site->getErrors(),true));
				}
				if (!$address->save()) {
					throw new Exception("Unable to save site address: ".print_r($address->getErrors(),true));
				}

				Audit::add('admin-Site','edit',serialize(array_merge(array('id'=>@$_GET['site_id']),$_POST)));

				$this->redirect('/admin/sites');
			}
		} else {
			Audit::add('admin-Site','view',@$_GET['site_id']);
		}

		$this->render('/admin/editsite',array(
			'site' => $site,
			'address' => $site->contact->address,
			'errors' => $errors,
		));
	}

	public function actionAddContact()
	{
		$contact = new Contact;

		if (!empty($_POST)) {
			$contact->attributes = $_POST['Contact'];

			if (!$contact->validate()) {
				$errors = $contact->getErrors();
			} else {
				if (!$contact->save()) {
					throw new Exception("Unable to save contact: ".print_r($contact->getErrors(),true));
				}
				Audit::add('admin-Contact','add',serialize($_POST));

				$this->redirect(array('/admin/editContact?contact_id='.$contact->id));
			}
		}

		$this->render('/admin/addcontact',array(
			'contact' => $contact,
			'errors' => @$errors,
		));
	}

	public function actionAddContactLabel()
	{
		$contactlabel = new ContactLabel;

		if (!empty($_POST)) {
			$contactlabel->attributes = $_POST['ContactLabel'];

			if (!$contactlabel->validate()) {
				$errors = $contactlabel->getErrors();
			} else {
				if (!$contactlabel->save()) {
					throw new Exception("Unable to save contactlabel: ".print_r($contactlabel->getErrors(),true));
				}
				Audit::add('admin-ContactLabel','add',serialize($_POST));
				$this->redirect('/admin/contactlabels/'.ceil($contactlabel->id/$this->items_per_page));
			}
		}

		$this->render('/admin/addcontactlabel',array(
			'contactlabel' => $contactlabel,
			'errors' => @$errors,
		));
	}

	public function actionEditContactLabel($id)
	{
		if (!$contactlabel = ContactLabel::model()->findByPk($id)) {
			throw new Exception("ContactLabel not found: $id");
		}

		if (!empty($_POST)) {
			$contactlabel->attributes = $_POST['ContactLabel'];

			if (!$contactlabel->validate()) {
				$errors = $contactlabel->getErrors();
			} else {
				if (!$contactlabel->save()) {
					throw new Exception("Unable to save contactlabel: ".print_r($contactlabel->getErrors(),true));
				}
				Audit::add('admin-ContactLabel','edit',serialize(array_merge(array('id'=>$id),$_POST)));

				$this->redirect('/admin/contactlabels/'.ceil($contactlabel->id/$this->items_per_page));
			}
		} else {
			Audit::add('admin-ContactLabel','view',$id);
		}

		$this->render('/admin/editcontactlabel',array(
			'contactlabel' => $contactlabel,
			'errors' => @$errors,
		));
	}

	public function actionDeleteContactLabel()
	{
		if (!$contactlabel = ContactLabel::model()->findByPk(@$_POST['contact_label_id'])) {
			throw new Exception("ContactLabel not found: ".@$_POST['contact_label_id']);
		}

		$count = Contact::model()->count('contact_label_id=?',array($contactlabel->id));

		if ($count == 0) {
			if (!$contactlabel->delete()) {
				throw new Exception("Unable to delete ContactLabel: ".print_r($contactlabel->getErrors(),true));
			}

			Audit::add('admin-ContactLabel','delete',@$_POST['contact_label_id']);
		}

		echo $count;
	}

	public function actionDataSources()
	{
		Audit::add('admin-DataSource','list');
		$this->render('/admin/datasources');
	}

	public function actionEditDataSource($id)
	{
		if (!$source = ImportSource::model()->findByPk($id)) {
			throw new Exception("Source not found: $id");
		}

		if (!empty($_POST)) {
			$source->attributes = $_POST['ImportSource'];

			if (!$source->validate()) {
				$errors = $source->getErrors();
			} else {
				if (!$source->save()) {
					throw new Exception("Unable to save source: ".print_r($source->getErrors(),true));
				}
				Audit::add('admin-DataSource','edit',serialize(array_merge(array('id'=>$id),$_POST)));
				$this->redirect('/admin/datasources/'.ceil($source->id/$this->items_per_page));
			}
		} else {
			Audit::add('admin-DataSource','view',$id);
		}

		$this->render('/admin/editdatasource',array(
			'source' => $source,
			'errors' => @$errors,
		));
	}

	public function actionAddDataSource()
	{
		$source = new ImportSource;

		if (!empty($_POST)) {
			$source->attributes = $_POST['ImportSource'];

			if (!$source->validate()) {
				$errors = $source->getErrors();
			} else {
				if (!$source->save()) {
					throw new Exception("Unable to save data source: ".print_r($source->getErrors(),true));
				}
				Audit::add('admin-DataSource','add',serialize($_POST));
				$this->redirect('/admin/datasources');
			}
		}

		$this->render('/admin/editdatasource',array(
			'source' => $source,
			'errors' => @$errors,
		));
	}

	public function actionDeleteDataSources()
	{
		if (!empty($_POST['source'])) {
			foreach ($_POST['source'] as $source_id) {
				if (Institution::model()->find('source_id=?',array($source_id))) {
					echo "0";
					return;
				}
				if (Site::model()->find('source_id=?',array($source_id))) {
					echo "0";
					return;
				}
				if (Person::model()->find('source_id=?',array($source_id))) {
					echo "0";
					return;
				}
			}

			foreach ($_POST['source'] as $source_id) {
				if ($source = ImportSource::model()->findByPk($source_id)) {
					if (!$source->delete()) {
						throw new Exception("Unable to delete import source: ".print_r($source->getErrors(),true));
					}
				}
			}

			Audit::add('admin-DataSource','delete',serialize($_POST['source']));
		}

		echo "1";
	}

	public function actionDeleteFirms() {
		$result = 1;

		if (!empty($_POST['firms'])) {
			foreach (Firm::model()->findAllByPk($_POST['firms']) as $firm) {
				try {
					$firm_id = $firm->id;
					if (!$firm->delete()) {
						$result = 0;
					} else {
						Audit::add('admin-Firm','delete',$firm_id);
					}
				} catch (Exception $e) {
					$result = 0;
				}
			}
		}

		echo $result;
	}

	public function actionCommissioning_bodies()
	{
		Audit::add('admin-CommissioningBody','list');
		$this->render('commissioning_bodies');
	}

	public function actionEditCommissioningBody()
	{
		if (isset($_GET['commissioning_body_id'])) {
			if (!$cb = CommissioningBody::model()->findByPk(@$_GET['commissioning_body_id'])) {
				throw new Exception("CommissioningBody not found: ".@$_GET['commissioning_body_id']);
			}
			if (!$address = $cb->contact->address) {
				$address = new Address;
				$address->country_id = 1;
			}
		} else {
			$cb = new CommissioningBody;
			$address = new Address;
			$address->country_id = 1;
		}

		$errors = array();

		if (!empty($_POST)) {
			$cb->attributes = $_POST['CommissioningBody'];

			if (!$cb->validate()) {
				$errors = $cb->getErrors();
			}

			$address->attributes = $_POST['Address'];

			if (!$address->validate()) {
				$errors = array_merge($errors, $address->getErrors());
			}

			if (empty($errors)) {
				if (!$contact = $cb->contact) {
					$contact = new Contact;
					if (!$contact->save()) {
						throw new Exception("Unable to save contact for commissioning body: ".print_r($contact->getErrors(),true));
					}
				}

				$cb->contact_id = $contact->id;

				$method = $cb->id ? 'edit' : 'add';

				$audit = $_POST;

				if ($method == 'edit') {
					$audit['id'] = $cb->id;
				}

				if (!$cb->save()) {
					throw new Exception("Unable to save CommissioningBody : ".print_r($cb->getErrors(),true));
				}

				$address->parent_class = 'Contact';
				$address->parent_id = $contact->id;

				if (!$address->save()) {
					throw new Exception("Unable to save CommissioningBody address: ".print_r($address->getErrors(),true));
				}

				Audit::add('admin-CommissioningBody',$method,serialize($audit));

				$this->redirect('/admin/commissioning_bodies');
			}
		} else {
			Audit::add('admin-CommissioningBody','view',@$_GET['commissioning_body_id']);
		}

		$this->render('/admin/editCommissioningBody',array(
			'cb' => $cb,
			'address' => $address,
			'errors' => $errors,
		));
	}

	public function actionAddCommissioning_Body()
	{
		return $this->actionEditCommissioningBody();
	}

	public function actionVerifyDeleteCommissioningBodies()
	{
		foreach (CommissioningBody::model()->findAllByPk(@$_POST['commissioning_body']) as $cb) {
			if (!$cb->canDelete()) {
				echo "0";
				return;
			}
		}

		echo "1";
	}

	public function actionDeleteCommissioningBodies()
	{
		$criteria = new CDbCriteria;
		$criteria->addInCondition('commissioning_body_id',@$_POST['commissioning_body']);

		foreach (CommissioningBodyService::model()->findAll($criteria) as $cbs) {
			$cbs->commissioning_body_id = null;
			if (!$cbs->save()) {
				throw new Exception("Unable to save commissioning body service: ".print_r($cbs->getErrors(),true));
			}
		}

		$criteria = new CDbCriteria;
		$criteria->addInCondition('id',@$_POST['commissioning_body']);

		if (CommissioningBody::model()->deleteAll($criteria)) {
			echo "1";
			Audit::add('admin-CommissioningBody','delete',serialize($_POST));
		} else {
			echo "0";
		}
	}

	public function actionCommissioning_body_types()
	{
		Audit::add('admin-CommissioningBodyType','list');
		$this->render('commissioning_body_types');
	}

	public function actionEditCommissioningBodyType()
	{
		if (isset($_GET['commissioning_body_type_id'])) {
			if (!$cbt = CommissioningBodyType::model()->findByPk(@$_GET['commissioning_body_type_id'])) {
				throw new Exception("CommissioningBody not found: ".@$_GET['commissioning_body_type_id']);
			}
		} else {
			$cbt = new CommissioningBodyType;
		}

		$errors = array();

		if (!empty($_POST)) {
			$cbt->attributes = $_POST['CommissioningBodyType'];

			if (!$cbt->validate()) {
				$errors = $cbt->getErrors();
			}

			if (empty($errors)) {
				$method = $cbt->id ? 'edit' : 'add';

				$audit = $_POST;

				if ($method == 'edit') {
					$audit['id'] = $cbt->id;
				}

				if (!$cbt->save()) {
					throw new Exception("Unable to save CommissioningBodyType : ".print_r($cbt->getErrors(),true));
				}
				Audit::add('admin-CommissioningBodyType',$method,serialize($audit));
				$this->redirect('/admin/commissioning_body_types');
			}
		}

		$this->render('/admin/editCommissioningBodyType',array(
			'cbt' => $cbt,
			'errors' => $errors,
		));
	}

	public function actionAddCommissioningBodyType()
	{
		$this->actionEditCommissioningBodyType();
	}

	public function actionVerifyDeleteCommissioningBodyTypes()
	{
		$criteria = new CDbCriteria;
		$criteria->addInCondition('commissioning_body_type_id',@$_POST['commissioning_body_type']);

		foreach (CommissioningBody::model()->findAll($criteria) as $cb) {
			if (!$cb->canDelete()) {
				echo "0";
				return;
			}
		}

		echo "1";
	}

	public function actionDeleteCommissioningBodyTypes()
	{
		$criteria = new CDbCriteria;
		$criteria->addInCondition('id',@$_POST['commissioning_body_type']);

		foreach (CommissioningBodyType::model()->findAll($criteria) as $cbt) {
			if (!$cbt->delete()) {
				echo "0";
				return;
			}
		}

		Audit::add('admin-CommissioningBodyType','delete',serialize($_POST));

		echo "1";
	}

	public function actionCommissioning_Body_Services()
	{
		Audit::add('admin-CommissioningBodyService','list');
		$this->render('commissioning_body_services');
	}

	public function actionEditCommissioningBodyService()
	{
		$address = new Address;
		$address->country_id = 1;

		if (isset($_GET['commissioning_body_service_id'])) {
			if (!$cbs = CommissioningBodyService::model()->findByPk(@$_GET['commissioning_body_service_id'])) {
				throw new Exception("CommissioningBody not found: ".@$_GET['commissioning_body_service_id']);
			}
			if ($cbs->contact && $cbs->contact->address) {
				$address = $cbs->contact->address;
			}
		} else {
			$cbs = new CommissioningBodyService;
		}

		$errors = array();

		if (!empty($_POST)) {
			$cbs->attributes = $_POST['CommissioningBodyService'];

			if (!$cbs->validate()) {
				$errors = $cbs->getErrors();
			}

			$address->attributes = $_POST['Address'];

			if (!$address->validate()) {
				$errors = array_merge($errors, $address->getErrors());
			}

			if (empty($errors)) {
				if (!$address->id) {
					$contact = new Contact;
					if (!$contact->save()) {
						throw new Exception("Unable to save contact: ".print_r($contact->getErrors(),true));
					}

					$cbs->contact_id = $contact->id;

					$address->parent_class = 'Contact';
					$address->parent_id = $contact->id;
				}

				$method = $cbs->id ? 'edit' : 'add';

				$audit = $_POST;

				if ($method == 'edit') {
					$audit['id'] = $cbs->id;
				}

				if (!$cbs->save()) {
					throw new Exception("Unable to save CommissioningBodyService: ".print_r($cbs->getErrors(),true));
				}

				if (!$address->save()) {
					throw new Exception("Unable to save CommissioningBodyService address: ".print_r($address->getErrors(),true));
				}

				Audit::add('admin-CommissioningBodyService',$method,serialize($audit));

				$this->redirect('/admin/commissioning_body_services');
			}
		}

		$this->render('/admin/editCommissioningBodyService',array(
			'cbs' => $cbs,
			'address' => $address,
			'errors' => $errors,
		));
	}

	public function actionAddCommissioningBodyService()
	{
		$this->actionEditCommissioningBodyService();
	}

	public function actionVerifyDeleteCommissioningBodyServices()
	{
		// Currently no foreign keys to this table
		echo "1";
	}

	public function actionDeleteCommissioningBodyServices()
	{
		$criteria = new CDbCriteria;
		$criteria->addInCondition('id',@$_POST['commissioning_body_service']);

		foreach (CommissioningBodyService::model()->findAll($criteria) as $cbs) {
			if (!$cbs->delete()) {
				echo "0";
				return;
			}
		}

		Audit::add('admin-CommissioningBodyService','delete',serialize($_POST));

		echo "1";
	}

	public function actionCommissioning_Body_Service_Types()
	{
		$this->render('commissioning_body_service_types');
	}

	public function actionEditCommissioningBodyServiceType()
	{
		if (isset($_GET['commissioning_body_service_type_id'])) {
			if (!$cbs = CommissioningBodyServiceType::model()->findByPk(@$_GET['commissioning_body_service_type_id'])) {
				throw new Exception("CommissioningBodyServiceType not found: ".@$_GET['commissioning_body_service_type_id']);
			}
		} else {
			$cbs = new CommissioningBodyServiceType;
		}

		$errors = array();

		if (!empty($_POST)) {
			$cbs->attributes = $_POST['CommissioningBodyServiceType'];

			if (!$cbs->validate()) {
				$errors = $cbs->getErrors();
			}

			$method = $cbs->id ? 'edit' : 'add';

			$audit = $_POST;

			if ($method == 'edit') {
				$audit['id'] = $cbs->id;
			}

			if (empty($errors)) {
				if (!$cbs->save()) {
					throw new Exception("Unable to save CommissioningBodyServiceType: ".print_r($cbs->getErrors(),true));
				}

				Audit::add('admin-CommissioningBodyServiceType',$method,serialize($audit));

				$this->redirect('/admin/commissioning_body_service_types');
			}
		}

		$this->render('/admin/editCommissioningBodyServiceType',array(
			'cbs' => $cbs,
			'errors' => $errors,
		));
	}

	public function actionAddCommissioningBodyServiceType()
	{
		$this->actionEditCommissioningBodyServiceType();
	}

	public function actionVerifyDeleteCommissioningBodyServiceTypes()
	{
		$criteria = new CDbCriteria;
		$criteria->addInCondition('commissioning_body_service_type_id',@$_POST['commissioning_body_service_type']);

		if (CommissioningBodyService::model()->find($criteria)) {
			echo "0";
		} else {
			echo "1";
		}
	}

	public function actionDeleteCommissioningBodyServiceTypes()
	{
		$criteria = new CDbCriteria;
		$criteria->addInCondition('id',@$_POST['commissioning_body_service_type']);

		if (!$er = CommissioningBodyServiceType::model()->deleteAll($criteria)) {
			throw new Exception("Unable to delete CommissioningBodyServiceTypes: ".print_r($er->getErrors(),true));
		}

		Audit::add('admin-CommissioningBodyServiceType','delete',serialize($_POST));

		echo "1";
	}

	public function actionEpisodeSummaries($subspecialty_id = null)
	{
		$this->render(
			'/admin/episodeSummaries',
			array(
				'subspecialty_id' => $subspecialty_id,
				'enabled_items' => EpisodeSummaryItem::model()->enabled($subspecialty_id)->findAll(),
				'available_items' => EpisodeSummaryItem::model()->available($subspecialty_id)->findAll(),
			)
		);
	}

	public function actionUpdateEpisodeSummary()
	{
		$item_ids = @$_POST['item_ids'] ? explode(',', $_POST['item_ids']) : array();
		$subspecialty_id = @$_POST['subspecialty_id'] ?: null;

		$tx = Yii::app()->db->beginTransaction();
		EpisodeSummaryItem::model()->assign($item_ids, $subspecialty_id);
		$tx->commit();

		$this->redirect(array('/admin/episodeSummaries', 'subspecialty_id' => $subspecialty_id));
	}
}
