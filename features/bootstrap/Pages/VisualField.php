<?php
/**
 * Created by PhpStorm.
 * User: fivium
 * Date: 14/12/18
 * Time: 9:50 AM
 */

use Behat\Behat\Exception\BehaviorException;

class VisualField extends EventPage
{
    public function __construct(\Behat\Mink\Session $session, \SensioLabs\Behat\PageObjectExtension\Context\PageFactoryInterface $pageFactory, array $parameters = array())
    {
        parent::__construct($session, $pageFactory, $parameters);
        $this->elements = array_merge($this->elements, self::getPageElements());
    }

    protected $path = "OphInVisualfields/Default/create?patient_id={patientId}";

    protected static function getPageElements()
    {
        array(
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
        );
    }

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
}