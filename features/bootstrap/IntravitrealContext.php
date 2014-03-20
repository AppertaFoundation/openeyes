<?php

use Behat\Behat\Context\ClosuredContextInterface,
    Behat\Behat\Context\TranslatedContextInterface,
    Behat\Behat\Context\BehatContext,
    Behat\Behat\Exception\PendingException;
use Behat\Gherkin\Node\PyStringNode,
    Behat\Gherkin\Node\TableNode;
use Behat\MinkExtension\Context\MinkContext;

use Behat\Mink\Driver\Selenium2Driver;
use \SensioLabs\Behat\PageObjectExtension\Context\PageObjectContext;

class IntravitrealContext extends PageObjectContext
{
    public function __construct(array $parameters)
    {

    }

    /**
     * @Then /^a check is made that the Allergy "([^"]*)" warning is displayed$/
     */
    public function aCheckIsMadeThatTheAllergyWarningIsDisplayed($allergy)
    {
        /**
         * @var Intravitreal $Intravitreal
         */
        $Intravitreal  = $this->getPage('Intravitreal');
        $Intravitreal->confirmAllergyWarning($allergy);
    }

    /**
     * @Then /^I remove the Right Side$/
     */
    public function iRemoveTheRightSide()
    {
        /**
         * @var Intravitreal $Intravitreal
         */
        $Intravitreal  = $this->getPage('Intravitreal');
        $Intravitreal->removeRightSide();
    }

    /**
     * @Then /^I select Add Right Side$/
     */
    public function iSelectAddRightSide()
    {
        /**
         * @var Intravitreal $Intravitreal
         */
        $Intravitreal  = $this->getPage('Intravitreal');
        $Intravitreal->addRightSide();
    }

    /**
     * @Then /^I choose Right Anaesthetic Type of Topical$/
     */
    public function RightAnaestheticTopical()
    {
        /**
         * @var Intravitreal $Intravitreal
         */
        $Intravitreal  = $this->getPage('Intravitreal');
        $Intravitreal->rightTypeTopical();
    }

    /**
     * @Then /^I choose Right Anaesthetic Type of LA$/
     */
    public function RightAnaestheticLa()
    {
        /**
         * @var Intravitreal $Intravitreal
         */
        $Intravitreal  = $this->getPage('Intravitreal');
        $Intravitreal->rightTypeLA();
    }

    /**
     * @Then /^I choose Right Anaesthetic Delivery of Retrobulbar$/
     */
    public function RightAnaestheticRetrobulbar()
    {
        /**
         * @var Intravitreal $Intravitreal
         */
        $Intravitreal  = $this->getPage('Intravitreal');
        $Intravitreal->rightDeliveryRetrobulbar();
    }

    /**
     * @Then /^I choose Right Anaesthetic Delivery of Peribulbar$/
     */
    public function RightAnaestheticPeribulbar()
    {
        /**
         * @var Intravitreal $Intravitreal
         */
        $Intravitreal  = $this->getPage('Intravitreal');
        $Intravitreal->rightDeliveryPeribulbar();
    }

    /**
     * @Then /^I choose Right Anaesthetic Delivery of Subtenons$/
     */
    public function RightAnaestheticSubtenons()
    {
        /**
         * @var Intravitreal $Intravitreal
         */
        $Intravitreal  = $this->getPage('Intravitreal');
        $Intravitreal->rightDeliverySubtenons();
    }

    /**
     * @Then /^I choose Right Anaesthetic Delivery of Subconjunctival$/
     */
    public function RightAnaestheticSubconjunctival()
    {
        /**
         * @var Intravitreal $Intravitreal
         */
        $Intravitreal  = $this->getPage('Intravitreal');
        $Intravitreal->rightDeliverySubconjunctival();
    }

    /**
     * @Then /^I choose Right Anaesthetic Delivery of Topical$/
     */
    public function RightAnaestheticDeliveryTopical()
    {
        /**
         * @var Intravitreal $Intravitreal
         */
        $Intravitreal  = $this->getPage('Intravitreal');
        $Intravitreal->rightDeliveryTopical();
    }

    /**
     * @Then /^I choose Right Anaesthetic Delivery of TopicalandIntracameral$/
     */
    public function RightAnaestheticDeliveryTopicalandIntracameral()
    {
        /**
         * @var Intravitreal $Intravitreal
         */
        $Intravitreal  = $this->getPage('Intravitreal');
        $Intravitreal->rightDeliveryTopicalIntracameral();
    }

    /**
     * @Then /^I choose Right Anaesthetic Delivery of Other$/
     */
    public function RightAnaestheticDeliveryOfOther()
    {
        /**
         * @var Intravitreal $Intravitreal
         */
        $Intravitreal  = $this->getPage('Intravitreal');
        $Intravitreal->rightDeliveryOther();
    }

    /**
     * @Given /^I choose Right Anaesthetic Agent "([^"]*)"$/
     */
    public function RightAnaestheticAgent($agent)
    {
        /**
         * @var Intravitreal $Intravitreal
         */
        $Intravitreal  = $this->getPage('Intravitreal');
        $Intravitreal->rightAnaestheticAgent($agent);
    }

    /**
     * @Then /^I choose Left Anaesthetic Type of Topical$/
     */
    public function LeftAnaestheticTypeOfTopical()
    {
        /**
         * @var Intravitreal $Intravitreal
         */
        $Intravitreal  = $this->getPage('Intravitreal');
        $Intravitreal->leftTypeTopical();
    }

    /**
     * @Then /^I choose Left Anaesthetic Type of LA$/
     */
    public function LeftAnaestheticTypeOfLa()
    {
        /**
         * @var Intravitreal $Intravitreal
         */
        $Intravitreal  = $this->getPage('Intravitreal');
        $Intravitreal->leftTypeLA();
    }

    /**
     * @Then /^I choose Left Anaesthetic Delivery of Retrobulbar$/
     */
    public function LeftAnaestheticDeliveryOfRetrobulbar()
    {
        /**
         * @var Intravitreal $Intravitreal
         */
        $Intravitreal  = $this->getPage('Intravitreal');
        $Intravitreal->leftDeliveryRetrobulbar();
    }

    /**
     * @Then /^I choose Left Anaesthetic Delivery of Peribulbar$/
     */
    public function LeftAnaestheticDeliveryOfPeribulbar()
    {
        /**
         * @var Intravitreal $Intravitreal
         */
        $Intravitreal  = $this->getPage('Intravitreal');
        $Intravitreal->leftDeliveryPeribulbar();
    }

    /**
     * @Then /^I choose Left Anaesthetic Delivery of Subtenons$/
     */
    public function LeftAnaestheticDeliveryOfSubtenons()
    {
        /**
         * @var Intravitreal $Intravitreal
         */
        $Intravitreal  = $this->getPage('Intravitreal');
        $Intravitreal->leftDeliverySubtenons();
    }

    /**
     * @Then /^I choose Left Anaesthetic Delivery of Subconjunctival$/
     */
    public function LeftAnaestheticDeliveryOfSubconjunctival()
    {
        /**
         * @var Intravitreal $Intravitreal
         */
        $Intravitreal  = $this->getPage('Intravitreal');
        $Intravitreal->leftDeliverySubconjunctival();
    }

    /**
     * @Then /^I choose Left Anaesthetic Delivery of Topical$/
     */
    public function LeftAnaestheticDeliveryOfTopical()
    {
        /**
         * @var Intravitreal $Intravitreal
         */
        $Intravitreal  = $this->getPage('Intravitreal');
        $Intravitreal->leftDeliveryTopical();
    }

    /**
     * @Then /^I choose Left Anaesthetic Delivery of TopicalandIntracameral$/
     */
    public function LeftAnaestheticDeliveryOfTopicalandintracameral()
    {
        /**
         * @var Intravitreal $Intravitreal
         */
        $Intravitreal  = $this->getPage('Intravitreal');
        $Intravitreal->leftDeliveryTopicalIntracameral();
    }

    /**
     * @Then /^I choose Left Anaesthetic Delivery of Other$/
     */
    public function LeftAnaestheticDeliveryOfOther()
    {
        /**
         * @var Intravitreal $Intravitreal
         */
        $Intravitreal  = $this->getPage('Intravitreal');
        $Intravitreal->leftDeliveryOther();
    }

    /**
     * @Given /^I choose Left Anaesthetic Agent "([^"]*)"$/
     */
    public function LeftAnaestheticAgent($agent)
    {
        /**
         * @var Intravitreal $Intravitreal
         */
        $Intravitreal  = $this->getPage('Intravitreal');
        $Intravitreal->leftAnaestheticAgent($agent);
    }

    /**
     * @Then /^I choose Right Pre Injection Antiseptic "([^"]*)"$/
     */
    public function RightPreInjectionAntiseptic($antiseptic)
    {
        /**
         * @var Intravitreal $Intravitreal
         */
        $Intravitreal  = $this->getPage('Intravitreal');
        $Intravitreal->rightPreInjectionAntiseptic($antiseptic);
    }

    /**
     * @Then /^I choose Right Pre Injection Skin Cleanser "([^"]*)"$/
     */
    public function RightPreInjectionSkinCleanser($skin)
    {
        /**
         * @var Intravitreal $Intravitreal
         */
        $Intravitreal  = $this->getPage('Intravitreal');
        $Intravitreal->rightPreInjectionSkinCleanser($skin);
    }

    /**
     * @Given /^I tick the Right Pre Injection IOP Lowering Drops checkbox$/
     */
    public function TickRightPreInjectionIopLoweringDropsCheckbox()
    {
        /**
         * @var Intravitreal $Intravitreal
         */
        $Intravitreal  = $this->getPage('Intravitreal');
        $Intravitreal->rightPreInjectionIOPDropsCheckbox();
    }

    /**
     * @Then /^I choose Right Pre Injection IOP Lowering Drops "([^"]*)"$/
     */
    public function iChooseRightPreInjectionIopLoweringDrops($drops)
    {
        /**
         * @var Intravitreal $Intravitreal
         */
        $Intravitreal  = $this->getPage('Intravitreal');
        $Intravitreal->rightPreInjectionIOPDropsLoweringDrops($drops);
    }

    /**
     * @Then /^I choose Right Drug "([^"]*)"$/
     */
    public function iChooseRightDrug($drug)
    {
        /**
         * @var Intravitreal $Intravitreal
         */
        $Intravitreal  = $this->getPage('Intravitreal');
        $Intravitreal->rightDrug($drug);
    }

    /**
     * @Given /^I enter "([^"]*)" number of Right injections$/
     */
    public function NumberOfRightInjections($injections)
    {
        /**
         * @var Intravitreal $Intravitreal
         */
        $Intravitreal  = $this->getPage('Intravitreal');
        $Intravitreal->rightInjections($injections);
    }

    /**
     * @Then /^I enter Right batch number "([^"]*)"$/
     */
    public function RightBatchNumber($batch)
    {
        /**
         * @var Intravitreal $Intravitreal
         */
        $Intravitreal  = $this->getPage('Intravitreal');
        $Intravitreal->rightBatchNumber($batch);
    }

    /**
     * @Then /^I choose Right Injection Given By "([^"]*)"$/
     */
    public function RightInjectionGivenBy($injection)
    {
        /**
         * @var Intravitreal $Intravitreal
         */
        $Intravitreal  = $this->getPage('Intravitreal');
        $Intravitreal->rightInjectionGivenBy($injection);
    }

    /**
     * @Given /^I enter a Right Injection time of "([^"]*)"$/
     */
    public function RightInjectionTimeOf($time)
    {
        /**
         * @var Intravitreal $Intravitreal
         */
        $Intravitreal  = $this->getPage('Intravitreal');
        $Intravitreal->rightInjectionTime($time);
    }

    /**
     * @Then /^I choose A Left Lens Status of "([^"]*)"$/
     */
    public function iChooseALeftLensStatusOf($lens)
    {
        /**
         * @var Intravitreal $Intravitreal
         */
        $Intravitreal  = $this->getPage('Intravitreal');
        $Intravitreal->leftLensStatus($lens);
    }

    /**
     * @Then /^I choose Left Pre Injection Antiseptic "([^"]*)"$/
     */
    public function LeftPreInjectionAntiseptic($antispetic)
    {
        /**
         * @var Intravitreal $Intravitreal
         */
        $Intravitreal  = $this->getPage('Intravitreal');
        $Intravitreal->leftPreInjectionAntiseptic($antispetic);
    }

    /**
     * @Then /^I choose Left Pre Injection Skin Cleanser "([^"]*)"$/
     */
    public function LeftPreInjectionSkinCleanser($skin)
    {
        /**
         * @var Intravitreal $Intravitreal
         */
        $Intravitreal  = $this->getPage('Intravitreal');
        $Intravitreal->leftPreInjectionSkinCleanser($skin);
    }

    /**
     * @Given /^I tick the Left Pre Injection IOP Lowering Drops checkbox$/
     */
    public function LeftPreInjectionIopLoweringDropsCheckbox()
    {
        /**
         * @var Intravitreal $Intravitreal
         */
        $Intravitreal  = $this->getPage('Intravitreal');
        $Intravitreal->leftPreInjectionIOPDropsCheckbox();
    }

    /**
     * @Then /^I choose Left Pre Injection IOP Lowering Drops "([^"]*)"$/
     */
    public function iChooseLeftPreInjectionIopLoweringDrops($drops)
    {
        /**
         * @var Intravitreal $Intravitreal
         */
        $Intravitreal  = $this->getPage('Intravitreal');
        $Intravitreal->leftPreInjectionIOPDropsLoweringDrops($drops);
    }

    /**
     * @Then /^I choose Left Drug "([^"]*)"$/
     */
    public function iChooseLeftDrug($drug)
    {
        /**
         * @var Intravitreal $Intravitreal
         */
        $Intravitreal  = $this->getPage('Intravitreal');
        $Intravitreal->leftDrug($drug);
    }

    /**
     * @Given /^I enter "([^"]*)" number of Left injections$/
     */
    public function NumberOfLeftInjections($injections)
    {
        /**
         * @var Intravitreal $Intravitreal
         */
        $Intravitreal  = $this->getPage('Intravitreal');
        $Intravitreal->leftInjections($injections);
    }

    /**
     * @Then /^I enter Left batch number "([^"]*)"$/
     */
    public function iLeftBatchNumber($batch)
    {
        /**
         * @var Intravitreal $Intravitreal
         */
        $Intravitreal  = $this->getPage('Intravitreal');
        $Intravitreal->leftBatchNumber($batch);
    }

    /**
     * @Then /^I choose Left Injection Given By "([^"]*)"$/
     */
    public function LeftInjectionGivenBy($injection)
    {
        /**
         * @var Intravitreal $Intravitreal
         */
        $Intravitreal  = $this->getPage('Intravitreal');
        $Intravitreal->leftInjectionGivenBy($injection);
    }

    /**
     * @Given /^I enter a Left Injection time of "([^"]*)"$/
     */
    public function iEnterALeftInjectionTimeOf($time)
    {
        /**
         * @var Intravitreal $Intravitreal
         */
        $Intravitreal  = $this->getPage('Intravitreal');
        $Intravitreal->leftInjectionTime($time);
    }

    /**
     * @Then /^I choose A Right Lens Status of "([^"]*)"$/
     */
    public function RightLensStatusOf($lens)
    {
        /**
         * @var Intravitreal $Intravitreal
         */
        $Intravitreal  = $this->getPage('Intravitreal');
        $Intravitreal->rightLensStatus($lens);
    }

    /**
     * @Given /^I choose Right Counting Fingers Checked Yes$/
     */
    public function RightCountingFingersCheckedYes()
    {
        /**
         * @var Intravitreal $Intravitreal
         */
        $Intravitreal  = $this->getPage('Intravitreal');
        $Intravitreal->rightCountingFingersYes();
    }

    /**
     * @Given /^I choose Right Counting Fingers Checked No$/
     */
    public function RightCountingFingersCheckedNo()
    {
        /**
         * @var Intravitreal $Intravitreal
         */
        $Intravitreal  = $this->getPage('Intravitreal');
        $Intravitreal->rightCountingFingersNo();
    }

    /**
     * @Given /^I choose Right IOP Needs to be Checked Yes$/
     */
    public function RightIopNeedsToBeCheckedYes()
    {
        /**
         * @var Intravitreal $Intravitreal
         */
        $Intravitreal  = $this->getPage('Intravitreal');
        $Intravitreal->rightIOPNeedsToBeCheckedYes();
    }

    /**
     * @Given /^I choose Right IOP Needs to be Checked No$/
     */
    public function RightIopNeedsToBeCheckedNo()
    {
        /**
         * @var Intravitreal $Intravitreal
         */
        $Intravitreal  = $this->getPage('Intravitreal');
        $Intravitreal->rightIOPNeedsToBeCheckedNo();
    }

    /**
     * @Then /^I choose Right Post Injection Drops "([^"]*)"$/
     */
    public function RightPostInjectionDrops($drops)
    {
        /**
         * @var Intravitreal $Intravitreal
         */
        $Intravitreal  = $this->getPage('Intravitreal');
        $Intravitreal->rightPostInjectionDrops($drops);
    }

    /**
     * @Given /^I choose Left Counting Fingers Checked Yes$/
     */
    public function LeftCountingFingersCheckedYes()
    {
        /**
         * @var Intravitreal $Intravitreal
         */
        $Intravitreal  = $this->getPage('Intravitreal');
        $Intravitreal->rightCountingFingersYes();
    }

    /**
     * @Given /^I choose Left Counting Fingers Checked No$/
     */
    public function LeftCountingFingersCheckedNo()
    {
        /**
         * @var Intravitreal $Intravitreal
         */
        $Intravitreal  = $this->getPage('Intravitreal');
        $Intravitreal->leftCountingFingersNo();
    }

    /**
     * @Given /^I choose Left IOP Needs to be Checked Yes$/
     */
    public function LeftIopNeedsToBeCheckedYes()
    {
        /**
         * @var Intravitreal $Intravitreal
         */
        $Intravitreal  = $this->getPage('Intravitreal');
        $Intravitreal->leftIOPNeedsToBeCheckedYes();
    }

    /**
     * @Given /^I choose Left IOP Needs to be Checked No$/
     */
    public function LeftIopNeedsToBeCheckedNo()
    {
        /**
         * @var Intravitreal $Intravitreal
         */
        $Intravitreal  = $this->getPage('Intravitreal');
        $Intravitreal->leftIOPNeedsToBeCheckedNo();
    }

    /**
     * @Then /^I choose Left Post Injection Drops "([^"]*)"$/
     */
    public function iChooseLeftPostInjectionDrops($drops)
    {
        /**
         * @var Intravitreal $Intravitreal
         */
        $Intravitreal  = $this->getPage('Intravitreal');
        $Intravitreal->leftPostInjectionDrops($drops);
    }

    /**
     * @Given /^I select Right Complications "([^"]*)"$/
     */
    public function RightComplications($complication)
    {
        /**
         * @var Intravitreal $Intravitreal
         */
        $Intravitreal  = $this->getPage('Intravitreal');
        $Intravitreal->rightComplications($complication);
    }

    /**
     * @Given /^I select Left Complications "([^"]*)"$/
     */
    public function LeftComplications($complication)
    {
        /**
         * @var Intravitreal $Intravitreal
         */
        $Intravitreal  = $this->getPage('Intravitreal');
        $Intravitreal->leftComplications($complication);
    }

    /**
     * @Then /^I Save the Intravitreal injection$/
     */
    public function iSaveTheIntravitrealInjection()
    {
        /**
         * @var Intravitreal $Intravitreal
         */
        $Intravitreal  = $this->getPage('Intravitreal');
        $Intravitreal->saveIntravitrealInjection();
    }

    /**
     * @Then /^I Save the Intravitreal injection and confirm it has been created successfully$/
     */
    public function iSaveTheIntravitrealInjectionAndConfirm()
    {
        /**
         * @var Intravitreal $Intravitreal
         */
        $Intravitreal  = $this->getPage('Intravitreal');
        $Intravitreal->saveIntravitrealAndConfirm();
    }

    /**
     * @Then /^I Confirm that Intravitreal Mandatory fields validation error messages are displayed$/
     */
    public function iConfirmThatIntravitrealMandatoryFieldsValidationErrorMessagesAreDisplayed()
    {
        /**
         * @var Intravitreal $Intravitreal
         */
        $Intravitreal  = $this->getPage('Intravitreal');
        $Intravitreal->intravitrealMandatoryFieldsErrorValidation();
    }


}