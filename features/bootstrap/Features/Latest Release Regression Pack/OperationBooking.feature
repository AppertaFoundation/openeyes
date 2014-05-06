@operationbooking @regression
Feature: Create New Operation Booking Event
         Regression coverage of this event is approx 50%

  Scenario: Route 1: Login and create a Operation Booking Anderson Glaucoma

    Given I am on the OpenEyes "master" homepage
    And I enter login credentials "admin" and "admin"
    And I select Site "2"
    Then I select a firm of "3"

    Then I search for hospital number "1009465"

    Then I select the Latest Event

    Then I expand the Glaucoma sidebar
    And I add a New Event "OpBooking"

    Then I select Diagnosis Eyes of "Left"
    And I select a Diagnosis of "30041005"
    Then I select Operation Eyes of "Left"
    And I select a Procedure of "41"

    Then I select No to Consultant required

    And I select a Anaesthetic type "Topical"

    Then I select Yes to a Post Operative Stay
    Then I select No to a Post Operative Stay

    And I select a Operation Site of "1"

    Then I select a Priority of Urgent

    And I select a decision date of "14"

    Then I select Save and Schedule now

    And I select an Available theatre slot date
    And I select an Available session time

#    Then I select a Ward of "2"
    And enter an admission time of "11:20"
    Then I add Session comments of "Session Comments Session Comments Session Comments Session Comments Session Comments"
    And I add Operation comments of "Operation Comments Operation Comments Operation Comments Operation Comments Operation Comments"
    And enter RTT comments of "RTT Comments RTT Comments RTT Comments RTT Comments RTT Comments RTT Comments RTT Comments"

    Then I confirm the operation slot

  Scenario: Route 2: Login and create a Operation Booking Anderson Cataract

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

  Scenario: Route 3: Login and create a Operation Booking Anderson Medical Retinal

    Given I am on the OpenEyes "master" homepage
    And I enter login credentials "admin" and "admin"
    And I select Site "2"
    Then I select a firm of "4"

    Then I search for hospital number "1009465"

    Then I select the Latest Event

    Then I expand the Medical Retinal sidebar
    And I add a New Event "OpBooking"

    Then I select Diagnosis Eyes of "Both"
    And I select a Diagnosis of "255024002"
    Then I select Operation Eyes of "Both"
    And I select a Procedure of "327"

    Then I select No to Consultant required

    And I select a Anaesthetic type "LAC"

    Then I select No to a Post Operative Stay

    And I select a Operation Site of "2"

    Then I select a Priority of Urgent

    And I select a decision date of "3"

    Then I add comments of "Insert test comments here"

    Then I select Save and Schedule now

    Then I change the Viewing Schedule to Emergency List

    Then I select an Available theatre slot date of next "Saturday"
    And I select an Available session time

    Then I add Session comments of "Insert session comments here"
    And I add Operation comments of "Insert operation comments here"

    Then I confirm the operation slot
