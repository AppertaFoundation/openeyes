<?php
/**
 * Created by PhpStorm.
 * User: fivium
 * Date: 14/12/18
 * Time: 9:50 AM
 */
use Behat\Behat\Context\ClosuredContextInterface, Behat\Behat\Context\TranslatedContextInterface, Behat\Behat\Context\BehatContext, Behat\Behat\Exception\PendingException;
use Behat\Gherkin\Node\PyStringNode, Behat\Gherkin\Node\TableNode;
use Behat\MinkExtension\Context\MinkContext;

use Behat\Mink\Driver\Selenium2Driver;
use \SensioLabs\Behat\PageObjectExtension\Context\PageObjectContext;
use WebDriver\WebDriver;


class VisualFieldContext extends PageObjectContext
{
    public function __construct(array $parameters)
    {
    }
    /**
     * @Then/^I select condition ability "([^"]*)"$/
     */
    public function iSelectConditionAbility($ability){
        /**
         * @var VisualField $visualField
         */
        $visualField = $this->getPage('VisualField');
        $visualField->selectAbility($ability);
    }

    /**
     * @Then/^I select glasses yes$/
     */
    public function iSelectGlassesYes(){
        /**
         * @var VisualField $visualField
         */
        $visualField=$this->getPage('VisualField');
        $visualField->selectGlasses(1);
    }



    /**
     * @Then/^I select glasses no$/
     */
    public function iSelectGlassesNo(){
        /**
         * @var VisualField $visualField
         */
        $visualField=$this->getPage('VisualField');
        $visualField->selectGlasses(0);
    }

    /**
     * @Then/^I write visual field comments "([^"]*)"$/
     */
    public function iWriteVisualFieldComments($comment){
        /**
         * @var VisualField $visualFeild
         */
        $visualFeild=$this->getPage('VisualField');
        $visualFeild->comment($comment);
    }
    /**
     * @Given/^I select result "([^"]*)"$/
     */
    public function iSelectResult($result){
        /**
         * @var VisualField $visualField
         */
        $visualField=$this->getPage('VisualField');
        $visualField->selectResult($result);
    }
    /**
     * @Then/^I write result comment "([^"]*)"$/
     */
    public function iWriteResultComment($result_comment){
        /**
         * @var VisualField $visualField
         */
        $visualField = $this->getPage('VisualField');
        $visualField->resultComment($result_comment);
    }
    /**
     * @Then /^I save the Visual Field Event$/
     */
    public function iSaveTheVisualFieldEvent() {
        /**
         * @var VisualField $visualField
         */
        $visualField = $this->getPage('VisualField');
        $visualField->saveVisualField();
    }

    /**
     * @Then /^I save the VisualField Event and confirm it has been created successfully$/
     */
    public function iSaveTheLaserEventAndConfirm() {
        /**
         * @var VisualField $visualField
         */
        $visualField = $this->getPage('VisualField');
        $visualField->saveVisualFieldAndConfirm();
    }




}