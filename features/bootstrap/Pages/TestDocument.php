<?php
/**
 * Created by PhpStorm.
 * User: fivium
 * Date: 12/12/18
 * Time: 10:42 AM
 */
use Behat\Behat\Exception\BehaviorException;

class TestDocument extends OpenEyesPage {
    protected $path = "OphCoDocument/Default/create?patient_id={patientId}";
    protected $elements = array(
        'EventSubType' => array(
            'xpath' => "//*[@id='Element_OphCoDocument_Document_event_sub_type']"
        ),
        'SingleDocument' => array(
            'xpath' => "//*[@id='upload_box_single_document']"
        ),
        'SingleFileUpload'=>array(
            'xpath'=>"//*[@value='single']"
        ),
        'DoubleFileUpload'=>array(
            'xpath'=>"//*[@value='double']"
        ),
        'DoubleFileUploadLeft'=>array(
            'xpath'=>"//*[@id='left_document_id_row']"
        ),
        'DoubleFileUploadRight'=>array(
            'xpath'=>"//*[@id='right_document_id_row']"
        ),
        'singleFileInput'=>array(
            'xpath'=>"//*[@id='Document_single_document_row_id']"
        ),
        'leftFileInput'=>array(
            'xpath'=>"//*[@id='Document_left_document_row_id']"
        ),
        'rightFileInput'=>array(
            'xpath'=>"//*[@id='Document_right_document_row_id']"
        ),
        'saveBtn'=>array(
            'xpath'=>"//*[@id='et_save']"
        ),
        'saveOK' => array (
            'xpath' => "//*[@id='flash-success']"
        ),
    );

    public function uploadSingleDocument(){
        $this->getElement('SingleDocument')->click();
    }

    public function clickDoubleFileUpload(){
        $this->getElement('DoubleFileUpload')->click();
    }

    public function clickSingleFileUpload(){
        $this->getElement('SingleFileUpload')->click();
    }

    public function changeEventSubType($event_sub_type){
        $this->waitForElementDisplayBlock ( 'EventSubType' );

        $this->getElement ( 'EventSubType' )->selectOption ( $event_sub_type );
        $this->waitForElementDisplayBlock ( 'EventSubType' );

    }

    public function uploadDoubleDocumentLeft(){
        $this->getElement('DoubleFileUploadLeft')->click();
    }

    public function uploadDoubleDocumentRight(){
        $this->getElement('DoubleFileUploadRight')->click();
    }

    public function uploadFileSingle($file_path){
        $this->getElement('singleFileInput')->attachFile($file_path);
    }
    public function uploadRightFile($file_path){
        $this->getElement('rightFileInput')->attachFile($file_path);
    }
    public function uploadLeftFile($file_path){
        $this->getElement('leftFileInput')->attachFile($file_path);
    }
    public function saveDocument(){
        $this->getElement('saveBtn')->click();
    }
    public function saveDocumentAndConfirm() {
        $this->getElement ( 'saveBtn' )->click ();

        $this->getSession ()->wait ( 5000, 'window.$ && $.active == 0' );
        if ($this->hasDocumentSaved()) {
            print "Document has been saved OK";
        }

        else {
            throw new BehaviorException ( "WARNING!!!  Document has NOT been saved!!  WARNING!!" );
        }
    }

    protected function hasDocumentSaved() {
        return ( bool ) $this->find ( 'xpath', $this->getElement ( 'saveOK' )->getXpath () );

    }

}