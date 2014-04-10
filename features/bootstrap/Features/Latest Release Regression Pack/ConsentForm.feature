@consent @regression
Feature: Create New Consent Form
         Regression coverage of this event is approx 70%

  Scenario: Route 1: Login and create a new Consent Form
            Site 2: Kings
            Firm: Anderson Glaucoma
            Type 1 chosen
            2x Additional Procedures

    Given I am on the OpenEyes "master" homepage
    And I enter login credentials "admin" and "admin"
    And I select Site "2"
    Then I select a firm of "3"

    Then I search for hospital number "1009465"

    Then I select the Latest Event

    Then I expand the Glaucoma sidebar
    And I add a New Event "Consent"
    Then I select Unbooked Procedures
    Then I select Add Consent Form
    And I choose Type "1"

    Then I choose Procedure eye of "Both"
    And I choose an Anaesthetic type of LA
    And I add a common procedure of "127"

    Then I choose an additional procedure of "41"
    Then I choose an additional procedure of "42"

    Then I choose Permissions for images No

    And I select the Information leaflet checkbox

    Then I save the Consent Form and confirm it has been created successfully

  Scenario: Route 2: Login and create a new Consent Form
            Site 2: Kings
            Firm 2: Broom Glaucoma
            Type 2 chosen

    Given I am on the OpenEyes "master" homepage
    And I enter login credentials "admin" and "admin"
    And I select Site "2"
    Then I select a firm of "2"

    Then I search for hospital number "1009465"

    Then I select the Latest Event

    Then I expand the Glaucoma sidebar
    And I add a New Event "Consent"
    Then I select Unbooked Procedures
    Then I select Add Consent Form
    And I choose Type "2"

    Then I choose Procedure eye of "Left"
    And I choose an Anaesthetic type of LA
    And I add a common procedure of "129"

    Then I choose Permissions for images Yes

    Then I save the Consent Form and confirm it has been created successfully

  Scenario: Route 3: Login and create a new Consent Form
            Site 1: Queens
            Firm 4: Medical Retinal
            Type 3 chosen

    Given I am on the OpenEyes "master" homepage
    And I enter login credentials "admin" and "admin"
    And I select Site "1"
    Then I select a firm of "4"

    Then I search for hospital number "1009465"

    Then I select the Latest Event

    Then I expand the Medical Retinal sidebar
    And I add a New Event "Consent"
    Then I select Unbooked Procedures
    Then I select Add Consent Form
    And I choose Type "3"

    Then I choose Procedure eye of "Left"
    And I choose an Anaesthetic type of LA
    And I add a common procedure of "327"

    Then I choose Permissions for images Yes

    Then I save the Consent Form and confirm it has been created successfully

  Scenario: Route 4: Login and create a new Consent Form
            Site 2: Kings
            Firm 3: Anderson Glaucoma
            Type 4 chosen

    Given I am on the OpenEyes "master" homepage
    And I enter login credentials "admin" and "admin"
    And I select Site "2"
    Then I select a firm of "3"

    Then I search for hospital number "1009465"

    Then I select the Latest Event

    Then I expand the Glaucoma sidebar
    And I add a New Event "Consent"
    Then I select Unbooked Procedures
    Then I select Add Consent Form
    And I choose Type "4"

    Then I choose Procedure eye of "Left"
    And I choose an Anaesthetic type of LA
    And I add a common procedure of "129"

    Then I choose Permissions for images Yes

    Then I save the Consent Form and confirm it has been created successfully

  Scenario: Route 5: Login and create a new Consent Form
            Site 2: Kings
            Firm: Anderson Glaucoma
            Validation error messages check

    Given I am on the OpenEyes "master" homepage
    And I enter login credentials "admin" and "admin"
    And I select Site "2"
    Then I select a firm of "3"

    Then I search for hospital number "1009465"

    Then I select the Latest Event

    Then I expand the Glaucoma sidebar
    And I add a New Event "Consent"
    Then I select Unbooked Procedures
    Then I select Add Consent Form

    Then I save the Consent Form Draft
    Then I confirm that the Consent Validation error messages have been displayed

  Scenario: Route 6A: Login and create a Operation Booking for Route 6B test

    Given I am on the OpenEyes "master" homepage
    And I enter login credentials "admin" and "admin"
    And I select Site "1"
    Then I select a firm of "1"

    Then I search for hospital number "1009465"

    Then I select the Latest Event

    Then I expand the Cataract sidebar
    And I add a New Event "OpBooking"

    Then I select Diagnosis Eyes of "Right"
    And I select a Diagnosis of "24010005"
    Then I select Operation Eyes of "Right"
    And I select a Procedure of "79"
          # Right Extracapsular cataract extraction

    Then I select Yes to Consultant required

    And I select a Anaesthetic type "LA"

    Then I select Yes to a Post Operative Stay

    And I select a Operation Site of "2"

    Then I select a Priority of Routine

    And I select a decision date of "10"

    Then I add comments of "Insert test comments here"

    Then I select Save and Schedule now

    Then I select Next Month

    And I select an Available theatre slot date
    And I select an Available session time

    Then I add Session comments of "Insert session comments here"
    And I add Operation comments of "Insert operation comments here"

    Then I confirm the operation slot


  Scenario: Route 6B: Login and create a new Consent Form choosing a previously created Operation Booking (from Route 6A)
            Site 2: Kings
            Firm: Anderson Glaucoma
            Type 1 chosen
            2x Additional Procedures

    Given I am on the OpenEyes "master" homepage
    And I enter login credentials "admin" and "admin"
    And I select Site "1"
    Then I select a firm of "1"

    Then I search for hospital number "1009465"

    Then I select the Latest Event

    Then I expand the Cataract sidebar
    And I add a New Event "Consent"

    Then I select the previously created operation "booking152"

    Then I select Add Consent Form

    Then I save the Consent Form and confirm it has been created successfully