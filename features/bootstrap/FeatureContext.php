<?php

use Behat\Behat\Context\ClosuredContextInterface,
	Behat\Behat\Context\TranslatedContextInterface,
	Behat\Behat\Context\BehatContext,
	Behat\Behat\Exception\PendingException;
use Behat\Gherkin\Node\PyStringNode,
	Behat\Gherkin\Node\TableNode;
use Behat\MinkExtension\Context\MinkContext;
use Symfony\Component\Finder\Finder;

use Behat\YiiExtension\Context\YiiAwareContextInterface;
use Behat\Mink\Driver\Selenium2Driver;
use \SensioLabs\Behat\PageObjectExtension\Context\PageObjectContext;

class FeatureContext extends PageObjectContext implements YiiAwareContextInterface, \Behat\MinkExtension\Context\MinkAwareInterface
{
	private $yii;
	private $screenshots;
	private $screenshotPath;

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
		//var_dump($this);
		//echo var_export($this->container->get('behat.console.command')->setFeaturesPaths(), true);
		//die("life is cool");
		$this->useContext('LoginContext', new LoginContext($parameters));
		$this->useContext('HomepageContext', new HomepageContext($parameters));
		$this->useContext('WaitingListContext', new WaitingListContext($parameters));
		$this->useContext('AddingNewEventContext', new AddingNewEventContext($parameters));
		$this->useContext('PatientViewContext', new PatientViewContext($parameters));
        $this->useContext('OperationNoteContext', new OperationNoteContext($parameters));
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

		$this->loadModuleContextsPages($parameters);

		$this->screenshots = array();
		$this->screenshotPath = realpath(join(DIRECTORY_SEPARATOR, array(__DIR__, '..', '..', 'features', 'screenshots')));
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
			$homepage = $this->getPage('HomePage');
			$homepage->open();
			$homepage->checkOpenEyesTitle('Please login');
		} else {
			throw new \Exception("Environment $environment doesn't exist");
		}
	}

	/**
	 * @And /^I Select Add a New Episode and Confirm$/
	 */
	public function addNewEpisode()
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
		try{
            $this->stackScreenshots($event);

		    if (4 === $event->getResult()) {
		    	$this->saveScreenshots();
		    }
        }catch(Exception $e){
        }
	}

	private function stackScreenshots($event)
	{
		try {
			$driver = $this->mink->getSession()->getDriver();

			if ($driver instanceof Behat\Mink\Driver\Selenium2Driver) {
				$step = $event->getStep();
				$path = array(
					'date' => date("Ymd-Hi"),
					'feature' => substr($step->getParent()->getFeature()->getTitle(), 0, 255),
					'scenario' => substr($step->getParent()->getTitle(), 0, 255),
					'step' => substr($step->getType() . ' ' . $step->getText(), 0, 255)
				);
				$path = preg_replace('/[^\-\.\w]/', '_', $path);
				$filename = $this->screenshotPath . DIRECTORY_SEPARATOR . implode('/', $path) . '.jpg';

				if (count($this->screenshots) >= 5) {
					$this->screenshots = array_slice($this->screenshots, 1);
				}
				$imgContent = $driver->getScreenshot();
				$this->screenshots[] = array('filename' => $filename, 'screenshotContent' => $imgContent);
			}
		} catch (Exception $e) {
			echo "Feature Context Exception " . get_class($e) . " \n\nFile: " . $e->getFile() . " \n\nMessage: " . $e->getMessage() .
				" \n\nLine: " . $e->getLine() . " \n\nCode: " . $e->getCode() . " \n\nTrace: " . $e->getTraceAsString();
		}
	}

	private function saveScreenshots()
	{
		foreach ($this->screenshots as $screenshot) {
			try{
				if (!@is_dir(dirname($screenshot['filename']))) {
					echo "\n\nCreating dir " . dirname($screenshot['filename']) . " \n";
					@mkdir(dirname($screenshot['filename']), 0775, TRUE);
				}
				$screenshotSaved = file_put_contents($screenshot['filename'], $screenshot['screenshotContent']);
				if($screenshotSaved === false){
					echo "\n\n ERROR saving SCREENSHOT : " . $screenshot['filename'] . " \n\n";
				}
			}
			catch(Exception $e){
				echo "Saving screenshots Exception " . get_class($e) . " \n\nFile: " . $e->getFile() . " \n\nMessage: " . $e->getMessage() .
						" \n\nLine: " . $e->getLine() . " \n\nCode: " . $e->getCode() . " \n\nTrace: " . $e->getTraceAsString();
			}
		}
		$this->screenshots = array();
	}

	/**
	 * clear up screenshot before new scenario is run
	 * @BeforeScenario
	 */

	public function clearScreenshots(){
		$this->screenshots = array();
	}

	/**
	 * @description custom loader of features contexts and pages from Yii modules
	 * @param $parameters
	 */
	private function loadModuleContextsPages($parameters){
		$modsPath = realpath(__DIR__.'/../../protected/modules');

		$moduleFeaturesPaths = array();

		foreach (Finder::create()->directories()->depth(0)->in($modsPath) as $path) {
			if (file_exists($path.'/features')) {
				$moduleFeaturesPaths[] = $path.'/features';
			}
		}

		foreach($moduleFeaturesPaths as $moduleFeaturesPath){
			foreach (Finder::create()->files()->name('*Context.php')->in($moduleFeaturesPath) as $contextFileName) {
				$contextName =  substr( $contextFileName->getBasename(), 0  , -4);
				require_once((string) $contextFileName);
				$this->useContext($contextName, new $contextName($parameters));
			}
			foreach( Finder::create()->files()->name('*.php')->in($moduleFeaturesPath . DIRECTORY_SEPARATOR . 'bootstrap' . DIRECTORY_SEPARATOR . 'Pages') as $pageObject){
				$pageName =  substr( $pageObject->getBasename(), 0  , -4);
				if(!class_exists($pageName) || false){
					require_once((string) $pageObject);
				}
				else{
				//	throw new Exception('Page object' . $pageName . ' was already defined somewhere. Duplicate path is ' . $pageObject);
					//$this->printDebug('Page object: ' . $pageName . ' was already defined somewhere. Duplicate path is ' . $pageObject);
				}
			}
		}
	}
}
