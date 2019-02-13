<?php
/**
 * Created by PhpStorm.
 * User: zhe
 * Date: 1/02/19
 * Time: 4:02 PM
 */

use \SensioLabs\Behat\PageObjectExtension\Context\PageObjectContext;
class LabResultsContext extends PageObjectContext
{
    /**
     * @Then /^I select Lab Results type "([^"]*)"$/
     */
    public function iSelectLabResultsType($type)
    {
        /**
         *
         * @var $labResults LabResults
         *
         */
        $labResults = $this->getPage('LabResults');
        $labResults->selectLabResultsType($type);
    }

    /**
     * @Given /^I select time of recording "([^"]*)"$/
     */
    public function iSelectTimeOfRecording($time)
    {
        $labResults = $this->getPage('LabResults');
        $labResults->selectTimeOfRecording($time);
    }

    /**
     * @Given /^I select the result of "([^"]*)"$/
     */
    public function iSelectTheResultOf($result)
    {
        $labResults = $this->getPage('LabResults');
        $labResults->selectResult($result);
    }

    /**
     * @Then /^I select a comment message of "([^"]*)"$/
     */
    public function iSelectACommentMessageOf($comment)
    {
        /**
         * @var $labResults LabResults
         */
        $labResults = $this->getPage('LabResults');
        $labResults->selectComment($comment);
    }

    /**
     * @Then /^I save the Lab Result and confirm$/
     *
     */
    public function iSaveTheLabResultAndConfirm()
    {
        /**
         * @var $labResults LabResults
         */
        $labResults = $this->getPage('LabResults');
        $labResults->saveAndConfirm();
    }

}