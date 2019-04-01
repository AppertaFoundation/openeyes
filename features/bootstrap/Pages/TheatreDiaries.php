<?php

use Behat\Behat\Exception\BehaviorException;

class TheatreDiaries extends OpenEyesPage
{
    protected $path = "/site/OphTrOperationbooking/theatreDiary/index";
    protected $elements = array(
        'startDate' => array(
            'xpath' => "//*[@name='date-start']"
        ),
        'endDate' => array(
            'xpath' => "//*[@name='date-end']"
        ),
        'searchButton' => array(
            'xpath' => "//*[@id='search_button']"
        ),
        'invalidStartDate' => array(
            'xpath' => "//*[contains(text(),'Invalid start date')]"
        ),
        'invalidEndDate' => array(
            'xpath' => "//*[contains(text(),'Invalid end date')]"
        )
    );

    public function searchWith($startDate, $endDate)
    {
        $this->getElement('startDate')->setValue($startDate);
        $this->getElement('endDate')->setValue($endDate);
        sleep(5);
        $this->getElement('searchButton')->press();
    }

    public function searchAndConfirmNoError()
    {
        sleep(15);
        if ($this->startDateErrorShown() || $this->endDateErrorShown()) {
            throw new BehaviorException ("Test Failed!!!");
        }
    }

    public function startDateErrorShown()
    {
        return ( bool )$this->find('xpath', $this->getElement('invalidStartDate')->getXpath());
    }

    public function endDateErrorShown()
    {
        return ( bool )$this->find('xpath', $this->getElement('invalidEndDate')->getXpath());
    }
}