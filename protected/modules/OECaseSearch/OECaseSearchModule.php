<?php

/**
 * Class OECaseSearchModule
 */
class OECaseSearchModule extends BaseModule
{
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
            'OECaseSearch.widgets.*',
        );
        if (!isset(Yii::app()->params['patient_identifiers'])) {
            unset($this->config['parameters']['OECaseSearch'][array_search('PatientIdentifier', $this->config['parameters']['OECaseSearch'], true)]);
        }
        // Set imports for other modules with parameters.
        foreach ($this->config['parameters'] as $module => $paramList) {
            if ($module !== 'core' && $module !== 'OECaseSearch' && !isset($dependencies[$module])) {
                $dependencies[$module] = "$module.models.*";
            }
        }
        // Set imports for other modules with variables.
        foreach ($this->config['variables'] as $module => $variableList) {
            if ($module !== 'core' && $module !== 'OECaseSearch' && !isset($dependencies[$module])) {
                $dependencies[$module] = "$module.models.*";
            }
        }
        $this->setImport($dependencies);
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
                $obj = new $className();
                $keys[] = array('type' => $className, 'label' => $obj->label, 'id' => $className);
            }
        }

        return $keys;
    }

    public function getVariableList()
    {
        $keys = array();
        foreach ($this->config['variables'] as $group) {
            foreach ($group as $variable) {
                /**
                 * @var $obj CaseSearchVariable
                 */
                $obj = null;
                if (is_array($variable)) {
                    $obj = new $variable['class'](null);
                    foreach ($variable as $k => $v) {
                        $obj->$k = $v;
                    }
                } else {
                    $obj = new $variable(null);
                }
                $keys[] = array('type' => $variable, 'label' => $obj->label, 'id' => $obj->field_name);
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
}
