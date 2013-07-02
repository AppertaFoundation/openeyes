<?php

use SensioLabs\Behat\PageObjectExtension\PageObject\Page;

class AddingNewEvent extends Page
{
    public static $createViewEpisodeEvent = "//*[@id='content']//*[contains(text(), 'create or view episodes and events')]";
    public static $addNewEvent = "//button[@id='addnewevent']";
    public static $anaestheticSatisfaction = "//*[@id='add-event-select-type']//*[contains(text(), 'anaesthetic satisfaction audit')]";
    public static $consentForm = "//*[@id='add-event-select-type']//*[contains(text(), 'consent form')]";
    public static $correspondence = "//*[@id='add-event-select-type']//*[contains(text(), 'correspondence')]";
    public static $examination = "//*[@id='add-event-select-type']//*[contains(text(), 'examination')]";
    public static $operationBooking = "//*[@id='add-event-select-type']//*[contains(text(), 'operation booking')]";
    public static $operationNote = "//*[@id='add-event-select-type']//*[contains(text(), 'operation note')]";
    public static $phasing = "//*[@id='add-event-select-type']//*[contains(text(), 'phasing')]";
    public static $prescription = "//*[@id='add-event-select-type']//*[contains(text(), 'prescription')]";
}