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
use Behat\Behat\Exception\BehaviorException;

class OphCoMessaging extends OpenEyesPage
{
    protected $savedSuccessXpath = "//*[@id='flash-success']";
    protected $bookmarks = array();

    protected $elements = array(
        'validationErrors' => array(
            'xpath' => "//div[contains(@class, 'alert-box') and contains(@class, 'error')]",
        ),
        'sidebar' => array(
            'xpath' => "//div[contains(@class, 'panel') and contains(@class, 'specialty')]",
        ),
        'newEventButton' => array(
            'xpath' => "//button[contains(@class, 'addEvent') and contains(@class, 'enabled')]",
        ),
        'newEventDialog' => array(
            'xpath' => "//*[@id='add-new-event-dialog']",
        ),
        'fao_search' => array(
            'xpath' => "//input[@id='find-user']",
        ),
        'selected_user_display' => array(
            'xpath' => "//*[@id='fao_user_display']",
        ),
        'message_type' => array(
            'xpath' => "//*[@id='OEModule_OphCoMessaging_models_Element_OphCoMessaging_Message_message_type_id']",
        ),
        'message_text' => array(
            'xpath' => "//*[@id='OEModule_OphCoMessaging_models_Element_OphCoMessaging_Message_message_text']",
        ),
        'save' => array(
            'xpath' => "//*[@id='et_save']",
        ),
        'edit' => array(
            'xpath' => "//ul[contains(@class, 'event-actions')]//a[text()='Edit']",
        ),
        'fao_display' => array(
            'xpath' => "//section[contains(@class,'Element_OphCoMessaging_Message')]//div[@class='row data-row'][1]//div[contains(@class,'data-value')]",
        ),
        'message_type_display' => array(
            'xpath' => "//section[contains(@class,'Element_OphCoMessaging_Message')]//div[@class='row data-row'][2]//div[contains(@class,'data-value')]",
        ),
        'message_text_display' => array(
            'xpath' => "//section[contains(@class,'Element_OphCoMessaging_Message')]//div[@class='row data-row'][3]//div[contains(@class,'data-value')]",
        ),
        'logout' => array(
            'xpath' => "//ul[contains(@class,'navigation')]//a[text()='Logout']",
        ),
        'dashboard' => array(
            'xpath' => "//div[@id='inbox-table']",
        ),
        'latestEvent' => array(
            'xpath' => "//*[@class='box patient-info episode-links']/a",
        ),
    );

    /**
     * This might be hideously brittle, but it will get the job done for now.
     * Is here to support bookmarking.
     *
     * @return mixed
     */
    public function getBaseUrl()
    {
        return $this->getDriver()->getMinkParameter('base_url');
    }

    protected function assertEquals($expected, $check, $message = 'Values do not match')
    {
        if ($expected != $check) {
            throw new BehaviorException("{$check} is not equal to {$expected}. {$message}");
        }
    }

    public function isValidationMessagePresent($message)
    {
        if ($validation = $this->getElement('validationErrors')) {
            return $validation->has('xpath', "//*[contains(text(),'{$message}')]");
        }
    }

    /**
     * @TODO: update PatientContext in core with this method
     */
    public function selectLatestEvent()
    {
        $this->getElement('latestEvent')->click();
        $this->waitForTitle('Episodes and events');
    }

    /**
     * more pragramatic approach to expanding sidebar, which should be in core.
     *
     * @TODO: put in core
     *
     * @param $subspecialty
     */
    public function expandSubspecialty($subspecialty)
    {
        $el = $this->getElement('sidebar')->find('xpath', "//a[contains(text(),'{$subspecialty}')]");
        if (!$el->hasClass('selected')) {
            $el->click();
        }
    }

    protected function clickNewEventButton()
    {
        $element_name = 'newEventButton';

        $element = $this->getElement($element_name);
        if (!$element->isVisible()) {
            $section_expander_xpath = $this->elements[$element_name]['xpath'].'/ancestor::section//span[contains(@class, \'icon-showhide\')]';
            $this->getElement('sidebar')->find('xpath', $section_expander_xpath)->click();
        }

        $element->click();
    }

    /**
     * Create a new event of the given name.
     *
     * @TODO: put in core
     *
     * @param $subspecialty
     * @param string $event_name
     *
     * @throws BehaviorException
     */
    public function addNewEvent($subspecialty, $event_name = 'Message')
    {
        $this->expandSubspecialty($subspecialty);
        $this->getDriver()->wait(5000, 'window.$ && $.active ==0');
        $this->clickNewEventButton();
        $this->getDriver()->wait(5000, 'window.$ && $.active ==0');
        if ($new_event_link = $this->getElement('newEventDialog')->find('xpath', "//*[contains(text(), '{$event_name}')]")) {
            $new_event_link->click();
        } else {
            throw new BehaviorException("new event link for {$event_name} not found.");
        }
    }

    /**
     * Search for a user in the FAO field.
     *
     * @param $search_term
     */
    public function typeIntoFAOSearch($search_term)
    {
        $field = $this->getElement('fao_search');
        $field->focus();
        $field->setValue($search_term);
        $field->keyDown(40);
    }

    /**
     * Crude selection of the autocomplete results (searching by text value is awkward because of span
     * highlighting for the match).
     *
     * @TODO: improve autocomplete results so can select by attribute of the term?
     *
     * @param $index
     */
    public function selectAutoCompleteOptionByIndex($index)
    {
        $this->getDriver()->wait(5000, 'window.$ && $.active ==0');
        $auto_results = $this->findAll('xpath', "//ul[contains(@class,'ui-autocomplete')]//li");
        $auto_results[$index]->click();
    }

    /**
     * @param $username
     *
     * @throws BehaviorException
     */
    public function selectedUserIs($username)
    {
        $this->assertEquals($username, $this->getElement('selected_user_display')->getText());
    }

    /**
     * @param $type
     *
     * @throws \Behat\Mink\Exception\ElementNotFoundException
     */
    public function selectMessageType($type)
    {
        $this->getElement('message_type')->selectOption($type);
    }

    public function enterMessage($message)
    {
        $this->getElement('message_text')->setValue($message);
    }

    /**
     * @TODO: move to core
     *
     * @return bool
     */
    protected function hasEventSaved()
    {
        return (bool) $this->find('xpath', $this->savedSuccessXpath);
    }

    /**
     * This should be the same behaviour for every OE page.
     *
     * @TODO: move to core
     */
    public function saveEvent()
    {
        $this->getElement('save')->click();
    }

    /**
     * @TODO: move into core as basic behaviour for all events
     *
     * @throws BehaviorException
     */
    public function saveAndConfirm()
    {
        // this resizing should be abstracted/configurable. Think it applies when running headless primarily
        $this->getDriver()->resizeWindow(1280, 800);
        $this->saveEvent();
        if (!$this->hasEventSaved()) {
            throw new BehaviorException('Event not saved');
        }
    }

    /**
     * @param $fao
     *
     * @throws BehaviorException
     */
    public function checkDisplayFaoIs($fao)
    {
        $this->assertEquals($fao, $this->getElement('fao_display')->getText());
    }

    /**
     * @param $type
     *
     * @throws BehaviorException
     */
    public function checkDisplayTypeIs($type)
    {
        $this->assertEquals($type, $this->getElement('message_type_display')->getText());
    }

    /**
     * @param $message
     *
     * @throws BehaviorException
     */
    public function checkDisplayMessageIs($message)
    {
        $this->assertEquals($message, $this->getElement('message_text_display')->getText());
    }

    public function clickEditLink()
    {
        $this->getElement('edit')->click();
    }

    public function checkNoUserSearchAvailable()
    {
        if ($this->getElement('fao_search')->isValid()) {
            throw new BehaviorException('FAO search still visible');
        }
    }

    public function storeBookmark($name)
    {
        $current_url = $this->getDriver()->getCurrentUrl();
        $this->bookmarks[$name] = str_replace($this->getMinkParameter('base_url'), '', $current_url);
    }

    /**
     * @TODO: move to core
     */
    public function logout()
    {
        $this->getElement('logout')->click();
    }

    /**
     * @param bool $remove_base
     *
     * @return mixed|string
     */
    public function getCurrentUrl($remove_base = true)
    {
        return $remove_base ?
            str_replace($this->getParameter('base_url'), '', $this->getDriver()->getCurrentUrl())
            : $this->getDriver()->getCurrentUrl();
    }

    /**
     * @param $url
     *
     * @return \Behat\Mink\Element\NodeElement|mixed|null
     */
    public function getLinkElementForUrl($url)
    {
        // use substring here because ends-with is only available in xpath 2.0
        // and we want to check the end of the URL to match on both full links as well as links without the domain
        return $this->find('xpath', "//a[substring(@href, string-length(@href) - string-length('{$url}') + 1) = '{$url}']");
    }

    /**
     * @param $name
     *
     * @throws BehaviorException
     */
    public function checkForLinkToUrl($url)
    {
        if (!$this->getLinkElementForUrl($url)) {
            throw new BehaviorException("Link for url {$url} not found.");
        }
    }

    /**
     * @throws BehaviorException
     */
    public function checkHaveMessagesInDashboard()
    {
        $db = $this->getElement('dashboard');
        if (!$db->findAll('xpath', '//tbody//tr')) {
            throw new BehaviorException('No messages in dashboard');
        }
    }
}
