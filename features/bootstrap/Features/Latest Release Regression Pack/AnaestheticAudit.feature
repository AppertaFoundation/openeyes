@asa @regression
Feature: Anaesthetic Satisfaction Audit Regression Tests
@ASA
  Regression coverage of this event is 100%
  Across 2 Sites and 4 Firms

  Scenario Outline: Route 1: Login and create a Anaesthetic Satisfaction Audit:
            Site 2:  Kings
            Firm 3:  Anderson Glaucoma

    Given I am on the OpenEyes "master" homepage
    And I enter login credentials "<uname>" and "<pwd>"
    And I select Site "<siteName/Number>"
    ||
    Then I select a firm of "<firmName/Number>"

    Then I search for patient name last name "<lastName>" and first name "<firstName>"

    Then I select Create or View Episodes and Events

    Then I expand the "<speciality>" sidebar
    And I add a New Event "<event>"

    Then I select an Anaesthetist "no"
    #And I select Satisfaction levels of Pain "2" Nausea "3"

    And I tick the Vomited checkbox

    Then I select Vital Signs of Respiratory Rate "3" Oxygen Saturation "3" Systolic Blood Pressure "4"
    And I select Vital Signs of Body Temperature "5" and Heart Rate "2" Conscious Level AVPU "2"

    Then I enter Comments "<comments>"

    And I select the Yes option for Ready to Discharge

    Then I Save the Event and confirm it has been created successfully

    Examples:
    |uname|pwd  |siteName/Number|firmName/Number        |lastName|firstName|speciality|event                         |comments    |
    |admin|admin|Barking        |A K Hamilton (Glaucoma)|Coffin, |Violet   |Glaucoma  |Anaesthetic Satisfaction Audit|This is test|


  Scenario Outline: Route 2: Login and create a Anaesthetic Satisfaction Audit:
            Site 1:  Queens
            Firm 1:  Anderson Cataract

    Given I am on the OpenEyes "master" homepage
    And I enter login credentials "<uname>" and "<pwd>"
    And I select Site "<siteName/Number>"
    Then I select a firm of "<firmName/Number>"

    Then I search for hospital number "<hospNo>"

    Then I select Create or View Episodes and Events

    Then I expand the "<speciality>" sidebar
    And I add a New Event "<event>"

    Then I select an Anaesthetist "non"
    #And I select Satisfaction levels of Pain "5" Nausea "1"

    And I untick the Vomited checkbox

    Then I select Vital Signs of Respiratory Rate "4" Oxygen Saturation "1" Systolic Blood Pressure "5"
    And I select Vital Signs of Body Temperature "1" and Heart Rate "5" Conscious Level AVPU "5"

    Then I enter Comments "<comments>"

    And I select the No option for Ready to Discharge

    Then I Save the Event and confirm it has been created successfully

    Examples:
    |uname|pwd  |siteName/Number|firmName/Number         |hospNo |speciality|event                         |comments    |
    |admin|admin|Croydon        |Cataract firm (Cataract)|1009465|Cataract  |Anaesthetic Satisfaction Audit|This is test|

  Scenario Outline: Route 3: Login and create a Anaesthetic Satisfaction Audit:
            Site 1: Queens
            Firm 2: Broom Glaucoma

    Given I am on the OpenEyes "master" homepage
    And I enter login credentials "<uname>" and "<pwd>"
    And I select Site "<siteName/Number>"
    Then I select a firm of "<firmName/Number>"

    Then I search for patient name last name "<lastName>" and first name "<firstName>"

    Then I select Create or View Episodes and Events

    Then I expand the "<speciality>" sidebar
    And I add a New Event "<event>"

    Then I select an Anaesthetist "no"
    #And I select Satisfaction levels of Pain "7" Nausea "2"

    And I tick the Vomited checkbox

    Then I select Vital Signs of Respiratory Rate "1" Oxygen Saturation "3" Systolic Blood Pressure "3"
    And I select Vital Signs of Body Temperature "4" and Heart Rate "5" Conscious Level AVPU "2"

    Then I enter Comments "<comments>"

    And I select the No option for Ready to Discharge

    Then I Save the Event and confirm it has been created successfully

    Examples:

      |uname|pwd  |siteName/Number|firmName/Number        |firstName|lastName|speciality|event                         |comments    |
      |admin|admin|Mile End       |A K Hamilton (Glaucoma)|Violet   |Coffin, |Glaucoma  |Anaesthetic Satisfaction Audit|This is test|

  Scenario Outline: Route 4: Login and create a Anaesthetic Satisfaction Audit:
            Site 2: Kings
            Firm 4: Medical Retinal

    Given I am on the OpenEyes "master" homepage
    And I enter login credentials "<uname>" and "<pwd>"
    And I select Site "<siteName/Number>"
    Then I select a firm of "<firmName/Number>"

    Then I search for patient name last name "<lastName>" and first name "<firstName>"

    Then I select Create or View Episodes and Events

    Then I expand the "<speciality>" sidebar
    And I add a New Event "<event>"

    Then I select an Anaesthetist "non"
    #And I select Satisfaction levels of Pain "0" Nausea "0"

    And I untick the Vomited checkbox

    Then I select Vital Signs of Respiratory Rate "2" Oxygen Saturation "4" Systolic Blood Pressure "6"
    And I select Vital Signs of Body Temperature "3" and Heart Rate "2" Conscious Level AVPU "2"

    Then I enter Comments "This test is for Site 2 Kings, Firm 4 Medical Retinal"

    And I select the No option for Ready to Discharge

    Then I Save the Event and confirm it has been created successfully

    Examples:
      |uname|pwd  |siteName/Number|firmName/Number                |firstName|lastName|speciality    |event                          |
      |admin|admin|Barking        |Angela Glasby (Medical Retinal)|Violet   |Coffin, |Medical Retinal|Anaesthetic Satisfaction Audit|

  @ASA_RT5
  Scenario Outline: Route 5: Validation Tests
            Ensure that correct validation messages are displayed when the user attempts to save

    Given I am on the OpenEyes "master" homepage
    And I enter login credentials "<uname>" and "<pwd>"
    And I select Site "<siteName/Number>"
    Then I select a firm of "<firmName/Number>"

    Then I search for patient name last name "<lastName>" and first name "<firstName>"

    Then I select Create or View Episodes and Events

    Then I expand the "<speciality>" sidebar
    And I add a New Event "<event>"

    Then I Save the Event

    Then I confirm that the ASA Validation error messages have been displayed

Examples:

  |uname|pwd  |siteName/Number|firmName/Number        |firstName|lastName|speciality    |event                         |
  |admin|admin|Mile End       |A K Hamilton (Glaucoma)|Violet   |Coffin, |Glaucoma      |Anaesthetic Satisfaction Audit|
