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

class FeatureContext extends PageObjectContext implements YiiAwareContextInterface
{
    private    $yii;

    protected $environment = array(
        'master' => 'http://admin:openeyesdevel@master.test.openeyes.org.uk',
        'develop' => 'http://admin:openeyesdevel@develop.test.openeyes.org.uk'
    );

    public function setYiiWebApplication(\CWebApplication $yii)
    {
        $this->yii = $yii;
    }

    public function __construct(array $parameters)
    {
        $this->useContext('LoginContext', new LoginContext($parameters));
        $this->useContext('HomepageContext', new HomepageContext($parameters));
        $this->useContext('WaitingListContext', new WaitingListContext($parameters));
        $this->useContext('AddingNewEventContext', new AddingNewEventContext($parameters));
        $this->useContext('PatientViewContext', new PatientViewContext($parameters));
        $this->useContext('OperationBookingContext', new OperationBookingContext($parameters));
        $this->useContext('AnaestheticAuditContext', new AnaestheticAuditContext($parameters));
        $this->useContext('ExaminationContext', new ExaminationContext($parameters));
        $this->useContext('LaserContext', new LaserContext($parameters));
        $this->useContext('PrescriptionContext', new PrescriptionContext($parameters));
        $this->useContext('PhasingContext', new PhasingContext($parameters));
        $this->useContext('CorrespondenceContext', new CorrespondenceContext($parameters));
        $this->useContext('IntravitrealContext', new IntravitrealContext($parameters));

    }

    /**
     * @Given /^I am on the OpenEyes "([^"]*)" homepage$/
     */
    public function iAmOnTheOpeneyesHomepage($environment)
    {
        /**
         * @var Login $loginPage
         */
        if (isset($this->environment[$environment])) {
            $this->getPage('HomePage')->open();
            ;

        } else {
            throw new \Exception("Environment $environment doesn't exist");
        }
    }

    /**
     * @And /^I Select Add a New Episode and Confirm$/
     */
    public function addNewEpisode ()
    {
        /**
         * @var AddingNewEvent $addNewEvent
         */
        $addNewEvent = $this->getPage('AddingNewEvent');
        $addNewEvent->addNewEpisode();
    }






//
//    /**
//     * @Then /^I select Add First New Episode and Confirm$/
//     */
//    public function iSelectAddFirstNewEpisodeAndConfirm()
//    {
//
//    }
//
//    /**
//     * @Then /^I add Right Side$/
//     */
//    public function iAddRightSide()
//    {
//
//    }
//
//    /**
//     * @Given /^I select a Right Side Diagnosis of "([^"]*)"$/
//     */
//    public function iSelectARightSideDiagnosisOf($arg1)
//    {
//
//    }
//
//    /**
//     * @Given /^I select a Left Side Diagnosis of "([^"]*)"$/
//     */
//    public function iSelectALeftSideDiagnosisOf($arg1)
//    {
//
//    }
//
//    /**
//     * @Then /^I select a Right Secondary To of "([^"]*)"$/
//     */
//    public function iSelectARightSecondaryToOf($arg1)
//    {
//
//    }
//
//    /**
//     * @Then /^I select a Left Secondary To of "([^"]*)"$/
//     */
//    public function iSelectALeftSecondaryToOf($arg1)
//    {
//
//    }
//
//    /**
//     * @Then /^I select Cerebrovascular accident Yes$/
//     */
//    public function iSelectCerebrovascularAccidentYes()
//    {
//
//    }
//
//    /**
//     * @Then /^I select Cerebrovascular accident No$/
//     */
//    public function iSelectCerebrovascularAccidentNo()
//    {
//
//    }
//
//    /**
//     * @Then /^I select Ischaemic attack Yes$/
//     */
//    public function iSelectIschaemicAttackYes()
//    {
//
//    }
//
//    /**
//     * @Then /^I select Ischaemic attack No$/
//     */
//    public function iSelectIschaemicAttackNo()
//    {
//
//    }
//
//    /**
//     * @Then /^I select Myocardial infarction Yes$/
//     */
//    public function iSelectMyocardialInfarctionYes()
//    {
//
//    }
//
//    /**
//     * @Then /^I select Myocardial infarction No$/
//     */
//    public function iSelectMyocardialInfarctionNo()
//    {
//
//    }
//
//    /**
//     * @Given /^I select a Consultant of "([^"]*)"$/
//     */
//    public function iSelectAConsultantOf($arg1)
//    {
//
//    }
//

//
//    /**
//     * @Then /^I search for patient name last name "([^"]*)" and first name "([^"]*)"$/
//     */
//    public function iSearchForPatientNameLastNameAndFirstName($arg1, $arg2)
//    {
//        throw new PendingException();
//    }
//
//    /**
//     * @Given /^I Add a New Episode and Confirm$/
//     */
//    public function iAddANewEpisodeAndConfirm()
//    {
//        throw new PendingException();
//    }
//

//    /**
//     * @Given /^I select the No option for Read to Discharge$/
//     */
//    public function iSelectTheNoOptionForReadToDischarge2()
//    {
//        throw new PendingException();
//    }
//
//    /**
//     * @Then /^I choose Left Post Injection Drops$/
//     */
//    public function iChooseLeftPostInjectionDrops()
//    {
//        throw new PendingException();
//    }
//
//    /**
//     * @Given /^I select an existing "([^"]*)" Episode$/
//     */
//    public function iSelectAnExistingEpisode($arg1)
//    {
//        throw new PendingException();
//    }


}
