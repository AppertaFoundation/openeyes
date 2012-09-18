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
class HomePageContext extends Behat\MinkExtension\Context\RawMinkContext {
	
	/**
	 * @When /^I search for patient "([^"]*)"$/
	 */
	public function iSearchForPatient($patient) {
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
	 * NOTE: Copied as its protected... wont work with getMainContext()
	 * 
     * Locates url, based on provided path.
     * Override to provide custom routing mechanism.
     *
     * @param string $path
     *
     * @return string
     */
    protected function locatePath($path)
    {
        $startUrl = rtrim($this->getMinkParameter('base_url'), '/') . '/';

        return 0 !== strpos($path, 'http') ? $startUrl . ltrim($path, '/') : $path;
    }
}