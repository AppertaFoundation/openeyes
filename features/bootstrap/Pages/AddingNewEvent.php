<?php

class AddingNewEvent
{


    public $createViewEpisodeEvent = "//*[@id='content']//*[contains(text(), 'Create or View Episodes and Events')]";

    public $addFirstNewEpisode = "//*[@id='event_display']/div[3]/button//*[contains(text(), 'Add episode')]";
    public $addEpisodeConfirm = "//*[@id='add-new-episode-form']//*[contains(text(), 'Confirm')]";
    public $addEpisodeCancel = "//*[@id='add-new-episode-form']//*[contains(text(), 'Cancel')]";

    public $addNewEpisodeButton = "//*[@id='episodes_sidebar']/div[1]/button";

    public $chooseCataractEpisode = "//*[@id='episodes_sidebar']//*[contains(text(), 'Cataract')]";
    public $chooseGlaucomaEpisode = "//*[@id='episodes_sidebar']//*[contains(text(), 'Glaucoma')]";

    public $addNewEventSideBar = "//*[@id='episodes_sidebar']//*[contains(text(), 'Add event')]";

    public $anaestheticSatisfaction = "//*[@id='add-new-event-dialog']//*[contains(text(), 'Anaesthetic Satisfaction Audit')]";
    public $consentForm = "//*[@id='add-new-event-dialog']//*[contains(text(), 'Consent form')]";
    public $correspondence = "//*[@id='add-new-event-dialog']//*[contains(text(), 'Correspondence')]";
    public $examination = "//*[@id='add-new-event-dialog']//*[contains(text(), 'Examination')]";
    public $operationBooking = "//*[@id='add-new-event-dialog']//*[contains(text(), 'Operation booking')]";
    public $operationNote = "//*[@id='add-new-event-dialog']//*[contains(text(), 'Operation note')]";
    public $phasing = "//*[@id='add-new-event-dialog']//*[contains(text(), 'Phasing')]";
    public $prescription = "//*[@id='add-new-event-dialog']//*[contains(text(), 'Prescription')]";
    public $intravitreal = "//*[@id='add-new-event-dialog']//*[contains(text(), 'Intravitreal injection')]";
    public $laser = "//*[@id='add-new-event-dialog']//*[contains(text(), 'Laser')]";
    public $therapyApplication = "//*[@id='add-new-event-dialog']//*[contains(text(), 'Therapy Application')]";




}