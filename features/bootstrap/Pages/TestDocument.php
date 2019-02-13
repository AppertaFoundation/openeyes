<?php
/**
 * Created by PhpStorm.
 * User: fivium
 * Date: 12/12/18
 * Time: 10:42 AM
 */

use Behat\Behat\Exception\BehaviorException;

class TestDocument extends EventPage
{
    public function __construct(\Behat\Mink\Session $session, \SensioLabs\Behat\PageObjectExtension\Context\PageFactoryInterface $pageFactory, array $parameters = array())
    {
        parent::__construct($session, $pageFactory, $parameters);
        $this->elements = array_merge($this->elements, self::getPageElements());
    }

    protected $path = "OphCoDocument/Default/create?patient_id={patientId}";

    protected static function getPageElements()
    {
        return array(
            'EventSubType' => array(
                'xpath' => "//*[@id='Element_OphCoDocument_Document_event_sub_type']"
            ),
            'SingleDocument' => array(
                'xpath' => "//*[@id='upload_box_single_document']"
            ),
            'SingleFileUpload' => array(
                'xpath' => "//*[@value='single']"
            ),
            'DoubleFileUpload' => array(
                'xpath' => "//*[@value='double']"
            ),
            'DoubleFileUploadLeft' => array(
                'xpath' => "//*[@id='left_document_id_row']"
            ),
            'DoubleFileUploadRight' => array(
                'xpath' => "//*[@id='right_document_id_row']"
            ),
            'singleFileInput' => array(
                'xpath' => "//*[@id='Document_single_document_row_id']"
            ),
            'leftFileInput' => array(
                'xpath' => "//*[@id='Document_left_document_row_id']"
            ),
            'rightFileInput' => array(
                'xpath' => "//*[@id='Document_right_document_row_id']"
            ),
        );
    }


    public function uploadSingleDocument()
    {
        $this->getElement('SingleDocument')->click();
    }

    public function clickDoubleFileUpload()
    {
        $this->getElement('DoubleFileUpload')->click();
    }

    public function clickSingleFileUpload()
    {
        $this->getElement('SingleFileUpload')->click();
    }

    public function changeEventSubType($event_sub_type)
    {
        $this->waitForElementDisplayBlock('EventSubType');

        $this->getElement('EventSubType')->selectOption($event_sub_type);
        $this->waitForElementDisplayBlock('EventSubType');

    }

    public function uploadDoubleDocumentLeft()
    {
        $this->getElement('DoubleFileUploadLeft')->click();
    }

    public function uploadDoubleDocumentRight()
    {
        $this->getElement('DoubleFileUploadRight')->click();
    }

    public function uploadFileSingle($file_path)
    {
        $this->getElement('singleFileInput')->attachFile($file_path);
    }

    public function uploadRightFile($file_path)
    {
        $this->getElement('rightFileInput')->attachFile($file_path);
    }

    public function uploadLeftFile($file_path)
    {
        $this->getElement('leftFileInput')->attachFile($file_path);
    }

    public function saveDocument()
    {
        $this->getElement('saveBtn')->click();
    }
}