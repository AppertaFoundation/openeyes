<?php

/**
 * Created by PhpStorm.
 * User: andre
 * Date: 26/05/2017
 * Time: 4:20 PM
 */
class CaseSearchParameterCode extends CCodeModel
{
    public $className;
    public $alias;
    public $name;
    public $type;
    public $attributeList;
    public $searchProviders = 'DBProvider'; // default to DBProvider
    public $path = 'application.modules.OECaseSearch';

    public function rules()
    {
        return array_merge(parent::rules(), array(
            array('className, name, type, alias, searchProviders, path', 'required'),
            array('className, alias', 'match', 'pattern' => '/^\w+$/'),
            array('attributeList, searchProviders', 'match', 'pattern' => '/^[\w,]+$/'),
            array('className, name, type, alias, attributeList, searchProviders', 'sticky')
        ));
    }

    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), array(
            'className' => 'Parameter Class Name',
            'name' => 'Parameter Name',
            'type' => 'Parameter Type',
            'alias' => 'SQL alias prefix',
            'attributeList' => 'Attributes',
            'searchProviders' => 'Supported Search Providers',
            'path' => 'Module Path',
        ));
    }

    public function prepare()
    {
        $parameterPath = Yii::getPathOfAlias($this->path . '.models.' . $this->className) . 'Parameter.php';
        $parameterCode = $this->render($this->templatePath.'/case_search_parameter.php');
        $testPath = Yii::getPathOfAlias($this->path . '.tests.unit.models.' . $this->className) . 'ParameterTest.php';
        $testCode = $this->render($this->templatePath.'/case_search_parameter_test.php');
        $this->files[] = new CCodeFile($parameterPath, $parameterCode);
        $this->files[] = new CCodeFile($testPath, $testCode);
    }
}
