<?php

/**
 * Created by Mike Smith <mike.smith@camc-ltd.co.uk>.
 */
class DataContext
{
    /**
     * @var CApplication
     */
    protected $app;

    protected static $VALID_ATTRIBUTES = array(
        'subspecialties', 'support_services'
    );

    protected static ?Firm $firm = null;

    protected $attributes = array();

    /**
     * DataContext constructor.
     * @param CApplication|null $app
     * @param array $attributes
     */
    public function __construct(CApplication $app = null, array $attributes = array())
    {
        if (is_null($app)) {
            $app = Yii::app();
        }
        $this->app = $app;

        if ($attributes === null || count($attributes) == 0) {
            $this->initialiseFromApp();
        } else {
            foreach ($attributes as $k => $v) {
                $this->setAttribute($k, $v);
            }
        }
    }

    /**
     * @return array|mixed|null
     */
    private function getCurrentFirm()
    {
        // Cache the firm from the database
        $dependency_sql = "SELECT UPDATE_TIME 
                           FROM   information_schema.tables
                           WHERE  TABLE_SCHEMA = DATABASE()
                           AND TABLE_NAME = 'firm'";
        $dependency = new CDbCacheDependency($dependency_sql);
        return Firm::model()
            ->cache(10000, $dependency, 3)
            ->with(array(
                'serviceSubspecialtyAssignment' => array(
                    'subspecialty'
                )
            ))
            ->findByPk($this->app->session['selected_firm_id']);

    }

    /**
     * Work out the appropriate settings from the current state of the Application.
     */
    protected function initialiseFromApp()
    {   
        if (!isset(static::$firm)){
            static::$firm = $this->getCurrentFirm();
        }
        
        if (!static::$firm) {
            // should only arise on the command line
            return;
        }
        if ($subspecialty = static::$firm->getSubspecialty()) {
            $this->setAttribute('subspecialties', $subspecialty);
        } else {
            $this->setAttribute('support_services', true);
        }
    }

    /**
     * Set an attribute
     *
     * @param $key
     * @param $value
     */
    public function setAttribute($key, $value)
    {
        if (in_array($key, static::$VALID_ATTRIBUTES)) {
            $this->attributes[$key] = $value;
        } else {
            throw new Exception("Unrecognised attribute '{$key}' for " . static::class);
        }
    }

    /**
     * @return Subspecialty[]
     */
    public function getSubspecialties()
    {
        if (array_key_exists('subspecialties', $this->attributes) && $this->attributes['subspecialties'] !== null) {
            $subspecialties = $this->attributes['subspecialties'];
            if (!is_array($subspecialties)) {
                $subspecialties = array($subspecialties);
            }
            return $subspecialties;
        }
        return array();
    }

    public function __get($key)
    {
        if ($key == 'subspecialties') {
            return $this->getSubspecialties();
        }

        if (array_key_exists($key, $this->attributes)) {
            return $this->attributes[$key];
        }
    }

    /**
     * Will add appropriate select constraints to the given criteria object to restrict event selection
     *
     * @param CDbCriteria $criteria
     */
    public function addEventConstraints(CDbCriteria $criteria)
    {
        if ($this->support_services) {
            // This is actually fairly meaningless, because support services episodes currently only support
            // correspondence events. The model may change though, so for now this logically would be the
            // appropriate behaviour in terms of backwards compatibility. In the future, support services
            // may well simply imply no subspecialty and therefore pull from across all "episodes"
            $criteria->compare('episode.support_services', true);
        }
        else {
            $criteria->addInCondition('serviceSubspecialtyAssignment.subspecialty_id', array_map(
                function($ss) {
                    return $ss->id;
                }, $this->subspecialties
            ));
        }
    }

    /**
     * @param Patient $patient
     * @return \Eye|null
     */
    public function getPrincipalEye(\Patient $patient)
    {
        $ss = $this->subspecialties;
        if (count($ss) == 1) {
            if ($episode = Episode::model()->getCurrentEpisodeBySubspecialtyId($patient->id, $ss[0]->id)) {
                return $episode->eye;
            }
        }
    }


}