<?php
/**
 * Created by PhpStorm.
 * User: fivium
 * Date: 14/12/18
 * Time: 9:50 AM
 */

use Behat\Behat\Exception\BehaviorException;

class VisualField extends OpenEyesPage
{
    protected $path = "OphInVisualfields/Default/create?patient_id={patientId}";
    protected $elements = array(
        'abilitySelect' => array(
            'xpath' => "//*[@id='MultiSelect_ability']"
        ),
        'comment' => array(
            'xpath' => "//*[@id='Element_OphInVisualfields_Comments_comments']"
        ),
        'result' => array(
            'xpath' => "//*[@id='MultiSelect_assessment']"
        ),
        'resultOther' => array(
            'xpath' => "//*[@id='Element_OphInVisualfields_Result_other']"
        ),
        'saveBtn' => array(
            'xpath' => "//*[@id='et_save']"
        ),
        'saveOK' => array(
            'xpath' => "//*[@id='flash-success']"
        ),
    );

    public function selectAbility($ability)
    {
        $this->getElement('abilitySelect')->selectOption($ability);
    }

    public function selectGlasses($glasses)
    {
        if ($glasses) {
            $this->elements['glasses'] = array(
                'xpath' => "//*[@id='Element_OphInVisualfields_Condition_glasses_1']"
            );
            $this->getElement('glasses')->click();
        } else {
            $this->elements['glasses'] = array(
                'xpath' => "//*[@id='Element_OphInVisualfields_Condition_glasses_0']"
            );
            $this->getElement('glasses')->click();
        }
    }

    public function comment($comment)
    {
        $this->getElement('comment')->setValue($comment);
    }

    public function selectResult($result)
    {
        $this->getElement('result')->selectOption($result);
    }

    public function resultComment($result_comment)
    {
        $this->getElement('resultOther')->setValue($result_comment);
    }

    public function saveVisualField()
    {
        $this->getElement('saveBtn')->click();
    }

    public function saveVisualFieldAndConfirm()
    {
        $this->getElement('saveBtn')->click();

        $this->getSession()->wait(5000, 'window.$ && $.active == 0');
        if (!$this->hasVisualFieldSaved()) {
            throw new BehaviorException ("WARNING!!!  VisualField has NOT been saved!!  WARNING!!");
        }
    }

    protected function hasVisualFieldSaved()
    {
        return ( bool )$this->find('xpath', $this->getElement('saveOK')->getXpath());

    }

}