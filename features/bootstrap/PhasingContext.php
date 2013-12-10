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
    public function __construct(array $parameters)
    {

    }

    /**
     * @Then /^I choose a right eye Intraocular Pressure Instrument  of "([^"]*)"$/
     */
    public function RightEyeIntraocular($rightEye)
    {
        /**
         * @var Phasing $phasing
         */
        $phasing= $this->getPage('Phasing');
        $phasing->confirmPhasingLogoExist();
        $phasing->rightInstrument($rightEye);
    }

    /**
 * @Given /^I choose right eye Dilation of Yes$/
 */
    public function iChooseRightEyeDilationYes()
    {
        /**
         * @var Phasing $phasing
         */
        $phasing= $this->getPage('Phasing');
        $phasing->rightDilationYes();
    }

    /**
     * @Given /^I choose right eye Dilation of No$/
     */
    public function iChooseRightEyeDilationNo()
    {
        /**
         * @var Phasing $phasing
         */
        $phasing= $this->getPage('Phasing');
        $phasing->rightDilationNo();
    }

    /**
     * @Then /^I choose a right eye Intraocular Pressure Reading Time of "([^"]*)"$/
     */
    public function iChooseARightEyeIntraocularPressureReadingTimeOf($time)
    {
        /**
         * @var Phasing $phasing
         */
        $phasing= $this->getPage('Phasing');
        $phasing->rightPressureTime($time);
    }


    /**
     * @Then /^I choose a right eye Intraocular Pressure Reading of "([^"]*)"$/
     */
    public function iChooseARightEyeIntraocularPressureReadingOf($righteye)
    {
        /**
         * @var Phasing $phasing
         */
        $phasing= $this->getPage('Phasing');
        $phasing->rightPressure($righteye);
    }

    /**
     * @Given /^I add right eye comments of "([^"]*)"$/
     */
    public function iAddRightEyeCommentsOf($comments)
    {
        /**
         * @var Phasing $phasing
         */
        $phasing= $this->getPage('Phasing');
        $phasing->rightComments($comments);
    }

    /**
     * @Then /^I choose a left eye Intraocular Pressure Instrument  of "([^"]*)"$/
     */
    public function iChooseALeftEyeIntraocularPressureInstrumentOf($leftEye)
    {
        /**
         * @var Phasing $phasing
         */
        $phasing= $this->getPage('Phasing');
        $phasing->leftInstrument($leftEye);
    }

    /**
     * @Given /^I choose left eye Dilation of Yes$/
     */
    public function iChooseLeftEyeDilationYes()
    {
        /**
         * @var Phasing $phasing
         */
        $phasing= $this->getPage('Phasing');
        $phasing->leftDilationYes();
    }

    /**
     * @Given /^I choose left eye Dilation of No$/
     */
    public function iChooseLeftEyeDilationNo()
    {
        /**
         * @var Phasing $phasing
         */
        $phasing= $this->getPage('Phasing');
        $phasing->leftDilationNo();
    }

    /**
     * @Then /^I choose a left eye Intraocular Pressure Reading Time of "([^"]*)"$/
     */
    public function iChooseALeftEyeIntraocularPressureReadingTimeOf($time)
    {
        /**
         * @var Phasing $phasing
         */
        $phasing= $this->getPage('Phasing');
        $phasing->leftPressureTime($time);
    }

    /**
     * @Then /^I choose a left eye Intraocular Pressure Reading of "([^"]*)"$/
     */
    public function iChooseALeftEyeIntraocularPressureReadingOf($leftEye)
    {
        /**
         * @var Phasing $phasing
         */
        $phasing= $this->getPage('Phasing');
        $phasing->leftPressure($leftEye);
    }

    /**
     * @Given /^I add left eye comments of "([^"]*)"$/
     */
    public function iAddLeftEyeCommentsOf($comments)
    {
        /**
         * @var Phasing $phasing
         */
        $phasing= $this->getPage('Phasing');
        $phasing->leftComments($comments);
    }

    /**
     * @Then /^I add a new Left Reading$/
     */
    public function iAddANewLeftReading()
    {
        /**
         * @var Phasing $phasing
         */
        $phasing= $this->getPage('Phasing');
        $phasing->addLeftReading();
    }

    /**
     * @Then /^I choose a second left eye Intraocular Pressure Reading Time of "([^"]*)"$/
     */
    public function iChooseASecondLeftEyeIntraocularPressureReadingTimeOf($time)
    {
        /**
         * @var Phasing $phasing
         */
        $phasing= $this->getPage('Phasing');
        $phasing->secondLeftTime($time);
    }

    /**
     * @Then /^I choose a second left eye Intraocular Pressure Reading of "([^"]*)"$/
     */
    public function iChooseASecondLeftEyeIntraocularPressureReadingOf($reading)
    {
        /**
         * @var Phasing $phasing
         */
        $phasing= $this->getPage('Phasing');
        $phasing->secondLeftReading($reading);
    }

    /**
     * @Then /^I add a new Right Reading$/
     */
    public function iAddANewRightReading()
    {
        /**
         * @var Phasing $phasing
         */
        $phasing= $this->getPage('Phasing');
        $phasing->addRightReading();
    }

    /**
     * @Then /^I choose a second right eye Intraocular Pressure Reading Time of "([^"]*)"$/
     */
    public function iChooseASecondRightEyeIntraocularPressureReadingTimeOf($time)
    {
        /**
         * @var Phasing $phasing
         */
        $phasing= $this->getPage('Phasing');
        $phasing->secondRightTime($time);
    }

    /**
     * @Then /^I choose a second right eye Intraocular Pressure Reading of "([^"]*)"$/
     */
    public function iChooseASecondRightEyeIntraocularPressureReadingOf($reading)
    {
        /**
         * @var Phasing $phasing
         */
        $phasing= $this->getPage('Phasing');
        $phasing->secondRightReading($reading);
    }

    /**
     * @Then /^I remove the last Right Reading$/
     */
    public function iRemoveTheLastRightReading()
    {
        /**
         * @var Phasing $phasing
         */
        $phasing= $this->getPage('Phasing');
        $phasing->removeRightReading();
    }

    /**
     * @Then /^I remove the last Left Reading$/
     */
    public function iRemoveTheLastLeftReading()
    {
        /**
         * @var Phasing $phasing
         */
        $phasing= $this->getPage('Phasing');
        $phasing->removeLeftReading();
    }

    /**
     * @Then /^I Save the Phasing Event$/
     */
    public function iSaveThePhasingEvent()
    {
        /**
         * @var Phasing $phasing
         */
        $phasing= $this->getPage('Phasing');
        $phasing->savePhasingEvent();
    }

}