<?php
/**
 * Created by PhpStorm.
 * User: zhe
 * Date: 1/02/19
 * Time: 4:03 PM
 */

use Behat\Behat\Exception\BehaviorException;

class LabResults extends EventPage
{
    public function __construct(\Behat\Mink\Session $session, \SensioLabs\Behat\PageObjectExtension\Context\PageFactoryInterface $pageFactory, array $parameters = array())
    {
        parent::__construct($session, $pageFactory, $parameters);
        $this->elements = array_merge($this->elements, self::getPageElements());
    }

    protected static function getPageElements()
    {
         return array(
            'typeBtn' => array(
                'xpath' => "//*[@id='Element_OphInLabResults_Details_result_type_id']"
            ),

            'timeField' => array(
                'xpath' => "//*[@id='Element_OphInLabResults_Inr_time']"
            ),
            'timeBtn' => array(
                'xpath' => "//*[@id='div_Element_OphInLabResults_Inr_time']"
            ),
            'resultField' => array(
                'xpath' => "//*[@id='Element_OphInLabResults_Inr_result']"
            ),
            'commentField' => array(
                'xpath' => "//*[@id='Element_OphInLabResults_Inr_comment']"
            ),
             'inrResultField' => array(
                 'xpath' => "//*[@class='element full edit Element_OphInLabResults_Inr']"
             ),

        );
    }

    public function selectLabResultsType($type)
    {
        $this->getElement('typeBtn')->click();
        $this->getElement('typeBtn')->selectOption($type);
    }

    public function selectTimeOfRecording($time)
    {
        $this->waitForElementDisplayBlock('inrResultField');
        if($this->getElement('inrResultField')->isVisible()){
        $this->getElement('timeBtn')->click();
        $this->getElement('timeField')->setValue($time);
        }
    }

    public function selectResult($result)
    {
        $this->getElement('resultField')->setValue($result);
    }

    public function selectComment($comment)
    {
        $this->getElement('commentField')->setValue($comment);
    }

}