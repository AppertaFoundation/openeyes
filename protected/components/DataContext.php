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
        return Firm::model()
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
        $firm = $this->getCurrentFirm();

        if ($subspecialty = $firm->getSubspecialty()) {
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
            $criteria->compare('episode.support_services', true);
        }
        else {
            print_r($this->subspecialty);
            $criteria->addInCondition('serviceSubspecialtyAssignment.subspecialty_id', array_map(
                function($ss) {
                    return $ss->id;
                }, $this->subspecialties
            ));
        }
    }

}