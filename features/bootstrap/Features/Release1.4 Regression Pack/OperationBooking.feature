@operationbooking @regression
Feature: Create New Operation Booking Event
  In order to cover every possible route throughout the site
  As an automation tester
  I want to build a template with supporting code for each web page

  Scenario: Route 1: Login and create a Operation Booking

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

    Then I confirm the operation slot

