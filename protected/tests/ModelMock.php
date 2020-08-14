<?php

/**
 * Class ModelMock
 * Stub class used for testing Fuzzy date validators.
 */
class ModelMock extends CModel
{
    public string $foo;
    public string $bar;

    /**
     * @return string[]
     */
    public function attributeNames()
    {
        return array(
            'foo',
            'bar'
        );
    }

    /**
     * @return array|string[][]
     */
    public function rules()
    {
        return array(
            array('foo, bar', 'OEFuzzyDateValidatorNotFuture')
        );
    }
}
