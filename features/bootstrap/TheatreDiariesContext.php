<?php
use Behat\Behat\Context\ClosuredContextInterface, Behat\Behat\Context\TranslatedContextInterface, Behat\Behat\Context\BehatContext, Behat\Behat\Exception\PendingException;
use Behat\Gherkin\Node\PyStringNode, Behat\Gherkin\Node\TableNode;
use Behat\MinkExtension\Context\MinkContext;

use Behat\Mink\Driver\Selenium2Driver;
use \SensioLabs\Behat\PageObjectExtension\Context\PageObjectContext;
use WebDriver\WebDriver;
class TheatreDiariesContext extends PageObjectContext
{
    public function __construct(array $parameters) {
    }
    /**
     * @Given /^I search with start date as "([^"]*)" and end date as "([^"]*)"$/
     */
    public function iSearchWithStartDateAsAndEndDateAs($start, $end)
    {
        /**
         *
         * @var TheatreDiaries $thDiaries
         */
        $thDiaries = $this->getPage ( 'TheatreDiaries' );
        $thDiaries->searchWith ( $start, $end );
    }

    /**
     * @Then /^I should not see Invalid date note$/
     */
    public function iShouldNotSeeInvalidDateNote()
    {
        /**
         *
         * @var TheatreDiaries $errorMsg
         */
        $errorMsg = $this->getPage ( 'TheatreDiaries' );
        $errorMsg->searchAndConfirmNoError ();
    }

}