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

class FeatureContext extends PageObjectContext implements YiiAwareContextInterface, \Behat\MinkExtension\Context\MinkAwareInterface
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
        $this->useContext('TherapyApplication', new TherapyApplicationContext($parameters));
        $this->useContext('ConsentForm', new ConsentFormContext($parameters));
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

	public function setMink(\Behat\Mink\Mink $mink)
	{
		$this->mink = $mink;
	}

	public function setMinkParameters(array $parameters)
	{
		$this->minkParameters = $parameters;
	}

	/**
	 * Take screenshot when step fails.
	 * Works only with Selenium2Driver.
	 * based on https://gist.github.com/t3node/5430470
	 * and https://gist.github.com/michalochman/3175175
	 * implementing the MinkAwareInterface and placing its contexts in $this->mink
	 *
	 * @AfterStep
	 */
	public function takeScreenshotAfterFailedStep($event)
	{
		if (4 === $event->getResult()) {
			$driver = $this->mink->getSession()->getDriver();
			if ($driver instanceof Behat\Mink\Driver\Selenium2Driver) {
				$step = $event->getStep();
				$path = array(
					'date' => date("Ymd-Hi"),
					'feature' => $step->getParent()->getFeature()->getTitle(),
					'scenario' => $step->getParent()->getTitle(),
					'step' => $step->getType() . ' ' . $step->getText()
				);
				$path = preg_replace('/[^\-\.\w]/', '_', $path);
				$filename = '/tmp/behat/' .  implode('/', $path) . '.jpg';

				// Create directories if needed
				if (!@is_dir(dirname($filename))) {
					@mkdir(dirname($filename), 0775, TRUE);
				}

				file_put_contents($filename, $driver->getScreenshot());
			}
		}
	}
}
