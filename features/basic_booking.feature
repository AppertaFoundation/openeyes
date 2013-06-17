@javascript
Feature: Basic booking
  In order to book visits of my patients
  As a system user
  I need to be able to schedule or reschedule an Adult operation

  Scenario: Successfully scheduling an operation that does not need a consultant or an anaesthetist
    Given I am logged in into the system
    And I am a cataract specialist
    And there is an adult patient with operation that does not need a consultant or an anaesthetist
    When I follow "Partial bookings waiting list"
    And I select awaiting patient from the waiting list
    And I follow "Schedule now"
    And I click on available date in the calendar
    And I select available theatre session from the list
    And I press "Confirm slot"
    Then I should see "Operation booking (Scheduled)"

  Scenario: Seeing list of existing sessions when choosing date from the calendar


  Scenario: Successfully scheduling an operation that does need a consultant but no anaesthetist
    Given I am logged in into the system
    And I am a strabismus specialist
    And there is an adult patient with operation that does need a consultant but no anaesthetist
    When I follow "Partial bookings waiting list"
    And I select awaiting patient from the waiting list
    And I follow "Schedule now"
    And I click on available date in the calendar
    And I select available theatre session from the list
    And I press "Confirm slot"
    Then I should see "Operation booking (Scheduled)"