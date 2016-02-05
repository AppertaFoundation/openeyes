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

class DefaultController extends BaseEventTypeController
{
	static protected $action_types = array(
		'loadElementByProcedure' => self::ACTION_TYPE_FORM,
		'getElementsToDelete' => self::ACTION_TYPE_FORM,
		'verifyProcedure' => self::ACTION_TYPE_FORM,
		'getImage' => self::ACTION_TYPE_FORM,
		'getTheatreOptions' => self::ACTION_TYPE_FORM,
	);

	/* @var Element_OphTrOperationbooking_Operation operation that this note is for when creating */
	protected $booking_operation;
	/* @var boolean - indicates if this note is for an unbooked procedure or not when creating */
	protected $unbooked = false;
	/* @var Proc[] - cache of bookings for the booking operation */
	protected $booking_procedures;

	/**
	 * returns list of procudures for the booking operation set on the controller
	 *
	 * @return Proc[]
	 */
	protected function getBookingProcedures()
	{
		if ($this->booking_operation) {
			if (!$this->booking_procedures) {
				$api = Yii::app()->moduleAPI->get('OphTrOperationbooking');
				$this->booking_procedures = $api->getProceduresForOperation($this->booking_operation->event_id);
			}
			return $this->booking_procedures;
		}
	}

	protected function beforeAction($action)
	{
		Yii::app()->clientScript->registerScriptFile($this->assetPath.'/js/eyedraw.js');
		return parent::beforeAction($action);
	}

	/**
	 * Set flash message for patient allergies
	 */
	protected function showAllergyWarning()
	{
		if ($this->patient->no_allergies_date) {
			Yii::app()->user->setFlash('info.prescription_allergy', $this->patient->getAllergiesString());
		}
		else {
			Yii::app()->user->setFlash('warning.prescription_allergy', $this->patient->getAllergiesString());
		}
	}

	/**
	 * Creates the procedure elements for the procedures selected in the procedure list element
	 *
	 * @return BaseEventTypeElement[]
	 */
	protected function getEventElements()
	{
		if ($this->event && !$this->event->isNewRecord) {
			return $this->event->getElements();
			//TODO: check for missing elements for procedures

		}
		else {
			$elements = $this->event_type->getDefaultElements();
			if ($procedures = $this->getBookingProcedures()) {
				// need to add procedure elements for the booking operation
				$extra_elements = array();

				foreach ($procedures as $proc) {
					$procedure_elements = $this->getProcedureSpecificElements($proc->id);
					foreach ($procedure_elements as $element) {
						$kls = $element->element_type->class_name;
						// only have one of any given procedure element
						if (!in_array($kls,$extra_elements)) {
							$extra_elements[] = $kls;
							$elements[] = new $kls;
						}
					}

					if (count($procedure_elements) == 0) {
						// no specific element for procedure, use generic
						$element = new Element_OphTrOperationnote_GenericProcedure;
						$element->proc_id = $proc->id;
						$elements[] = $element;
					}
				}
			}
			return $elements;
		}
	}

	/**
	 * For new notes for a specific operation, initialise procedure list with relevant procedures
	 *
	 * @param Element_OphTrOperationnote_ProcedureList $element
	 * @param string $action
	 */
	protected function setElementDefaultOptions_Element_OphTrOperationnote_ProcedureList($element, $action)
	{
		if ($action == 'create' && $procedures = $this->getBookingProcedures()) {
			$element->procedures = $procedures;

			$api = Yii::app()->moduleAPI->get('OphTrOperationbooking');
			$element->eye = $api->getEyeForOperation($this->booking_operation->event_id);
			$element->booking_event_id = $this->booking_operation->event_id;
		}
	}

	/**
	 * Determine if the witness field is required, and set various defaults from the patient and related booking
	 *
	 * @param Element_OphTrOperationnote_Anaesthetic $element
	 * @param string $action
	 */
	protected function setElementDefaultOptions_Element_OphTrOperationnote_Anaesthetic($element, $action)
	{
		if (Yii::app()->params['fife']) {
			$element->witness_required = true;
		}
		if ($action == 'create') {
			if ($this->booking_operation) {
				$element->anaesthetic_type_id = $this->booking_operation->anaesthetic_type_id;
			}
			else {
				$key = $this->patient->isChild() ? 'ophtroperationnote_default_anaesthetic_child' : 'ophtroperationnote_default_anaesthetic';

				if (isset(Yii::app()->params[$key])) {
					if ($at = AnaestheticType::model()->find('code=?',array(Yii::app()->params[$key]))) {
						$element->anaesthetic_type_id = $at->id;
					}
				}
			}
			$element->anaesthetic_agents = $this->getAnaestheticAgentsBySiteAndSubspecialty('siteSubspecialtyAssignmentDefaults');
		}
	}

	/**
	 * Set the default drugs from site and subspecialty
	 *
	 * @param Element_OphTrOperationnote_PostOpDrugs $element
	 * @param string $action
	 */
	protected function setElementDefaultOptions_Element_OphTrOperationnote_PostOpDrugs($element, $action)
	{
		if ($action == 'create') {
			$element->drugs = $this->getPostOpDrugsBySiteAndSubspecialty(true);
		}
	}

	/**
	 * Set the default operative devices from the site and subspecialty
	 *
	 * @param Element_OphTrOperationnote_Cataract $element
	 * @param $action
	 */
	protected function setElementDefaultOptions_Element_OphTrOperationnote_Cataract($element, $action)
	{
		if ($action == 'create') {
			$element->operative_devices = $this->getOperativeDevicesBySiteAndSubspecialty(true);
		}
	}
	/**
	 * Edit actions common initialisation
	 */
	protected function initEdit()
	{
		$this->showAllergyWarning();
		$this->jsVars['eyedraw_iol_classes'] = Yii::app()->params['eyedraw_iol_classes'];
		$this->moduleStateCssClass = 'edit';
	}

	/**
	 * Set up the controller properties for booking relationship
	 *
	 * @throws Exception
	 */
	protected function initActionCreate()
	{
		parent::initActionCreate();

		if (isset($_GET['booking_event_id'])) {
			if (!$api = Yii::app()->moduleAPI->get('OphTrOperationbooking')) {
				throw new Exception('invalid request for booking event');
			}
			if (!$this->booking_operation = $api->getOperationForEvent($_GET['booking_event_id'])) {
				throw new Exception('booking event not found');
			}
		}
		elseif (isset($_GET['unbooked'])) {
			$this->unbooked = true;
		}

		$this->initEdit();
	}

	/**
	 * Call the core edit action initialisation
	 *
	 * (non-phpdoc)
	 * @see parent::initActionUpdate()
	 */
	protected function initActionUpdate()
	{
		parent::initActionUpdate();
		$this->initEdit();
	}

	/**
	 * Handle the selection of a booking for creating an op note
	 *
	 * (non-phpdoc)
	 * @see parent::actionCreate()
	 */
	public function actionCreate()
	{
		$errors = array();

		if (!empty($_POST)) {
			if (preg_match('/^booking([0-9]+)$/',@$_POST['SelectBooking'],$m)) {
				$this->redirect(array('/OphTrOperationnote/Default/create?patient_id='.$this->patient->id.'&booking_event_id='.$m[1]));
			} elseif (@$_POST['SelectBooking'] == 'emergency') {
				$this->redirect(array('/OphTrOperationnote/Default/create?patient_id='.$this->patient->id.'&unbooked=1'));
			}

			$errors = array('Operation' => array('Please select a booked operation'));
		}

		if ($this->booking_operation || $this->unbooked) {
			parent::actionCreate();
		} else {
			// set up form for selecting a booking for the Op note
			$bookings = array();

			if ($api = Yii::app()->moduleAPI->get('OphTrOperationbooking')) {
				$bookings = $api->getOpenBookingsForEpisode($this->episode->id);
			}

			$this->title = "Please select booking";
			$this->event_tabs = array(
				array(
					'label' => 'Select a booking',
					'active' => true,
				),
			);
			$cancel_url = ($this->episode) ? '/patient/episode/'.$this->episode->id : '/patient/episodes/'.$this->patient->id;
			$this->event_actions = array(
				EventAction::link('Cancel',
					Yii::app()->createUrl($cancel_url),
					null, array('class' => 'button small warning')
				)
			);

			$this->render('select_event',array(
				'errors' => $errors,
				'bookings' => $bookings,
			));
		}
	}

	/**
	 * Ensures that any attached operation booking status is updated after the op note is removed
	 *
	 * @param $id
	 * @return bool|void
	 */
	public function actionDelete($id)
	{
		$proclist = Element_OphTrOperationnote_ProcedureList::model()->find('event_id=?',array($id));

		$this->dont_redirect = true;

		if (parent::actionDelete($id)) {
			if ($proclist && $proclist->booking_event_id) {
				if ($api = Yii::app()->moduleAPI->get('OphTrOperationbooking')) {
					$api->setOperationStatus($proclist->booking_event_id, 'Scheduled or Rescheduled');
				}
			}

			if (Event::model()->count('episode_id=?',array($this->event->episode_id)) == 0) {
				$this->redirect(array('/patient/episodes/'.$this->event->episode->patient->id));
			} else {
				$this->redirect(array('/patient/episode/'.$this->event->episode_id));
			}
		}
	}

	/**
	 * Suppress default behaviour - optional elements are managed through the procedure selection
	 *
	 * @return array
	 */
	public function getOptionalElements()
	{
		return array();
	}

	/**
	 * Ajax action to load the required elements for a procedure
	 *
	 * @throws SystemException
	 */
	public function actionLoadElementByProcedure()
	{
		if (!$proc = Procedure::model()->findByPk((integer) @$_GET['procedure_id'])) {
			throw new SystemException('Procedure not found: '.@$_GET['procedure_id']);
		}

		$form = new BaseEventTypeCActiveForm;

		$procedureSpecificElements = $this->getProcedureSpecificElements($proc->id);

		foreach ($procedureSpecificElements as $element) {
			$class_name = $element->element_type->class_name;

			$element = new $class_name;
			$patientId = Yii::app()->request->getParam('patientId');
			if( $patientId > 0 ) {
				$element->patientId = $patientId;
			}

			// FIXME: define a property on the element to indicate that specific eye is required
			$requiresEye = array(
				'Element_OphTrOperationnote_Cataract',
				'Element_OphTrOperationnote_Vitrectomy',
				'Element_OphTrOperationnote_Buckle'
			);
			if (in_array($class_name, $requiresEye) && array_key_exists('eye', $_GET) && !in_array($_GET['eye'],array(Eye::LEFT,Eye::RIGHT))) {
				echo "must-select-eye";
				return;
			}

			$element->setDefaultOptions();
			$this->renderElement($element, 'create', $form, array(), array('ondemand' => true), false, true);
		}

		if (count($procedureSpecificElements) == 0) {
			$element = new Element_OphTrOperationnote_GenericProcedure;
			$element->proc_id = $proc->id;
			$element->setDefaultOptions();
			$this->renderElement($element, 'create', $form, array(), array('ondemand' => true), false, true);
		}
	}

	/**
	 * Ajax function that works out what elements are no longer needed when a procedure has been removed.
	 *
	 * @throws SystemException
	 */
	public function actionGetElementsToDelete()
	{
		if (!$proc = Procedure::model()->findByPk((integer) @$_POST['procedure_id'])) {
			throw new SystemException('Procedure not found: '.@$_POST['procedure_id']);
		}

		$procedures = @$_POST['remaining_procedures'] ? explode(',',$_POST['remaining_procedures']) : array();

		$elements = array();

		foreach ($this->getProcedureSpecificElements($proc->id) as $element) {
			if (empty($procedures) || !OphTrOperationnote_ProcedureListOperationElement::model()->find('procedure_id in ('.implode(',',$procedures).') and element_type_id = '.$element->element_type->id)) {
				$elements[] = $element->element_type->class_name;
			}
		}

		die(json_encode($elements));
	}

	/**
	 *
	 * @param $procedure_id
	 * @return OphTrOperationnote_ProcedureListOperationElement[]
	 */
	public function getProcedureSpecificElements($procedure_id)
	{
		$criteria = new CDbCriteria;
		$criteria->compare('procedure_id',$procedure_id);
		$criteria->order = 'display_order asc';

		return OphTrOperationnote_ProcedureListOperationElement::model()->findAll($criteria);
	}

	/**
	 * Renders procedure specific elements - wrapper for rendering child elements of the procedure list element
	 *
	 * @param $action
	 * @param BaseCActiveBaseEventTypeCActiveForm $form
	 * @param array $data
	 */
	public function renderAllProcedureElements($action, $form=null, $data=null)
	{
		foreach ($this->open_elements as $el) {
			if (get_class($el) == 'Element_OphTrOperationnote_ProcedureList') {
				$this->renderChildOpenElements($el, $action, $form, $data);
			}
		}
	}

	/**
	 * Overrides for procedure list to render the elements in the order they are selected
	 *
	 * @param BaseEventTypeElement $parent_element
	 * @param string $action
	 * @param BaseCActiveBaseEventTypeCActiveForm $form
	 * @param array $data
	 * @throws Exception
	 *
	 * (non-phpdoc)
	 * @see parent::renderChildOpenElements($parent_element, $action, $form, $data)
	 */
	public function renderChildOpenElements($parent_element, $action, $form=null, $data=null)
	{

		if (get_class($parent_element) == 'Element_OphTrOperationnote_ProcedureList') {
			// index the child elements
			$by_cls = array();
			$by_proc_id = array();
			$children = $this->getChildElements($parent_element->getElementType());

			foreach ($children as $child) {
				$cls = get_class($child);
				if ($child->hasAttribute('proc_id')) {
					$by_proc_id[$child->proc_id] = $child;
				}
				else {
					if (isset($by_cls[$cls])) {
						$by_cls[$cls][] = $child;
					} else {
						$by_cls[$cls] = array($child);
					}
				}
			}

			// generate correctly ordered list of elements based on procedure order
			$elements = array();
			foreach ($parent_element->procedures as $proc) {
				if (isset($by_proc_id[$proc->id])) {
					$elements[] = $by_proc_id[$proc->id];
				}
				else {
					$procedure_elements = $this->getProcedureSpecificElements($proc->id);
					foreach ($procedure_elements as $proc_el) {
						if (isset($by_cls[$proc_el->element_type->class_name])) {
							if ($el = array_shift($by_cls[$proc_el->element_type->class_name])) {
								$el->patientId = $this->patient->id;
								$elements[] = $el;
							}
						}
					}
				}
			}

			foreach ($elements as $el) {
				$this->renderElement($el, $action, $form, $data);
			}
		}
		else {
			parent::renderChildOpenElements($parent_element, $action, $form, $data);
		}

	}

	/**
	 * Ajax method for checking whether a procedure requires the eye to be set
	 */
	public function actionVerifyprocedure()
	{
		if (!empty($_GET['name'])) {
			$proc = Procedure::model()->findByAttributes(array('term' => $_GET['name']));
			if ($proc) {
				if ($this->procedure_requires_eye($proc->id)) {
					echo "no";
				} else {
					echo "yes";
				}
			}
		} else {
			$i = 0;
			$result = true;
			$procs = array();
			while (isset($_GET['proc'.$i])) {
				if ($this->procedure_requires_eye($_GET['proc'.$i])) {
					$result = false;
					$procs[] = Procedure::model()->findByPk($_GET['proc'.$i])->term;
				}
				$i++;
			}
			if ($result) {
				echo "yes";
			} else {
				echo implode("\n",$procs);
			}
		}
	}

	/**
	 * returns true if the passed procedure id requires the selection of 'left' or 'right' eye
	 *
	 * @param $procedure_id
	 * @return boolean
	 */
	public function procedure_requires_eye($procedure_id)
	{
		foreach ($this->getProcedureSpecificElements($procedure_id) as $plpa) {
			$element_type = ElementType::model()->findByPk($plpa->element_type_id);

			if (in_array($element_type->class_name,array('Element_OphTrOperationnote_Cataract','Element_OphTrOperationnote_Buckle','Element_OphTrOperationnote_Vitrectomy'))) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Works out the eye that should be used for an eyedraw
	 *
	 * @FIXME: This should be a property on the element, or a variable passed to render.
	 * @return Eye
	 * @throws SystemException
	 */
	public function getSelectedEyeForEyedraw()
	{
		$eye = new Eye;

		if (!empty($_POST['Element_OphTrOperationnote_ProcedureList']['eye_id'])) {
			$eye = Eye::model()->findByPk($_POST['Element_OphTrOperationnote_ProcedureList']['eye_id']);
		} else if ($this->event && $this->event->id) {
			$eye = Element_OphTrOperationnote_ProcedureList::model()->find('event_id=?',array($this->event->id))->eye;
		} else if (!empty($_GET['eye'])) {
			$eye = Eye::model()->findByPk($_GET['eye']);
		} else if ($this->action->id == 'create') {
			// Get the procedure list and eye from the most recent booking for the episode of the current user's subspecialty
			if (!$patient = Patient::model()->findByPk(@$_GET['patient_id'])) {
				throw new SystemException('Patient not found: '.@$_GET['patient_id']);
			}

			if ($episode = $patient->getEpisodeForCurrentSubspecialty()) {
				if ($api = Yii::app()->moduleAPI->get('OphTrOperationbooking')) {
					if ($booking = $api->getMostRecentBookingForEpisode($episode)) {
						$eye = $booking->operation->eye;
					}
				}
			}
		}

		if ($eye->name == 'Both') {
			$eye = Eye::model()->find('name=?',array('Right'));
		}

		return $eye;
	}

	/**
	 * @param Element_OphTrOperationnote_ProcedureList $element
	 * @param $data
	 * @param $index
	 */
	protected function setComplexAttributes_Element_OphTrOperationnote_ProcedureList($element, $data, $index)
	{
		$procs = array();
		if (isset($data['Procedures_procs'])) {
			foreach ($data['Procedures_procs'] as $proc_id) {
				$procs[] = Procedure::model()->findByPk($proc_id);
			}
		}
		$element->procedures = $procs;
	}

	/**
	 * @param Element_OphTrOperationnote_ProcedureList $element
	 * @param array $data
	 * @param integer $index
	 */
	protected function saveComplexAttributes_Element_OphTrOperationnote_ProcedureList($element, $data, $index)
	{
		$element->updateProcedures(isset($data['Procedures_procs']) ? $data['Procedures_procs'] : array());
	}

	/**
	 * Update the anaesthetic agents and complications
	 *
	 * @param Element_OphTrOperationnote_Anaesthetic $element
	 * @param $data
	 * @param $index
	 */
	protected function saveComplexAttributes_Element_OphTrOperationnote_Anaesthetic($element, $data, $index)
	{
		$element->updateAnaestheticAgents(isset($data['AnaestheticAgent']) ? $data['AnaestheticAgent'] : array());
		$element->updateComplications(isset($data['OphTrOperationnote_AnaestheticComplications']) ? $data['OphTrOperationnote_AnaestheticComplications'] : array());
	}

	/**
	 * Set complications and operative devices for validation
	 *
	 * @param $element
	 * @param $data
	 * @param $index
	 */
	protected function setComplexAttributes_Element_OphTrOperationnote_Cataract($element, $data, $index)
	{
		$complications = array();
		if (isset($data['OphTrOperationnote_CataractComplications']) && is_array($data['OphTrOperationnote_CataractComplications'])) {
			foreach ($data['OphTrOperationnote_CataractComplications'] as $c_id) {
				$complications[] = OphTrOperationnote_CataractComplications::model()->findByPk($c_id);
			}
		}
		$element->complications = $complications;

		$devices = array();
		if (isset($data['OphTrOperationnote_CataractOperativeDevices'])) {
			foreach ($data['OphTrOperationnote_CataractOperativeDevices'] as $oa_id) {
				$devices[] = OphTrOperationnote_CataractComplications::model()->findByPk($oa_id);
			}
		}
		$element->operative_devices = $devices;
	}

	/**
	 * Update the complications and the operative devices
	 *
	 * @param Element_OphTrOperationnote_Cataract $element
	 * @param $data
	 * @param $index
	 */
	protected function saveComplexAttributes_Element_OphTrOperationnote_Cataract($element, $data, $index)
	{
		$element->updateComplications(isset($data['OphTrOperationnote_CataractComplications']) ? $data['OphTrOperationnote_CataractComplications'] : array());
		$element->updateOperativeDevices(isset($data['OphTrOperationnote_CataractOperativeDevices']) ? $data['OphTrOperationnote_CataractOperativeDevices'] : array());
	}

	/**
	 * Set the drugs for the element
	 *
	 * @param Element_OphTrOperationnote_PostOpDrugs $element
	 * @param $data
	 * @param $index
	 */
	protected function setComplexAttributes_Element_OphTrOperationnote_PostOpDrugs($element, $data, $index)
	{
		$drugs = array();
		if (isset($data['Drug']) && (!empty($data['Drug']))) {
			foreach ($data['Drug'] as $d_id) {
				$drugs[] = OphTrOperationnote_PostopDrug::model()->findByPk($d_id);
			}
		}
		$element->drugs = $drugs;
	}

	/**
	 * Update the drug assignments
	 *
	 * @param Element_OphTrOperationnote_PostOpDrugs $element
	 * @param $data
	 * @param $index
	 */
	protected function saveComplexAttributes_Element_OphTrOperationnote_PostOpDrugs($element, $data, $index)
	{
		$element->updateDrugs(isset($data['Drug']) ? $data['Drug'] : array());

	}

	/**
	 * Set the complications for the Element_OphTrOperationote_Trabectome element
	 * @param $element
	 * @param $data
	 * @param $index
	 */
	protected function setComplexAttributes_Element_OphTrOperationnote_Trabectome($element, $data, $index)
	{
		$model_name = CHtml::modelName($element);
		$complications = array();
		if (@$data[$model_name]['complications']) {
			foreach ($data[$model_name]['complications'] as $id) {
				$complications[] = OphTrOperationnote_Trabectome_Complication::model()->findByPk($id);
			}
		}
		$element->complications = $complications;
	}

	protected function saveComplexAttributes_Element_OphTrOperationnote_Trabectome($element, $data, $index)
	{
		$model_name = CHtml::modelName($element);
		$element->updateComplications(isset($data[$model_name]['complications']) ? $data[$model_name]['complications'] : array() );
	}

	/**
	 * Return the anaesthetic agent list
	 *
	 * @param Element_OphTrOperationnote_Anaesthetic $element
	 * @return array
	 */
	public function getAnaesthetic_agent_list($element)
	{
		$agents = $this->getAnaestheticAgentsBySiteAndSubspecialty();
		$list = CHtml::listData($agents,'id','name');
		$curr_list = CHtml::listData($element->anaesthetic_agents, 'id', 'name');
		if ($missing = array_diff($curr_list, $list)) {
			foreach ($missing as $id => $name) {
				$list[$id] =  $name;
			}
		}
		return $list;
	}

	/**
	 * Retrieve AnaestheticAgent instances relevant to the current site and subspecialty. The relation flag indicates
	 * whether we are retrieve the full list of defaults.
	 *
	 * @param string $relation
	 * @return array
	 */
	protected function getAnaestheticAgentsBySiteAndSubspecialty($relation = 'siteSubspecialtyAssignments')
	{
		$criteria = new CDbCriteria;
		$criteria->addCondition('site_id = :siteId and subspecialty_id = :subspecialtyId');
		$criteria->params[':siteId'] = Yii::app()->session['selected_site_id'];
		$criteria->params[':subspecialtyId'] = $this->firm->getSubspecialtyID();
		$criteria->order = 'name';

		return AnaestheticAgent::model()
			->active()
			->with(array(
					$relation => array(
						'joinType' => 'JOIN',
					),
				))
			->findAll($criteria);
	}

	/**
	 * Return the list of possible operative devices for the given element
	 *
	 * @param Element_OphTrOperationnote_Cataract $element
	 * @return array
	 */
	public function getOperativeDeviceList($element)
	{
		$curr_list = CHtml::listData($element->operative_devices, 'id', 'name');
		$devices = $this->getOperativeDevicesBySiteAndSubspecialty(false,array_keys($curr_list));
		return CHtml::listData($devices,'id','name');
	}

	/**
	 * Get the ids of the default anaesthetic agents for the current site and subspecialty
	 *
	 * @return array
	 */
	public function getOperativeDeviceDefaults()
	{
		$ids = array();
		foreach ($this->getOperativeDevicesBySiteAndSubspecialty(true) as $operative_device) {
			$ids[] = $operative_device->id;
		}
		return $ids;
	}

	/**
	 * Retrieve OperativeDevice instances relevant to the current site and subspecialty. The default flag indicates
	 * whether we are retrieve the full list of defaults.
	 *
	 * @param bool $default
	 * @return OperativeDevice[]
	 */
	protected function getOperativeDevicesBySiteAndSubspecialty($default = false, $include_ids = null)
	{
		$criteria = new CDbCriteria;
		$criteria->addCondition('subspecialty_id = :subspecialtyId and site_id = :siteId');
		$criteria->params[':subspecialtyId'] = $this->firm->getSubspecialtyID();
		$criteria->params[':siteId'] = Yii::app()->session['selected_site_id'];

		if ($default) {
			$criteria->addCondition('siteSubspecialtyAssignments.default = :one');
			$criteria->params[':one'] = 1;
		}

		$criteria->order = 'name asc';

		return OperativeDevice::model()
			->activeOrPk($include_ids)
			->with(array(
					'siteSubspecialtyAssignments' => array(
						'joinType' => 'JOIN',
					),
				))
			->findAll($criteria);
	}

	/**
	 * Get the drug options for the element for the controller state
	 *
	 * @param Element_OphTrOperationnote_PostOpDrugs $element
	 * @return array
	 */
	public function getPostOpDrugList($element)
	{
		$drug_ids = array();
		foreach ($element->drugs as $drug) {
			$drug_ids[] = $drug->id;
		}

		$drugs = $this->getPostOpDrugsBySiteAndSubspecialty(false,$drug_ids);
		return CHtml::listData($drugs,'id','name');
	}

	/**
	 * Return the post op drugs for the current site and subspecialty
	 *
	 * @param bool $default
	 * @return OphTrOperationnote_PostopDrug[]
	 */
	protected function getPostOpDrugsBySiteAndSubspecialty($default=false, $include_ids=null)
	{
		$criteria = new CDbCriteria;
		$criteria->addCondition('subspecialty_id = :subspecialtyId and site_id = :siteId');
		$criteria->params[':subspecialtyId'] = $this->firm->getSubspecialtyID();
		$criteria->params[':siteId'] = Yii::app()->session['selected_site_id'];

		if ($default) {
			$criteria->addCondition('siteSubspecialtyAssignments.default = :one');
			$criteria->params[':one'] = 1;
		}

		$criteria->order = 'name asc';

		return OphTrOperationnote_PostopDrug::model()
			->with(array(
					'siteSubspecialtyAssignments' => array(
						'joinType' => 'JOIN',
					),
				))
			->activeOrPk($include_ids)
			->findAll($criteria);
	}

	/**
	 * Helper method to get the site for the operation booking on this event
	 *
	 * (currently only supports events that have been saved)
	 *
	 * @return null
	 */
	public function findBookingSite()
	{
		if ($pl = Element_OphTrOperationnote_ProcedureList::model()->find('event_id=?',array($this->event->id))) {
			if ($pl->bookingEvent) {
				if ($api = Yii::app()->moduleAPI->get('OphTrOperationbooking')) {
					return $api->findSiteForBookingEvent($pl->bookingEvent);
				}
			}
		}

		return null;
	}

	protected function setComplexAttributes_Element_OphTrOperationnote_Trabeculectomy($element, $data, $index)
	{
		$difficulties = array();

		if (!empty($data['MultiSelect_Difficulties'])) {
			foreach ($data['MultiSelect_Difficulties'] as $difficulty_id) {
				$assignment = new OphTrOperationnote_Trabeculectomy_Difficulties;
				$assignment->difficulty_id = $difficulty_id;

				$difficulties[] = $assignment;
			}
		}

		$element->difficulties = $difficulties;

		$complications = array();

		if (!empty($data['MultiSelect_Complications'])) {
			foreach ($data['MultiSelect_Complications'] as $complication_id) {
				$assignment = new OphTrOperationnote_Trabeculectomy_Complications;
				$assignment->complication_id = $complication_id;

				$complications[] = $assignment;
			}
		}

		$element->complications = $complications;
	}

	protected function saveComplexAttributes_Element_OphTrOperationnote_Trabeculectomy($element, $data, $index)
	{
		$element->updateMultiSelectData('OphTrOperationnote_Trabeculectomy_Difficulties',empty($data['MultiSelect_Difficulties']) ? array() : $data['MultiSelect_Difficulties'],'difficulty_id');
		$element->updateMultiSelectData('OphTrOperationnote_Trabeculectomy_Complications',empty($data['MultiSelect_Complications']) ? array() : $data['MultiSelect_Complications'],'complication_id');
	}

	public function actionGetImage()
	{
		preg_match('/data\:image\/png;base64,(.*)$/',$_POST['image'],$m);

		file_put_contents("/tmp/image.png",base64_decode($m[1]));
	}

	public function getBookingOperation()
	{
		if($this->booking_operation){
			return $this->booking_operation;
		}else{
			return false;
		}
	}

	public function actionGetTheatreOptions(){
		$siteId = $this->request->getParam("siteId");
		if( $siteId >0 ) {
			$optionValues = OphTrOperationbooking_Operation_Theatre::model()->findAll(array(
				'condition' => 'active=1 and site_id=' . $siteId,
				'order' => 'name'
			));
			//var_dump($optionValues);
			echo CHtml::dropDownList(
				"theatre_id",
				false,
				CHtml::listData($optionValues, 'id', 'name'),
				array(
					'empty'=>'-- Please select --')
				);
		}

	}
}
