<?php

/**
 * OpenEyes.
 *
 * Copyright OpenEyes Foundation, 2017
 *
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

/**
 * This component class is intended to encaspulate the logic of interacting with the Worklists.
 *
 * Class WorklistManager
 */
class WorklistManager extends CComponent
{
    public static $AUDIT_TARGET_MANUAL = 'Manual Worklist';
    public static $AUDIT_TARGET_AUTO = 'Automatic Worklists';
    /**
     * @var string
     */
    protected static $DEFAULT_WORKLIST_START_TIME = '08:00';
    /**
     * @var string
     */
    protected static $DEFAULT_WORKLIST_END_TIME = '17:00';

    /**
     * @var string
     */
    protected static $DEFAULT_GENERATION_LIMIT = '1 month';

    /**
     * @var int
     */
    protected static $DEFAULT_WORKLIST_PAGE_SIZE = 10;

    /**
     * The interval between now and the future used for determining which Automatic Worklists
     * should be rendered on the dashboard.
     *
     * @var string
     */
    protected static $DEFAULT_DASHBOARD_FUTURE_DAYS = 1;

    /**
     * Array of 3 letter days of the week that should be skipped for picking dates to render
     * worklist dashboards for.

     * @TODO: leverage for day or week selection for definition setup
     *
     * @var array
     */
    protected static $DEFAULT_DASHBOARD_SKIP_DAYS = array('Sun');

    /**
     * Whether worklists with no patient assignments should be displayed or not.
     *
     * @var bool
     */
    protected static $DEFAULT_SHOW_EMPTY = true;

    /**
     * Whether patients can be added to the same automatic worklist more than once.
     *
     * @var bool
     */
    protected static $DEFAULT_DUPLICATE_PATIENTS = false;

    /**
     * Internal store of error messages.
     *
     * @var array
     */
    protected $errors = array();

    /**
     * Flag to turn off auditing.
     *
     * @var bool
     */
    protected $do_audit = true;

    /**
     * @var PatientIdentifierType
     */
    public $patient_identifier_type = null;

    /**
     * @var CApplication
     */
    protected $yii;

    public function __construct(CApplication $yii = null)
    {
        if (is_null($yii)) {
            $yii = Yii::app();
        }

        $this->yii = $yii;
    }

    /**
     * Abstraction for getting model instance of class.
     *
     * @param $class
     *
     * @return mixed
     */
    protected function getModelForClass($class)
    {
        return $class::model();
    }

    /**
     * Abstraction for getting instance of class.
     *
     * @param $class
     *
     * @return mixed
     */
    protected function getInstanceForClass($class, $args = array())
    {
        if (empty($args)) {
            return new $class();
        }

        $cls = new ReflectionClass($class);

        return $cls->newInstanceArgs($args);
    }

    /**
     * Wrapper for starting a transaction.
     *
     * @return CDbTransaction|null
     */
    protected function startTransaction()
    {
        return $this->yii->db->getCurrentTransaction() === null
            ? $this->yii->db->beginTransaction()
            : null;
    }

    /**
     * Wrapper for partial rendering to encapsulate the call out to the static app for retrieving the controller object.
     *
     * @param $view
     * @param array $parameters
     *
     * @return mixed
     */
    protected function renderPartial($view, $parameters = array())
    {
        return $this->yii->controller->renderPartial($view, $parameters, true);
    }

    /**
     * Wrapper for retrieving current active User.
     *
     * @return mixed
     */
    protected function getCurrentUser()
    {
        return $this->yii->user;
    }

    /**
     * @return Institution|null
     */
    protected function getCurrentInstitution()
    {
        if (!$institution_id = $this->yii->session['selected_institution_id']) {
            return;
        }

        return $this->getModelForClass('Institution')->findByPk($institution_id);
    }

    /**
     * @return Site|null
     */
    protected function getCurrentSite()
    {
        if (!$site_id = $this->yii->session['selected_site_id']) {
            return;
        }

        return $this->getModelForClass('Site')->findByPk($site_id);
    }

    /**
     * @return Firm|null
     */
    protected function getCurrentFirm()
    {
        if (!$firm_id = $this->yii->session->get('selected_firm_id')) {
            return;
        }

        return $this->getModelForClass('Firm')->findByPk($firm_id);
    }

    /**
     * Wrapper for retrieve app parameters.
     *
     * @param $name
     *
     * @return array|string|null
     */
    protected function getAppParam($name)
    {
        return isset($this->yii->params[$name]) ?
            $this->yii->params[$name] : null;
    }

    /**
     * Simple function for use during bulk procesess.
     */
    public function disableAudit()
    {
        $this->do_audit = false;
    }

    /**
     * Re-enable after disabling auditing.
     */
    public function enableAudit()
    {
        $this->do_audit = true;
    }

    /**
     * Audit Wrapper.
     *
     * @param $target
     * @param $action
     * @param null  $data
     * @param null  $log_message
     * @param array $properties
     *
     * @throws Exception
     */
    protected function audit($target, $action, $data = null, $log_message = null, $properties = array())
    {
        if (!$this->do_audit) {
            return;
        }

        if (!isset($properties['user_id'])) {
            $properties['user_id'] = $this->getCurrentUser()->id;
        }

        if (is_array($data)) {
            $data = json_encode($data);
        }

        Audit::add($target, $action, $data, $log_message, $properties);
    }

    /**
     * Wrapper for managing default start time for scheduled worklists.
     *
     * @return string
     */
    public function getDefaultStartTime()
    {
        return $this->getAppParam('worklist_default_start_time') ?: self::$DEFAULT_WORKLIST_START_TIME;
    }

    /**
     * Wrapper for managing default end time for scheduled worklists.
     *
     * @return string
     */
    public function getDefaultEndTime()
    {
        return $this->getAppParam('worklist_default_end_time') ?: self::$DEFAULT_WORKLIST_END_TIME;
    }

    /**
     * @return int
     */
    public function getWorklistPageSize()
    {
        return $this->getAppParam('worklist_default_pagination_size') ?: self::$DEFAULT_WORKLIST_PAGE_SIZE;
    }

    /**
     * @return WorklistDefinition[]
     */
    public function getWorklistDefinitions($exclude_unbooked = false)
    {
        $definitions = $this->getModelForClass('WorklistDefinition' . ($exclude_unbooked ? ':withoutUnbooked' : '') )->displayOrder()->findAll();

        //this is to move the elements with 0 instances at the top of the list
        //in this way the display_order will be respected by all the other entries
        $reordered_definitions = [];
        foreach ($definitions as $key => $definition) {
            if ($definition->worklistCount === "0") {
                $reordered_definitions[] = $definition;
                unset($definitions[$key]);
            }
        }

        return $reordered_definitions + $definitions;
    }

    /**
     * @return DateTime
     */
    public function getGenerationTimeLimitDate(WorklistDefinition $definition = null, $date_limit = null)
    {
        if (is_null($date_limit)) {
            $limit = $this->getAppParam('worklist_default_generation_limit') ?: self::$DEFAULT_GENERATION_LIMIT;
            $interval = DateInterval::createFromDateString($limit);

            $date_limit = new DateTime();
            $date_limit->add($interval);
        }

        if ($definition && $definition->active_until) {
            $active_until = new DateTime($definition->active_until);
            if ($active_until < $date_limit) {
                $date_limit = $active_until;
            }
        }

        return $date_limit;
    }

    /**
     * @return bool
     */
    public function shouldRenderEmptyWorklist()
    {
        return !is_null($this->getAppParam('worklist_show_empty')) ?
            $this->getAppParam('worklist_show_empty')
            : self::$DEFAULT_SHOW_EMPTY;
    }

    /**
     * @return bool
     */
    public function allowDuplicatePatients()
    {
        return !is_null($this->getAppParam('worklist_allow_duplicate_patients')) ?
            $this->getAppParam('worklist_allow_duplicate_patients')
            : self::$DEFAULT_DUPLICATE_PATIENTS;
    }

    /**
     * The time before which we are relaxed about appointments not finding matches.
     *
     * @return DateTime
     */
    public function getWorklistIgnoreDate()
    {
        if ($dt = $this->getAppParam('worklist_ignore_date')) {
            return DateTime::createFromFormat('Y-m-d', $dt);
        }

        return;
    }

    /**
     * Works out the dates we should retrieve Worklists for rendering.
     *
     * @param DateTime $date
     *
     * @return array
     *
     * @throws Exception
     */
    public function getDashboardRenderDates(DateTime $start_date, $end_date)
    {
        // in case the passed in date is being used for anything else
        $r_date = clone $start_date;
        $r_date->setTime(0, 0, 0);

        $future_days = $this->getAppParam('worklist_dashboard_future_days');
        if (is_null($future_days)) {
            $future_days = self::$DEFAULT_DASHBOARD_FUTURE_DAYS;
        }

        $skip_days = $this->getAppParam('worklist_dashboard_skip_days') ?: self::$DEFAULT_DASHBOARD_SKIP_DAYS;
        if (count($skip_days) >= 7) {
            throw new Exception('Too many days set to be skipped');
        }

        $future_dates = array();
        while ((!$end_date && count($future_dates) < $future_days) || ($end_date && $r_date < $end_date)) {
            $r_date = clone $r_date;
            $r_date->modify('+1 day');
            if (!in_array($r_date->format('D'), $skip_days)) {
                $future_dates[] = $r_date;
            }
        }

        if (!in_array($start_date->format('D'), $skip_days)) {
            array_unshift($future_dates, clone $start_date);
        }
        return $future_dates;
    }

    /**
     * @param null $id
     *
     * @return WorklistDefinition|null
     */
    public function getWorklistDefinition($id = null)
    {
        if (is_null($id)) {
            $definition = $this->getInstanceForClass('WorklistDefinition');
            $definition->attributes = array(
                'start_time' => $this->getDefaultStartTime(),
                'end_time' => $this->getDefaultEndTime(),
            );
        } else {
            $definition = $this->getModelForClass('WorklistDefinition')->findByPk($id);
        }

        return $definition;
    }

    /**
     * @param WorklistDefinition $definition
     *
     * @return bool
     */
    public function saveWorklistDefinition(WorklistDefinition $definition)
    {
        if (!$this->canUpdateWorklistDefinition($definition)) {
            $this->addError('cannot save Definition that is un-editable');

            return false;
        }

        $action = $definition->isNewRecord ? 'create' : 'update';

        $transaction = $this->startTransaction();

        try {
            if (!$definition->save()) {
                $this->addModelErrors($definition->getErrors());
                throw new Exception("Couldn't save definition");
            }

            $this->audit(self::$AUDIT_TARGET_AUTO, $action, array(
                'worklist_definition_id' => $definition->id,
            ));

            if ($transaction) {
                $transaction->commit();
            }
        } catch (Exception $e) {
            $this->addError($e->getMessage());
            if ($transaction) {
                $transaction->rollback();
            }

            return false;
        }

        return true;
    }

    /**
     * Set the display order for the worklist definitions
     * The start and end values will support a re-ordering request from a paginated list.
     *
     * @param array $ids
     * @param null  $start
     * @param null  $end
     *
     * @return bool
     */
    public function setWorklistDefinitionDisplayOrder($ids = array(), $start = null, $end = null)
    {
        foreach ($ids as $i => $id) {
            $display_lookup[$id] = $i + 1;
        }
        $transaction = $this->startTransaction();

        try {
            $model = $this->getModelForClass('WorklistDefinition');
            $criteria = $this->getInstanceForClass('CDbCriteria');
            if ($start) {
                $criteria->addCondition('display_order > :start');
                $criteria->params = array_merge($criteria->params, array(
                    ':start' => $start,
                ));
            }
            if ($end) {
                $criteria->addCondition('display_order < :end');
                $criteria->params = array_merge($criteria->params, array(
                    ':end' => $end,
                ));
            }

            foreach ($model->findAll($criteria) as $definition) {
                $definition->scenario = 'sortDisplayOrder';
                if (!array_key_exists($definition->id, $display_lookup)) {
                    throw new Exception('Missing definition id for re-ordering request.');
                }
                $definition->display_order = $display_lookup[$definition->id];
                if (!$definition->save()) {
                    throw new Exception("Unable to update display order for definition {$definition->id}");
                }
            }

            $this->audit(self::$AUDIT_TARGET_AUTO, 're-order', array('order' => $ids), 'Definitions display order set');

            if ($transaction) {
                $transaction->commit();
            }

            return true;
        } catch (Exception $e) {
            $this->addError($e->getMessage());
            if ($transaction) {
                $transaction->rollback();
            }

            return false;
        }
    }

    /**
     * @param $id
     *
     * @return Worklist
     */
    public function getWorklist($id)
    {
        if (is_null($id)) {
            return $this->getInstanceForClass('Worklist');
        }

        return $this->getModelForClass('Worklist')->findByPk($id);
    }

    /**
     * @param $worklist
     * @param $user
     * @param null $display_order
     *
     * @return mixed
     */
    public function addWorklistToUserDisplay($worklist, $user, $display_order = null)
    {
        if (is_null($display_order)) {
            $criteria = new CDbCriteria();
            $criteria->addColumnCondition(array('user_id' => $user->id));
            $criteria->select = 'max(display_order) as maxDisplay';
            $row = $this->getModelForClass('WorklistDisplayOrder')->find($criteria);

            $max_display_order = $row['maxDisplay'];
            $display_order = $max_display_order ? $max_display_order + 1 : 1;
        }

        $wdo = $this->getInstanceForClass('WorklistDisplayOrder');
        $wdo->worklist_id = $worklist->id;
        $wdo->user_id = $user->id;
        $wdo->display_order = $display_order;

        return $wdo->save();
    }

    /**
     * @param Worklist $worklist
     * @param null     $user
     * @param bool     $display
     *
     * @return bool
     *
     * @throws CDbException
     */
    public function createWorklistForUser(Worklist $worklist, $user = null, $display = true)
    {
        if (!$user) {
            $user = $this->getCurrentUser();
        }

        $transaction = $this->startTransaction();

        try {
            $worklist->created_user_id = $user->id;
            $worklist->last_modified_user_id = $user->id;

            // save call must force the parent class to accept the set owner id
            if (!$worklist->save(true, null, true)) {
                $this->addModelErrors($worklist->getErrors());
                throw new Exception('Could not create Worklist.');
            }

            if ($display) {
                if (!$this->addWorklistToUserDisplay($worklist, $user)) {
                    throw new Exception('Could not set new worklist display order.');
                }
            }

            $this->audit(self::$AUDIT_TARGET_MANUAL, 'create',
                array('worklist_id' => $worklist->id, 'owner_id' => $user->id),
                'Worklist created.');

            if ($transaction) {
                $transaction->commit();
            }
        } catch (Exception $e) {
            $this->addError($e->getMessage());
            if ($transaction) {
                $transaction->rollback();
            }

            return false;
        }

        return true;
    }

    /**
     * @param $user
     *
     * @return array
     */
    public function getCurrentManualWorklistsForUser($user)
    {
        $worklists = array();
        foreach ($this->getModelForClass('WorklistDisplayOrder')->with('worklist')->findAll(array(
            'condition' => 'user_id=:uid',
            'order' => 'display_order asc',
            'params' => array(':uid' => $user->id), )) as $wdo) {
            $worklists[] = $wdo->worklist;
        }

        return $worklists;
    }

    public function getCurrentAutomaticWorklistsForUser($user, $start_date = null, $end_date = null, $filter = null)
    {
        $worklists = [];

        if (!$user) {
            $user = $this->getCurrentUser();
        }

        $institution = $this->getCurrentInstitution();

        if ($filter) {
            $site = $this->getModelForClass('Site')->findByPk($filter->getSiteId());

            $firm = $filter->coversAllContexts()
                  ? $this->getCurrentFirm()
                  : $this->getModelForClass('Firm')->findByPk($filter->getContextId());
        } else {
            $site = $this->getCurrentSite();
            $firm = $this->getCurrentFirm();
        }

        $days = $this->getDashboardRenderDates($start_date ? $start_date : new DateTime(), $end_date);

        foreach ($days as $when) {
            foreach ($this->getCurrentAutomaticWorklistsForUserContext($institution, $site, $firm, $when) as $worklist) {
                $worklist_patients = $this->getPatientsForWorklist($worklist);
                if ($this->shouldRenderEmptyWorklist() || $worklist_patients->getTotalItemCount() > 0) {
                    $worklists[] = $worklist;
                }
            }
        }

        $unique_ids = array();
        $unique_worklists = array();

        if (!$filter || $filter->coversAllWorklists()){
            foreach ($worklists as $wl) {
                if(!in_array($wl->id, $unique_ids)) {
                    $unique_worklists[] = $wl;
                    $unique_ids[] = $wl->id;
                }
            }
        } else {
            $selected_ids = $filter->getWorklists();

            foreach ($worklists as $wl) {
                if(in_array($wl->id, $selected_ids) && !in_array($wl->id, $unique_ids)) {
                    $unique_worklists[] = $wl;
                    $unique_ids[] = $wl->id;
                }
            }
        }

        return $unique_worklists;
    }

    public function shouldDisplayWorklistForContext(Worklist $worklist, Institution $institution, Site $site, Firm $firm)
    {
        if ($definition = $worklist->worklist_definition) {
            if ($definition->patient_identifier_type->institution_id == $site->institution_id) {
                $display_contexts = $definition->display_contexts;
                if (!count($display_contexts)) {
                    return true;
                }
                foreach ($display_contexts as $dc) {
                    if ($dc->checkInstitution($institution) && $dc->checkSite($site) && $dc->checkFirm($firm)) {
                        return true;
                    }
                }
                // got this far, we haven't found a valid display context
                return false;
            } else {
                return false;
            }
        }

        // not implemented context checking for non-automatic worklists yet
        return true;
    }

    /**
     * @param $user
     * @param Site     $site
     * @param Firm     $firm
     * @param DateTime $when
     *
     * @return array
     */
    public function getCurrentAutomaticWorklistsForUserContext(Institution $institution, Site $site, Firm $firm, DateTime $when)
    {
        $worklists = array();
        $model = $this->getModelForClass('Worklist');
        $model->automatic = true;
        $model->on = $when;
        foreach ($model->with(array('worklist_definition', 'worklist_definition.display_contexts', 'worklist_patients'))->search(false)->getData() as $wl) {
            if ($this->shouldDisplayWorklistForContext($wl, $institution, $site, $firm)) {
                $worklists[] = $wl;
            }
        }

        return $worklists;
    }

    /**
     * @param $user
     *
     * @return mixed
     */
    public function getAvailableManualWorklistsForUser($user)
    {
        $worklists = array();
        $model = $this->getModelForClass('Worklist');
        $model->automatic = false;
        $model->created_user_id = $user->id;

        $search = $model->with('worklist_patients')->search();
        $criteria = $search->criteria;
        $criteria->order = 'created_date desc';

        $current = $this->getCurrentManualWorklistsForUser($user);

        foreach ($search->getData() as $wl) {
            if (!in_array($wl, $current)) {
                $worklists[] = $wl;
            }
        }

        return $worklists;
    }

    /**
     * @param $user
     * @param array $worklist_ids
     *
     * @return bool
     *
     * @throws CDbException
     */
    public function setWorklistDisplayOrderForUser($user, $worklist_ids = array())
    {
        $transaction = $this->startTransaction();
        $model = $this->getModelForClass('WorklistDisplayOrder');
        try {
            $model->deleteAllByAttributes(array('user_id' => $user->id));

            if ($worklist_ids) {
                $rows = array();
                foreach ($worklist_ids as $display_order => $worklist_id) {
                    $order = $this->getInstanceForClass('WorklistDisplayOrder');
                    $order->attributes = array(
                        'worklist_id' => $worklist_id,
                        'user_id' => $user->id,
                        'display_order' => $display_order,
                    );
                    if (!$order->save()) {
                        throw new Exception('Could not save order entry');
                    }
                }
            }

            $this->audit(self::$AUDIT_TARGET_MANUAL, 'ordered', array('user_id' => $user->id),
                'Worklists reordered for user.');

            if ($transaction) {
                $transaction->commit();
            }
        } catch (Exception $e) {
            $this->addError($e->getMessage());
            if ($transaction) {
                $transaction->rollback();
            }

            return false;
        }

        return true;
    }

    /**
     * @param Worklist $worklist
     * @param Patient  $patient
     *
     * @return array|CActiveRecord|mixed|null
     */
    public function getWorklistPatient(Worklist $worklist, Patient $patient)
    {
        return $this->getModelForClass('WorklistPatient')->findByAttributes(array('patient_id' => $patient->id, 'worklist_id' => $worklist->id));
    }

    /**
     * @param WorklistPatient $worklist_patient
     * @param array           $attributes
     *
     * @return bool
     *
     * @throws CDbException
     * @throws Exception
     */
    public function setAttributesForWorklistPatient(WorklistPatient $worklist_patient, $attributes = array())
    {
        $transaction = $this->startTransaction();
        $worklist = $worklist_patient->worklist;
        try {
            $current_attributes = $worklist_patient->getCurrentAttributesById();
            $valid_attributes = $worklist->getMappingAttributeIdsByName();

            foreach ($attributes as $attr => $val) {
                if (!array_key_exists($attr, $valid_attributes)) {
                    throw new Exception("Unrecognised attribute {$attr} for {$worklist->name}");
                }

                $wlattr = isset($current_attributes[$valid_attributes[$attr]]) ?
                    $current_attributes[$valid_attributes[$attr]] :
                    $this->getInstanceForClass('WorklistPatientAttribute');

                $wlattr->attributes = array(
                    'worklist_patient_id' => $worklist_patient->id,
                    'worklist_attribute_id' => $valid_attributes[$attr],
                    'attribute_value' => $val,
                );

                if (!$wlattr->save()) {
                    throw new Exception("Unable to save attribute {$attr} for patient worklist.");
                }
            }

            // TODO: decide if we should check for current attributes that are no longer valid
            // and delete them here.

            if ($transaction) {
                $transaction->commit();
            }
        } catch (Exception $e) {
            $this->addError($e->getMessage());
            throw $e;
            if ($transaction) {
                $transaction->rollback();
            }

            return false;
        }

        return true;
    }

    /**
     * Clones the default pathway for the associated worklist for the specified worklist patient.
     * @param WorklistPatient $wp
     * @return bool
     * @throws Exception
     */
    public function createPathway(WorklistPatient $wp)
    {
        $wp->refresh();

        if (!$wp->pathway) {
            $pathway_type_id = $wp->worklist->worklist_definition->pathway_type_id;
            $pathway_type = PathwayType::model()->findByPk($pathway_type_id);

            if ($pathway_type) {
                if (!$pathway_type->createNewPathway($wp->id)) {
                    throw new Exception('Unable to create pathway.');
                }
                $wp->refresh(); // Need to refresh first to synchronise the pathway relation to the newly created pathway.

                $start_status = $wp->getWorklistPatientAttribute('Status'); // Could we genericise this attribute name in future?
                if ($wp->pathway
                    && !$wp->pathway->start_time
                    && $start_status
                    && strtolower($start_status->attribute_value) === strtolower('Attended')) {
                    // Start the pathway immediately.
                    if (count($wp->pathway->steps) === 0) {
                        $wp->pathway->type->instancePathway($wp->pathway->id);
                    }
                    $wp->pathway->startPathway();
                }
            } else {
                return false;
            }
        }
        return true;
    }

    /**
     * If the given Patient is successfully added to the given Worklist, returns true. false otherwise.
     *
     * @param Patient  $patient
     * @param Worklist $worklist
     * @param DateTime $when
     * @param array    $attributes
     *
     * @return WorklistPatient|null
     */
    public function addPatientToWorklist(Patient $patient, Worklist $worklist, DateTime $when = null, $attributes = array())
    {
        $this->reset();
        if (!$this->allowDuplicatePatients() && $this->getWorklistPatient($worklist, $patient)) {
            $this->addError('Patient is already on the given worklist.');

            return;
        }

        $transaction = $this->startTransaction();

        try {
            $wp = $this->getInstanceForClass('WorklistPatient');
            $wp->patient_id = $patient->id;
            $wp->worklist_id = $worklist->id;
            if ($when) {
                $wp->when = $when->format('Y-m-d H:i:s');
            }

            if (!$wp->save()) {
                throw new Exception('Unable to save patient to worklist.');
            }

            if (count($attributes)) {
                if (!$this->setAttributesForWorklistPatient($wp, $attributes)) {
                    throw new Exception('Could not set attributes for patient on worklist');
                }
            }

            if (!$this->createPathway($wp)) {
                throw new Exception('Unable to create pathway for patient visit.');
            }

            $target = $worklist->worklist_definition_id ? self::$AUDIT_TARGET_AUTO : self::$AUDIT_TARGET_MANUAL;

            $this->audit($target, 'add-patient',
                array('worklist_id' => $worklist->id), 'Patient added to worklist',
                array('patient_id' => $patient->id));

            if ($transaction) {
                $transaction->commit();
            }

            return $wp;
        } catch (Exception $e) {
            $this->addError($e->getMessage());
            if ($transaction) {
                $transaction->rollback();
            }

            return;
        }
    }

    /**
     * @param $worklist
     *
     * @return mixed
     */
    public function renderWorklistForDashboard($worklist)
    {
        $worklist_patients = $this->getPatientsForWorklist($worklist);
        if ($this->shouldRenderEmptyWorklist() || $worklist_patients->getTotalItemCount() > 0) {
            $this->yii->assetManager->registerScriptFile('js/worklist-dashboard.js', null, null, AssetManager::OUTPUT_SCREEN);

            return $this->renderPartial('//worklist/_worklist', array(
                    'worklist' => $worklist,
                    'worklist_patients' => $this->getPatientsForWorklist($worklist),
                )
            );
        }
    }

    /**
     * @param User|null $user
     *
     * @return array|null
     */
    public function renderManualDashboard($user = null)
    {
        if (!$user) {
            $user = $this->getCurrentUser();
        }

        $content = '';
        foreach ($this->getCurrentManualWorklistsForUser($user) as $worklist) {
            $content .= $this->renderWorklistForDashboard($worklist);
        }

        if (strlen($content)) {
            return array(
                'title' => 'Manual Worklists',
                'content' => $content,
                'options' => array(
                    'container-id' => \Yii::app()->user->id.'-manual-worklists-container',
                    'js-toggle-open' => true,
                ),
            );
        }
    }

    /**
     * Render the automatic dashboard for the given user.
     *
     * @param CWebUser $user
     *
     * @return array|null
     */
    public function renderAutomaticDashboard($user = null)
    {
        if (!$user) {
            $user = $this->getCurrentUser();
        }
        $institution = $this->getCurrentInstitution();
        $site = $this->getCurrentSite();
        $firm = $this->getCurrentFirm();

        $content = '';
        $days = $this->getDashboardRenderDates(new DateTime());
        foreach ($days as $when) {
            foreach ($this->getCurrentAutomaticWorklistsForUserContext($institution, $site, $firm, $when) as $worklist) {
                $content .= $this->renderWorklistForDashboard($worklist);
            }
        }

        if (strlen($content)) {
            return array(
                'title' => 'Automatic Worklists',
                'content' => $content,
                'options' => array(
                    'container-id' => \Yii::app()->user->id.'-automatic-worklists-container',
                    'js-toggle-open' => true,
                ),
            );
        }
    }

    /**
     * @TODO: test me
     *
     * @param $worklist
     *
     * @return CActiveDataProvider
     */
    public function getPatientsForWorklist($worklist)
    {
        $criteria = new CDbCriteria();
        $criteria->addColumnCondition(array('t.worklist_id' => $worklist->id));

        if ($worklist->scheduled) {
            $criteria->order = 't.when';
        } else {
            $criteria->order = 'LOWER(contact.last_name), LOWER(contact.first_name)';
        }

        $criteria->with = array('patient', 'patient.contact');

        return new CActiveDataProvider('WorklistPatient', array(
            'criteria' => $criteria,
            'pagination' => array(
                'pageSize' => $this->getWorklistPageSize(),
            ),
        ));
    }

    /**
     * @TODO: test me
     *
     * @param $worklist
     *
     * @return CSqlDataProvider
     */
    public function getPatientsForWorklistSQL($worklist, $filter)
    {
        return $filter->getWorklistPatientsProvider($this->getWorklistPageSize(), $worklist);
    }

    /**
     * Manipulates the given RRULE string so that it finishes on the given date.
     *
     * @param string   $rrule
     * @param DateTime $limit
     *
     * @return string of parts
     */
    public function setDateLimitOnRrule(string $rrule, DateTime $limit) : string
    {
        if (strpos($rrule, 'UNTIL=')) {
            preg_replace('/UNTIL=[^;]*/', 'UNTIL='.$limit->format('Ymd\THis\Z'), $rrule);
        } else {
            $rrule .= ';UNTIL='.$limit->format('Ymd\THis\Z');
        }

        return $rrule;
    }

    /**
     * Generates a Worklist instance name from the given definition and date.
     *
     * @TODO: implement support for a name definition that can be used to define what worklist instance names should look like
     *
     * @param $definition
     * @param DateTime $date
     *
     * @return string
     */
    public function generateWorklistName($definition, DateTime $date)
    {
        return $definition->name;
    }

    /**
     * Create a worklist instance from the given definition for the given date.
     *
     * @param $definition
     * @param DateTime $date
     *
     * @return bool - indicate whether the instance was created (true) or if it already existed (false)
     *
     * @throws Exception
     */
    protected function createAutomaticWorklist(WorklistDefinition $definition, DateTime $date)
    {
        $model = $this->getModelForClass('Worklist');
        // use the time attribute of the definition with the given date to get the 'range' date for finding/creating
        // an instance.
        $range_date = clone $date;
        $range_date->setTime(substr($definition->start_time, 0, 2), substr($definition->start_time, 3, 2));

        $start_time = $range_date->format('Y-m-d H:i:s');

        if (!$instance = $model->findByAttributes(array('worklist_definition_id' => $definition->id, 'start' => $start_time))) {
            // instance needs to be created
            $instance = $this->getInstanceForClass('Worklist');
            $range_date->setTime(substr($definition->end_time, 0, 2), substr($definition->end_time, 3, 2));

            $instance->attributes = array(
                'worklist_definition_id' => $definition->id,
                'start' => $start_time,
                'end' => $range_date->format('Y-m-d H:i:s'),
                'scheduled' => true,
                'description' => $definition->description,
                'name' => $definition->worklist_name ? $definition->worklist_name : $this->generateWorklistName($definition, $date),
            );

            $instance->save();

            // assign the possible attributes for worklist
            foreach ($definition->mappings as $mapping) {
                $worklist_attribute = $this->getInstanceForClass('WorklistAttribute');
                $worklist_attribute->name = $mapping->key;
                $worklist_attribute->display_order = $mapping->display_order;
                $worklist_attribute->worklist_id = $instance->id;
                if (!$worklist_attribute->save()) {
                    foreach ($worklist_attribute->getErrors() as $err) {
                        $this->addError($err);
                    }
                    throw new Exception("Couldn't create worklist attribute");
                };
            }

            return true;
        };

        return false;
    }

    /**
     * Generate worklist instances for the given definition up until the given date limit
     * If false is returned, getErrors should be used to determine the issue.
     *
     * @param WorklistDefinition $worklist
     * @param DateTime           $date_limit
     *
     * @return bool - true if the process had no failures, false otherwise.
     */
    public function generateAutomaticWorklists(WorklistDefinition $definition, $date_limit = null)
    {
        $date_limit = $this->getGenerationTimeLimitDate($definition, $date_limit);

        $rrule_str = $this->setDateLimitOnRrule($definition->rrule, $date_limit);
        $rrule = $this->getInstanceForClass('\RRule\RRule', array($rrule_str));

        $new_count = 0;

        $transaction = $this->startTransaction();

        try {
            foreach ($rrule as $occurence) {
                if ($this->createAutomaticWorklist($definition, $occurence)) {
                    ++$new_count;
                }
            }

            $this->audit(self::$AUDIT_TARGET_AUTO, 'generate',
                array('worklist_definition_id' => $definition->id, 'generated' => $new_count),
                'Worklists generated');

            if ($transaction) {
                $transaction->commit();
            }

            return $new_count;
        } catch (Exception $e) {
            $this->addError($e->getMessage());
            if ($transaction) {
                $transaction->rollback();
            }

            return false;
        }
    }

    /**
     * Iterate through all the worklist definitions and generate the instances up until the given date limit.
     * Returns false for errors, otherwise a total count of worklist instances that have been created.
     *
     * @param DateTime $date_limit
     * @return bool|int
     * @throws CException
     */
    public function generateAllAutomaticWorklists(DateTime $date_limit)
    {
        $count = 0;

        $transaction = $this->startTransaction();

        try {
            $this->disableAudit();
            $definitions = $this->getModelForClass('WorklistDefinition')->withoutUnbooked()->findAll();
            $definition_count = 0;
            foreach ($definitions as $definition) {
                $result = $this->generateAutomaticWorklists($definition, $date_limit);
                if ($result === false) {
                    throw new Exception("Couldn't generate worklists for {$definition->name}");
                }
                $count += $result;
                ++$definition_count;
            }
            $this->enableAudit();

            $this->audit(self::$AUDIT_TARGET_AUTO, 'generate', array(
                    'definition_count' => $definition_count,
                    'generated' => $count,
                    'date_limit' => $date_limit->format(Helper::NHS_DATE_FORMAT),
                ),
                'All Definitions Generated.',
                array('user_id' => 1)); // hard coded as expected to be called from the command line

            if ($transaction) {
                $transaction->commit();
            }

            return $count;
        } catch (Exception $e) {
            $this->addError($e->getMessage());
            if ($transaction) {
                $transaction->rollback();
            }

            return false;
        }
    }

    /**
     * Update the mapping specification for an automatic worklist definition.
     *
     * @param WorklistDefinitionMapping $mapping
     * @param $key
     * @param string $values
     *
     * @return bool
     */
    public function updateWorklistDefinitionMapping(WorklistDefinitionMapping $mapping, $key, $values, $display = true)
    {
        $values = strlen($values) ? explode(',', $values) : array();

        $definition = $mapping->worklist_definition;

        if (!$this->canUpdateWorklistDefinition($definition)) {
            $this->addError('Cannot update mapping for un-editable Worklist Definition');

            return false;
        }

        if (!$definition->validateMappingKey($key, $mapping->id)) {
            $this->addError("Mapping key {$key} already exists for definition");

            return false;
        }

        $mapping->key = $key;

        if (!$display) {
            $mapping->display_order = null;
        } elseif (!$mapping->display_order) {
            $mapping->display_order = $definition->getNextDisplayOrder();
        }

        $transaction = $this->startTransaction();

        try {
            if (!$mapping->save()) {
                throw new Exception('Could not save mapping');
            }

            if (!$mapping->updateValues($values)) {
                throw new Exception('Could not save mapping values');
            }

            $this->audit(self::$AUDIT_TARGET_AUTO, 'mapping-update');

            if ($transaction) {
                $transaction->commit();
            }
        } catch (Exception $e) {
            $this->addError($e->getMessage());
            echo $e->getMessage();
            if ($transaction) {
                $transaction->rollback();
            }

            return false;
        }

        return true;
    }

    /**
     * @param WorklistDefinition $definition
     * @param array              $ids
     *
     * @return bool
     */
    public function setWorklistDefinitionMappingDisplayOrder(WorklistDefinition $definition, $ids = array())
    {
        foreach ($ids as $i => $id) {
            $display_lookup[$id] = $i + 1;
        }
        $transaction = $this->startTransaction();

        try {
            foreach ($definition->displayed_mappings as $mapping) {
                $mapping->display_order = $display_lookup[$mapping->id];
                $mapping->save();
            }

            if ($transaction) {
                $transaction->commit();
            }

            return true;
        } catch (Exception $e) {
            $this->addError($e->getMessage());
            if ($transaction) {
                $transaction->rollback();
            }

            return false;
        }
    }

    /**
     * @param Worklist $wl
     * @param $attributes
     *
     * @return bool
     *
     * @throws Exception
     */
    protected function checkWorklistMappingMatch(Worklist $wl, $attributes)
    {
        if (!$wl->worklist_definition) {
            throw new Exception("Cannot match Worklist that doesn't have a WorklistDefinition.");
        }

        foreach ($wl->worklist_definition->mappings as $mapping) {
            if (!array_key_exists($mapping->key, $attributes)) {
                $this->addError("Missing key {$mapping->key} for {$wl->name}");

                return false;
            }

            if (!$mapping->values) {
                continue;
            }

            $match = false;
            foreach ($mapping->values as $val) {
                if (strtolower($val->mapping_value) == strtolower(trim($attributes[$mapping->key]))) {
                    $match = true;
                }
            }
            if (!$match) {
                $this->addError($attributes[$mapping->key]." not valid for key '{$mapping->key}'");

                return false;
            }
        }
        // get to the end and no mismatch found. must match.
        return true;
    }

    /**
     * @param DateTime $when
     * @param array    $attributes
     *
     * @return Worklist|null
     */
    protected function getWorklistForMapping(DateTime $when, $attributes = array())
    {
        $model = $this->getModelForClass('Worklist');
        $model->at = $when;
        $model->automatic = true;

        $candidates = array();
        foreach ($model->search(false)->getData() as $wl) {
            if(isset($this->patient_identifier_type) && $wl->worklist_definition->patient_identifier_type_id == $this->patient_identifier_type->id) {
                if ($this->checkWorklistMappingMatch($wl, $attributes)) {
                    $candidates[] = $wl;
                }
            }
        }

        if (count($candidates) == 1) {
            return $candidates[0];
        } elseif (count($candidates) > 1) {
            $this->addError('More than worklist matched criteria');
        } else {
            $this->addError('No worklist found for criteria');
        }

        return;
    }

    /**
     * @param Patient  $patient
     * @param DateTime $when
     * @param array    $attributes
     *
     * @return WorklistPatient|null
     */
    public function mapPatientToWorklistDefinition(Patient $patient, DateTime $when, $attributes = array())
    {
        $worklist = $this->getWorklistForMapping($when, $attributes);
        if (!$worklist) {
            return;
        }

        return $this->addPatientToWorklist($patient, $worklist, $when, $attributes);
    }

    /**
     * @param WorklistPatient $worklist_patient
     * @param DateTime        $when
     * @param array           $attributes
     * @param bool            $allow_worklist_change - only allow values to change that don't affect which worklist is mapped
     *
     * @return WorklistPatient|null
     */
    public function updateWorklistPatientFromMapping(WorklistPatient $worklist_patient,
                                                     DateTime $when,
                                                     $attributes = array(),
                                                     $allow_worklist_change = false)
    {
        $worklist = $this->getWorklistForMapping($when, $attributes);
        if (!$worklist) {
            return;
        }

        if (!$allow_worklist_change && $worklist->id != $worklist_patient->worklist_id) {
            $this->addError('Worklist mapping change not allowed for partial update.');

            return;
        }

        $transaction = $this->startTransaction();

        try {
            $worklist_patient->worklist_id = $worklist->id;
            $worklist_patient->when = $when->format('Y-m-d H:i:s');
            if (!$this->setAttributesForWorklistPatient($worklist_patient, $attributes)) {
                throw new Exception('Could not set worklist attributes');
            }

            if (!$worklist_patient->save()) {
                foreach ($worklist_patient->getErrors() as $key => $error) {
                    foreach ($error as $message) {
                        $this->addError("{$key}: {$message}");
                    }
                    throw new Exception('Could not update WorklistPatient');
                }
            }

            if ($transaction) {
                $transaction->commit();
            }

            return $worklist_patient;
        } catch (Exception $e) {
            echo $e->getMessage();
            $this->addError($e->getMessage());
            if ($transaction) {
                $transaction->rollback();
            }

            return;
        }
    }

    /**
     * @param WorklistDefinition $definition
     *
     * @return int
     */
    public function canUpdateWorklistDefinition(WorklistDefinition $definition)
    {
        // at the moment we don't allow changes to the definition if worklists exist for it
        if ($this->getAppParam('worklist_always_allow_definition_edit')) {
            return true;
        }

        return $definition->isNewRecord || $definition->getWorklistCount() == 0;
    }

    /**
     * @TODO: consider the future!!
     *
     * @param WorklistDefinition $definition
     *
     * @return bool
     */
    public function deleteWorklistDefinitionInstances(WorklistDefinition $definition)
    {
        $transaction = $this->startTransaction();

        try {
            foreach ($definition->worklists as $worklist) {
                if (!$worklist->delete()) {
                    throw new Exception("Could not delete worklist {$worklist->name}");
                };
            }

            if ($transaction) {
                $transaction->commit();
            }

            return true;
        } catch (Exception $e) {
            $this->addError($e->getMessage());
            if ($transaction) {
                $transaction->rollback();
            }

            return false;
        }
    }

    /**
     * Internal method to reset state for error tracking.
     */
    protected function reset()
    {
        $this->errors = array();
    }

    /**
     * @param $errors
     */
    protected function addModelErrors($errors)
    {
        foreach ($errors as $fld => $errs) {
            foreach ($errs as $message) {
                $this->addError("{$fld}: {$message}");
            }
        }
    }

    /**
     * @param string $message
     */
    protected function addError($message)
    {
        if (!in_array($message, $this->errors)) {
            $this->errors[] = $message;
        }
    }

    /**
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * @return bool
     */
    public function hasErrors()
    {
        return !empty($this->errors);
    }

    public function setWorklistPatientId($worklist_patient_id)
    {
        \Yii::app()->user->setState("worklist_patient_id", $worklist_patient_id);
    }

    public function clearWorklistPatientId()
    {
        \Yii::app()->user->setState("worklist_patient_id", null);
    }

    public function getWorklistpatientId()
    {
        return \Yii::app()->user->getState("worklist_patient_id", null);
    }
}

