<?php

use Behat\Behat\Context\ClosuredContextInterface,
    Behat\Behat\Context\TranslatedContextInterface,
    Behat\Behat\Context\BehatContext,
    Behat\Behat\Exception\PendingException;
use Behat\Gherkin\Node\PyStringNode,
    Behat\Gherkin\Node\TableNode;
use Behat\MinkExtension\Context\MinkContext;

use Behat\YiiExtension\Context\YiiAwareContextInterface;
use Behat\Mink\Driver\Selenium2Driver;
use \SensioLabs\Behat\PageObjectExtension\Context\PageObjectContext;

class PhasingContext extends PageObjectContext
{
    public function __construct(array $paramters)
    {

    }

//    /**
//     * @Then /^I choose a right eye Intraocular Pressure Instrument  of "([^"]*)"$/
//     */
//    public function RightEyeIntraocular($righteye)
//    {
//       $this->selectOption(Phasing::$phasingInstrumentRight, $righteye);
//    }
//
//    /**
//     * @Given /^I choose right eye Dilation of "([^"]*)"$/
//     */
//    public function iChooseRightEyeDilationOf($dilation)
//    {
//        $this->clickLink(Phasing::$phasingDilationRight);
//    }
//
//    /**
//     * @Then /^I choose a right eye Intraocular Pressure Reading of "([^"]*)"$/
//     */
//    public function iChooseARightEyeIntraocularPressureReadingOf($righteye)
//    {
//        $this->fillField(Phasing::$phasingPressureLeft, $righteye);
//    }
//
//    /**
//     * @Given /^I add right eye comments of "([^"]*)"$/
//     */
//    public function iAddRightEyeCommentsOf($comments)
//    {
//        $this->fillField(Phasing::$phasingCommentsRight, $comments);
//    }
//
//    /**
//     * @Then /^I choose a left eye Intraocular Pressure Instrument  of "([^"]*)"$/
//     */
//    public function iChooseALeftEyeIntraocularPressureInstrumentOf($lefteye)
//    {
//        $this->selectOption(Phasing::$phasingInstrumentLeft,$lefteye);
//    }
//
//    /**
//     * @Given /^I choose left eye Dilation of "([^"]*)"$/
//     */
//    public function iChooseLeftEyeDilationOf($dilation)
//    {
//        $this->clickLink(Phasing::$phasingDilationLeft);
//    }
//
//    /**
//     * @Then /^I choose a left eye Intraocular Pressure Reading of "([^"]*)"$/
//     */
//    public function iChooseALeftEyeIntraocularPressureReadingOf($lefteye)
//    {
//       $this->fillField(Phasing::$phasingPressureRight, $lefteye);
//    }
//
//    /**
//     * @Given /^I add left eye comments of "([^"]*)"$/
//     */
//    public function iAddLeftEyeCommentsOf($comments)
//    {
//        $this->fillField(Phasing::$phasingCommentsLeft, $comments);
//    }

}