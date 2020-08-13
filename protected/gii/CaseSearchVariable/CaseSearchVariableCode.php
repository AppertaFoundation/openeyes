<?php


class CaseSearchVariableCode extends CCodeModel
{
    public $className;
    public $name;
    public $label;
    public $unit;
    public $eyeCardinality = false;
    public $searchProviders = 'DBProvider'; // default to DBProvider
    public $path = 'application.modules.OECaseSearch';

    public function rules()
    {
        return array_merge(parent::rules(), array(
            array('className, name, label, searchProviders, path', 'required'),
            array('className, name, label', 'match', 'pattern' => '/^\w+$/'),
            array('searchProviders', 'match', 'pattern' => '/^[\w,]+$/'),
            array('unit', 'match', 'pattern' => '/^[\w\s,]+$/'),
            array('eyeCardinality', 'boolean'),
            array('className, name, label, unit, searchProviders', 'sticky')
        ));
    }

    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), array(
            'className' => 'Variable Class Name',
            'name' => 'Variable name',
            'label' => 'Variable label',
            'unit' => 'Unit of measure',
            'eyeCardinality' => 'Eye-based cardinality',
            'searchProviders' => 'Supported Search Providers',
            'path' => 'Module Path',
        ));
    }

    /**
     * @throws CException
     */
    public function prepare()
    {
        $variablePath = Yii::getPathOfAlias($this->path . '.models.' . $this->className) . 'Variable.php';
        $variableCode = $this->render($this->templatePath.'/case_search_variable.php');
        $testPath = Yii::getPathOfAlias($this->path . '.tests.unit.models.' . $this->className) . 'VariableTest.php';
        $testCode = $this->render($this->templatePath.'/case_search_variable_test.php');
        $this->files[] = new CCodeFile($variablePath, $variableCode);
        $this->files[] = new CCodeFile($testPath, $testCode);
    }
}
