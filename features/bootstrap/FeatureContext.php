<?php

use Behat\Behat\Context\ClosuredContextInterface,
	Behat\Behat\Context\TranslatedContextInterface,
	Behat\Behat\Context\BehatContext,
	Behat\Behat\Exception\PendingException;
use Behat\Gherkin\Node\PyStringNode,
	Behat\Gherkin\Node\TableNode;
use Behat\Mink\Exception\ResponseTextException;

//
// Require 3rd-party libraries here:
//
require_once 'PHPUnit/Autoload.php';
require_once 'PHPUnit/Framework/Assert/Functions.php';
require_once 'mink/autoload.php';

/**
 * Features context.
 */
class FeatureContext extends Behat\Mink\Behat\Context\MinkContext {

	/**
	 * Initializes context.
	 * Every scenario gets it's own context object.
	 *
	 * @param   array   $parameters     context parameters (set them up through behat.yml)
	 */
	public function __construct(array $parameters) {
		
	}

	/**
	 * @When /^I search for patient "([^"]*)"$/
	 */
	public function iSearchForPatient($argument1) {
		$parts = explode(':', $argument1);
		if (count($parts) == 2) {
			$this->getSession()->visit($this->locatePath('/'));
			$this->getSession()->getPage()->fillField('Patient[first_name]', $parts[0]);
			$this->getSession()->getPage()->fillField('Patient[last_name]', $parts[1]);
			$this->getSession()->getPage()->pressButton('findPatient_details');
		} else {
			throw new ResponseTextException("Argument passed incorrectly, expected 'firstname:surname'", $this->getSession());
		}
	}

	/**
	 * Check if you are logged in as a particular user, accepted values:
	 * 
	 * "username:password" - Will force logout every scenario
	 * "username:password:firstname:lastname" - Will check to see if already logged in
	 * 
	 * e.g. "admin:admin"
	 * e.g. "admin:admin:Enoch:Root"
	 * 
	 * @Given /^I am logged in as "([^"]*)"$/
	 * @When /^I log in as "([^"]*)"$/
	 */
	public function iAmLoggedInAs($argument1) {
		$parts = explode(":", $argument1);
		if (is_array($parts) && count($parts) > 1) {

			$this->getSession()->visit($this->locatePath('/'));
			$actual = $this->getSession()->getPage()->getText();
			$login = TRUE;

			if (count($parts) == 2) {
				$expected = str_replace('\\"', '"', "Logout");

				if (strstr($actual, $expected)) {
					// Need to logout first as we can't determine what user we are
					$this->getSession()->getPage()->clickLink('Logout');
				}
			} elseif (count($parts) == 4) {
				// e.g. Hi Enoch Root
				$expected = str_replace('\\"', '"', "Hi $parts[2] $parts[3]");

				if (strstr($actual, $expected)) {
					// Already logged in
					$login = FALSE;
				}
			} else {
				throw new ResponseTextException("Argument passed incorrectly, expected 'username:password[:firstname:lastname]'", $this->getSession());
			}

			if ($login) {
				$this->getSession()->getPage()->fillField('LoginForm[username]', $parts[0]);
				$this->getSession()->getPage()->fillField('LoginForm[password]', $parts[1]);
				$this->getSession()->getPage()->selectFieldOption('LoginForm[siteId]', 'City Road');
				$this->getSession()->getPage()->pressButton('Login');
			}
		} else {
			throw new ResponseTextException("Argument passed incorrectly, expected 'username:password[:firstname:lastname]'", $this->getSession());
		}
	}

	/**
	 * @Given /^I am logged out$/
	 */
	public function iAmLoggedOut() {
		$this->getSession()->visit($this->locatePath('/'));

		$expected = str_replace('\\"', '"', "Logout");
		$actual = $this->getSession()->getPage()->getText();

		try {
			assertNotContains($expected, $actual);
		} catch (AssertException $e) {
			// Should be logged in
			$this->getSession()->getPage()->clickLink($expected);
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
			throw new ResponseTextException("Argument passed incorrectly, expected 'firstname:lastname' OR 'id'", $this->getSession());
		}

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
		$this->iSelectFirm($firm);
	}

	/**
	 * @When /^I select the "([^"]*)" radio button$/
	 */
	public function iSelectTheRadioButton($radio_label) {
		$radio_button = $this->getSession()->getPage()->findField($radio_label);
		if (null === $radio_button) {
			throw new ElementNotFoundException(
					$this->getSession(), 'form field', 'id|name|label|value', $field
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
        $this->getSession()->getPage()->selectFieldOption('selected_firm_id', $firm);
        $this->getSession()->wait(50, '$("#selected_firm_id").trigger("change")');
    }

}
