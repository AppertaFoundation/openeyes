<?php
/**
 * Created by PhpStorm.
 * User: Hemanth
 * Date: 11/11/2015
 * Time: 14:37
 */
use Behat\Behat\Exception\BehaviorException;
class EditExistingEvent extends OpenEyesPage
{
    protected $path = "OphInBiometry/default/view/{eventId}}";
    protected $elements = array(

        'EditBtn'=>array(
//            'css' => ".button.header-tab"
        // There is a space at the end of this class which should be a typo
            'xpath'=>"//*[@class='button header-tab ']"
        ),
        'saveBtn'=>array(
            'xpath'=>"//*[@id='et_save']"
        ),
//            'expandCataractEpisode' => array (
//					'xpath' => "//*[@class='episode-title']//*[contains(text(),'Cataract')]"
//			),
//			'expandGlaucomaEpisode' => array (
//					'xpath' => "//*[@class='episode-title']//*[contains(text(),'Glaucoma')]"
//			),
//			'expandRefractiveEpisode' => array (
//					'xpath' => "//*[@class='episode-title']//*[contains(text(),'Refractive')]"
//			),
//			'expandMedicalRetinalEpisode' => array (
//					'xpath' => "//*[@class='episode-title']//*[contains(text(),'Medical Retinal')]"
//			)
    );

//    public function expandCataract() {
//        $this->getElement ( 'expandCataractEpisode' )->click ();
//    }
//    public function expandGlaucoma() {
//        $this->getElement ( 'expandGlaucomaEpisode' )->click ();
//    }
//    public function expandMedicalRetinal() {
//        $this->getElement ( 'expandMedicalRetinalEpisode' )->click ();
//    }
//    public function expandSupportFirm() {
//        $this->getElement ( 'expandSupportFirm' )->click ();
//    }
//    public function expandAdnexal(){
//        $this->getElement('expandAdnexal')->click();
//    }
//    public function expandVitreoretinal(){
//        $this->getElement('expandVitreoretinal')->click();
//    }
//    public function clickExistingEvent($event)
//    {
//
//    }
    public function expandFirm($firm){
        $this->elements['expandFirmEpisode'] = array(
            'xpath' => "//*[@class='episode-title']//*[contains(text(),'$firm')]"
        );
        $this->getElement('expandFirmEpisode')->click();
    }

    public function selectEvent($event_id){
        $this->elements['SelectedEvent'] = array(
            'xpath'=>"//*[@id='js-sideEvent$event_id']",
        );
        $this->getElement('SelectedEvent')->click();
    }

    public function selectEdit(){
        $this->getElement('EditBtn')->click();
    }

}

