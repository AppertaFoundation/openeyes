<?php
/**
 * OpenEyes.
 *
 * (C) OpenEyes Foundation, 2019
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
use \SensioLabs\Behat\PageObjectExtension\Context\PageObjectContext;

class OphCoMessagingContext extends PageObjectContext
{
    protected $bookmarks = array();

    /**
     * @Given /^I select Create Message$/
     */
    public function iSelectCreateMessage()
    {
        $message = $this->getPage('OphCoMessaging');
        $message->createMessage();
    }

    /**
     * @Then /^the application returns a validation error containing \'([^\']*)\'$/
     */
    public function applicationReturnsAnError($message)
    {
        /*
         * @var OphCoMessaging
         */
        $page = $this->getPage('OphCoMessaging');
        if (!$page->isValidationMessagePresent($message)) {
            throw new \Behat\Behat\Exception\BehaviorException("Validation error '{$message}' not found");
        };
    }

    /**
     * @Given /^I add a New Event "([^"]*)" for "([^"]*)"$/
     */
    public function iAddANewEventFor($event_name, $subspecialty)
    {
        /*
         * @var OphCoMessaging
         */
        $message = $this->getPage('OphCoMessaging');
        $message->addNewEvent($subspecialty, $event_name);
    }

    /**
     * @Then /^I type "([^"]*)" into the for the attention of field$/
     */
    public function iTypeIntoTheForTheAttentionOfField($arg1)
    {
        $page = $this->getPage('OphCoMessaging');
        $page->typeIntoFAOSearch($arg1);
    }

    /**
     * @Then /^"([^"]*)" is displayed as the selected user$/
     */
    public function isDisplayedAsTheSelectedUser($username)
    {
        $page = $this->getPage('OphCoMessaging');
        $page->selectedUserIs($username);
    }

    /**
     * @Then /^I select "([^"]*)" for the type of message$/
     */
    public function iSelectForTheTypeOfMessage($arg1)
    {
        $page = $this->getPage('OphCoMessaging');
        $page->selectMessageType($arg1);
    }

    /**
     * @Given /^I type "([^"]*)" into the message box$/
     */
    public function iTypeIntoTheMessageBox($arg1)
    {
        $page = $this->getPage('OphCoMessaging');
        $page->enterMessage($arg1);
    }

    /**
     * index is 1-based, not zero based.
     *
     * @Given /^I select option "([^"]*)" from the autocomplete list$/
     */
    public function iSelectOptionFromTheAutocompleteList($index)
    {
        $message = $this->getPage('OphCoMessaging');
        $message->selectAutoCompleteOptionByIndex($index - 1);
    }

    /**
     * @Then /^I Save the Message and confirm it has been created successfully$/
     */
    public function iSaveTheMessageAndConfirmItHasBeenCreatedSuccessfully()
    {
        /*
         * @var OphCoMessaging
         */
        $message = $this->getPage('OphCoMessaging');
        $message->saveAndConfirm();
    }

    /**
     * @Given /^I confirm that fao user is "([^"]*)"$/
     */
    public function iConfirmThatFaoUserIs($arg1)
    {
        /*
         * @var OphCoMessaging
         */
        $message = $this->getPage('OphCoMessaging');
        $message->checkDisplayFaoIs($arg1);
    }

    /**
     * @Given /^I confirm the message type is "([^"]*)"$/
     */
    public function iConfirmTheMessageTypeIs($arg1)
    {
        /*
         * @var OphCoMessaging
         */
        $message = $this->getPage('OphCoMessaging');
        $message->checkDisplayTypeIs($arg1);
    }

    /**
     * @Given /^I confirm that the message text is "([^"]*)"$/
     */
    public function iConfirmThatTheMessageTextIs($arg1)
    {
        /*
         * @var OphCoMessaging
         */
        $message = $this->getPage('OphCoMessaging');
        $message->checkDisplayMessageIs($arg1);
    }

    /**
     * @Then /^I Edit the displayed event$/
     */
    public function iEditTheDisplayedEvent()
    {
        $message = $this->getPage('OphCoMessaging');
        $message->clickEditLink();
    }

    /**
     * @Given /^I confirm I cannot change the FAO user$/
     */
    public function iConfirmICannotChangeTheFAOUser()
    {
        $message = $this->getPage('OphCoMessaging');
        $message->checkNoUserSearchAvailable();
    }

    /**
     * @Then /^I Save the Message$/
     */
    public function iSaveTheMessage()
    {
        /*
         * @var OphCoMessaging
         */
        $message = $this->getPage('OphCoMessaging');
        $message->saveEvent();
    }

    /**
     * @Given /^I bookmark the current page as "([^"]*)"$/
     */
    public function iBookmarkTheCurrentPageAs($arg1)
    {
        $this->bookmarks[$arg1] = $this->getPage('OphCoMessaging')->getCurrentUrl();
    }

    /**
     * @Then /^I logout$/
     */
    public function iLogout()
    {
        /*
         * @var OphCoMessaging
         */
        $message = $this->getPage('OphCoMessaging');
        $message->logout();
    }

    /**
     * @Given /^I see I have messages in the messages dashboard$/
     */
    public function iSeeIHaveMessagesInTheMessagesDashboard()
    {
        /*
         * @var OphCoMessaging
         */
        $message = $this->getPage('OphCoMessaging');
        $message->checkHaveMessagesInDashboard();
    }

    /**
     * @Given /^there's a row for the bookmark "([^"]*)"$/
     */
    public function thereSARowForTheBookmark($arg1)
    {
        /*
         * @var OphCoMessaging
         */
        $url = $this->bookmarks[$arg1];
        $message = $this->getPage('OphCoMessaging');
        $message->checkForLinkToUrl($url);
    }

    /**
     * @Then /^I select the Latest Event Page$/
     */
    public function iSelectTheLatestEvent()
    {
        /*
         * @var OphCoMessaging
         */
        $message = $this->getPage('OphCoMessaging');
        $message->selectLatestEvent();
    }
}
