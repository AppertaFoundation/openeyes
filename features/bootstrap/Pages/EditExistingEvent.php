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
}

