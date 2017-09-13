<?php
use Behat\Behat\Exception\BehaviorException;
class AdminPage extends OpenEyesPage
{
    //protected $path = "/site/admin/users";
    protected $path = "/admin/users";
    protected $elements = array(
        //'addFirstNewEpisode' => array ('xpath' => "//*[@id='event_display']/div[3]/button//*[contains(text(), 'Add episode')]"
        'closeSiteAndFirmPopup' => array('xpath' => "//*[@class='ui-dialog-titlebar-close ui-corner-all']//*[contains(text(),'close')]"
        ),
        'systemAdminTab' => array('xpath' => "//*[@class='box_admin_header']//*[contains(text(),'System')]"
        ),
        'systemSettingsSubTab' => array('xpath' => "//*[@class='navigation admin']//*[contains(text(),'Settings')]"
        ),
        'systemLogViewerSubTab' => array('xpath' => "//*[@class='navigation admin']//*[contains(text(),'Log Viewer')]"
        ),
        'biometryAdminTab' => array('xpath' => "//*[@class='box_admin_header']//*[contains(text(),'Biometry')]"
        ),
        'biometryLensTypeSubTab' => array('xpath' => "//*[@class='navigation admin']//*[contains(text(),'Lens types')]"
        ),
        //'biometryFileWatcherSubTab' => array('xpath' => "//*[@class='navigation admin']//*[contains(text(),'File Watcher')]"
        //),
        //'biometryLogViewerSubTab' => array('xpath' => "//*[@class='navigation admin']//*[contains(text(),'Log Viewer')]"
        //),
        'fileWatcherPage' => array('xpath' => "//*[@class='admin box']//*[contains(text(),'DICOM File Watcher')]"
        ),
        'lensTypesPage'=> array('xpath' => "//*[@class='admin box']//*[contains(text(),'Lens types')]"
        ),
        'dicomLogViewerPage'=> array('xpath' => "//*[@class='admin box']//*[contains(text(),'DICOM Log Viewer')]"
        ),
        'dicomLogViewerPage1'=> array('xpath' => "//*[@class='admin box']//*[contains(text(),'')]"
        ),
        'dicomFileList'=> array('xpath' => "//*[@name='dicomfiles']"
        ),
        'dicomFileSubmit'=> array('xpath' => "//*[@name='move_dicom_file']"
        ),
        'logStationId' => array('xpath' => "//*[@id='station_id']"
        ),
        'logLocation' => array('xpath' => "//*[@id='location']"
        ),
        'logPatientNumber' => array('xpath' => "//*[@id='hos_num']"
        ),
        'logStatus' => array('xpath' => "//*[@id='status']"
        ),
        'logType' => array('xpath' => "//*[@id='type']"
        ),
        'logStudyId' => array('xpath' => "//*[@id='study_id']"
        ),
        'logFileName' => array('xpath' => "//*[@id='file_name']"
        ),
        'logImportDate' => array('xpath' => "//*[@id='import_date']"
        ),
        'logStudyDate' => array('xpath' => "//*[@id='study_date']"
        ),
        'logFromDate' => array('xpath' => "//*[@id='date_from']"
        ),
        'logToDate' => array('xpath' => "//*id='date_to']"
        ),
        'logSearchButton' => array('xpath' => "//*[@type='submit']"
        ),
        'machineDetails' => array('xpath' => "//*[@id='machineDetailsData']"
        )
    );
    public function Episode() {

    }

    public function selectTab($tab)
    {
        //switch case was here
        if ($tab == 'Biometry') {
            $this->waitForElementDisplayBlock('biometryAdminTab');
            $element = $this->getElement ( 'biometryAdminTab' );
            $this->scrollWindowToElement ( $element );
            //$this->scrollWindowTo('biometryAdminTab');
            if ($this->getElement('biometryLensTypeSubTab')->isVisible()) {
                $this->waitForElementDisplayBlock('biometryAdminTab');
            } else {
                $this->getElement('biometryAdminTab')->click();
            }
        }
        elseif($tab =='System'){
            $this->waitForElementDisplayBlock('systemAdminTab');
            $element = $this->getElement ( 'systemAdminTab' );
            $this->scrollWindowToElement ( $element );
            if ($this->getElement('biometryLensTypeSubTab')->isVisible()) {
                $this->waitForElementDisplayBlock('systemAdminTab');
            } else {
                $this->getElement('systemAdminTab')->click();
            }
        }
    }

    public function selectSubTab($subTab){
        if($subTab=='Lens Types'){
            $this->waitForElementDisplayBlock('biometryLensTypeSubTab');
            $this->getElement('biometryLensTypeSubTab')->click();
        }
        elseif($subTab=='Log Viewer'){
            $this->waitForElementDisplayBlock('systemLogViewerSubTab');
            $this->getElement('systemLogViewerSubTab')->click();
        }
    }

    public function lookLog($dicomFile)
    {
        $this->waitForElementDisplayBlock('dicomFileList');
        $this->elements['dicomFileMore'] = array(
           'xpath' => "//*[@filename='$dicomFile']//*[contains(text(),'More')]"
        );
        $this->getElement('ficomFileMore')->click();
        //switch($dicomFile){
        //  case '1.2.276.0.75.2.1.10.0.2.150925110222375.15295058.1554_0000_000001_144317518000a5.dcm':
//        $this->elements['dicomFileNew'] = array(
//            'xpath' => "//*[@status='new']//*[@id='filename']//*[contains(text(),'$dicomFile')]"
//        );
//        $this->elements['dicomFileInProgress'] = array(
//            'xpath' => "//*[@status='in_progress']//*[@id='filename']//*[contains(text(),'$dicomFile')]"
//        );
//        $this->elements['dicomFileFailed'] = array(
//            'xpath' => "//*[@status='failed']//*[@id='filename']//*[contains(text(),'$dicomFile')]"
//        );
//        $this->elements['dicomFileSuccess'] = array(
//            'xpath' => "//*[@status='success']//*[@id='filename']//*[contains(text(),'$dicomFile')]"
//        );
//        if ($processStatus == 'new') {
//            if ($this->getElement('dicomFileNew')->isVisible()) {
//                print ("DICOM File dropped and picked up by File Watcher");
//            } else {
//                throw new BehaviorException ("WARNING!!! Process Status doesn't match");
//            }
//        }
//        elseif ($processStatus == 'in progress') {
//
//            if ($this->getElement('dicomFileInProgress')->isVisible()) {
//                print "DICOM File dropped and picked up by File Watcher and is in Progress";
//            } else {
//                throw new BehaviorException ("WARNING!!! Process Status doesn't match");
//            }
//        }
//        elseif ($processStatus == 'failed') {
//            if ($this->getElement('dicomFileFailed')->isVisible()) {
//                print "DICOM File dropped and picked up by File Watcher and FAILED to parse data";
//            } else {
//                throw new BehaviorException ("WARNING!!! Process Status doesn't match");
//            }
//        }
//        elseif ($processStatus == 'success') {
//            if ($this->getElement('dicomFileSuccess')->isVisible()) {
//                print "DICOM File dropped and picked up by File Watcher and was successfully parsed!";
//            } else {
//                throw new BehaviorException ("WARNING!!! Process Status doesn't match");
//            }
//        }
//        else{
//            throw new BehaviorException ("WARNING!!! Process Status doesn't match");
//        }

    }

    public function chooseFromList($dicom){
        //$this->waitForElementDisplayBlock('fileWatcherPage');
        sleep(5);
        $this->getElement('dicomFileList')->selectOption($dicom);
    }

    public function clickSubmit(){
        $this->getElement('dicomFileSubmit')->click();
    }

    public function seeMessage($message){
        $this->elements['dicomMessage'] = array(
            'xpath' => "//*[@id='dicom_file_watcher']//*[contains(text(),'$message')]"
        );
        if($this->getElement('dicomMessage')->isVisible()){
            print ("Message displayed successfully.");
            print ("WAITING FOR THE DICOM FILE TO BE PROCESSED AND PARSED!");
            sleep(10);
        }
        else{
            throw new BehaviorException ("Message not displayed!");
        }

    }

    public function verifyStatus($dicomFile,$processStatus,$processName){
        $this->elements['dicomFileInMore'] = array(
            'xpath' => "//*[@class='dialogbox ui-dialog-content ui-widget-content']//*[contains(text(),'$dicomFile')]"
        );
        $this->elements['dicomMoreBlock'] = array(
            'xpath' => "//*[@class='dialogbox ui-dialog-content ui-widget-content']"
        );
        $this->elements['dicomFileStatus'] = array(
            'xpath' => "//*[@id='fileWatcherHistoryData']//*[@filename='$dicomFile']//*[contains(text(),'$processStatus')]"
        );
        $this->elements['dicomFileProcessName'] = array(
            'xpath' => "//*[@id='fileWatcherHistoryData']//*[@filename='$dicomFile']//*[contains(text(),'$processName')]"
        );
        $this->waitForElementDisplayBlock('dicomMoreBlock');
        $element = $this->getElement ( 'dicomMoreBlock' );
        $this->scrollWindowToElement ( $element );
        if($this->getElement('dicomFileInMore')->isVisible()){
            if($this->getElement('dicomFileStatus')->isVisible()){
                if($this->getElement('dicomFileProcessName')->isVisible()){
                    print "Dicom File name, Status and Process Name are displayed correctly";
                }
                else{
                    throw new BehaviorException ("Warning!! Process Name not displayed Correctly!");
                }
            }
            else{
                throw new BehaviorException ("Warning!! Process Status not displayed Correctly!");
            }
        }
        else{
            throw new BehaviorException ("Warning!! Dicom File Name not displayed Correctly!");
        }
    }

    public function machineDetails($make,$model,$softwareVersion){
        $this->elements['machineDetailsMake'] = array(
            'xpath' => "//*[@id='machineDetailsData']//*[contains(text(),'$make')]"
        );
        $this->elements['machineDetailsModel'] = array(
            'xpath' => "//*[@id='machineDetailsData']//*[contains(text(),'$model')]"
        );
        $this->elements['machineDetailsVersion'] = array(
            'xpath' => "//*[@id='machineDetailsData']//*[contains(text(),'$softwareVersion')]"
        );
        $this->waitForElementDisplayBlock('machineDetails');
        if($this->getElement('machineDetailsMake')->isVisible()){
            print "Make Details Displayed successfully!";
        }
        else{
            throw new BehaviorException ("Warning!! Machine Make not displayed correctly!");
        }
        if($this->getElement('machineDetailsModel')->isVisible()){
            print "Machine Model displayed correctly!";
        }
        else{
            throw new BehaviorException ("Warning!! Machine Model not displayed correctly!");
        }
        if($this->getElement('machineDetailsVersion')->isVisible()){
            print "Machine Version Displayed correctly!";
        }
        else{
            throw new BehaviorException ("Warning!! Machine Version not displayed correctly!");
        }
    }

    public function enterSearch($DICOMFile,$stationID,$location,$patientNumber,$status,$type,$studyInstanceId){
       $this->getElement('logStationId')->setValue($stationID);
        $this->getElement('logLocation')->setValue($location);
        $this->getElement('logPatientNumber')->setValue($patientNumber);
        $this->getElement('logStatus')->setValue($status);
        $this->getElement('logType')->setValue($type);
        $this->getElement('logStudyId')->setValue($studyInstanceId);
        $this->getElement('logFileName')->setValue($DICOMFile);

    }

    public function enterSearchDate($dateType,$startDate,$endDate){
        if($dateType=='Import'){
            $this->getElement('logImportDate')->check();
        }
        elseif($dateType=='Study'){
            $this->getElement('logStudyDate')->check();
        }
        else{
            throw new BehaviorException ("Warning!! Invalid date type option specified!");
        }

        $this->getElement('logFromDate')->setValue($startDate);
        $this->getElement('logToDate')->setValue($endDate);
    }

    public function clickLogSearch(){
        $this->getElement('logSearchButton')->click();
    }

    public function searchDebugData($dicomValue){
        //*[@id='debug_data']//*[contains(text(),'birth date: 21/3/1964')]
        $this->elements['debugData'] = array(
            'xpath' => "//*[@id='debug_data']//*[contains(text(),'$dicomValue')]"
        );
        if($this->getElement('debugData')->isVisible()){
            print "Search Value displayed correctly in the Debug Data!";
        }
        else{
            throw new BehaviorException ("Warning!! Search Value not displayed correctly in the Debug Data!");
        }
    }
}
