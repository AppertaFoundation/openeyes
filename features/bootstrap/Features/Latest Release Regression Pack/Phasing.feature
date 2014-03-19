  @phasing @regression
  Feature: Create New Phasing Event
           Regression coverage of this event is 100%

  Scenario: Route 1: Login and create a Phasing Event

    Given I am on the OpenEyes "master" homepage
    And I enter login credentials "admin" and "admin"
    And I select Site "1"
    Then I select a firm of "3"

    Then I search for hospital number "1009465 "

    Then I select the Latest Event

    Then I expand the Glaucoma sidebar
    And I add a New Event "Phasing"

    Then I choose a right eye Intraocular Pressure Instrument  of "1"

    And I choose right eye Dilation of Yes

    Then I choose a right eye Intraocular Pressure Reading Time of "14:00"
    Then I choose a right eye Intraocular Pressure Reading of "5"
    And I add right eye comments of "Right eye comments here"

    Then I choose a left eye Intraocular Pressure Instrument  of "5"

    And I choose left eye Dilation of Yes

    Then I choose a left eye Intraocular Pressure Reading Time of "14:42"
    Then I choose a left eye Intraocular Pressure Reading of "7"
    And I add left eye comments of "Left eye comments here"

    Then I Save the Phasing Event and confirm it has been created successfully

    Scenario: Route 2: Login and create a Phasing Event

      Given I am on the OpenEyes "master" homepage
      And I enter login credentials "admin" and "admin"
      And I select Site "1"
      Then I select a firm of "1"

      Then I search for hospital number "1009465 "

      Then I select the Latest Event

      Then I expand the Cataract sidebar
      And I add a New Event "Phasing"

      Then I choose a right eye Intraocular Pressure Instrument  of "3"

      And I choose right eye Dilation of No

      Then I choose a right eye Intraocular Pressure Reading Time of "21:00"
      Then I choose a right eye Intraocular Pressure Reading of "14"
      And I add right eye comments of "Right eye comments here"

      Then I choose a left eye Intraocular Pressure Instrument  of "4"

      And I choose left eye Dilation of Yes

      Then I choose a left eye Intraocular Pressure Reading Time of "04:42"
      Then I choose a left eye Intraocular Pressure Reading of "12"
      And I add left eye comments of "Left eye comments here"

      Then I Save the Phasing Event and confirm it has been created successfully

    Scenario: Route 3: Login and create a Phasing Event

      Given I am on the OpenEyes "master" homepage
      And I enter login credentials "admin" and "admin"
      And I select Site "2"
      Then I select a firm of "3"

      Then I search for hospital number "1009465 "

      Then I select the Latest Event

      Then I expand the Glaucoma sidebar
      And I add a New Event "Phasing"

      Then I choose a right eye Intraocular Pressure Instrument  of "3"

      And I choose right eye Dilation of No

      Then I choose a right eye Intraocular Pressure Reading Time of "08:00"
      Then I choose a right eye Intraocular Pressure Reading of "5"
      And I add right eye comments of "Right eye comments here"

      Then I choose a left eye Intraocular Pressure Instrument  of "3"

      And I choose left eye Dilation of Yes

      Then I choose a left eye Intraocular Pressure Reading Time of "14:42"
      Then I choose a left eye Intraocular Pressure Reading of "9"
      And I add left eye comments of "Left eye comments here"

      Then I Save the Phasing Event and confirm it has been created successfully

    Scenario: Route 4: Login and create a Phasing Event

      Given I am on the OpenEyes "master" homepage
      And I enter login credentials "admin" and "admin"
      And I select Site "1"
      Then I select a firm of "4"

      Then I search for hospital number "1009465 "

      Then I select the Latest Event

      Then I expand the Medical Retinal sidebar
      And I add a New Event "Phasing"

      Then I choose a right eye Intraocular Pressure Instrument  of "4"

      And I choose right eye Dilation of No

      Then I choose a right eye Intraocular Pressure Reading Time of "08:00"
      Then I choose a right eye Intraocular Pressure Reading of "5"
      And I add right eye comments of "Right eye comments here"

      Then I choose a left eye Intraocular Pressure Instrument  of "1"

      And I choose left eye Dilation of Yes

      Then I choose a left eye Intraocular Pressure Reading Time of "14:42"
      Then I choose a left eye Intraocular Pressure Reading of "9"
      And I add left eye comments of "Left eye comments here"

      Then I add a new Left Reading
      Then I choose a second left eye Intraocular Pressure Reading Time of "11:07"
      Then I choose a second left eye Intraocular Pressure Reading of "6"

      Then I add a new Right Reading
#      Then I choose a second right eye Intraocular Pressure Reading Time of "15:43"
      Then I choose a second right eye Intraocular Pressure Reading of "20"

      Then I remove the last Right Reading
      Then I remove the last Left Reading

      Then I Save the Phasing Event and confirm it has been created successfully

    Scenario: Route 5: Login and create a Phasing Event
              Invalid time entry validation tests (Intraocular Pressing Reading Times)

      Given I am on the OpenEyes "master" homepage
      And I enter login credentials "admin" and "admin"
      And I select Site "1"
      Then I select a firm of "3"

      Then I search for hospital number "1009465 "

      Then I select the Latest Event

      Then I expand the Glaucoma sidebar
      And I add a New Event "Phasing"

      Then I choose a right eye Intraocular Pressure Instrument  of "1"

      And I choose right eye Dilation of Yes

      Then I choose a right eye Intraocular Pressure Reading Time of "25:12"
      Then I choose a right eye Intraocular Pressure Reading of "5"
      And I add right eye comments of "Right eye comments here"

      Then I choose a left eye Intraocular Pressure Instrument  of "5"

      And I choose left eye Dilation of Yes

      Then I choose a left eye Intraocular Pressure Reading Time of "09:67"
      Then I choose a left eye Intraocular Pressure Reading of "7"
      And I add left eye comments of "Left eye comments here"

      Then I Save the Phasing Event

      Then I Confirm that the Readings Invalid time error messages are displayed