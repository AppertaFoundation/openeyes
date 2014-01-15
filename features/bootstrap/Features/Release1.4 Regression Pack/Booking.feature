@confidence
Feature: Basic booking Time slot Available INSIDE RTT
  In order to book visits of my patients
  As a system user
  I need to be able to schedule or reschedule an Adult operation

    Scenario: Route 1: Successfully scheduling an adult operation that does not need a consultant or an anaesthetist

      Given I enter login credentials "admin" and "admin"
      And I select Site "1"
      Then I select a firm of "1"
      And there is an adult patient with operation that does not need a consultant or an anaesthetist
      When I follow "Partial bookings waiting list"
      And I select awaiting patient from the waiting list
      Then I expand the Cataract sidebar
      And I add a New Event "OpBooking"

      Then I select Diagnosis Eyes of "Left"
      And I select a Diagnosis of "267626000"
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
      And I click on available date in the calendar
      And I select available theatre session from the list
      And I press "Confirm slot"
      Then I should see "Operation booking (Scheduled)"


    Scenario: Route 2: Successfully scheduling an adult operation that does need a consultant but no anaesthetist

      Given I enter login credentials "admin" and "admin"
      And I select Site "1"
      Then I select a firm of "1"
      And there is an adult patient with operation that does need a consultant but no anaesthetist
      When I follow "Partial bookings waiting list"
      And I select awaiting patient from the waiting list
      Then I expand the Cataract sidebar
      And I add a New Event "OpBooking"

      Then I select Diagnosis Eyes of "Left"
      And I select a Diagnosis of "267626000"
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

      And I click on available date in the calendar
      And I select available theatre session from the list
      And I press "Confirm slot"
      Then I should see "Operation booking (Scheduled)"

    Scenario: Route 3: Successfully scheduling an adult operation that does not need a consultant but anaesthetist with no GA

      Given I enter login credentials "admin" and "admin"
      And I select Site "1"
      Then I select a firm of "1"
      And there is an adult patient with operation that does not need a consultant but anaesthetist with no GA
      When I follow "Partial bookings waiting list"
      And I select awaiting patient from the waiting list
      Then I expand the Cataract sidebar
      And I add a New Event "OpBooking"

      Then I select Diagnosis Eyes of "Left"
      And I select a Diagnosis of "267626000"
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

      And I click on available date in the calendar
      And I select available theatre session from the list
      And I press "Confirm slot"
      Then I should see "Operation booking (Scheduled)"

    Scenario: Route 4: Successfully scheduling an adult operation that does not need a consultant but anaesthetist with GA

      Given I enter login credentials "admin" and "admin"
      And I select Site "1"
      Then I select a firm of "1"
      And there is an adult patient with operation that does not need a consultant but anaesthetist with GA
      When I follow "Partial bookings waiting list"
      And I select awaiting patient from the waiting list
      Then I expand the Cataract sidebar
      And I add a New Event "OpBooking"

      Then I select Diagnosis Eyes of "Left"
      And I select a Diagnosis of "267626000"
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

      And I click on available date in the calendar
      And I select available theatre session from the list
      And I press "Confirm slot"
      Then I should see "Operation booking (Scheduled)"

    Scenario: Route 5: Successfully scheduling an adult operation that does need a consultant and anaesthetist with no GA

      Given I enter login credentials "admin" and "admin"
      And I select Site "1"
      Then I select a firm of "1"
      And there is an adult patient with operation that does need a consultant and anaesthetist with no GA
      When I follow "Partial bookings waiting list"
      And I select awaiting patient from the waiting list
      When I follow "Partial bookings waiting list"
      And I select awaiting patient from the waiting list
      Then I expand the Cataract sidebar
      And I add a New Event "OpBooking"

      Then I select Diagnosis Eyes of "Left"
      And I select a Diagnosis of "267626000"
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

      And I click on available date in the calendar
      And I select available theatre session from the list
      And I press "Confirm slot"
      Then I should see "Operation booking (Scheduled)"

    Scenario: Route 6: Successfully scheduling an adult operation that does need a consultant and anaesthetist with GA

      Given I enter login credentials "admin" and "admin"
      And I select Site "1"
      Then I select a firm of "1"
      And there is an adult patient with operation that does need a consultant and anaesthetist with GA
      When I follow "Partial bookings waiting list"
      And I select awaiting patient from the waiting list
      Then I expand the Cataract sidebar
      And I add a New Event "OpBooking"

      Then I select Diagnosis Eyes of "Left"
      And I select a Diagnosis of "267626000"
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

      And I click on available date in the calendar
      And I select available theatre session from the list
      And I press "Confirm slot"
      Then I should see "Operation booking (Scheduled)"

  Scenario: Route 7: Successfully scheduling a child operation that does not need a consultant or an anaesthetist

    Given I enter login credentials "admin" and "admin"
    And I select Site "1"
    Then I select a firm of "1"
    And there is a child patient with operation that does not need a consultant or an anaesthetist
    When I follow "Partial bookings waiting list"
    And I select awaiting patient from the waiting list
    Then I expand the Cataract sidebar
    And I add a New Event "OpBooking"

    Then I select Diagnosis Eyes of "Left"
    And I select a Diagnosis of "267626000"
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

    And I click on available date in the calendar
    And I select available theatre session from the list
    And I press "Confirm slot"
    Then I should see "Operation booking (Scheduled)"
#
  Scenario: Route 8: Successfully scheduling a child operation that does need a consultant but no anaesthetist

    Given I enter login credentials "admin" and "admin"
    And I select Site "1"
    Then I select a firm of "1"
    And there is a child patient with operation that does need a consultant but no anaesthetist
    When I follow "Partial bookings waiting list"
    And I select awaiting patient from the waiting list
    Then I expand the Cataract sidebar
    And I add a New Event "OpBooking"

    Then I select Diagnosis Eyes of "Left"
    And I select a Diagnosis of "267626000"
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

    And I click on available date in the calendar
    And I select available theatre session from the list
    And I press "Confirm slot"
    Then I should see "Operation booking (Scheduled)"

  Scenario: Route 9: Successfully scheduling a child operation that does not need a consultant but anaesthetist with no GA

    Given I enter login credentials "admin" and "admin"
    And I select Site "1"
    Then I select a firm of "1"
    And there is a child patient with operation that does not need a consultant but anaesthetist with no GA
    When I follow "Partial bookings waiting list"
    And I select awaiting patient from the waiting list
    Then I expand the Cataract sidebar
    And I add a New Event "OpBooking"

    Then I select Diagnosis Eyes of "Left"
    And I select a Diagnosis of "267626000"
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

    And I click on available date in the calendar
    And I select available theatre session from the list
    And I press "Confirm slot"
    Then I should see "Operation booking (Scheduled)"

  Scenario: Route 10: Successfully scheduling a child operation that does not need a consultant but anaesthetist with GA

    Given I enter login credentials "admin" and "admin"
    And I select Site "1"
    Then I select a firm of "1"
    And there is a child patient with operation that does not need a consultant but anaesthetist with GA
    When I follow "Partial bookings waiting list"
    And I select awaiting patient from the waiting list
    Then I expand the Cataract sidebar
    And I add a New Event "OpBooking"

    Then I select Diagnosis Eyes of "Left"
    And I select a Diagnosis of "267626000"
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

    And I click on available date in the calendar
    And I select available theatre session from the list
    And I press "Confirm slot"
    Then I should see "Operation booking (Scheduled)"
#
  Scenario: Route 11: Successfully scheduling a child operation that does need a consultant and anaesthetist with no GA

    Given I enter login credentials "admin" and "admin"
    And I select Site "1"
    Then I select a firm of "1"
    And there is a child patient with operation that does need a consultant and anaesthetist with no GA
    When I follow "Partial bookings waiting list"
    And I select awaiting patient from the waiting list
    Then I expand the Cataract sidebar
    And I add a New Event "OpBooking"

    Then I select Diagnosis Eyes of "Left"
    And I select a Diagnosis of "267626000"
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

    And I click on available date in the calendar
    And I select available theatre session from the list
    And I press "Confirm slot"
    Then I should see "Operation booking (Scheduled)"

  Scenario: Route 12: Successfully scheduling a child operation that does need a consultant and anaesthetist with GA

    Given I enter login credentials "admin" and "admin"
    And I select Site "1"
    Then I select a firm of "1"
    And there is a child patient with operation that does need a consultant and anaesthetist with GA
    When I follow "Partial bookings waiting list"
    And I select awaiting patient from the waiting list
    Then I expand the Cataract sidebar
    And I add a New Event "OpBooking"

    Then I select Diagnosis Eyes of "Left"
    And I select a Diagnosis of "267626000"
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

    And I click on available date in the calendar
    And I select available theatre session from the list
    And I press "Confirm slot"
    Then I should see "Operation booking (Scheduled)"

#  Examples: Waiting patients
#  | site | firm | patient               |
#  | 1    | 1    | AINSWORTH, Ruby       |
#  | 1    | 1    | BEERBOHM, Vicary      |
#  | 1    | 1    | BEWLEY, Melinda       |
#  | 1    | 1    | GOODFELLOW, Kit       |
#  | 1    | 1    | RICHARDSON, Valerie   |
#  | 1    | 1    | BESTOR, Jenny         |
#  | 1    | 1    | SAVIDGE, Kylie        |
#  | 1    | 1    | JACOBS, Eleanor       |
#  | 1    | 1    | CRESSWELL, Teresa     |
#  | 1    | 1    | WIDDRINGTON, Sophia   |
#  | 1    | 1    | WHITTINGHAM, Chet     |
#  | 1    | 1    | GOLDEN, Edmund        |
