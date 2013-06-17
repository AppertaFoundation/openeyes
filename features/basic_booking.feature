Feature: Basic booking
  In order to book visits of my patients
  As a system user
  I need to be able to schedule or reschedule an Adult operation

  Scenario: Successfully scheduling an operation
    Given I am logged in into the system
    And there is an adult patient with operation
    But this operation does not need a consultant or an anaesthetist
    When I follow "Partial bookings waiting list"
    And I select awaiting patient from the waiting list
    And I press "Schedule now"
    And I select a date from the calendar
    And I select available theatre session from the list
    And I press "Confirm slot"
    Then operation should be assigned to the theatre session

  Scenario: Seeing list of existing sessions when choosing date from the calendar
