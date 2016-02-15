@setup
Feature: Environment Setup on clean database
  This feature is to be run first on a clean database
  It sets up 3 Firms Glaucoma, Medical Retina and Cataract
  Each with an event setup ready for the regression tag to be run
  
  Scenario Outline: Environment Setup Broom Glaucoma

    Given I am on the OpenEyes "master" homepage
    And I enter login credentials "<uname>" and "<pwd>"
    And I select Site "<site>"
    Then I select a firm of "<firm>"

    Then I search for hospital number "<hospNum>"

    Then I select Add First New Episode and Confirm
    Then I expand the "<speciality>" sidebar

    And I add a New Event "<event>"

    Then I select an Anaesthetist "no"
    #And I select Satisfaction levels of Pain "2" Nausea "3"

    And I tick the Vomited checkbox

    Then I select Vital Signs of Respiratory Rate "3" Oxygen Saturation "3" Systolic Blood Pressure "4"
    And I select Vital Signs of Body Temperature "5" and Heart Rate "2" Conscious Level AVPU "2"

    Then I enter Comments "This test is for Site 2 Kings, Firm 3 Anderson Glaucoma"

    And I select the Yes option for Ready to Discharge

    Then I Save the Event

    Examples:
    |uname|pwd  |site   |firm                   |hospNum|speciality|event       |
    |admin|admin|Barking|A K Hamilton (Glaucoma)|1009465|glaucoma  |Satisfaction|

  Scenario Outline: Environment Setup Medical Retinal

    Given I am on the OpenEyes "master" homepage
    And I enter login credentials "<uname>" and "<pwd>"
    And I select Site "<site>"
    Then I select a firm of "<firm>"

    Then I search for hospital number "<hospNum>"

    Then I select Add First New Episode and Confirm
    Then I expand the "<speciality>" sidebar
    And I add a New Event "<event>"

    Then I select an Anaesthetist "no"
    #And I select Satisfaction levels of Pain "2" Nausea "3"

    And I tick the Vomited checkbox

    Then I select Vital Signs of Respiratory Rate "3" Oxygen Saturation "3" Systolic Blood Pressure "4"
    And I select Vital Signs of Body Temperature "5" and Heart Rate "2" Conscious Level AVPU "2"

    Then I enter Comments "This test is for Site 2 Kings, Firm 3 Anderson Glaucoma"

    And I select the Yes option for Ready to Discharge

    Then I Save the Event

  Examples:
  |uname|pwd  |site       |firm                           |hospNum|speciality    |event       |
  |admin|admin|Bridge Lane|Angela Glasby (Medical Retinal)|1009465|medicalRetinal|Satisfaction|

  @SetupAndCat
  Scenario Outline: Environment Setup Anderson Cataract

    Given I am on the OpenEyes "master" homepage
    And I enter login credentials "<uname>" and "<pwd>"
    And I select Site "<site>"
    Then I select a firm of "<firm>"

    Then I search for hospital number "<hospNum>"

    Then I select Add First New Episode and Confirm
    Then I expand the "<speciality>" sidebar

    And I add a New Event "<event>"

    Then I select an Anaesthetist "no"
    #And I select Satisfaction levels of Pain "2" Nausea "3"

    And I tick the Vomited checkbox

    Then I select Vital Signs of Respiratory Rate "3" Oxygen Saturation "3" Systolic Blood Pressure "4"
    And I select Vital Signs of Body Temperature "5" and Heart Rate "2" Conscious Level AVPU "2"

    Then I enter Comments "This test is for Site 2 Kings, Firm 3 Anderson Glaucoma"

    And I select the Yes option for Ready to Discharge

    Then I Save the Event

    Examples:
      |uname|pwd  |site   |firm                    |hospNum|speciality|event       |
      |admin|admin|Barking|Cataract firm (Cataract)|1009465|cataract  |Satisfaction|

  Scenario Outline: Environment Setup Anderson Glaucoma

    Given I am on the OpenEyes "master" homepage
    And I enter login credentials "<uname>" and "<pwd>"
    And I select Site "<site>"
    Then I select a firm of "<firm>"

    Then I search for hospital number "<hospNum>"

    Then I select the Latest Event
    Then I expand the "<speciality>" sidebar

    And I add a New Event "<event>"

    Then I select an Anaesthetist "no"
    #And I select Satisfaction levels of Pain "2" Nausea "3"

    And I tick the Vomited checkbox

    Then I select Vital Signs of Respiratory Rate "3" Oxygen Saturation "3" Systolic Blood Pressure "4"
    And I select Vital Signs of Body Temperature "5" and Heart Rate "2" Conscious Level AVPU "2"

    Then I enter Comments "This test is for Site 2 Kings, Firm 3 Anderson Glaucoma"

    And I select the Yes option for Ready to Discharge

    Then I Save the Event

    Examples:
      |uname|pwd  |site  |firm                      |hospNum|speciality|event       |
      |admin|admin|Ealing|Coral Woodhouse (Glaucoma)|1009465|cataract  |Satisfaction|


#  Scenario: Environment Setup Support Firm
#
#    Given I am on the OpenEyes "master" homepage
#    And I enter login credentials "admin" and "admin"
#    And I select Site "2"
#    Then I select a firm of "5"
#
#    Then I search for hospital number "1009465"
#
#    Then I select the Latest Event
#    And I select Add Episode from the sidebar
#    Then I expand the Support Firm sidebar
#    And I add a New Event "Correspondence"
#
#    Then I select Site ID "1"
#    And I select Address Target "Gp1"
#
#    And I select Clinic Date "7"
#
#    Then I choose an Introduction of "site21"
#    And I add Findings of "examination334"
#    And I choose a Diagnosis of "site541"
#    Then I choose a Management of "site181"
#    And I choose Drugs "site301"
#    Then I choose Outcome "site341"
#
#
#
#    And I add a New Enclosure
#
#    Then I Save the Correspondence Draft




