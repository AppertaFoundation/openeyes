@regression
Feature: Anaesthetic Satisfaction Audit Regression Tests
  Regression coverage of this event is approx 90%
  Across 2 Sites and 4 Firms

  Scenario: Login and create a Anaesthetic Satisfaction Audit Regression Test Route 1: Site 2 Kings, Firm 3 Anderson Glaucoma

    Given I am on the OpenEyes "master" homepage
    And I enter login credentials "admin" and "admin"
    And I select Site "2"
    Then I select a firm of "3"

    Then I search for patient name last name "Coffin," and first name "Violet"

    Then I select the Latest Event

    Then I expand the Glaucoma sidebar
    And I add a New Event "Satisfaction"

    Then I select an Anaesthetist "no"
    And I select Satisfaction levels of Pain "2" Nausea "3"

    And I tick the Vomited checkbox

    Then I select Vital Signs of Respiratory Rate "3" Oxygen Saturation "3" Systolic Blood Pressure "4"
    And I select Vital Signs of Body Temperature "5" and Heart Rate "2" Conscious Level AVPU "2"

    Then I enter Comments "This test is for Site 2 Kings, Firm 3 Anderson Glaucoma"

    And I select the Yes option for Ready to Discharge

    Then I Save the Event

  Scenario: Login and create a Anaesthetic Satisfaction Audit Regression Test Route 2: Site 1 Queens, Firm 1 Anderson Cataract

    Given I am on the OpenEyes "master" homepage
    And I enter login credentials "admin" and "admin"
    And I select Site "1"
    Then I select a firm of "1"

    Then I search for hospital number "1009465"

    Then I select the Latest Event

    Then I expand the Cataract sidebar
    And I add a New Event "Satisfaction"

    Then I select an Anaesthetist "non"
    And I select Satisfaction levels of Pain "5" Nausea "1"

    And I untick the Vomited checkbox

    Then I select Vital Signs of Respiratory Rate "4" Oxygen Saturation "1" Systolic Blood Pressure "5"
    And I select Vital Signs of Body Temperature "1" and Heart Rate "5" Conscious Level AVPU "5"

    Then I enter Comments "This test is for Site 1 Queens, Firm 1 Anderson Cataract"

    And I select the No option for Ready to Discharge

    Then I Save the Event

  Scenario: Login and create a Anaesthetic Satisfaction Audit Regression Test Route 3: Site 1 Queens, Firm 2 Broom Glaucoma

    Given I am on the OpenEyes "master" homepage
    And I enter login credentials "admin" and "admin"
    And I select Site "1"
    Then I select a firm of "2"

    Then I search for patient name last name "Coffin," and first name "Violet"

    Then I select the Latest Event

    Then I expand the Glaucoma sidebar
    And I add a New Event "Satisfaction"

    Then I select an Anaesthetist "no"
    And I select Satisfaction levels of Pain "7" Nausea "2"

    And I tick the Vomited checkbox

    Then I select Vital Signs of Respiratory Rate "1" Oxygen Saturation "3" Systolic Blood Pressure "3"
    And I select Vital Signs of Body Temperature "4" and Heart Rate "5" Conscious Level AVPU "2"

    Then I enter Comments "This test is for Site 1 Queens, Firm 2 Broom Glaucoma"

    And I select the No option for Ready to Discharge

    Then I Save the Event

  Scenario: Login and create a Anaesthetic Satisfaction Audit Regression Test Route 4: Site 2 Kings, Firm 4 Medical Retinal

    Given I am on the OpenEyes "master" homepage
    And I enter login credentials "admin" and "admin"
    And I select Site "2"
    Then I select a firm of "4"

    Then I search for patient name last name "Coffin," and first name "Violet"

    Then I select the Latest Event

    Then I expand the Medical Retinal sidebar
    And I add a New Event "Satisfaction"

    Then I select an Anaesthetist "non"
    And I select Satisfaction levels of Pain "0" Nausea "0"

    And I untick the Vomited checkbox

    Then I select Vital Signs of Respiratory Rate "2" Oxygen Saturation "4" Systolic Blood Pressure "6"
    And I select Vital Signs of Body Temperature "3" and Heart Rate "2" Conscious Level AVPU "2"

    Then I enter Comments "This test is for Site 2 Kings, Firm 4 Medical Retinal"

    And I select the No option for Ready to Discharge

    Then I Save the Event

  # Respiratory Rate
  #"1" = 8 or less
  #"2" = 9-11
  #"3" = 12-20
  #"4" = 21-24
  #"5" = 25 or above

  # Oxygen Saturation
  #"1" = 85 or lower
  #"2" = 85-89
  #"3" = 90-94
  #"4" = 95 or above

  # Systolic Blood Pressure
  #"1">70 or lower
  #"2">71-80
  #"3">81-95
  #"4">96-189
  #"5">190-199
  #"6">200 or above

  # Body Temperature
  #"1">35 or lower
  #"2">35.1-36
  #"3">36.1-37.4
  #"4">37.5-38.4
  #"5">38.5-38.9
  #"6">39 or above

  # Heart Rate
  #"1">40 or lower
  #"2">41-50
  #"3">51-100
  #"4">101-110
  #"5">111-129
  #"6">130 or above

  # Conscious Level AVPU
  #"2">Alert
  #"3">Responds to VERBAL commands
  #"4">Responds to PAIN
  #"5">Unresponsive