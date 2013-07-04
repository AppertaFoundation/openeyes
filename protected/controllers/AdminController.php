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

class AdminController extends BaseController
{
	public $layout = 'admin';
	public $items_per_page = 30;

	public function accessRules() {
		return array(
			array('deny'),
		);
	}

	protected function beforeAction($action) {
		$this->registerCssFile('admin.css', Yii::app()->createUrl("css/admin.css"));
		Yii::app()->clientScript->registerScriptFile(Yii::app()->createUrl("js/admin.js"));

		$this->jsVars['items_per_page'] = $this->items_per_page;

		return parent::beforeAction($action);
	}

	public function actionIndex() {
		$this->redirect(array('/admin/users'));
	}

	public function actionUsers($id=false) {
		if ((integer)$id) {
			$page = $id;
		} else {
			$page = 1;
		}

		$this->render('/admin/users',array(
			'users' => $this->getItems(array(
				'model' => 'User',
				'page' => $page,
			)),
		));
	}

	public function actionAddUser() {
		$user = new User;

		if (!empty($_POST)) {
			$user->attributes = $_POST['User'];

			if (!$user->validate()) {
				$errors = $user->getErrors();
			} else {
				if (!$user->save()) {
					throw new Exception("Unable to save user: ".print_r($user->getErrors(),true));
				}
				$this->redirect('/admin/users/'.ceil($user->id/$this->items_per_page));
			}
		}

		$user->password = '';

		$this->render('/admin/adduser',array(
			'user' => $user,
			'errors' => @$errors,
		));
	}

	public function actionEditUser($id) {
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

				$this->redirect('/admin/users/'.ceil($user->id/$this->items_per_page));
			}
		}

		$user->password = '';

		$this->render('/admin/edituser',array(
			'user' => $user,
			'errors' => @$errors,
		));
	}

	public function actionFirms($id=false) {
		if ((integer)$id) {
			$page = $id;
		} else {
			$page = 1;
		}

		$this->render('/admin/firms',array(
			'firms' => $this->getItems(array(
				'model' => 'Firm',
				'page' => $page,
			)),
		));
	}

	public function actionEditFirm($id) {
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
				$this->redirect('/admin/firms/'.ceil($firm->id/$this->items_per_page));
			}
		}

		$this->render('/admin/editfirm',array(
			'firm' => $firm,
			'errors' => @$errors,
		));
	}

	public function getItems($params) {
		$model = $params['model']::model();
		$pages = ceil(Yii::app()->db->createCommand()->select("count(*)")->from($model->tableName())->queryScalar() / $this->items_per_page);

		if ($params['page'] <1) {
			$page = 1;
		} else if ($params['page'] > $pages) {
			$page = $pages;
		} else {
			$page = $params['page'];
		}

		$criteria = new CDbCriteria;
		if (isset($params['order'])) {
			$criteria->order = $params['order'];
		} else {
			$criteria->order = 'id asc';
		}
		$criteria->offset = ($page-1) * $this->items_per_page;
		$criteria->limit = $this->items_per_page;


		if (!empty($_REQUEST['search'])) {
			$criteria->addSearchCondition("username",$_REQUEST['search'],true,'OR');
			$criteria->addSearchCondition("first_name",$_REQUEST['search'],true,'OR');
			$criteria->addSearchCondition("last_name",$_REQUEST['search'],true,'OR');
		}
		
		return array(
			'items' => $params['model']::model()->findAll($criteria),
			'page' => $page,
			'pages' => $pages,
		);
	}

	public function actionLookupUser() {
		Yii::app()->event->dispatch('lookup_user', array('username' => $_GET['username']));

		if ($user = User::model()->find('username=?',array($_GET['username']))) {
			echo $user->id;
		} else {
			echo "NOTFOUND";
		}
	}

	public function actionContacts($id=false) {
		if ((integer)$id) {
			$page = $id;
		} else {
			$page = 1;
		}

		if (!empty($_GET)) {
			$contacts = $this->searchContacts();
		}

		$this->render('/admin/contacts',array('contacts'=>@$contacts));
	}

	public function actionContactlabels($id=false) {
		if ((integer)$id) {
			$page = $id;
		} else {
			$page = 1;
		}

		$this->render('/admin/contactlabels',array(
			'contactlabels' => $this->getItems(array(
				'model' => 'ContactLabel',
				'order' => 'name asc',
				'page' => $page,
			)),
		));
	}

	public function searchContacts() {
		$criteria = new CDbCriteria;

		$ex = explode(' ',@$_GET['q']);

		if (empty($ex)) {
			throw new Exception("Empty search query string, this shouldn't happen");
		}

		if (count($ex) == 1) {
			$criteria->addSearchCondition("lower(`t`.first_name)",strtolower(@$_GET['q']),false);
			$criteria->addSearchCondition("lower(`t`.last_name)",strtolower(@$_GET['q']),false,'OR');
		} else if (count($ex) == 2) {
			$criteria->addSearchCondition("lower(`t`.first_name)",strtolower(@$ex[0]),false);
			$criteria->addSearchCondition("lower(`t`.last_name)",strtolower(@$ex[1]),false);
		} else if (count($ex) >= 3) {
			$criteria->addSearchCondition("lower(`t`.title)",strtolower(@$ex[0]),false);
			$criteria->addSearchCondition("lower(`t`.first_name)",strtolower(@$ex[1]),false);
			$criteria->addSearchCondition("lower(`t`.last_name)",strtolower(@$ex[2]),false);
		}

		if (@$_GET['label']) {
			$criteria->compare('contact_label_id',@$_GET['label']);
		}

		$criteria->order = 'title, first_name, last_name';

		$contacts = Contact::model()->findAll($criteria);

		if (count($contacts) == 1) {
			foreach ($contacts as $contact) {}
			return $this->redirect(array('/admin/editContact?contact_id='.$contact->id));
		}

		$pages = ceil(count($contacts) / $this->items_per_page);

		$page = (integer)@$_GET['page'];

		if ($page <1) {
			$page = 1;
		} else if ($page > $pages) {
			$page = $pages;
		}

		$_contacts = array();

		foreach ($contacts as $i => $contact) {
			if ($i >= (($page-1) * $this->items_per_page)) {
				$_contacts[] = $contact;
			}

			if (count($_contacts) >= $this->items_per_page) {
				break;
			}
		}

		return array(
			'page' => $page,
			'pages' => $pages,
			'contacts' => $_contacts,
		);
	}

	public function actionEditContact() {
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
				$this->redirect('/admin/contacts?q='.$contact->fullName);
			}
		}

		$this->render('/admin/editcontact',array(
			'contact' => $contact,
			'errors' => @$errors,
		));
	}

	public function actionContactLocation() {
		if (!$cl = ContactLocation::model()->findByPk(@$_GET['location_id'])) {
			throw new Exception("ContactLocation not found: ".@$_GET['location_id']);
		}

		$this->render('/admin/contactlocation',array(
			'location' => $cl,
		));
	}

	public function actionRemoveLocation() {
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

		return "1";
	}

	public function actionAddContactLocation() {
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

	public function actionGetInstitutionSites() {
		if (!$institution = Institution::model()->findByPk(@$_GET['institution_id'])) {
			throw new Exception("Institution not found: ".@$_GET['institution_id']);
		}

		echo json_encode(CHtml::listData($institution->sites,'id','name'));
	}

	public function actionInstitutions($id=false) {
		if ((integer)$id) {
			$page = $id;
		} else {
			$page = 1;
		}

		$this->render('/admin/institutions',array(
			'institutions' => $this->getItems(array(
				'model' => 'Institution',
				'order' => 'name asc',
				'page' => $page,
			)),
		));
	}
	
	public function actionAddInstitution() {
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
								
				$this->redirect(array('/admin/editInstitution?institution_id='.$institution->id));
			}
		}
		
		$this->render('/admin/addinstitution',array(
				'institution' => $institution,
				'address' => $address,
				'errors' => @$errors,
		));
	}
	
	public function actionEditInstitution() {
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

				$this->redirect('/admin/institutions');
			}
		}

		$this->render('/admin/editinstitution',array(
			'institution' => $institution,
			'address' => $address,
			'errors' => $errors,
		));
	}

	public function actionSites($id=false) {
		if ((integer)$id) {
			$page = $id;
		} else {
			$page = 1;
		}

		$this->render('/admin/sites',array(
			'sites' => $this->getItems(array(
				'model' => 'Site',
				'order' => 'name asc',
				'page' => $page,
			)),
		));
	}

	public function actionEditsite() {
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

				$this->redirect('/admin/sites');
			}
		}

		$this->render('/admin/editsite',array(
			'site' => $site,
			'address' => $site->contact->address,
			'errors' => $errors,
		));
	}

	public function actionAddContact() {
		$contact = new Contact;

		if (!empty($_POST)) {
			$contact->attributes = $_POST['Contact'];

			if (!$contact->validate()) {
				$errors = $contact->getErrors();
			} else {
				if (!$contact->save()) {
					throw new Exception("Unable to save contact: ".print_r($contact->getErrors(),true));
				}
				$this->redirect(array('/admin/editContact?contact_id='.$contact->id));
			}
		}

		$this->render('/admin/addcontact',array(
			'contact' => $contact,
			'errors' => @$errors,
		));
	}

	public function actionAddContactLabel() {
		$contactlabel = new ContactLabel;

		if (!empty($_POST)) {
			$contactlabel->attributes = $_POST['ContactLabel'];

			if (!$contactlabel->validate()) {
				$errors = $contactlabel->getErrors();
			} else {
				if (!$contactlabel->save()) {
					throw new Exception("Unable to save contactlabel: ".print_r($contactlabel->getErrors(),true));
				}
				$this->redirect('/admin/contactlabels/'.ceil($contactlabel->id/$this->items_per_page));
			}
		}

		$this->render('/admin/addcontactlabel',array(
			'contactlabel' => $contactlabel,
			'errors' => @$errors,
		));
	}

	public function actionEditContactLabel($id) {
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
				$this->redirect('/admin/contactlabels/'.ceil($contactlabel->id/$this->items_per_page));
			}
		}

		$this->render('/admin/editcontactlabel',array(
			'contactlabel' => $contactlabel,
			'errors' => @$errors,
		));
	}

	public function actionDeleteContactLabel() {
		if (!$contactlabel = ContactLabel::model()->findByPk(@$_POST['contact_label_id'])) {
			throw new Exception("ContactLabel not found: ".@$_POST['contact_label_id']);
		}

		$count = Contact::model()->count('contact_label_id=?',array($contactlabel->id));

		if ($count == 0) {
			if (!$contactlabel->delete()) {
				throw new Exception("Unable to delete ContactLabel: ".print_r($contactlabel->getErrors(),true));
			}
		}

		echo $count;
	}

	public function actionDataSources() {
		$this->render('/admin/datasources');
	}

	public function actionEditDataSource($id) {
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
				$this->redirect('/admin/datasources/'.ceil($source->id/$this->items_per_page));
			}
		}

		$this->render('/admin/editdatasource',array(
			'source' => $source,
			'errors' => @$errors,
		));
	}

	public function actionAddDataSource() {
		$source = new ImportSource;

		if (!empty($_POST)) {
			$source->attributes = $_POST['ImportSource'];

			if (!$source->validate()) {
				$errors = $source->getErrors();
			} else {
				if (!$source->save()) {
					throw new Exception("Unable to save data source: ".print_r($source->getErrors(),true));
				}
				$this->redirect('/admin/datasources');
			}
		}

		$this->render('/admin/editdatasource',array(
			'source' => $source,
			'errors' => @$errors,
		));
	}

	public function actionDeleteDataSources() {
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
		}

		echo "1";
	}
}
