<?php

/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2016
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2016, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

/**
 * This component class is intended to encaspulate the logic of interacting with the Worklists
 *
 * Class WorklistManager
 */
class WorklistManager extends CComponent
{
    public static $AUDIT_TARGET_MANUAL = "Manual Worklist";
    public static $AUDIT_TARGET_AUTO = "Automatic Worklists";
    /**
     * @var string
     */
    protected static $DEFAULT_WORKLIST_START_TIME = '09:00';
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
     * should be rendered on the dashboard
     *
     * @var string
     */
    protected static $DEFAULT_DASHBOARD_FUTURE_DAYS = 1;

    /**
     * Array of 3 letter days of the week that should be skipped for picking dates to render
     * worklist dashboards for.

     * @TODO: leverage for day or week selection for definition setup
     * @var array
     */
    protected static $DEFAULT_DASHBOARD_SKIP_DAYS = ['Sun'];

    /**
     * Internal store of error messages
     *
     * @var array
     */
    protected $errors = [];

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
     * Abstraction for getting model instance of class
     *
     * @param $class
     * @return mixed
     */
    protected function getModelForClass($class)
    {
        return $class::model();
    }

    /**
     * Abstraction for getting instance of class
     *
     * @param $class
     * @return mixed
     */
    protected function getInstanceForClass($class, $args = array())
    {
        if (empty($args))
            return new $class();

        $cls = new ReflectionClass($class);
        return $cls->newInstanceArgs($args);
    }

    /**
     * Wrapper for starting a transaction
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
     * @return mixed
     */
    protected function renderPartial($view, $parameters = array())
    {
        return $this->yii->controller->renderPartial($view, $parameters,true);
    }

    /**
     * Wrapper for retrieving current active User
     *
     * @return mixed
     */
    protected function getCurrentUser()
    {
        return $this->yii->user;
    }

    /**
     * @return Site|null
     */
    protected function getCurrentSite()
    {
        if (!$site_id = $this->yii->session['selected_site_id'])
            return null;

        return $this->getModelForClass("Site")->findByPk($site_id);
    }

    /**
     * @return Firm|null
     */
    protected function getCurrentFirm()
    {
        if (!$firm_id = $this->yii->session->get('selected_firm_id'))
            return null;

        return $this->getModelForClass('Firm')->findByPk($firm_id);
    }

    /**
     * Wrapper for retrieve app parameters
     *
     * @param $name
     * @return array|string|null
     */
    protected function getAppParam($name)
    {
        return isset($this->yii->params[$name]) ?
            $this->yii->params[$name] : null;
    }

    /**
     * Audit Wrapper
     *
     * @param $target
     * @param $action
     * @param null $data
     * @param null $log_message
     * @param array $properties
     * @throws Exception
     */
    protected function audit($target, $action, $data=null, $log_message=null, $properties=array())
    {
        if (!isset($properties['user_id']))
            $properties['user_id'] = $this->getCurrentUser()->id;

        if (is_array($data)) {
            $data = json_encode($data);
        }

        Audit::add($target, $action, $data, $log_message, $properties);
    }

    /**
     * Wrapper for managing default start time for scheduled worklists
     *
     * @return string
     */
    public function getDefaultStartTime()
    {
        return $this->getAppParam('default_worklist_start_time') ?: self::$DEFAULT_WORKLIST_START_TIME;
    }

    /**
     * Wrapper for managing default end time for scheduled worklists
     *
     * @return string
     */
    public function getDefaultEndTime()
    {
        return $this->getAppParam('default_worklist_end_time') ?: self::$DEFAULT_WORKLIST_END_TIME;
    }

    public function getWorklistPageSize()
    {
        return $this->getAppParam('default_worklist_pagination_size') ?: self::$DEFAULT_WORKLIST_PAGE_SIZE;
    }

    public function getWorklistDefinitions()
    {
        return $this->getModelForClass('WorklistDefinition')->findAll();
    }

    public function getGenerationTimeLimitDate()
    {
        $limit = $this->getAppParam('default_generation_limit') ?: self::$DEFAULT_GENERATION_LIMIT;
        $interval = DateInterval::createFromDateString($limit);

        return (new DateTime())->add($interval);
    }

    public function getDashboardRenderDates(DateTime $date)
    {
        // in case the passed in date is being used for anything else
        $r_date = clone $date;
        $r_date->setTime(0,0,0);

        $future_days = $this->getAppParam('worklist_dashboard_future_days') ?: self::$DEFAULT_DASHBOARD_FUTURE_DAYS;
        $skip_days = $this->getAppParam('worklist_dashboard_skip_days') ?: self::$DEFAULT_DASHBOARD_SKIP_DAYS;
        if (count($skip_days) >= 7) 
            throw new Exception("Too many days set to be skipped");
        
        $future_dates = array();
        while (count($future_dates) < $future_days) {
            $r_date = clone $r_date;
            $r_date->modify('+1 day');
            if (!in_array($r_date->format('D'), $skip_days))
                $future_dates[] = $r_date;
        }

        if (!in_array($date->format('D'), $skip_days))
            array_unshift($future_dates, clone $date);

        return $future_dates;
    }
    /**
     * @param null $id
     * @return WorklistDefinition|null
     */
    public function getWorklistDefinition($id = null)
    {
        if (is_null($id)) {
            $definition = $this->getInstanceForClass('WorklistDefinition');
            $definition->attributes = array(
                'start_time' => $this->getDefaultStartTime(),
                'end_time' => $this->getDefaultEndTime()
            );
        } else {
            $definition = $this->getModelForClass('WorklistDefinition')->findByPk($id);
        }

        return $definition;
    }

    /**
     * @param $definition
     * @return bool
     */
    public function saveWorklistDefinition($definition)
    {
        if (!$this->canEditWorklistDefinition($definition)) {
            $this->addError("cannot save Definition that is un-editable");
            return false;
        }

        $action = $definition->isNewRecord ? "create" : "update";

        $transaction = $this->startTransaction();

        try {
            if (!$definition->save())
                throw new Exception("Couldn't save definition");

            $this->audit(self::$AUDIT_TARGET_AUTO, $action, array(
                'worklist_definition_id' => $definition->id
            ));

            if ($transaction)
                $transaction->commit();
        }
        catch (Exception $e) {
            $this->addError($e->getMessage());
            if ($transaction)
                $transaction->rollback();
            return false;
        }

        return true;
    }

    /**
     * @param $id
     * @return Worklist
     */
    public function getWorklist($id)
    {
        if (is_null($id))
            return $this->getInstanceForClass('Worklist');

        return $this->getModelForClass('Worklist')->findByPk($id);
    }

    /**
     * @param $worklist
     * @param $user
     * @param null $display_order
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
            $display_order = $max_display_order ? $max_display_order+1 : 1;
        }

        $wdo = $this->getInstanceForClass('WorklistDisplayOrder');
        $wdo->worklist_id = $worklist->id;
        $wdo->user_id = $user->id;
        $wdo->display_order = $display_order;

        return $wdo->save();
    }


    /**
     * @param Worklist $worklist
     * @param null $user
     * @param bool $display
     * @return bool
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
                // TODO: handle different error structure (i.e. the model errors)
                throw new Exception("Could not create Worklist.");
            }

            if ($display)
                if (!$this->addWorklistToUserDisplay($worklist, $user))
                    throw new Exception("Could not set new worklist display order.");

            $this->audit(self::$AUDIT_TARGET_MANUAL, 'create',
                array('worklist_id' => $worklist->id, 'owner_id' => $user->id),
                "Worklist created.");

            if ($transaction)
                $transaction->commit();

        }
        catch (Exception $e) {
            $this->addError($e->getMessage());
            if ($transaction)
                $transaction->rollback();
            return false;
        }

        return true;
    }

    /**
     * @param $user
     * @return array
     */
    public function getCurrentManualWorklistsForUser($user)
    {
        $worklists = array();
        foreach ($this->getModelForClass('WorklistDisplayOrder')->with('worklist')->findAll(array(
            'condition' => 'user_id=:uid',
            'order' => 'display_order asc',
            'params' => array(':uid' => $user->id))) as $wdo) {
            $worklists[] = $wdo->worklist;
        }

        return $worklists;
    }

    /**
     * @param $user
     * @param Site $site
     * @param Firm $firm
     * @param DateTime $when
     * @return array
     */
    public function getCurrentAutomaticWorklistsForUserContext($user, Site $site, Firm $firm, DateTime $when)
    {
        $worklists = array();
        $model = $this->getModelForClass('Worklist');
        $model->automatic = true;
        $model->on = $when;
        foreach ($model->with('worklist_patients')->search()->getData() as $wl) {
            $worklists[] = $wl;
        }

        return $worklists;
    }

    /**
     * @param $user
     * @return mixed
     */
    public function getAvailableManualWorklistsForUser($user)
    {
        $worklists = array();
        $model = $this->getModelForClass("Worklist");
        $model->automatic = false;
        $model->created_user_id = $user->id;

        $search = $model->with('worklist_patients')->search();
        $criteria = $search->criteria;
        $criteria->order = 'created_date desc';

        $current = $this->getCurrentManualWorklistsForUser($user);

        foreach ($search->getData() as $wl) {
            if (!in_array($wl, $current))
                $worklists[] = $wl;
        }

        return $worklists;
    }

    /**
     *
     * @param $user
     * @param array $worklist_ids
     * @return bool
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
                    if (!$order->save())
                        throw new Exception("Could not save order entry");
                }
            }

            $this->audit(self::$AUDIT_TARGET_MANUAL, 'ordered', array('user_id' => $user->id),
                'Worklists reordered for user.');

            if ($transaction)
                $transaction->commit();
        }
        catch (Exception $e) {
            $this->addError($e->getMessage());
            if ($transaction)
                $transaction->rollback();
            return false;
        }

        return true;
    }

    /**
     * @param Worklist $worklist
     * @param Patient $patient
     * @return array|CActiveRecord|mixed|null
     */
    public function getWorklistPatient(Worklist $worklist, Patient $patient)
    {
        return $this->getModelForClass('WorklistPatient')->findByAttributes(array('patient_id' => $patient->id, 'worklist_id' => $worklist->id));
    }

    /**
     * @param WorklistPatient $worklist_patient
     * @param array $attributes
     * @return bool
     * @throws CDbException
     * @throws Exception
     */
    public function setAttributesForWorklistPatient(WorklistPatient $worklist_patient, $attributes = array())
    {
        $transaction = $this->startTransaction();
        $worklist = $worklist_patient->worklist;
        try {
            $valid_attributes = array();
            foreach ($worklist->mapping_attributes as $attr)
                $valid_attributes[$attr->name] = $attr->id;
            OELog::log(print_r($valid_attributes, true));
            foreach ($attributes as $attr => $val) {
                if (!array_key_exists($attr, $valid_attributes))
                    throw new Exception("Unrecognised attribute {$attr} for {$worklist->name}");

                $wlattr = $this->getInstanceForClass('WorklistPatientAttribute');
                $wlattr->attributes = array(
                    'worklist_patient_id' => $worklist_patient->id,
                    'worklist_attribute_id' => $valid_attributes[$attr],
                    'attribute_value' => $val
                );

                if (!$wlattr->save())
                    throw new Exception("Unable to save attribute {$attr} for patient worklist.");
            }

            if ($transaction)
                $transaction->commit();
        }
        catch (Exception $e)
        {
            $this->addError($e->getMessage());
            if ($transaction)
                $transaction->rollback();
            return false;
        }

        return true;
    }

    /**
     * If the given Patient is successfully added to the given Worklist, returns true. false otherwise
     *
     * @param Patient $patient
     * @param Worklist $worklist
     * @param DateTime $when
     * @param array $attributes
     * @return WorklistPatient|null
     */
    public function addPatientToWorklist(Patient $patient, Worklist $worklist, DateTime $when=null, $attributes = array())
    {
        $this->reset();

        if ($this->getWorklistPatient($worklist, $patient)) {
            $this->addError("Patient is already on the given worklist.");
            return null;
        }

        $transaction = $this->startTransaction();

        try {
            $wp = $this->getInstanceForClass('WorklistPatient');
            $wp->patient_id = $patient->id;
            $wp->worklist_id = $worklist->id;
            if ($when)
                $wp->when = $when->format("Y-m-d H:i:s");

            if (!$wp->save())
                throw new Exception("Unable to save patient to worklist.");

            if (count($attributes))
                if (!$this->setAttributesForWorklistPatient($wp, $attributes))
                    throw new Exception("Could not set attributes for patient on worklist");

            $target = $worklist->worklist_definition_id ? self::$AUDIT_TARGET_AUTO : self::$AUDIT_TARGET_MANUAL;

            $this->audit($target, 'add-patient',
                array('worklist_id' => $worklist->id), "Patient added to worklist",
                array('patient_id' => $patient->id));

            if ($transaction)
                $transaction->commit();

            return $wp;
        }
        catch (Exception $e) {
            $this->addError($e->getMessage());
            if ($transaction)
                $transaction->rollback();
            return null;
        }

    }

    /**
     * @param $worklist
     * @return mixed
     */
    public function renderWorklistForDashboard($worklist)
    {
        $this->yii->assetManager->registerScriptFile('js/worklist-dashboard.js', null, null, AssetManager::OUTPUT_SCREEN);

        return $this->renderPartial('//worklist/dashboard', array(
                'worklist' => $worklist,
                'worklist_patients' => $this->getPatientsForWorklist($worklist)
            )
        );
    }

    /**
     * @param User|null $user
     * @return array|null
     */
    public function renderManualDashboard($user = null)
    {
        if (!$user)
            $user = $this->getCurrentUser();

        $content = "";
        foreach ($this->getCurrentManualWorklistsForUser($user) as $worklist) {
            $content .= $this->renderWorklistForDashboard($worklist);
        }

        if (strlen($content))
            return array(
                'title' => "Manual Worklists",
                'content' => $content,
                'options' => array(
                    'container-id' => \Yii::app()->user->id . '-manual-worklists-container',
                    'js-toggle-open' => true,
                )
            );
    }

    /**
     * Render the automatic dashboard for the given user.
     * @param CWebUser $user
     * @return array|null
     */
    public function renderAutomaticDashboard($user = null)
    {
        if (!$user)
            $user = $this->getCurrentUser();
        $site = $this->getCurrentSite();
        $firm = $this->getCurrentFirm();

        $content = "";
        $days = $this->getDashboardRenderDates(new DateTime());
        foreach ($days as $when)
            foreach ($this->getCurrentAutomaticWorklistsForUserContext($user, $site, $firm, $when) as $worklist)
                $content .= $this->renderWorklistForDashboard($worklist);

        if (strlen($content))
            return array(
                'title' => "Automatic Worklists",
                'content' => $content,
                'options' => array(
                    'container-id' => \Yii::app()->user->id.'-automatic-worklists-container',
                    'js-toggle-open' => true,
                )
            );

    }


    /**
     *
     * @TODO: test me
     * @param $worklist
     * @return CActiveDataProvider
     */
    public function getPatientsForWorklist($worklist)
    {
        $criteria = new CDbCriteria();
        $criteria->addColumnCondition(array("t.worklist_id" => $worklist->id));

        if ($worklist->scheduled) {
            $criteria->order = "t.when";
        }
        else {
            $criteria->order = "LOWER(contact.last_name), LOWER(contact.first_name)";
        }

        $criteria->with = array('patient','patient.contact');
        return new CActiveDataProvider("WorklistPatient", array(
            'criteria' => $criteria,
            'pagination' => array(
                'pageSize' => $this->getWorklistPageSize()
            )
        ));
    }

    /**
     * Manipulates the given RRULE string so that it finishes on the given date
     *
     * @param string $rrule
     * @param DateTime $limit
     * @return string
     */
    public function setDateLimitOnRrule($rrule, DateTime $limit) {
        if (strpos($rrule, 'UNTIL=')) {
            preg_replace('/UNTIL=[^;]*/', 'UNTIL='.$limit->format('Y-m-d'), $rrule);
        }
        else {
            $rrule .= ';UNTIL='.$limit->format('Y-m-d');
        }
        return $rrule;
    }

    /**
     * Generates a Worklist instance name from the given definition and date
     *
     * @TODO: implement support for a name definition that can be used to define what worklist instance names should look like
     *
     * @param $definition
     * @param DateTime $date
     * @return string
     */
    public function generateWorklistName($definition, DateTime $date)
    {
        return $definition->name . ' - ' . $date->format(Helper::NHS_DATE_FORMAT);
    }

    /**
     * Create a worklist instance from the given definition for the given date
     *
     * @param $definition
     * @param DateTime $date
     * @return bool
     */
    protected function createAutomaticWorklist(WorklistDefinition $definition, DateTime $date) {
        $model = $this->getModelForClass('Worklist');
        $range_date = clone $date;
        $range_date->setTime(substr($definition->start_time,0,2), substr($definition->start_time,3,2));

        $start_time = $range_date->format("Y-m-d H:i:s");

        if (!$instance = $model->findByAttributes(array('worklist_definition_id' => $definition->id, 'start' => $start_time))) {

            //TODO: consider a transaction loop here
            $instance = $this->getInstanceForClass('Worklist');
            $range_date->setTime(substr($definition->end_time,0,2), substr($definition->end_time, 3,2));

            $instance->attributes = array(
                'worklist_definition_id' => $definition->id,
                'start' => $start_time,
                'end' => $range_date->format("Y-m-d H:i:s"),
                'scheduled' => true,
                'description' => $definition->description,
                'name' => $this->generateWorklistName($definition, $date),
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
     * @param WorklistDefinition $worklist
     * @param DateTime $date_limit
     */
    public function generateAutomaticWorklists(WorklistDefinition $definition, $date_limit = null)
    {
        if (is_null($date_limit))
            $date_limit = $this->getGenerationTimeLimitDate();

        $rrule_str = $this->setDateLimitOnRrule($definition->rrule, $date_limit);
        $rrule = $this->getInstanceForClass('\RRule\RRule',array($rrule_str));

        $new_count = 0;

        $transaction = $this->startTransaction();

        try {
            foreach ($rrule as $occurence) {
                if ($this->createAutomaticWorklist($definition, $occurence))
                    $new_count++;
            }
            
            $this->audit(self::$AUDIT_TARGET_AUTO, 'generate',
                array('worklist_definition_id' => $definition->id, 'generated' => $new_count),
                "Worklists generated");

            if ($transaction)
                $transaction->commit();

            return $new_count;
        }
        catch (Exception $e)
        {
            $this->addError($e->getMessage());
            if ($transaction)
                $transaction->commit();

            return false;
        }
    }



    /**
     *
     * @param WorklistDefinitionMapping $mapping
     * @param $key
     * @param string $values
     * @return bool
     */
    public function updateWorklistDefinitionMapping(WorklistDefinitionMapping $mapping, $key, $values, $display = true)
    {
        if (!$values) {
            $this->addError("At least one mapping value must be provided");
            return false;
        }
        $values = explode(",", $values);

        $definition = $mapping->worklist_definition;

        if (!$this->canEditWorklistDefinition($definition)) {
            $this->addError("Cannot update mapping for un-editable Worklist Definition");
            return false;
        }

        if (!$definition->validateMappingKey($key, $mapping->id)) {
            $this->addError("Mapping key {$key} already exists for definition");
            return false;
        }

        $mapping->key = $key;

        if (!$display) {
            $mapping->display_order = null;
        }
        else if (!$mapping->display_order) {
            $mapping->display_order = $definition->getNextDisplayOrder();
        }

        $transaction = $this->startTransaction();

        try {
            if (!$mapping->save())
                throw new Exception("Could not save mapping");

            if (!$mapping->updateValues($values))
                throw new Exception("Could not save mapping values");

            $this->audit(self::$AUDIT_TARGET_AUTO, 'mapping-update');

            if ($transaction)
                $transaction->commit();
        }
        catch (Exception $e) {
            $this->addError($e->getMessage());
            if ($transaction)
                $transaction->commit();
            return false;
        }

        return true;
    }

    /**
     * @param WorklistDefinition $definition
     * @param array $ids
     * @return bool
     */
    public function setWorklistDefinitionMappingDisplayOrder(WorklistDefinition $definition, $ids = array())
    {
        foreach ($ids as $i => $id) {
            $display_lookup[$id] = $i+1;
        }
        $transaction = $this->startTransaction();

        try {
            foreach ($definition->displayed_mappings as $mapping) {
                $mapping->display_order = $display_lookup[$mapping->id];
                $mapping->save();
            }

            if ($transaction)
                $transaction->commit();

            return true;
        }
        catch (Exception $e) {
            $this->addError($e->getMessage());
            if ($transaction)
                $transaction->rollback();
            return false;
        }
    }

    /**
     * @param Worklist $wl
     * @param $attributes
     * @return bool
     * @throws Exception
     */
    protected function checkWorklistMappingMatch(Worklist $wl, $attributes)
    {
        if (!$wl->worklist_definition)
            throw new Exception("Cannot match Worklist that doesn't have a WorklistDefinition.");

        foreach ($wl->worklist_definition->mappings as $mapping) {
            if (!array_key_exists($mapping->key, $attributes)) {
                $this->addError("Missing key {$mapping->key}");
                return false;
            }

            $match = false;
            foreach ($mapping->values as $val) {
                if (strtolower($val->mapping_value) == strtolower(trim($attributes[$mapping->key])))
                    $match = true;
            }
            if (!$match) {
                $this->addError($attributes[$mapping->key] . " not valid for key '{$mapping->key}'");
                return false;
            }
        }
        // get to the end and no mismatch found. must match.
        return true;
    }


    /**
     * @param DateTime $when
     * @param array $attributes
     * @return Worklist|null
     */
    protected function getWorklistForMapping(DateTime $when, $attributes = array())
    {
        $model = $this->getModelForClass('Worklist');
        $model->at = $when;
        $model->automatic = true;

        $candidates = array();
        foreach ($model->search()->getData() as $wl)
        {
            if ($this->checkWorklistMappingMatch($wl, $attributes))
                $candidates[] = $wl;
        }

        if (count($candidates) == 1) {
            return $candidates[0];
        }
        elseif (count($candidates) > 1) {
            $this->addError("More than worklist matched criteria");
        }
        else {
            $this->addError("No worklist found for criteria");
        }
        return null;
    }

    /**
     * @param Patient $patient
     * @param DateTime $when
     * @param array $attributes
     * @return WorklistPatient|null
     */
    public function mapPatientToWorklistDefinition(Patient $patient, DateTime $when, $attributes = array())
    {
        $worklist = $this->getWorklistForMapping($when, $attributes);
        if (!$worklist)
            return null;

        return $this->addPatientToWorklist($patient, $worklist, $when, $attributes);
    }

    /**
     * @param WorklistPatient $worklist_patient
     * @param DateTime $when
     * @param array $attributes
     * @return WorklistPatient|null
     */
    public function updateWorklistPatientFromMapping(WorklistPatient $worklist_patient, DateTime $when, $attributes = array())
    {
        $worklist = $this->getWorklistForMapping($when, $attributes);
        if (!$worklist)
            return null;

        $transaction  = $this->startTransaction();

        try {
            $worklist_patient->worklist_id = $worklist->id;
            $worklist_patient->when = $when->format('Y-m-d H:i:s');
            $this->setAttributesForWorklistPatient($worklist_patient, $attributes);

            if (!$worklist_patient->save()) {
                foreach ($worklist_patient->getErrors() as $key => $error) {
                    foreach ($error as $message) {
                        $this->addError("{$key}: {$message}");
                    }
                    throw new Exception("Could not update WorklistPatient");
                }
            }

            if ($transaction)
                $transaction->commit();

            return $worklist_patient;
        }
        catch (Exception $e) {
            $this->addError($e->getMessage());
            if ($transaction)
                $transaction->rollback();
            return null;
        }

    }

    /**
     * @param WorklistDefinition $definition
     * @return int
     */
    public function canEditWorklistDefinition(WorklistDefinition $definition)
    {
        // at the moment we don't allow changes to the definition if worklists exist for it
        return $definition->isNewRecord || count($definition->worklists) == 0;
    }

    /**
     * @TODO: consider the future!!
     * @param WorklistDefinition $definition
     * @return bool
     */
    public function deleteWorklistDefinitionInstances(WorklistDefinition $definition)
    {
        $transaction = $this->startTransaction();

        try {
            foreach($definition->worklists as $worklist) {
                if (!$worklist->delete()) {
                    throw new Exception("Could not delete worklist {$worklist->name}");
                };
            }

            if ($transaction)
                $transaction->commit();

            return true;
        }
        catch (Exception $e) {
            $this->addError($e->getMessage());
            if ($transaction)
                $transaction->rollback();
            return false;
        }
    }

    /**
     * Internal method to reset state for error tracking
     */
    protected function reset()
    {
        $this->errors = array();
    }

    /**
     * @param string $message
     */
    protected function addError($message)
    {
        if (!in_array($message, $this->errors))
            $this->errors[] = $message;
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
}