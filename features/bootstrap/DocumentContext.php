<?php
/**
 * Created by PhpStorm.
 * User: fivium
 * Date: 12/12/18
 * Time: 10:54 AM
 */
use Behat\Behat\Context\ClosuredContextInterface, Behat\Behat\Context\TranslatedContextInterface, Behat\Behat\Context\BehatContext, Behat\Behat\Exception\PendingException;
use Behat\Gherkin\Node\PyStringNode, Behat\Gherkin\Node\TableNode;
use Behat\MinkExtension\Context\MinkContext;

use Behat\Mink\Driver\Selenium2Driver;
use \SensioLabs\Behat\PageObjectExtension\Context\PageObjectContext;
use WebDriver\WebDriver;

class DocumentContext extends PageObjectContext
{
    public function __construct(array $parameters)
    {
    }
    /**
     * @Then/^I click on single document upload$/
     */
    public function iClickOnSingleDocumentUpload()
    {
        /**
         *
         * @var TestDocument $document
         */
        $document = $this->getPage('TestDocument');
        $document ->uploadSingleDocument();

    }
    /**
     * @Then/^I click on double files upload$/
     */
    public function iClickOnDoubleFilesUpload(){
        /**
         * @var TestDocument $document
         */
        $document = $this->getPage('TestDocument');
        $document->clickDoubleFileUpload();
    }
    /**
     * @Then/^I select Event Sub Type of "([^"]*)"$/
     */
    public function iSelectEventSubTypeOf($event_sub_type){
        /**
         * @var TestDocument $document
         */
        $document = $this->getPage('TestDocument');
        $document->changeEventSubType($event_sub_type);
    }
    /**
     * @Then/^I click on double document upload left$/
     */
    public function iClickOnDoubleDocumentUploadLeft(){
        /**
         *
         * @var TestDocument $document
         */
        $document = $this->getPage('TestDocument');
        $document ->uploadDoubleDocumentLeft();
    }
    /**
     * @Then/^I click on double document upload right$/
     */
    public function iClickOnDoubleDocumentUploadRight(){
        /**
         *
         * @var TestDocument $document
         */
        $document = $this->getPage('TestDocument');
        $document ->uploadDoubleDocumentRight();
    }
    /**
     * @Then/^I upload single file "([^"]*)"$/
     */
    public function iUploadSingleFile($file_path){
        /**
         * @var Testdocument $document
         */
        $document=$this->getPage('TestDocument');
        $document->uploadFileSingle($file_path);
    }
    /**
     * @Then/^I upload right file "([^"]*)"$/
     */
    public function iUploadRightFile($file_path){
        /**
         * @var Testdocument $document
         */
        $document=$this->getPage('TestDocument');
        $document->uploadRightFile($file_path);
    }
    /**
     * @Then/^I upload left file "([^"]*)"$/
     */
    public function iUploadLeftFile($file_path){
        /**
         * @var Testdocument $document
         */
        $document=$this->getPage('TestDocument');
        $document->uploadLeftFile($file_path);
    }
    /**
     * @Then/^I save document event$/
     */
    public function iSaveDocumentEvent(){
        /**
         * @var Testdocument $document
         */
        $document=$this->getPage('TestDocument');
        $document->saveDocument();
    }
    /**
     * @Then/^I save document event and confirm it saved successfully$/
     */
    public function iSaveDocumentEventAndConfirmItSavedSuccessfully(){
        /**
         * @var Testdocument $document
         */
        $document=$this->getPage('TestDocument');
        $document->saveDocumentAndConfirm();
    }

}