 @iol
Feature: To Test the process of IOLMaster imports to Biometry event
  @OE-5841  @filedrop
  Scenario Outline: To Drop DICOM file into test share for file watcher to pick it up
  Given I am on the OpenEyes "<mainPage>" homepage
  And I enter login credentials "<uname>" and "<pwd>"
  #And I close the site and firm selection popup
  And I select Site "<siteName/Number>"
  Then I select a firm of "<firmName/Number>"
  Then I open url "<url>"
  Then I choose the "<DICOMFile>" from DICOM file list
  Then I click on submit
  Then I should see "<message>" on the DICOM File Watcher page

  Examples:
  |mainPage|uname|pwd  |siteName/Number|firmName/Number         |page |tab   |subTab      |DICOMFile                                                                            |message                  |url                                                          |
  |master  |admin|admin|Barking        |Cataract firm (Cataract)|Admin|System|File Watcher|1.2.276.0.75.2.1.10.0.2.150909105354671.15295058.30971_0000_000001_14417922420056.dcm|File Copied Successfully!|http://iolmaster.openeyes.org.uk/TestHarness/DICOMFileWatcher|


   @logview
    Scenario Outline: To check the status of the dropped file in DICOM log viewer
    Given I am on the OpenEyes "<mainPage>" homepage
    And I enter login credentials "<uname>" and "<pwd>"
  #And I close the site and firm selection popup
    And I select Site "<siteName/Number>"
    Then I select a firm of "<firmName/Number>"
    And I select "<page>" from more tab
    And I select "<tab>" from the tabs on the admin page
    Then I select the "<subTab>"
    Then I look for the "<DICOMFile>" from the DICOM log and open process
     Then I verify the "<DICOMFile>" with the "<processStatus>" and "<processName>"

    Examples:
      |mainPage|uname|pwd  |siteName/Number|firmName/Number         |page |tab   |subTab    |DICOMFile                                                                            |processStatus|processName       |
      |master  |admin|admin|Barking        |Cataract firm (Cataract)|Admin|System|Log Viewer|1.2.276.0.75.2.1.10.0.2.150909105354671.15295058.30971_0000_000001_14417922420056.dcm|new          |runFileWatcher.php|


   @OE-5813 @fileParser
   Scenario Outline: To check the status of the dropped file in DICOM log viewer
     Given I am on the OpenEyes "<mainPage>" homepage
     And I enter login credentials "<uname>" and "<pwd>"
  #And I close the site and firm selection popup
     And I select Site "<siteName/Number>"
     Then I select a firm of "<firmName/Number>"
     And I select "<page>" from more tab
     And I select "<tab>" from the tabs on the admin page
     Then I select the "<subTab>"
     #Then I enter "<DICOMFile>" with the "<stationID>","<location>","<patientNumber>","<status>","<type>" and "<studyInstanceId>" in the search fields
     #Then I select <"dateType">, "<startDate>" and "<endDate>"
     #Then I click search
     Then I look for the "<DICOMFile>" from the DICOM log and open process
     Then I look for "<make>","<model>" and "<softwareVersion>" in machine details
     Then I search for "<patientName>" in debug data
     Then I search for "<hospitalNumber>" in debug data
     Then I search for "<birthDate>" in debug data
     Then I search for "<surgeon>" in debug data
     Then I search for "<leftAxisK1>" in debug data
     Then I search for "<leftAxisK2>" in debug data
     Then I search for "<leftK1>" in debug data
     Then I search for "<leftK2>" in debug data
     Then I search for "<leftDeltaK>" in debug data
     Then I search for "<leftDeltaKAxis>" in debug data
     Then I search for "<leftACD>" in debug data
     Then I search for "<leftAxialLength>" in debug data
     Then I search for "<leftSNR>" in debug data
     Then I search for "<rightAxisK1>" in debug data
     Then I search for "<rightAxisK2>" in debug data
     Then I search for "<rightK1>" in debug data
     Then I search for "<rightK2>" in debug data
     Then I search for "<rightDeltaK>" in debug data
     Then I search for "<rightDeltaKAxis>" in debug data
     Then I search for "<rightACD>" in debug data
     Then I search for "<rightAxialLength>" in debug data
     Then I search for "<rightSNR>" in debug data


     Examples:
       |mainPage|uname|pwd  |siteName/Number|firmName/Number         |page |tab   |subTab    |DICOMFile                                                                            |processStatus|processName       |stationID|location|patientNumber|status|type|studyInstanceId|fileName|dateType|startDate|endDate|make|model|softwareVersion|dicomValue|patientName|hospitalNumber|birthDate|surgeon|leftAxisK1|leftAxisK2|leftK1|leftK2|leftDeltaK|leftDeltaKAxis|leftACD|leftAxialLength|leftSNR|rightAxisK1|rightAxisK2|rightK1|rightK2|rightDeltaK|rightDeltaKAxis|rightACD|rightAxialLength|rightSNR|
       |master  |admin|admin|Barking        |Cataract firm (Cataract)|Admin|System|Log Viewer|1.2.276.0.75.2.1.10.0.2.150909105354671.15295058.30971_0000_000001_14417922420056.dcm|new          |runFileWatcher.php|         |        |             |      |    |               |        |        |         |       |    |     |               |          |           |              |         |       |          |          |      |      |          |              |       |               |       |           |           |       |       |           |               |        |                |        |


