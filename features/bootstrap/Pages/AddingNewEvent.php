<?php

class AddingNewEvent
{


    public static $createViewEpisodeEvent = "//*[@id='content']//*[contains(text(), 'Create or View Episodes and Events')]";

    public static $addFirstNewEpisode = "//*[@id='event_display']/div[3]/button//*[contains(text(), 'Add episode')]";
    public static $addEpisodeConfirm = "//*[@id='add-new-episode-form']//*[contains(text(), 'Confirm')]";
    public static $addEpisodeCancel = "//*[@id='add-new-episode-form']//*[contains(text(), 'Cancel')]";

    public static $addNewEpisodeButton = "//*[@id='episodes_sidebar']/div[1]/button";

    public static $chooseCataractEpisode = "//*[@id='episodes_sidebar']//*[contains(text(), 'Cataract')]";
    public static $chooseGlaucomaEpisode = "//*[@id='episodes_sidebar']//*[contains(text(), 'Glaucoma')]";

    public static $addNewEventSideBar = "//*[@id='episodes_sidebar']//*[contains(text(), 'Add event')]";

    public static $anaestheticSatisfaction = "//*[@id='add-new-event-dialog']//*[contains(text(), 'Anaesthetic Satisfaction Audit')]";
    public static $consentForm = "//*[@id='add-new-event-dialog']//*[contains(text(), 'Consent form')]";
    public static $correspondence = "//*[@id='add-new-event-dialog']//*[contains(text(), 'Correspondence')]";
    public static $examination = "//*[@id='add-new-event-dialog']//*[contains(text(), 'Examination')]";
    public static $operationBooking = "//*[@id='add-new-event-dialog']//*[contains(text(), 'Operation booking')]";
    public static $operationNote = "//*[@id='add-new-event-dialog']//*[contains(text(), 'Operation note')]";
    public static $phasing = "//*[@id='add-new-event-dialog']//*[contains(text(), 'Phasing')]";
    public static $prescription = "//*[@id='add-new-event-dialog']//*[contains(text(), 'Prescription')]";
    public static $intravitreal = "//*[@id='add-new-event-dialog']//*[contains(text(), 'Intravitreal injection')]";
    public static $laser = "//*[@id='add-new-event-dialog']//*[contains(text(), 'Laser')]";
    public static $therapyApplication = "//*[@id='add-new-event-dialog']//*[contains(text(), 'Therapy Application')]";




}