<?php

use Behat\Behat\Context\ClosuredContextInterface,
	Behat\Behat\Context\TranslatedContextInterface,
	Behat\Behat\Context\BehatContext,
	Behat\Behat\Exception\PendingException,
	Behat\Behat\Context\Step;
use Behat\Gherkin\Node\PyStringNode,
	Behat\Gherkin\Node\TableNode;
use Behat\Mink\Exception\ResponseTextException;

//
// Require 3rd-party libraries here:
//
require_once 'PHPUnit/Autoload.php';
require_once 'PHPUnit/Framework/Assert/Functions.php';
//require_once 'mink/vendor/autoload.php';

/**
 * Features context.
 */
class FeatureContext extends Behat\MinkExtension\Context\MinkContext {

	/**
	 * Initializes context.
	 * Every scenario gets it's own context object.
	 *
	 * @param	array	$parameters		context parameters (set them up through behat.yml)
	 */
	public function __construct(array $parameters) {
		$this->useContext('home_page_context', new HomePageContext());
	}

	/**
	 * BeforeSuite
	 */
	static function setupBeforeSuite(){
		$modules_dir = __DIR__."/../../protected/modules/";
		if(file_exists($modules_dir)){
			$modules = scandir($modules_dir);
			$sample_dir = '';
			foreach($modules as $module){
				if(preg_match("/sample/i", $module)){
					$sample_dir = $module;
					break;
				}
			}
			if(!$sample_dir){
				throw new Exception('Sample data module not installed. Please install to OPENEYES_ROOTDIR/protected/modules/. https://github.com/openeyes/Sample');
			}
			$dumps = new DirectoryIterator($modules_dir.$sample_dir.'/dump/');
			$timestamp = null;
			foreach($dumps as $dump){
				if(!$dump->isDot()){
					if($dump->getMTime() > $timestamp){
						$dump_file = $dump->getFileName();
						$timestamp = $dump->getMTime();
					}
				}
			}
			if(!$dump_file){
				throw new Exception('No dump files found in "'.$module_path.$sample_dir.'/dump/". Expecting sql dump of sample data.');
			}
			// Getting DB settings
			if(!file_exists(__DIR__.'/../../protected/config/local/common.php')){
				throw new Exception('Unable to detect OpenEyes config local file "../../protected/config/local/common.php". Can\'t import sample data');
			}
			$config = include(__DIR__.'/../../protected/config/local/common.php');
			if(!is_array($config)){
				throw new Exception('Unable to read config file correctly.');
			}

			var_dump($dump_file);
			var_dump($config);
			die();
		}else{
			throw new Exception('Unable to detect OpenEyes install, assuming "protected" folder is "../../protected/" from "'.__FILE__.'"');
		}
	}

	/**
	 * @Given /^patient "([^"]*)" exists$/
	 */
	public function patientExists($patient) {
		if (is_numeric($patient)) {
			$field = str_replace('\\"', '"', "Patient[hos_num]");
			$value = str_replace('\\"', '"', (int) $patient);
			$this->getSession()->getPage()->fillField($field, $value);

			$button = str_replace('\\"', '"', "findPatient_details");
			$this->getSession()->getPage()->pressButton($button);
		} elseif (strstr($patient, ":")) {
			$name = explode(":", $patient);
			$this->getSession()->visit($this->locatePath('/'));

			$field = str_replace('\\"', '"', "Patient[first_name]");
			$value = str_replace('\\"', '"', $name[0]);
			$this->getSession()->getPage()->fillField($field, $value);

			$field = str_replace('\\"', '"', "Patient[last_name]");
			$value = str_replace('\\"', '"', $name[1]);
			$this->getSession()->getPage()->fillField($field, $value);

			$button = str_replace('\\"', '"', "findPatient_details");
			$this->getSession()->getPage()->pressButton($button);
		} else {
			throw new ResponseTextException("Argument passed incorrectly, expected 'firstname:lastname' OR 'hos_num'", $this->getSession());
		}
		$this->iWaitSeconds("0.5");
		$actual = parse_url($this->getSession()->getCurrentUrl(), PHP_URL_PATH);

		try {
			assertRegExp("/\/patient\/view\/*/", $actual);
		} catch (Exception $e) {
			throw new ResponseTextException("Patient ($patient) does not exist.", $this->getSession());
		}
	}

	/**
	 * @Then /^I should see an "([^"]*)" element on screen$/
	 */
	public function iShouldSeeAnElementOnScreen($element) {
		$node = $this->getSession()->getPage()->find('css', $element);

		if (null === $node) {
			throw new ElementNotFoundException(
					$this->getSession(), 'element', 'css', $element
			);
		}

		if (!$node->isVisible()) {
			throw new ResponseTextException("'$element' is not visible on page.", $this->getSession());
		}
	}

	/**
	 * @Then /^I should not see an "([^"]*)" element on screen$/
	 */
	public function iShouldNotSeeAnElementOnScreen($element) {
		$node = $this->getSession()->getPage()->find('css', $element);

		if (null === $node) {
			throw new ElementNotFoundException(
					$this->getSession(), 'element', 'css', $element
			);
		}

		if ($node->isVisible()) {
			throw new ResponseTextException("'$element' is visible on page.", $this->getSession());
		}
	}

	/**
	 * @Given /^firm "([^"]*)" is selected$/
	 */
	public function firmIsSelected($firm) {
		$firm = str_replace('\\"', '"', $firm);

		$el = $this->getSession()->getPage()->find('css', "#selected_firm_id");
		if(!$el){
			throw new exception('Dropdown "'.$field.'" not found on page');
		}
		$selectedValue = $el->getValue();
		$selectedLabel = $el->getSelectedText();
		if ($selectedValue != $firm && $selectedLabel != $firm) {
			$this->iSelectFirm($firm);
		}
	}

	/**
	 * @When /^I select the "([^"]*)" radio button$/
	 */
	public function iSelectTheRadioButton($radio_label) {
		$radio_button = $this->getSession()->getPage()->findField($radio_label);
		if (null === $radio_button) {
			throw new Exception(
					"Radio button '$radio_label' not found"
			);
		}
		$value = $radio_button->getAttribute('value');
		$this->fillField($radio_label, $value);
	}

	/**
	 * @Then /^the "([^"]*)" radio should be checked/
	 */
	public function theRadioShouldBeSelected($radio_label) {
		$radio_button = $this->getSession()->getPage()->findField($radio_label);
		if (null === $radio_button) {
			throw new ElementNotFoundException(
					$this->getSession(), 'form field', 'id|name|label|value', $field
			);
		}
		if(! $radio_button->hasAttribute('checked')){
			throw new ResponseTextException("'$radio_label' has no attribute 'checked'", $this->getSession());
		}

	}

	/**
	* @Then /^I should see "([^"]*)" in the "([^"]*)" dropdown$/
	*/
	public function iShouldSeeInTheDropdown($value, $field){
		$el = $this->getSession()->getPage()->find('css', $field);
		if(!$el){
			throw new exception('Dropdown "'.$field.'" not found on page');
		}
		$selectedValue = $el->getValue();
		$selectedLabel = $el->getSelectedText();
		if ($selectedValue != $value && $selectedLabel != $value) {
			throw new exception('Value/Label "'.$value.'" not selected in "'.$field.'" dropdown');
		}
	}

	/**
	* @When /^I wait "([^"]*)" second/
	*/
	public function iWaitSeconds($value){
		$value = floatval($value);
		$this->getMink()->getSession()->wait($value * 1000);
	}

	/**
	 * @When /^I select "([^"]*)" firm$/
	 */
	public function iSelectFirm($firm)
	{
		$this->getSession()->wait(50, 'redirectHomeAfterChangingFirm = false;');
		$this->getSession()->getPage()->selectFieldOption('selected_firm_id', $firm);
		//$this->getSession()->wait(50, '$("#selected_firm_id").trigger("change")');
	}

	/**
	 * @Given /^patient "([^"]*)" has a scheduled operation booking$/
	 */
	public function patientHasAScheduledOperationBooking($patient)
	{
		$this->firmIsSelected('Aylward Bill (Vitreoretinal)');
		$this->patientExists($patient);
		$this->getSession()->getPage()->clickLink('Create or View Episodes and Events');
		// Ugly! Hopefully will get changed with the new frontend
		$script = <<<JS
links = [];
jQuery('#episodes_sidebar .events li').each(function(){
	if(jQuery(this).find('.quicklook .event').text() === "Operation"){
		links.push(jQuery(this).children('a').attr('href'));
	}
});

return links;
JS;
		$operations = $this->getSession()->evaluateScript($script);

		if(!empty($operations)){
			$booked = FALSE;
			foreach($operations as $link){
				$this->getSession()->visit($this->locatePath($link));
				$node = $this->getSession()->getPage()->find('css', '#event_content h3');
				if(strstr($node->getText(), 'Operation (Scheduled)')){
					$booked = TRUE;
					break;
				}
			}

			if($booked){
				return;
			}
		}

		$this->getSession()->getPage()->pressButton('addNewEvent');
		$this->getSession()->getPage()->clickLink('Operation');

		$this->iWaitSeconds("5");

		$this->getSession()->getPage()->selectFieldOption('ElementDiagnosis_disorder_id', 'Myopia');
		$this->getSession()->wait(50, '$("#ElementDiagnosis_disorder_id").trigger("change")');

		$this->getSession()->getPage()->selectFieldOption('select_procedure_id', 'Removal of IOL');
		$this->getSession()->wait(50, '$("#select_procedure_id").trigger("click").trigger("change").trigger("select")');

		// Normally defaulted, but bug in OE-Dev
		$this->checkOption('ElementOperation_anaesthetic_type_id_1');
		$this->checkOption('ElementOperation_priority_id_1');
		$this->iWaitSeconds("1");

		$this->getSession()->wait(50, '$("#scheduleNow").trigger("click")');
		$this->iWaitSeconds("2");

		$availableSlots = $this->getSession()->getPage()->findAll('css', 'td.available');
		if(!$availableSlots || count($availableSlots) < 1){
			do{
				$this->getSession()->getPage()->pressButton('next_month');
				$this->iWaitSeconds("2");
				$availableSlots = $this->getSession()->getPage()->findAll('css', 'td.available');
			}while (count($availableSlots) < 1);
		}
		$slot = $availableSlots[0];
		$slot->click();
		$this->iWaitSeconds("2");

		$timeBlocks = $this->getSession()->getPage()->findAll('css', 'div.timeBlock');
		if(!$timeBlocks || count($timeBlocks) < 1){
			throw new exception('not implemented looping through timeblocks yet');
		}

		$block = $timeBlocks[0];
		$block->click();
		$this->iWaitSeconds("1");
		$this->getSession()->getPage()->pressButton('confirm_slot');
		$this->iWaitSeconds("3");
	}

	/**
	 * @Given /^patient "([^"]*)" has an episode$/
	 */
	public function patientHasAnEpisode($patient){
		new Step\Given("patient '$patient' exists"); // Leave us on the patient summary page

		// .all-episodes = table row for 'All Episodes'
		$rows = $this->getSession()->getPage()->findAll('css', '.all-episodes');
		if(!$rows || count($rows) < 1){
			throw new ElementNotFoundException($this->getSession(), 'element: .all-episodes ');
		}
	}


	/**
	 * @When /^I press the "([^"]*)" key "([^"]*)" times on the "([^"]*)" element/
	 */
	public function iPressTheKeyTimesOnTheElement($key, $times, $element)
	{
		$el = $this->getSession()->getPage()->findField($element);
		if(!$el){
			throw new exception('Element "'.$element.'" not found on page');
		}
		$el->focus();

		for($i = 0; $i <= $times; $i++){
			$el->keyPress($key);
		}
	}
}
