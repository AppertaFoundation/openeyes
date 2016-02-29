<?php
/**
 * OpenEyes
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

/**
 * The followings are the available columns in table '':
 * @property string $id
 * @property integer $event_id
 *
 * The followings are the available model relations:
 * @property Event $event
 */
class ElementLetter extends BaseEventTypeElement
{
	public $cc_targets = array();
	public $address_target = null;
	// track the original source address so when overridden for copies to cc addresses, we can still keep
	// the correct cc footer information
	public $source_address = null;
	public $lock_period_hours = 24;
	public $macro = null;

	/**
	 * Returns the static model of the specified AR class.
	 * @return ElementOperation the static model class
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
		return 'et_ophcocorrespondence_letter';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('event_id, site_id, print, address, use_nickname, date, introduction, cc, re, body, footer, draft, direct_line, fax, clinic_date, print_all', 'safe'),
			array('use_nickname, site_id, date, address, introduction, body, footer', 'required'),
			array('date','OEDateValidator'),
			array('clinic_date','OEDateValidatorNotFuture'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, event_id, site_id, use_nickname, date, introduction, re, body, footer, draft, direct_line', 'safe', 'on' => 'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'element_type' => array(self::HAS_ONE, 'ElementType', 'id','on' => "element_type.class_name='".get_class($this)."'"),
			'eventType' => array(self::BELONGS_TO, 'EventType', 'event_type_id'),
			'event' => array(self::BELONGS_TO, 'Event', 'event_id'),
			'user' => array(self::BELONGS_TO, 'User', 'created_user_id'),
			'usermodified' => array(self::BELONGS_TO, 'User', 'last_modified_user_id'),
			'site' => array(self::BELONGS_TO, 'Site', 'site_id'),
			'enclosures' => array(self::HAS_MANY, 'LetterEnclosure', 'element_letter_id', 'order'=>'display_order'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'use_nickname' => 'Nickname',
			'date' => 'Date',
			'introduction' => 'Salutation',
			're' => 'Re',
			'body' => 'Body',
			'footer' => 'Footer',
			'draft' => 'Draft',
			'direct_line' => 'Direct line',
			'fax' => 'Direct fax',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria = new CDbCriteria;

		$criteria->compare('id', $this->id, true);
		$criteria->compare('event_id', $this->event_id, true);

		return new CActiveDataProvider(get_class($this), array(
			'criteria' => $criteria,
		));
	}

	public function afterFind()
	{
		parent::afterFind();
		$this->source_address = $this->address;
	}

	public function getAddress_targets()
	{
		if (Yii::app()->getController()->getAction()->id == 'create') {
			if (!$patient = Patient::model()->with(array('gp','practice'))->findByPk(@$_GET['patient_id'])) {
				throw new Exception('patient not found: '.@$_GET['patient_id']);
			}
		} else {
			$patient = $this->event->episode->patient;
		}

		$options = array('Patient'.$patient->id => $patient->fullname.' (Patient)');
		if(!isset($patient -> contact -> address)) $options['Patient'.$patient->id] .= ' - NO ADDRESS';

		if ($patient->gp) {
			if (@$patient->gp->contact) {
				$options['Gp'.$patient->gp_id] = $patient->gp->contact->fullname.' (GP)';
			} else {
				$options['Gp'.$patient->gp_id] = Gp::UNKNOWN_NAME.' (GP)';
			}
			if (!$patient->practice || !@$patient->practice->contact->address) {
				$options['Gp'.$patient->gp_id] .= ' - NO ADDRESS';
			}
		}
		else {
			if ($patient->practice) {
				$options['Practice'.$patient->practice_id] = Gp::UNKNOWN_NAME.' (GP)';
				if (@$patient->practice->contact && !@$patient->practice->contact->address) {
					$options['Practice'.$patient->practice_id] .= ' - NO ADDRESS';
				}
			}
		}

		// get the ids of the commissioning body types that should be shown as potential recipients to filter against
		$cbt_ids = array();
		foreach (OphCoCorrespondence_CommissioningBodyType_Recipient::model()->getCommissioningBodyTypes() as $cbt) {
			$cbt_ids[] = $cbt->id;
		}

		if ($cbs = $patient->getDistinctCommissioningBodiesByType()) {
			$criteria = new CDbCriteria;
			$criteria->addInCondition('id',array_keys($cbs));
			$cbtype_lookup = CHtml::listData(CommissioningBodyType::model()->findAll($criteria),'id','name');

			foreach ($cbs as $cb_type_id => $cb_list) {
				foreach ($cb_list as $cb) {
					if (in_array($cb_type_id, $cbt_ids) ) {
						$options['CommissioningBody'.$cb->id] = $cb->name . ' (' . $cbtype_lookup[$cb_type_id] . ')';
						if (!$cb->getAddress()) {
							$options['CommissioningBody'.$cb->id] .= ' - NO ADDRESS';
						}
					}

					// include all services at the moment, regardless of whether the commissioning body type is filtered
					if ($services = $cb->services) {
						foreach ($services as $svc) {
							$options['CommissioningBodyService'.$svc->id] = $svc->name . ' (' . $svc->getTypeShortName() . ')';
						}
					}
				}
			}
		}

		foreach (PatientContactAssignment::model()->with(array(
			'contact' => array(
				'with' => array('address'),
			),
			'location' => array(
				'with' => array(
					'contact' => array(
						'alias' => 'contact2',
						'with' => array(
							'label',
						),
					),
				),
			),
		))->findAll('patient_id=?',array($patient->id)) as $pca) {
			if ($pca->location) {
				$options['ContactLocation'.$pca->location_id] = $pca->location->contact->fullName.' ('.$pca->location->contact->label->name.', '.$pca->location.')';
			} else {
				// Note that this index will always be the basis for a Person model search - if PCA has a wider use case than this,
				// this will need to be revisited
				$options['Contact'.$pca->contact_id] = $pca->contact->fullName.' ('.$pca->contact->label->name;
				if ($pca->contact->address) {
					$options['Contact'.$pca->contact_id] .= ', '.$pca->contact->address->address1.')';
				} else {
					$options['Contact'.$pca->contact_id] .= ') - NO ADDRESS';
				}
			}
		}

		asort($options);

		return $options;
	}

	public function getStringGroups()
	{
		return LetterStringGroup::model()->findAll(array('order'=>'display_order'));
	}

	public function calculateRe($patient)
	{
		$re = $patient->first_name.' '.$patient->last_name;

		foreach (array('address1','address2','city','postcode') as $field) {
			if ($patient->contact->address && $patient->contact->address->{$field}) {
				$re .= ', '.$patient->contact->address->{$field};
			}
		}

		return $re . ', DOB: '.$patient->NHSDate('dob').', Hosp No: '.$patient->hos_num.', NHS No: '.$patient->nhsnum;
	}

	public function setDefaultOptions()
	{
		if (Yii::app()->getController()->getAction()->id == 'create') {
			$this->site_id = Yii::app()->session['selected_site_id'];

			if (!$patient = Patient::model()->with(array('contact'=>array('with'=>array('address'))))->findByPk(@$_GET['patient_id'])) {
				throw new Exception('Patient not found: '.@$_GET['patient_id']);
			}

			$this->re = $patient->first_name.' '.$patient->last_name;

			foreach (array('address1','address2','city','postcode') as $field) {
				if ($patient->contact->address && $patient->contact->address->{$field}) {
					$this->re .= ', '.$patient->contact->address->{$field};
				}
			}

			$this->re .= ', DOB: '.$patient->NHSDate('dob').', Hosp No: '.$patient->hos_num.', NHS No: '.$patient->nhsnum;

			$user = Yii::app()->session['user'];
			$firm = Firm::model()->with('serviceSubspecialtyAssignment')->findByPk(Yii::app()->session['selected_firm_id']);

			if ($contact = $user->contact) {
				$consultant = null;
				// only want to get consultant for medical firms
				if ($specialty = $firm->getSpecialty()) {
					if ($specialty->medical) {
						$consultant = $firm->consultant;
					}
				}

				$this->footer = "Yours sincerely\n\n\n\n\n".trim($contact->title.' '.$contact->first_name.' '.$contact->last_name.' '.$contact->qualifications)."\n".$user->role;

				if ($consultant && $consultant->id != $user->id) {
					$this->footer .= "\nConsultant: {$consultant->contact->title} {$consultant->contact->first_name} {$consultant->contact->last_name}";
				}

				$ssa = $firm->serviceSubspecialtyAssignment;
			}

			// Look for a macro based on the episode_status
			if ($episode = $patient->getEpisodeForCurrentSubspecialty()) {
				if (!$this->macro = LetterMacro::model()->find('firm_id=? and episode_status_id=?',array($firm->id, $episode->episode_status_id))) {
					if ($firm->service_subspecialty_assignment_id) {
						$subspecialty_id = $firm->serviceSubspecialtyAssignment->subspecialty_id;

						if (!$this->macro = LetterMacro::model()->find('subspecialty_id=? and episode_status_id=?',array($subspecialty_id, $episode->episode_status_id))) {
							$this->macro = LetterMacro::model()->find('site_id=? and episode_status_id=?',array(Yii::app()->session['selected_site_id'],$episode->episode_status_id));
						}
					}
				}
			}

			if ($this->macro) {
				$this->populate_from_macro($patient);
			}

			if (Yii::app()->params['populate_clinic_date_from_last_examination'] && Yii::app()->findModule('OphCiExamination')) {
				if ($episode = $patient->getEpisodeForCurrentSubspecialty()) {
					if ($event_type = EventType::model()->find('class_name=?',array('OphCiExamination'))) {
						$criteria = new CDbCriteria;
						$criteria->addCondition('event_type_id = '.$event_type->id);
						$criteria->addCondition('episode_id = '.$episode->id);
						$criteria->order = "created_date desc";
						$criteria->limit = 1;

						if ($event = Event::model()->find($criteria)) {
							$this->clinic_date = $event->created_date;
						}
					}
				}
			}

			if ($dl = FirmSiteSecretary::model()->find('firm_id=? and site_id=?',array(Yii::app()->session['selected_firm_id'],$this->site_id))) {
				$this->direct_line = $dl->direct_line;
				$this->fax = $dl->fax;
			}
		}
	}

	public function populate_from_macro($patient)
	{
		if ($this->macro->use_nickname) {
			$this->use_nickname = 1;
		}

		$address_contact = null;
		if ($this->macro->recipient && $this->macro->recipient->name == 'Patient') {
			$address_contact = $patient;
			$this->address_target = 'patient';
			$this->introduction = $patient->getLetterIntroduction(array(
				'nickname' => $this->use_nickname,
			));
		} elseif ($this->macro->recipient && $this->macro->recipient->name == 'GP') {
			$this->address_target = 'gp';
			if($patient->gp) {
				$this->introduction = $patient->gp->getLetterIntroduction(array(
					'nickname' => $this->use_nickname,
				));
				$address_contact = $patient->gp;
			} else {
				$this->introduction = "Dear " . Gp::UNKNOWN_SALUTATION . ",";
				$address_contact = @$patient->practice;
			}
		}

		if ($address_contact) {
			$this->address = $address_contact->getLetterAddress(array(
				'patient' => $patient,
				'include_name' => true,
				'include_label' => true,
				'delimiter' => "\n",
			));
		}

		$this->macro->substitute($patient);
		$this->body = $this->macro->body;

		if ($this->macro->cc_patient && $patient->contact->address) {
			$this->cc = $patient->getLetterAddress(array(
				'include_name' => true,
				'include_prefix' => true,
				'delimiter'=>'| ',
			));
			$this->cc=str_replace(',',';',$this->cc);
			$this->cc=str_replace('|',',',$this->cc);
			$this->cc_targets[] = 'patient';
		}

		if ($this->macro->cc_doctor && $patient->gp && @$patient->practice->contact->address) {
			$this->cc = $patient->gp->getLetterAddress(array(
				'patient' => $patient,
				'include_name' => true,
				'include_label' => true,
				'delimiter' => '| ',
				'include_prefix' => true,
			));
			$this->cc=str_replace(',',';',$this->cc);
			$this->cc=str_replace('|',',',$this->cc);
			$this->cc_targets[] = 'gp';
		}
	}

	public function getLetter_macros()
	{
		$macros = array();
		$macro_names = array();

		$firm = Firm::model()->with('serviceSubspecialtyAssignment')->findByPk(Yii::app()->session['selected_firm_id']);

		$criteria = new CDbCriteria;
		$criteria->compare('firm_id', $firm->id);
		$criteria->order = 'display_order asc';

		foreach (LetterMacro::model()->findAll($criteria) as $macro) {
			if (!in_array($macro->name, $macro_names)) {
				$macros[$macro->id] = $macro_names[] = $macro->name;
			}
		}

		if ($firm->service_subspecialty_assignment_id) {
			$criteria = new CDbCriteria;
			$criteria->compare('subspecialty_id', $firm->serviceSubspecialtyAssignment->subspecialty_id);
			$criteria->order = 'display_order asc';

			foreach (LetterMacro::model()->findAll($criteria) as $macro) {
				if (!in_array($macro->name, $macro_names)) {
					$macros[$macro->id] = $macro_names[] = $macro->name;
				}
			}
		}

		$criteria = new CDbCriteria;
		$criteria->compare('site_id', Yii::app()->session['selected_site_id']);
		$criteria->order = 'display_order asc';

		foreach (LetterMacro::model()->findAll($criteria) as $macro) {
			if (!in_array($macro->name, $macro_names)) {
				$macros[$macro->id] = $macro_names[] = $macro->name;
			}
		}

		return $macros;
	}

	public function beforeSave()
	{
		if (in_array(Yii::app()->getController()->getAction()->id,array('create','update'))) {
			if (!$this->draft) {
				$this->print = 1;
				$this->print_all = 1;
			}
		}

		foreach (array('address','introduction','re','body','footer','cc') as $field) {
			$this->$field = trim($this->$field);
		}

		if (!$this->clinic_date) {
			$this->clinic_date = null;
		}

		return parent::beforeSave();
	}

	public function afterSave()
	{
		if (@$_POST['update_enclosures']) {
			foreach ($this->enclosures as $enclosure) {
				$enclosure->delete();
			}

			if (is_array(@$_POST['EnclosureItems'])) {
				$i = 1;

				foreach (@$_POST['EnclosureItems'] as $key => $value) {
					if (strlen(trim($value)) >0) {
						$enc = new LetterEnclosure;
						$enc->element_letter_id = $this->id;
						$enc->display_order = $i++;
						$enc->content = $value;
						if (!$enc->save()) {
							throw new Exception('Unable to save EnclosureItem: '.print_r($enc->getErrors(),true));
						}
					}
				}
			}
		}

		return parent::afterSave();
	}

	public function getInfotext()
	{
		if ($this->draft) {
			return 'Letter is being drafted';
		}
	}

	public function getCcTargets()
	{
		$targets = array();

		if (trim($this->cc)) {
			foreach (explode("\n",trim($this->cc)) as $cc) {
				$ex = explode(", ",trim($cc));

				if (isset($ex[1]) && (ctype_digit($ex[1]) || is_int($ex[1]))) {
					$ex[1] .= ' '.$ex[2];
					unset($ex[2]);
				}

				$targets[] = explode(',',implode(',',$ex));
			}
		}

		return $targets;
	}

	public function isEditable()
	{
		return $this->draft;
	}

	public function getFirm_members()
	{
		$members = CHtml::listData(Yii::app()->getController()->firm->members, 'id', 'fullNameAndTitle');

		$user = Yii::app()->session['user'];

		if (!isset($members[$user->id])) {
			$members[$user->id] = $user->fullNameAndTitle;
		}

		return $members;
	}

	public function renderIntroduction()
	{
		return str_replace("\n","<br/>",trim(CHtml::encode($this->introduction)));
	}

	public function renderBody()
	{
		$body = array();

		foreach (explode(chr(10),CHtml::encode($this->body)) as $line) {
			$processed_line = '';
			if (preg_match('/^([\t]+)/',$line,$m)) {
				for ($i=0; $i<strlen($m[1]); $i++) {
					for ($j=0; $j<8; $j++) {
						$processed_line .= '&nbsp;';
					}
				}
				$processed_line .= preg_replace('/^[\t]+/','',$line);
			} elseif (preg_match('/^([\s]+)/',$line,$m)) {
				for ($i=0; $i<strlen($m[1]); $i++) {
					$processed_line .= '&nbsp;';
				}
				$processed_line .= preg_replace('/^[\s]+/','',$line);
			} else {
				$processed_line .= $line;
			}
			$body[] = $processed_line;
		}

		return implode('<br/>', $body);
	}

	public function getCreate_view()
	{
		return 'create_'.$this->getDefaultView();
	}

	public function getUpdate_view()
	{
		return 'update_'.$this->getDefaultView();
	}

	public function getPrint_view() {
		return 'print_'.$this->getDefaultView();
	}

	public function getContainer_view_view()
	{
		return false;
	}

	public function getContainer_print_view()
	{
		return false;
	}

	public function renderFooter()
	{
		return str_replace("\n","<br/>",CHtml::encode($this->footer));
	}

	/**
	 * Single line render of to address
	 *
	 * @return mixed
	 */
	public function renderToAddress()
	{
		return preg_replace('/[\r\n]+/',', ',CHtml::encode($this->address));
	}

	/**
	 * Single line render of source_address
	 * @return mixed
	 */
	public function renderSourceAddress()
	{
		return preg_replace('/[\r\n]+/',', ',CHtml::encode($this->source_address));
	}
}
