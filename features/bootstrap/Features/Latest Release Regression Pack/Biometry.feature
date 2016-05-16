@OE-5698 @BIO
Feature: Ticket related and Regression tests for biometry
  Scenario Outline: To check that the lens type selection is non-mandatory
    Given I am on the OpenEyes "master" homepage
     And I enter login credentials "<uname>" and "<pwd>"
     And I select Site "<siteName/Number>"
     Then I select a firm of "<firmName/Number>"

     Then I search for hospital number "<hospitalNumber>"

    Then I select Create or View Episodes and Events
     Then I expand the "<speciality>" sidebar
     Then I add a New Event "<event>"

     Then I Save the Biometry
     Then I Check that no lenstype selection is not showing any error

     Examples:
     |uname|pwd  |siteName/Number|firmName/Number         |hospitalNumber|speciality|event|
     |admin|admin|Barking        |Cataract firm (Cataract)|1009465       |Cataract|Biometry|
     |admin|admin|Barking        |Cataract firm (Cataract)|9999992       |Cataract|Biometry|
     |admin|admin|Barking        |Cataract firm (Cataract)|9999997       |Cataract|Biometry|
     |admin|admin|Barking        |Cataract firm (Cataract)|9999991       |Cataract|Biometry|
     |admin|admin|Barking        |Cataract firm (Cataract)|9999998       |Cataract|Biometry|


   @OE-5791 @sprint25 @BIO
   Scenario Outline: To check that the lens type selection is not made my default
    Given I am on the OpenEyes "master" homepage
    And I enter login credentials "<uname>" and "<pwd>"
    And I select Site "<siteName/Number>"
    Then I select a firm of "<firmName/Number>"

    Then I search for hospital number "<hospitalNumber>"

    Then I select Create or View Episodes and Events
    Then I expand the "<speciality>" sidebar
    Then I add a New Event "<event>"

    Then I Check that lenstype is not selected by default

    Examples:
     |uname|pwd  |siteName/Number|firmName/Number         |hospitalNumber|speciality|event|
     |admin|admin|St Ann's       |Cataract firm (Cataract)|1009465       |Cataract  |Biometry|



    #Biometry event previously generated from the DICOM files

   @OE-5770 @sprint26 @BIO
    Scenario Outline: To check that the l
     Given I am on the OpenEyes "master" homepage
     And I enter login credentials "<uname>" and "<pwd>"
     And I select Site "<siteName/Number>"
     Then I select a firm of "<firmName/Number>"

     Then I search for hospital number "<hospitalNumber>"

     Then I select Create or View Episodes and Events
     Then I click to expand the "<speciality>" sidebar

     Then I click on existing "<event>"
     Then I verify that the event is auto generated from DICOM files
     Then I look for the "<info-alert>"
    Examples:
   |uname|pwd  |siteName/Number|firmName/Number       |hospitalNumber|speciality|event   |info-alert|
   |admin|admin|Barking        |Buddhi Wang (Cataract)|1009465       |Cataract  |Biometry|          |



  @OE-5770_2 @sprint26 @BIO
    Scenario Outline: To check that the lens type selection is not made my default
  Given I am on the OpenEyes "master" homepage
  And I enter login credentials "<uname>" and "<pwd>"
  And I select Site "<siteName/Number>"
  Then I select a firm of "<firmName/Number>"

  Then I search for hospital number "<hospitalNumber>"

  Then I select Create or View Episodes and Events
  Then I expand the "<speciality>" sidebar

  Then I add a New Event "<event>"

  Then I click on existing "<event>"
  Then I verify that the event is manually entered
  Then I look for the manual entry alert message

  Examples:
   |uname|pwd  |siteName/Number|firmName/Number       |hospitalNumber|speciality|event   |
   |admin|admin|Barking        |Buddhi Wang (Cataract)|1009465       |Cataract  |Biometry|



  @IOLParser_check
    Scenario Outline: To check the DICOM file values displayed correctly on the biometry event
    Given I am on the OpenEyes "<mainPage>" homepage
    And I enter login credentials "<uname>" and "<pwd>"
  #And I close the site and firm selection popup
    And I select Site "<siteName/Number>"
    Then I select a firm of "<firmName/Number>"
    Then I open url "<url>"
    Then I choose the "<DICOMFile>" from DICOM file list
    Then I click on submit
    Then I should see "<message>" on the DICOM File Watcher page
    Then I click on "<primaryTab>" in primary selection tab
      Then I search for hospital number "<hospitalNumber>"
      Then I look for the "<biometryEventExists>" alert on the patient summary page
      Then I select Create or View Episodes and Events
      Then I expand the "<speciality>" sidebar
      Then I add a New Event "<event>"
      Then I select a auto generated biometry event with "<eventDateTime>"
    Then I click on continue
    Then I should see "<alert>" on biometry "<viewType>" page
    Then I select <"biometryTab">


    Examples:
      |uname|pwd  |siteName/Number|firmName/Number       |hospitalNumber|speciality|event   |
      |admin|admin|Barking        |Buddhi Wang (Cataract)|1009465       |Cataract  |Biometry|



