<?php

class BaseEventTypeController extends BaseController
{
	public $model;
	public $firm;
	public $patient;
	public $site;
	public $editable;
	public $editing;
	public $event;
	public $event_type;
	public $title;
	public $assetPath;
	public $episode;

	public function actionIndex()
	{
		$this->render('index');
	}

	public function printActions() {
		return array('print');
	}
	
	protected function beforeAction($action) {

		// Need to initialise base CSS first
		$parent_return = parent::beforeAction($action);
		
		// Set asset path
		if (file_exists(Yii::getPathOfAlias('application.modules.'.$this->getModule()->name.'.assets'))) {
			$this->assetPath = Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('application.modules.'.$this->getModule()->name.'.assets'), false, -1, YII_DEBUG);
		}

		// Automatic file inclusion unless it's an ajax call
		if($this->assetPath && !Yii::app()->getRequest()->getIsAjaxRequest()) {
		
			if (in_array($action->id,$this->printActions())) {
				
				// Register print css
				if(file_exists(Yii::getPathOfAlias('application.modules.'.$this->getModule()->name.'.assets.css').'/print.css')) {
					Yii::app()->getClientScript()->registerCssFile($this->assetPath.'/css/print.css');
				}

			} else {

				// Register js
				$js_dh = opendir(Yii::getPathOfAlias('application.modules.'.$this->getModule()->name.'.assets.js'));
				while ($file = readdir($js_dh)) {
					if (preg_match('/\.js$/',$file)) {
						Yii::app()->clientScript->registerScriptFile($this->assetPath.'/js/'.$file);
					}
				}
				closedir($js_dh);

				// Register css
				$css_dh = opendir(Yii::getPathOfAlias('application.modules.'.$this->getModule()->name.'.assets.css'));
				while ($file = readdir($css_dh)) {
					if (preg_match('/\.css$/',$file)) {
						if ($file != 'print.css') {
							// Skip print.css as it's /only/ for print layouts
							Yii::app()->getClientScript()->registerCssFile($this->assetPath.'/css/'.$file);
						}
					}
				}
				closedir($css_dh);
				
			}
		}
		
		parent::storeData();

		$this->firm = Firm::model()->findByPk($this->selectedFirmId);

		if (!isset($this->firm)) {
			// No firm selected, reject
			throw new CHttpException(403, 'You are not authorised to view this page without selecting a firm.');
		}

		// Clear js for ajax calls
		if (Yii::app()->getRequest()->getIsAjaxRequest()) {
			$scriptMap = Yii::app()->clientScript->scriptMap;
			$scriptMap['jquery.js'] = false;
			$scriptMap['jquery.min.js'] = false;
			$scriptMap['jquery-ui.js'] = false;
			$scriptMap['jquery-ui.min.js'] = false;
			$scriptMap['module.js'] = false;
			Yii::app()->clientScript->scriptMap = $scriptMap;
		}

		return $parent_return;
	}

	/**
	 * Get all the elements for an event, the current module or an event_type
	 *
	 * @return array
	 */
	public function getDefaultElements($action, $event_type_id=false, $event=false) {
		if (!$event && isset($this->event)) {
			$event = $this->event;
		}

		if (isset($event->event_type_id)) {
			$event_type = EventType::model()->find('id = ?',array($event->event_type_id));
		} else if ($event_type_id) {
			$event_type = EventType::model()->find('id = ?',array($event_type_id));
		} else {
			$event_type = EventType::model()->find('class_name = ?',array($this->getModule()->name));
		}

		$criteria = new CDbCriteria;
		$criteria->compare('event_type_id',$event_type->id);
		$criteria->order = 'display_order asc';

		$elements = array();

		if (empty($_POST)) {
			if (isset($event->event_type_id)) {
				foreach (ElementType::model()->findAll($criteria) as $element_type) {
					$element_class = $element_type->class_name;

					if ($element = $element_class::model()->find('event_id = ?',array($event->id))) {
						$elements[] = $element;
					}
				}
			} else {
				$criteria->compare('`default`',1);

				foreach (ElementType::model()->findAll($criteria) as $element_type) {
					$element_class = $element_type->class_name;
					$elements[] = new $element_class;
				}
			}
		} else {
			foreach ($_POST as $key => $value) {
				if (preg_match('/^Element|^OEElement/',$key)) {
					if ($element_type = ElementType::model()->find('class_name=?',array($key))) {
						$element_class = $element_type->class_name;

						if (isset($event->event_type_id) && ($element = $element_class::model()->find('event_id = ?',array($event->id)))) {
							$elements[] = $element;
						} else {
							if ($action != 'update' || !$element_type->default) {
								$elements[] = new $element_class;
							}
						}
					}
				}
			}
		}

		return $elements;
	}

	/**
	 * Get the optional elements for the current module's event type
	 * This will be overriden by the module
	 *
	 * @return array
	 */
	public function getOptionalElements($action) {
		switch ($action) {
			case 'create':
			case 'view':
			case 'print':
				return array();
			case 'update':
				$event_type = EventType::model()->findByPk($this->event->event_type_id);

				$criteria = new CDbCriteria;
				$criteria->compare('event_type_id',$event_type->id);
				$criteria->compare('`default`',1);
				$criteria->order = 'display_order asc';

				$elements = array();
				$element_classes = array();

				foreach (ElementType::model()->findAll($criteria) as $element_type) {
					$element_class = $element_type->class_name;
					if (!$element_class::model()->find('event_id = ?',array($this->event->id))) {
						$elements[] = new $element_class;
					}
				}

				return $elements;
		}
	}

	public function actionCreate() {
		$this->event_type = EventType::model()->find('class_name=?', array($this->getModule()->name));
		if (!$this->patient = Patient::model()->findByPk($_REQUEST['patient_id'])) {
			throw new CHttpException(403, 'Invalid patient_id.');
		}

		if (is_array(Yii::app()->params['modules_disabled']) && in_array($this->event_type->class_name,Yii::app()->params['modules_disabled'])) {
			return $this->redirect(array('/patient/episodes/'.$this->patient->id));
		}

		$session = Yii::app()->session;
		$firm = Firm::model()->findByPk($session['selected_firm_id']);
		$this->episode = $this->getEpisode($firm, $this->patient->id);

		// firm changing sanity
		if (!empty($_POST) && !empty($_POST['firm_id']) && $_POST['firm_id'] != $this->firm->id) {
			// The firm id in the firm is not the same as the session firm id, e.g. they've changed
			// firms in a different tab. Set the session firm id to the provided firm id.

			$firms = $session['firms'];
			$firmId = intval($_POST['firm_id']);

			if ($firms[$firmId]) {
				$session['selected_firm_id'] = $firmId;
				$this->selectedFirmId = $firmId;
				$this->firm = Firm::model()->findByPk($this->selectedFirmId);
			} else {
				// They've supplied a firm id in the post to which they are not entitled??
				throw new Exception('Invalid firm id on attempting to create event.');
			}
		}
		$elements = $this->getDefaultElements('create', $this->event_type->id);

		if (empty($_POST) && !count($elements)) {
			throw new CHttpException(403, 'Gadzooks!	I got me no elements!');
		}

		if (!empty($_POST) && isset($_POST['cancel'])) {
			$this->redirect(array('/patient/view/'.$this->patient->id));
			return;
		} else if(!empty($_POST) && !count($elements)) {
			$errors['Event'][] = 'No elements selected';
		} else if (!empty($_POST)) {

			$elements = array();
			$element_names = array();

			foreach (ElementType::model()->findAll('event_type_id=?',array($this->event_type->id)) as $element_type) {
				if (isset($_POST[$element_type->class_name])) {
					$elements[] = new $element_type->class_name;
					$element_names[$element_type->class_name] = $element_type->name;
				}
			}

			$elementList = array();

			// validation
			foreach ($elements as $element) {
				$elementClassName = get_class($element);
				$element->attributes = Helper::convertNHS2MySQL($_POST[$elementClassName]);
				$elementList[] = $element;
				if (!$element->validate()) {
					foreach ($element->getErrors() as $errormsgs) {
						foreach ($errormsgs as $error) {
							$index = $element_names[$elementClassName]; //preg_replace('/^Element/','',$elementClassName);
							$errors[$index][] = $error;
						}
					}
				}
			}

			// creation
			if (empty($errors)) {
				// The user has submitted the form to create the event
				$eventId = $this->createElements(
					$elements, $_POST, $this->firm, $this->patient->id, Yii::app()->user->id, $this->event_type->id
				);

				if ($eventId) {
					$this->logActivity('created event.');

					$event = Event::model()->findByPk($eventId);

					$audit_data = array('event' => $event->getAuditAttributes());

					foreach ($elements as $element) {
						$audit_data[get_class($element)] = $element->getAuditAttributes();
					}

					$audit = new Audit;
					$audit->action = "create";
					$audit->target_type = "event";
					$audit->patient_id = $event->episode->patient->id;
					$audit->episode_id = $event->episode_id;
					$audit->event_id = $event->id;
					$audit->user_id = (Yii::app()->session['user'] ? Yii::app()->session['user']->id : null);
					$audit->data = serialize($audit_data);
					$audit->save();

					Yii::app()->user->setFlash('success', "{$this->event_type->name} created.");
					$this->redirect(array('default/view/'.$eventId));
					return;
				}
			}
		}

		$this->editable = false;
		$this->title = 'Create';

		$this->renderPartial(
			'create',
			array('elements' => $this->getDefaultElements('create'), 'eventId' => null, 'errors' => @$errors),
			// processOutput is true so that the css/javascript from the event_header.php are processed when rendering the view
			false, true
		);

	}

	public function actionView($id) {
		if (!$this->event = Event::model()->findByPk($id)) {
			throw new CHttpException(403, 'Invalid event id.');
		}
		$this->patient = $this->event->episode->patient;
		$this->event_type = EventType::model()->findByPk($this->event->event_type_id);
		$this->episode = $this->event->episode;

		$elements = $this->getDefaultElements('view');

		// Decide whether to display the 'edit' button in the template
		if (!$this->event->episode->firm) {
			$this->editable = false;
		} else {	
			if ($this->firm->serviceSubspecialtyAssignment->subspecialty_id != $this->event->episode->firm->serviceSubspecialtyAssignment->subspecialty_id) {
				$this->editable = false;
			} else {
				$this->editable = true;
			}
		}
		// Allow elements to override the editable status
		if ($this->editable) {
			foreach ($elements as $element) {
				if (!$element->isEditable()) {
					$this->editable = false;
					break;
				}
			}
		}

		$currentSite = Site::model()->findByPk(Yii::app()->request->cookies['site_id']->value);
		$this->logActivity('viewed event');

		$audit = new Audit;
		$audit->action = "view";
		$audit->target_type = "event";
		$audit->patient_id = $this->event->episode->patient->id;
		$audit->episode_id = $this->event->episode_id;
		$audit->event_id = $this->event->id;
		$audit->user_id = (Yii::app()->session['user'] ? Yii::app()->session['user']->id : null);
		$audit->save();

		$this->title = $this->event_type->name;

		$this->renderPartial(
			'view', array(
			'elements' => $elements,
			'eventId' => $id,
			), false, true);
	}

	public function actionUpdate($id) {
		if (!$this->event = Event::model()->findByPk($id)) {
			throw new CHttpException(403, 'Invalid event id.');
		}

		// Check the user's firm is of the correct subspecialty to have the
		// rights to update this event
		if ($this->firm->serviceSubspecialtyAssignment->subspecialty_id !=
			$this->event->episode->firm->serviceSubspecialtyAssignment->subspecialty_id) {
			throw new CHttpException(403, 'The firm you are using is not associated with the subspecialty for this event.');
		}

		$this->event_type = EventType::model()->findByPk($this->event->event_type_id);
		$this->patient = $this->event->episode->patient;
		$this->episode = $this->event->episode;

		// firm changing sanity
		if (!empty($_POST) && !empty($_POST['firm_id']) && $_POST['firm_id'] != $this->firm->id) {
			// The firm id in the firm is not the same as the session firm id, e.g. they've changed
			// firms in a different tab. Set the session firm id to the provided firm id.

			$session = Yii::app()->session;

			$firms = $session['firms'];
			$firmId = intval($_POST['firm_id']);

			if ($firms[$firmId]) {
				$session['selected_firm_id'] = $firmId;
				$this->selectedFirmId = $firmId;
				$this->firm = Firm::model()->findByPk($this->selectedFirmId);
			} else {
				// They've supplied a firm id in the post to which they are not entitled??
				throw new Exception('Invalid firm id on attempting to update event.');
			}
		}

		if (empty($_POST) && !count($this->getDefaultElements('update'))) {
			throw new CHttpException(403, 'Gadzooks!	I got me no elements!');
		}

		if (!empty($_POST) && isset($_POST['cancel'])) {
			// Cancel button pressed, so just bounce to view
			$this->redirect(array('default/view/'.$this->event->id));
			return;
		} else if(!empty($_POST) && !count($this->getDefaultElements('update'))) {
			$errors['Event'][] = 'No elements selected';
		} else if (!empty($_POST)) {
			
			$elements = array();
			$to_delete = array();
			foreach (ElementType::model()->findAll('event_type_id=?',array($this->event_type->id)) as $element_type) {
				$class_name = $element_type->class_name;
				if (isset($_POST[$class_name])) {
					if ($element = $class_name::model()->find('event_id=?',array($this->event->id))) {
						// Add existing element to array
						$elements[] = $element;
					} else {
						// Add new element to array
						$elements[] = new $class_name;
					}
				} else if($element = $class_name::model()->find('event_id=?',array($this->event->id))) {
					// Existing element is not posted, so we need to delete it
					$to_delete[] = $element;
				}
			}

			// validation
			foreach ($elements as $element) {
				$elementClassName = get_class($element);
				$element->attributes = Helper::convertNHS2MySQL($_POST[$elementClassName]);
				if (!$element->validate()) {
					$elementName = $element->getElementType()->name;
					foreach ($element->getErrors() as $errormsgs) {
						foreach ($errormsgs as $error) {
							$errors[$elementName][] = $error;
						}
					}
				}
			}

			// creation
			if (empty($errors)) {
				
				// Need to pass through _all_ elements to updateElements (those not in _POST will be deleted)
				$all_elements = array_merge($elements, $to_delete);
				$success = $this->updateElements($all_elements, $_POST, $this->event);

				if ($success) {
					$info_text = '';
					foreach ($elements as $element) {
						if ($element->infotext) {
							$info_text .= $element->infotext;
						}
					}

					$this->logActivity('updated event');

					$audit_data = array('event' => $this->event->getAuditAttributes());

					foreach ($elements as $element) {
						$audit_data[get_class($element)] = $element->getAuditAttributes();
					}

					$audit = new Audit;
					$audit->action = "update";
					$audit->target_type = "event";
					$audit->patient_id = $this->event->episode->patient->id;
					$audit->episode_id = $this->event->episode_id;
					$audit->event_id = $this->event->id;
					$audit->user_id = (Yii::app()->session['user'] ? Yii::app()->session['user']->id : null);
					$audit->data = serialize($audit_data);
					$audit->save();

					// Update event to indicate user has made a change
					// $this->event->datetime = date("Y-m-d H:i:s");
					$this->event->user = Yii::app()->user->id;
					$this->event->info = $info_text;

					if (!$this->event->save()) {
						throw new SystemException('Unable to update event: '.print_r($this->event->getErrors(),true));
					}

					OELog::log("Updated event {$this->event->id}");

					$this->redirect(array('default/view/'.$this->event->id));
					return;
				}
			}
		}

		$this->editing = true;
		$this->title = 'Update';

		$this->renderPartial(
			'update',
			array(
				'elements' => $this->getDefaultElements('update'),
				'errors' => @$errors
			),
			// processOutput is true so that the css/javascript from the event_header.php are processed when rendering the view
			false, true
		);
	}

	public function renderDefaultElements($action, $form=false, $data=false) {
		foreach ($this->getDefaultElements($action) as $element) {
			if ($action == 'create' && empty($_POST)) {
				$element->setDefaultOptions();
			}

			$this->renderPartial(
				$action . '_' . $element->{$action.'_view'},
				array('element' => $element, 'data' => $data, 'form' => $form),
				false, false
			);
		}
	}

	public function renderOptionalElements($action, $form=false,$data=false) {
		foreach ($this->getOptionalElements($action) as $element) {
			if ($action == 'create' && empty($_POST)) {
				$element->setDefaultOptions();
			}

			$this->renderPartial(
				$action . '_' . $element->{$action.'_view'},
				array('element' => $element, 'data' => $data, 'form' => $form),
				false, false
			);
		}
	}

	public function header($editable=null) {
		$episodes = $this->patient->episodes;
		$ordered_episodes = $this->patient->getOrderedEpisodes();
		/*
		$ordered_episodes = array();
		foreach ($episodes as $ep) {
			$ordered_episodes[$ep->firm->serviceSubspecialtyAssignment->subspecialty->specialty->name][] = $ep;
		}
		*/
		$legacyepisodes = $this->patient->legacyepisodes;

		if($editable === null){
			if(isset($this->event)){
				$editable = $this->event->editable;
			}else{
				$editable = false;
			}
		}

		$this->renderPartial('//patient/event_header',array(
			'ordered_episodes'=>$ordered_episodes,
			'legacyepisodes'=>$legacyepisodes,
			'eventTypes'=>EventType::model()->getEventTypeModules(),
			'model'=>$this->patient,
			'editable'=>$editable,
		));
	}

	public function footer() {
		$episodes = $this->patient->episodes;
		$legacyepisodes = $this->patient->legacyepisodes;

		$this->renderPartial('//patient/event_footer',array(
			'episodes'=>$episodes,
			'legacyepisodes'=>$legacyepisodes,
			'eventTypes'=>EventType::model()->getEventTypeModules()
		));
	}

	public function createElements($elements, $data, $firm, $patientId, $userId, $eventTypeId)
	{
		$valid = true;
		$elementsToProcess = array();

		// Go through the array of elements to see which the user is attempting to
		// create, which are required and whether they pass validation.
		foreach ($elements as $element) {
			$elementClassName = get_class($element);

			if ($element->required || isset($data[$elementClassName])) {
				if (isset($data[$elementClassName])) {
					$element->attributes = Helper::convertNHS2MySQL($data[$elementClassName]);
				}

				if (!$element->validate()) {
					$valid = false;
				} else {
					$elementsToProcess[] = $element;
				}
			}
		}

		if (!$valid) {
			return false;
		}

		/**
		 * Create the event. First check to see if there is currently an episode for this
		 * subspecialty for this patient. If so, add the new event to it. If not, create an
		 * episode and add it to that.
		 */
		$episode = $this->getOrCreateEpisode($firm, $patientId);
		$event = $this->createEvent($episode, $userId, $eventTypeId, $elementsToProcess);

		// Create elements for the event
		foreach ($elementsToProcess as $element) {
			$element->event_id = $event->id;

			// No need to validate as it has already been validated and the event id was just generated.
			if (!$element->save(false)) {
				throw new Exception('Unable to save element ' . get_class($element) . '.');
			}
		}

		return $event->id;
	}

	/**
	 * Update elements based on arrays passed over from $_POST data
	 *
	 * @param array		$elements		array of SiteElementTypes
	 * @param array		$data			$_POST data to update
	 * @param object $event				the associated event
	 *
	 * @return boolean $success		true if all elements suceeded, false otherwise
	 */
	public function updateElements($elements, $data, $event)
	{
		$success = true;
		$toDelete = array();
		$toSave = array();

		foreach ($elements as $element) {
			$elementClassName = get_class($element);
			$needsValidation = false;

			if (isset($data[$elementClassName])) {
				$element->attributes = Helper::convertNHS2MySQL($data[$elementClassName]);

				$toSave[] = $element;

				$needsValidation = true;
			} elseif ($element->required) {
				// The form has failed to provide an array of data for a required element.
				// This isn't supposed to happen - a required element should at least have the
				// $data[$elementClassName] present, even if there's nothing in it.
				$success = false;
			} elseif ($element->event_id) {
				// This element already exists, isn't required and has had its data deleted.
				// Therefore it needs to be deleted.
				$toDelete[] = $element;
			}

			if ($needsValidation) {
				if (!$element->validate()) {
					$success = false;
				}
			}
		}

		if (!$success) {
			// An element failed validation or a required element didn't have an
			// array of data provided for it.
			return false;
		}

		foreach ($toSave as $element) {
			if (!isset($element->event_id)) {
				$element->event_id = $event->id;
			}

			if (!$element->save()) {
				OELog::log("Unable to save element: $element->id ($elementClassName): ".print_r($element->getErrors(),true));
				throw new SystemException('Unable to save element: '.print_r($element->getErrors(),true));
			}
		}

		foreach ($toDelete as $element) {
			$element->delete();
		}

		return true;
	}

	public function getEpisode($firm, $patientId) {
		$subspecialtyId = $firm->serviceSubspecialtyAssignment->subspecialty->id;
		return Episode::model()->getBySubspecialtyAndPatient($subspecialtyId, $patientId);
	}
	
	public function getOrCreateEpisode($firm, $patientId) {
		if (!$episode = $this->getEpisode($firm, $patientId)) {
			$episode = new Episode();
			$episode->patient_id = $patientId;
			$episode->firm_id = $firm->id;
			$episode->start_date = date("Y-m-d H:i:s");

			if (!$episode->save()) {
				OELog::log("Unable to create new episode for patient_id=$episode->patient_id, firm_id=$episode->firm_id, start_date='$episode->start_date'");
				throw new Exception('Unable to create create episode.');
			}

			OELog::log("New episode created for patient_id=$episode->patient_id, firm_id=$episode->firm_id, start_date='$episode->start_date'");

			$audit = new Audit;
			$audit->action = "create";
			$audit->target_type = "episode";
			$audit->patient_id = $episode->patient->id;
			$audit->episode_id = $episode->id;
			$audit->user_id = (Yii::app()->session['user'] ? Yii::app()->session['user']->id : null);
			$audit->data = $episode->getAuditAttributes();
			$audit->save();

			Yii::app()->event->dispatch('episode_after_create', array('episode' => $episode));
		}

		return $episode;
	}

	public function createEvent($episode, $userId, $eventTypeId, $elementsToProcess)
	{
		$info_text = '';

		foreach ($elementsToProcess as $element) {
			if ($element->infotext) {
				$info_text .= $element->infotext;
			}
		}

		$event = new Event();
		$event->episode_id = $episode->id;
		$event->event_type_id = $eventTypeId;
		$event->datetime = date("Y-m-d H:i:s");
		$event->info = $info_text;

		if (!$event->save()) {
			OELog::log("Failed to creat new event for episode_id=$episode->id, event_type_id=$eventTypeId, datetime='$event->datetime'");
			throw new Exception('Unable to save event.');
		}

		OELog::log("Created new event for episode_id=$episode->id, event_type_id=$eventTypeId, datetime='$event->datetime'");

		return $event;
	}

	public function displayErrors($errors) {
		$this->renderPartial('//elements/form_errors',array('errors'=>$errors));
	}

	/**
	 * Print action
	 * @param integer $id event id
	 */
	public function actionPrint($id) {
		$this->printInit($id);
		$elements = $this->getDefaultElements('print');
		$pdf = (isset($_GET['pdf']) && $_GET['pdf']);
		$this->printLog($id, $pdf);
		if($pdf) {
			$this->printPDF($id, $elements);
		} else {
			$this->printHTML($id, $elements);
		}
	}

	/**
	 * Initialise print action
	 * @param integer $id event id
	 * @throws CHttpException
	 */
	protected function printInit($id) {
		if (!$this->event = Event::model()->findByPk($id)) {
			throw new CHttpException(403, 'Invalid event id.');
		}
		$this->patient = $this->event->episode->patient;
		$this->event_type = $this->event->eventType;
		$this->site = Site::model()->findByPk(Yii::app()->request->cookies['site_id']->value);
		$this->title = $this->event_type->name;
	}
	
	/**
	 * Render HTML
	 * @param integer $id event id
	 * @param array $elements
	 */
	protected function printHTML($id, $elements) {
		$this->renderPartial('print', array(
			'elements' => $elements,
			'eventId' => $id,
		), false, true);
	}
	
	/**
	 * Render PDF
	 * @param integer $id event id
	 * @param array $elements
	 */
	protected function printPDF($id, $elements) {

		// Remove any existing css
		Yii::app()->getClientScript()->reset();
		
		$this->layout = '//layouts/pdf';
		$pdf_print = new OEPDFPrint('Openeyes', 'PDF', 'PDF');
		$oeletter = new OELetter();
		$oeletter->setBarcode('E:'.$id);
		$body = $this->render('print', array(
			'elements' => $elements,
			'eventId' => $id,
		), true);
		$oeletter->addBody($body);
		$pdf_print->addLetter($oeletter);
		$pdf_print->output();
	}
	
	/**
	 * Log print action
	 * @param integer $id event id
	 * @param boolean $pdf
	 */
	protected function printLog($id, $pdf) {
		$this->logActivity("printed event (pdf=$pdf)");
		$audit = new Audit;
		$audit->action = "print";
		$audit->target_type = "event";
		$audit->patient_id = $this->event->episode->patient->id;
		$audit->episode_id = $this->event->episode_id;
		$audit->event_id = $id;
		$audit->user_id = (Yii::app()->session['user'] ? Yii::app()->session['user']->id : null);
		$audit->save();
	}
	
	public function actionDelete($id) {
		if (!$this->event = Event::model()->findByPk($id)) {
			throw new CHttpException(403, 'Invalid event id.');
		}

		// Only the event creator can delete the event, and only 24 hours after its initial creation
		if (!$this->event->canDelete()) {
			return $this->redirect(array('default/view/'.$this->event->id));
		}

		if (!empty($_POST)) {
			//if (isset($_POST['et_deleteevent'])) {
				$this->event->deleted = 1;
				$this->event->save();

				$audit = new Audit;
				$audit->action = "delete";
				$audit->target_type = "event";
				$audit->patient_id = $this->event->episode->patient->id;
				$audit->episode_id = $this->event->episode_id;
				$audit->event_id = $this->event->id;
				$audit->user_id = (Yii::app()->session['user'] ? Yii::app()->session['user']->id : null);
				$audit->save();

				if (Event::model()->count('episode_id=?',array($this->event->episode_id)) == 0) {
					$this->event->episode->deleted = 1;
					$this->event->episode->save();

					$audit = new Audit;
					$audit->action = "delete";
					$audit->target_type = "episode";
					$audit->patient_id = $this->event->episode->patient->id;
					$audit->episode_id = $this->event->episode_id;
					$audit->user_id = (Yii::app()->session['user'] ? Yii::app()->session['user']->id : null);
					$audit->save();

					return header('Location: '.Yii::app()->createUrl('/patient/episodes/'.$this->event->episode->patient->id));
				}

				return header('Location: '.Yii::app()->createUrl('/patient/episode/'.$this->event->episode_id));
			//}
			return header('Location: '.Yii::app()->createUrl('/'.$this->event->eventType->class_name.'/default/view/'.$this->event->id));
		}

		$this->patient = $this->event->episode->patient;

		$this->event_type = EventType::model()->findByPk($this->event->event_type_id);

		$this->title = "Delete ".$this->event_type->name;

		$this->renderPartial(
			'delete', array(
			'eventId' => $id,
			), false, true);
	}

	public function report($params=array()) {
		$data = array();

		foreach ($params['datasets'] as $name => $dataset) {
			$data[$name] = $this->getReportDataset($dataset);
		}

		$results = array();

		foreach ($params['items'] as $item => $item_spec) {
			switch ($item_spec['type']) {
				case 'total':
					$results[$item] = count($data[$item_spec['dataset']]['data']);
					break;
				case 'mean_and_range':
					$lowest = 999999999;
					$highest = 0;
					$values = array();

					foreach ($data[$item_spec['dataset']]['data'] as $dataItem) {
						if ($item_spec['field']) {
							if ($item_spec['field'] == 'age') {
								$dataItem['age'] = Helper::getAge($dataItem['dob']);
							}
							if ($dataItem[$item_spec['field']] < $lowest) {
								$lowest = $dataItem[$item_spec['field']];
							}
							if ($dataItem[$item_spec['field']] > $highest) {
								$highest = $dataItem[$item_spec['field']];
							}
							$values[] = $dataItem[$item_spec['field']];
						}
					}

					if (count($values) == 0) {
						$results[$item] = array(
							'from' => 0,
							'to' => 0,
							'mean' => 0,
						);
					} else {
						$results[$item] = array(
							'from' => $lowest,
							'to' => $highest,
							'mean' => number_format(array_sum($values)/count($values),2),
						);
					}

					break;
				case 'number_and_percentage':
					if (isset($item_spec['field'])) {
						$results[$item] = $this->numberWithPercentageFromField($data[$item_spec['dataset']], $item_spec['field'], $item_spec['value']);
					} else if (isset($item_spec['element'])) {
						$results[$item] = $this->numberWithPercentageFromElementRelation($data[$item_spec['dataset']], $item_spec, $data[$item_spec['dataset']]['element_ids']);
					}
					break;
				case 'number_and_percentage_pair':
					foreach ($item_spec['fields'] as $field => $field_spec) {
						$results[$item][$field] = $this->numberWithPercentageFromField($data[$item_spec['dataset']], $field_spec['field'], $field_spec['value']);
					}
					break;
				case 'list':
					foreach ($data[$item_spec['dataset']]['data'] as $dataItem) {
						$dataListItem = array();

						foreach ($item_spec['fields'] as $field => $field_spec) {
							switch ($field_spec['type']) {
								case 'NHSDate':
									$dataListItem[$field] = Helper::convertMySQL2NHS($dataItem[$field]);
									break;
								case 'string':
									$dataListItem[$field] = $dataItem[$field];
									break;
								case 'list_from_element_relation':
									$model = $field_spec['element'];
									$element = $model::model()->findByPk($dataItem[$data[$item_spec['dataset']]['element_ids'][$model]]);
									$dataListItem[$field] = array();

									if ($element) {
										foreach ($element->{$field_spec['element_relation']} as $element_related_item) {
											$elementRelationListItem = array();

											foreach ($field_spec['fields'] as $relation_list_field => $relation_list_field_spec) {
												switch ($relation_list_field_spec['type']) {
													case 'element_relation':
														$elementRelationListItem[$relation_list_field] = $element_related_item->{$relation_list_field_spec['field']};
														break;
													case 'string':
														$elementRelationListItem[$relation_list_field] = $dataItem[$relation_list_field_spec['field']];
														break;
												}
											}

											$dataListItem[$field][] = $elementRelationListItem;
										}
									}
									break;
								case 'conditional':
									foreach ($field_spec['conditions'] as $condition) {
										if ($dataItem[$condition['field']] == $condition['value']) {
											$dataListItem[$field] = $condition['result'];
											break;
										}
									}
									break;
							}
						}

						$results[$item][] = $dataListItem;
					}
					break;
			}
		}

		return $results;
	}

	function getReportDataset($dataset) {
		$select = array('e.datetime,p.dob,p.hos_num,c.first_name,c.last_name');
		$where = "e.deleted = ? and ep.deleted = ?";
		$whereParams = array(0,0);

		if (@$dataset['where']['firm_id']) {
			$where .= " and ep.firm_id = ?";
			$whereParams[] = $dataset['where']['firm_id'];
		}
		if (@$dataset['where']['date_from']) {
			$where .= " and e.datetime >= ?";
			$whereParams[] = date('Y-m-d',strtotime($dataset['where']['date_from']))." 00:00:00";
		}
		if (@$dataset['where']['date_to']) {
			$where .= " and e.datetime <= ?";
			$whereParams[] = date('Y-m-d',strtotime($dataset['where']['date_to']))." 23:59:59";
		}

		if (@$dataset['whereOr']) {
			foreach ($dataset['whereOr'] as $whereOr) {
				$clause = '';
				foreach ($whereOr['fields'] as $field => $value) {
					if ($clause) $clause .= ' or ';
					$clause .= "$field = ?";
					$whereParams[] = $value;
				}
				if ($clause) {
					$where .= " and ($clause)";
				}
			}
		}

		$data = Yii::app()->db->createCommand()
			->from('event e')
			->join('episode ep','e.episode_id = ep.id')
			->join('patient p','ep.patient_id = p.id')
			->join('contact c',"c.parent_class = 'Patient' and c.parent_id = p.id");

		$element_ids = array();

		foreach (array_keys($dataset['elements']) as $i => $element_class) {
			$model = new $element_class;
			$table = $model->tableName();

			if (@$dataset['elements'][$element_class]['optional']) {
				$data->leftJoin("$table el$i","el$i.event_id = e.id");
			} else {
				$data->join("$table el$i","el$i.event_id = e.id");
			}

			$select[] = "el$i.id as el{$i}_id";

			if (isset($dataset['elements'][$element_class]['select'])) {
				foreach ($dataset['elements'][$element_class]['select'] as $select_field) {
					$select[] = "el$i.$select_field";
				}
			}

			$element_ids[$element_class] = "el{$i}_id";

			if (isset($dataset['elements'][$element_class]['join'])) {
				foreach ($dataset['elements'][$element_class]['join'] as $join) {
					$data->join($join['join_table'],"el$i.".$join['join_clause']);
					$select[] = $join['join_select'];
				}
			}
		}

		return array(
			'data' => $data->select(implode(',',$select))->where($where,$whereParams)->queryAll(),
			'element_ids' => $element_ids,
		);
	}

	function numberWithPercentageFromField($data, $field, $value) {
		$result = array('number'=>0);

		foreach ($data['data'] as $dataItem) {
			if ($dataItem[$field] == $value) {
				$result['number']++;
			}
		}

		if ($result['number'] >0) {
			$result['percentage'] = number_format($result['number'] / (count($data['data'])/100),2);
		} else {
			$result['percentage'] = 0;
		}

		return $result;
	}

	function numberWithPercentageFromElementRelation($data, $item_spec, $element_ids) {
		$result = array('number'=>0);

		$model = $item_spec['element'];

		foreach ($data['data'] as $dataItem) {
			$element = $model::model()->findByPk($dataItem[$element_ids[$model]]);
			$matches = false;
			foreach ($element->{$item_spec['element_relation']} as $related_model) {
				if (isset($item_spec['element_relation_field']) && isset($item_spec['element_relation_value'])) {
					if ($related_model->{$item_spec['element_relation_field']} == $item_spec['element_relation_value']) {
						$matches = true;
					}
				} else {
					$matches = true;
				}
			}
			if ($matches) {
				$result['number']++;
			}
		}

		$result['percentage'] = number_format($result['number']/(count($data['data'])/100),2);

		return $result;
	}
}
