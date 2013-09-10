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

class ConsentFormContext extends PageObjectContext
{
    public function __construct(array $parameters)
    {

    }


    /**
     * @Then /^I select Add Consent Form$/
     */
    public function iSelectAddConsentForm()
    {
        /**
         * @var ConsentForm $consentForm
         */
        $consentForm = $this->getPage('ConsentForm');
        $consentForm->createConsentForm();
    }

}
