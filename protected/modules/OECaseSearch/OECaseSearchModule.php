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
        $dependencies = array(
            'OECaseSearch.components.*',
            'OECaseSearch.models.*',
            'OECaseSearch.controllers.*',
        );
        if (!isset(Yii::app()->params['patient_identifiers'])) {
            unset($this->config['parameters']['OECaseSearch'][array_search('PatientIdentifier', $this->config['parameters']['OECaseSearch'], true)]);
        }
        foreach ($this->config['parameters'] as $module => $paramList) {
            if ($module !== 'core' && $module !== 'OECaseSearch' && !isset($dependencies[$module])) {
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
                $keys[] = array('type' => $className, 'label' => $obj->getLabel(), 'id' => $className);
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
     * @param $providerID string|int The unique ID of the search provider you wish to use. This can be found in config/common.php for each included search provider.
     * @return SearchProvider The search provider identified by $providerID
     */
    public function getSearchProvider($providerID)
    {
        return $this->searchProviders[$providerID];
    }
}
