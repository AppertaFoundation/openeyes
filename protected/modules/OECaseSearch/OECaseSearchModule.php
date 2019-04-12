<?php

/**
 * Class OECaseSearchModule
 */
class OECaseSearchModule extends BaseModule
{
    private $searchProviders = array();
    /**
     * @var array $config
     */
    private $config;

    public function init()
    {
        // import the module-level models and components
        $this->config = Yii::app()->params['CaseSearch'];
        $dependencies = array();
        if (!isset(Yii::app()->params['patient_identifiers'])){
            unset($this->config['parameters']['core'][array_search('PatientIdentifier',$this->config['parameters']['core'])]);
        }
        foreach ($this->config['parameters'] as $module => $paramList) {
            if ($module !== 'core' && !isset($dependencies[$module])) {
                $dependencies[$module] = "$module.models.*";
            }
        }

        foreach ($this->config['fixedParameters'] as $module => $paramList) {
            if ($module !== 'core' && !isset($dependencies[$module])) {
                $dependencies[$module] = "$module.models.*";
            }
        }
        $this->setImport($dependencies);

        // Initialise the search provider/s.
        foreach ($this->config['providers'] as $providerID => $searchProvider) {
            $this->searchProviders[$providerID] = new $searchProvider($providerID);
        }
    }

    /**
     * @return array The list of fixed parameter class instances configured for the case search module.
     */
    public function getFixedParams()
    {
        $fixedParams = array();
        $count = 0;
        foreach ($this->config['fixedParameters'] as $group) {
            foreach ($group as $parameter) {
                $className = $parameter . 'Parameter';
                $obj = new $className;
                $obj->id = "fixed_$count";
                $fixedParams[$obj->id] = $obj;
                $count++;
            }
        }

        return $fixedParams;
    }

    /**
     * @return array The list of parameter classes configured for the case search module.
     */
    public function getParamList()
    {
        $keys = array();
        foreach ($this->config['parameters'] as $group) {
            foreach ($group as $parameter) {
                $className = $parameter . 'Parameter';
                /**
                 * @var $obj CaseSearchParameter
                 */
                $obj = new $className;
                $keys[$className] = $obj->getLabel();
            }
        }

        return $keys;
    }

    /**
     * @param $param mixed The key of the respective config parameter for OECaseSearch.
     * @return mixed The config parameter value
     */
    public function getConfigParam($param)
    {
        return $this->config[$param];
    }

    /**
     * @param $providerID mixed The unique ID of the search provider you wish to use. This can be found in config/common.php for each included search provider.
     * @return SearchProvider The search provider identified by $providerID
     */
    public function getSearchProvider($providerID)
    {
        return $this->searchProviders[$providerID];
    }

    public function beforeControllerAction($controller, $action)
    {
        if (parent::beforeControllerAction($controller, $action)) {
            // this method is called before any module controller action is performed
            // you may place customized code here
            return true;
        }

        return false;
    }
}
