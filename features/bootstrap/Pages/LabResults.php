<?php
/**
 * Created by PhpStorm.
 * User: zhe
 * Date: 1/02/19
 * Time: 4:03 PM
 */
use Behat\Behat\Exception\BehaviorException;
class LabResults extends OpenEyesPage
{
    protected $elements = array(
        'typeBtn' => array(
            'xpath' => "//*[@id='Element_OphInLabResults_Details_result_type_id']"
        ),

        'timeField' => array(
            'xpath' => "//*[@id='Element_OphInLabResults_Inr_time']"
        ),
        'timeBtn' => array(
            'xpath' => "//*[@id='div_Element_OphInLabResults_Inr_time']"
        ),
        'resultField' => array(
            'xpath' => "//*[@id='Element_OphInLabResults_Inr_result']"
        ),
        'commentField' => array(
            'xpath' => "//*[@id='Element_OphInLabResults_Inr_comment']"
        ),
        'saveBtn' => array(
            'xpath' => "//*[@id='et_save']"
        ),
        'saveLabResultOK' => array(
            'xpath' => "//*[@id='flash-success']"
        ),
    );

    public function selectLabResultsType($type){
        $this->getElement('typeBtn')->click();
        $this->getElement('typeBtn')->selectOption($type);
        sleep(1);
    }

    public function selectTimeOfRecording($time){
        $this->getElement('timeBtn')->click();
        $this->getElement('timeField')->setValue($time);
    }

    public function selectResult($result){
        $this->getElement('resultField')->setValue($result);
    }
    public function selectComment($comment){
        $this->getElement('commentField')->setValue($comment);
    }
    public function saveAndConfirm(){
        $this->getSession()->executeScript('window.stop()');
        $this->getSession()->wait(5000, 'window.$ && $.active ==0');
        $this->getElement('saveBtn')->click();
        if(!$this->hasLabResultsSaved()){
            throw new BehaviorException("WARNING!!! Lab Results Has NOT Been Saved!! WARNING!!");
        }
    }

    protected function hasLabResultsSaved(){
        return ( bool ) $this->find('xpath', $this->getElement('saveLabResultOK')->getXpath());
    }

}