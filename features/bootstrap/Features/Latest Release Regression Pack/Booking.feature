@confidence
Feature: Basic booking Time slot Available INSIDE RTT
  In order to book visits of my patients
  As a system user
  I need to be able to schedule or reschedule an Adult operation

    Scenario Outline: Route 1: Successfully scheduling an adult operation that does not need a consultant or an anaesthetist

      Given I enter login credentials "<uname>" and "<pwd>"
      And I select Site "<siteName/Number>"
      Then I select a firm of "<firmName/Number>"
      And there is an adult patient with operation that does not need a consultant or an anaesthetist
      When I follow "Partial bookings waiting list"
      And I select awaiting patient from the waiting list
      Then I expand the "<speciality>" sidebar
      And I add a New Event "<event>"

      Then I select Diagnosis Eyes of "<diagEye>"
      And I select a Diagnosis of "<diag>"
      Then I select Operation Eyes of "<opEye>"
      And I select a Procedure of "<procedure>"

      Then I select No to Consultant required

      And I select a Anaesthetic type "<ASType>"

      Then I select Yes to a Post Operative Stay
      Then I select No to a Post Operative Stay

      And I select a Operation Site of "<opSite>"

      Then I select a Priority of Urgent

      And I select a decision date of "<decDate>"

      Then I select Save and Schedule now
      And I click on available date in the calendar
      And I select available theatre session from the list
      And I press "Confirm slot"
      Then I should see "Operation booking (Scheduled)"

      Examples:
      |uname|pwd  |siteName/Number|firmName/Number|speciality|event    |diag     |diagEye|opEye|procedure|ASType |opSite|decDate|
      |admin|admin|1              |1              |Cataract  |OpBooking|267626000|Left   |Left |41       |Topical|1     |14     |

    Scenario Outline: Route 2: Successfully scheduling an adult operation that does need a consultant but no anaesthetist

      Given I enter login credentials "<uname>" and "<pwd>"
      And I select Site "<siteName/Number>"
      Then I select a firm of "<firmName/Number>"
      And there is an adult patient with operation that does need a consultant but no anaesthetist
      When I follow "Partial bookings waiting list"
      And I select awaiting patient from the waiting list
      Then I expand the "<speciality>" sidebar
      And I add a New Event "<event>"

      Then I select Diagnosis Eyes of "<diagEye>"
      And I select a Diagnosis of "<diag>"
      Then I select Operation Eyes of "<opEye>"
      And I select a Procedure of "<procedure>"

      Then I select No to Consultant required

      And I select a Anaesthetic type "<ASType>"

      Then I select Yes to a Post Operative Stay
      Then I select No to a Post Operative Stay

      And I select a Operation Site of "<opSite>"

      Then I select a Priority of Urgent

      And I select a decision date of "<decDate>"

      Then I select Save and Schedule now

      And I click on available date in the calendar
      And I select available theatre session from the list
      And I press "Confirm slot"
      Then I should see "Operation booking (Scheduled)"

      Examples:
        |uname|pwd  |siteName/Number|firmName/Number|speciality|event    |diag     |diagEye|opEye|procedure|ASType |opSite|decDate|
        |admin|admin|1              |1              |Cataract  |OpBooking|267626000|Left   |Left |41       |Topical|1     |14     |

    Scenario Outline: Route 3: Successfully scheduling an adult operation that does not need a consultant but anaesthetist with no GA

      Given I enter login credentials "<uname>" and "<pwd>"
      And I select Site "<siteName/Number>"
      Then I select a firm of "<firmName/Number>"
      And there is an adult patient with operation that does not need a consultant but anaesthetist with no GA
      When I follow "Partial bookings waiting list"
      And I select awaiting patient from the waiting list
      Then I expand the "<speciality>" sidebar
      And I add a New Event "<event>"

      Then I select Diagnosis Eyes of "<diagEye>"
      And I select a Diagnosis of "267626000"
      Then I select Operation Eyes of "<opEye>"
      And I select a Procedure of "<procedure>"

      Then I select No to Consultant required

      And I select a Anaesthetic type "<ASType>"

      Then I select Yes to a Post Operative Stay
      Then I select No to a Post Operative Stay

      And I select a Operation Site of "<opSite>"

      Then I select a Priority of Urgent

      And I select a decision date of "<decDate>"

      Then I select Save and Schedule now

      And I click on available date in the calendar
      And I select available theatre session from the list
      And I press "Confirm slot"
      Then I should see "Operation booking (Scheduled)"

      Examples:

        |uname|pwd  |siteName/Number|firmName/Number|speciality|event    |diagEye|diag     |opEye|procedure|ASType |opSite|decDate|
        |admin|admin|1              |1              |Cataract  |OpBooking|Left   |267626000|Left |41       |Topical|1     |14     |

    Scenario Outline: Route 4: Successfully scheduling an adult operation that does not need a consultant but anaesthetist with GA

      Given I enter login credentials "<uname>" and "<pwd>"
      And I select Site "<siteName/Number>"
      Then I select a firm of "<firmName/Number>"
      And there is an adult patient with operation that does not need a consultant but anaesthetist with GA
      When I follow "Partial bookings waiting list"
      And I select awaiting patient from the waiting list
      Then I expand the "<speciality>" sidebar
      And I add a New Event "<event>"

      Then I select Diagnosis Eyes of "<diagEye>"
      And I select a Diagnosis of "<diag>"
      Then I select Operation Eyes of "<opEye>"
      And I select a Procedure of "<procedure>"

      Then I select No to Consultant required

      And I select a Anaesthetic type "<ASType>"

      Then I select Yes to a Post Operative Stay
      Then I select No to a Post Operative Stay

      And I select a Operation Site of "<opSite>"

      Then I select a Priority of Urgent

      And I select a decision date of "<decDate>"

      Then I select Save and Schedule now

      And I click on available date in the calendar
      And I select available theatre session from the list
      And I press "Confirm slot"
      Then I should see "Operation booking (Scheduled)"

      Examples:

        |uname|pwd  |siteName/Number|firmName/Number|speciality|event    |diagEye|diag     |opEye|procedure|ASType |opSite|decDate|
        |admin|admin|1              |1              |Cataract  |OpBooking|Left   |267626000|Left |41       |Topical|1     |14     |

    Scenario Outline: Route 5: Successfully scheduling an adult operation that does need a consultant and anaesthetist with no GA

      Given I enter login credentials "<uname>" and "<pwd>"
      And I select Site "<siteName/Number>"
      Then I select a firm of "<firmName/Number>"
      And there is an adult patient with operation that does need a consultant and anaesthetist with no GA
      When I follow "Partial bookings waiting list"
      And I select awaiting patient from the waiting list
      When I follow "Partial bookings waiting list"
      And I select awaiting patient from the waiting list
      Then I expand the "<speciality>" sidebar
      And I add a New Event "<event>"

      Then I select Diagnosis Eyes of "<diagEye>"
      And I select a Diagnosis of "<diag>"
      Then I select Operation Eyes of "<opEye>"
      And I select a Procedure of "<procedure>"

      Then I select No to Consultant required

      And I select a Anaesthetic type "<ASType>"

      Then I select Yes to a Post Operative Stay
      Then I select No to a Post Operative Stay

      And I select a Operation Site of "<opSite>"

      Then I select a Priority of Urgent

      And I select a decision date of "<decDate>"

      Then I select Save and Schedule now

      And I click on available date in the calendar
      And I select available theatre session from the list
      And I press "Confirm slot"
      Then I should see "Operation booking (Scheduled)"

      Examples:

        |uname|pwd  |siteName/Number|firmName/Number|speciality|event    |diagEye|diag     |opEye|procedure|ASType |opSite|decDate|
        |admin|admin|1              |1              |Cataract  |OpBooking|Left   |267626000|Left |41       |Topical|1     |14     |

    Scenario Outline: Route 6: Successfully scheduling an adult operation that does need a consultant and anaesthetist with GA

      Given I enter login credentials "<uname>" and "<pwd>"
      And I select Site "<siteName/Number>"
      Then I select a firm of "<firmName/Number>"
      And there is an adult patient with operation that does need a consultant and anaesthetist with GA
      When I follow "Partial bookings waiting list"
      And I select awaiting patient from the waiting list
      Then I expand the "<speciality>" sidebar
      And I add a New Event "<event>"

      Then I select Diagnosis Eyes of "<diagEye>"
      And I select a Diagnosis of "<diag>"
      Then I select Operation Eyes of "<opEye>"
      And I select a Procedure of "<procedure>"

      Then I select No to Consultant required

      And I select a Anaesthetic type "<ASType>"

      Then I select Yes to a Post Operative Stay
      Then I select No to a Post Operative Stay

      And I select a Operation Site of "<opSite>"

      Then I select a Priority of Urgent

      And I select a decision date of "<decDate>"

      Then I select Save and Schedule now

      And I click on available date in the calendar
      And I select available theatre session from the list
      And I press "Confirm slot"
      Then I should see "Operation booking (Scheduled)"

      Examples:

        |uname|pwd  |siteName/Number|firmName/Number|speciality|event    |diagEye|diag     |opEye|procedure|ASType |opSite|decDate|
        |admin|admin|1              |1              |Cataract  |OpBooking|Left   |267626000|Left |41       |Topical|1     |14     |

  Scenario Outline: Route 7: Successfully scheduling a child operation that does not need a consultant or an anaesthetist

    Given I enter login credentials "<uname>" and "<pwd>"
    And I select Site "<siteName/Number>"
    Then I select a firm of "<firmName/Number>"
    And there is a child patient with operation that does not need a consultant or an anaesthetist
    When I follow "Partial bookings waiting list"
    And I select awaiting patient from the waiting list
    Then I expand the "<speciality>" sidebar
    And I add a New Event "<event>"

    Then I select Diagnosis Eyes of "<diagEye>"
    And I select a Diagnosis of "<diag>"
    Then I select Operation Eyes of "<opEye>"
    And I select a Procedure of "<procedure>"

    Then I select No to Consultant required

    And I select a Anaesthetic type "<ASType>"

    Then I select Yes to a Post Operative Stay
    Then I select No to a Post Operative Stay

    And I select a Operation Site of "<opSite>"

    Then I select a Priority of Urgent

    And I select a decision date of "<decDate>"

    Then I select Save and Schedule now

    And I click on available date in the calendar
    And I select available theatre session from the list
    And I press "Confirm slot"
    Then I should see "Operation booking (Scheduled)"

    Examples:

      |uname|pwd  |siteName/Number|firmName/Number|speciality|event    |diagEye|diag     |opEye|procedure|ASType |opSite|decDate|
      |admin|admin|1              |1              |Cataract  |OpBooking|Left   |267626000|Left |41       |Topical|1     |14     |

#
  Scenario Outline: Route 8: Successfully scheduling a child operation that does need a consultant but no anaesthetist

    Given I enter login credentials "<uname>" and "<pwd>"
    And I select Site "<siteName/Number>"
    Then I select a firm of "<firmName/Number>"
    And there is a child patient with operation that does need a consultant but no anaesthetist
    When I follow "Partial bookings waiting list"
    And I select awaiting patient from the waiting list
    Then I expand the "<speciality>" sidebar
    And I add a New Event "<event>"

    Then I select Diagnosis Eyes of "<diagEye>"
    And I select a Diagnosis of "<diag>"
    Then I select Operation Eyes of "<opEye>"
    And I select a Procedure of "<procedure>"

    Then I select No to Consultant required

    And I select a Anaesthetic type "<ASType>"

    Then I select Yes to a Post Operative Stay
    Then I select No to a Post Operative Stay

    And I select a Operation Site of "<opSite>"

    Then I select a Priority of Urgent

    And I select a decision date of "<decDate>"

    Then I select Save and Schedule now

    And I click on available date in the calendar
    And I select available theatre session from the list
    And I press "Confirm slot"
    Then I should see "Operation booking (Scheduled)"

    Examples:

      |uname|pwd  |siteName/Number|firmName/Number|speciality|event    |diagEye|diag     |opEye|procedure|ASType |opSite|decDate|
      |admin|admin|1              |1              |Cataract  |OpBooking|Left   |267626000|Left |41       |Topical|1     |14     |


  Scenario Outline: Route 9: Successfully scheduling a child operation that does not need a consultant but anaesthetist with no GA

    Given I enter login credentials "<uname>" and "<pwd>"
    And I select Site "<siteName/Number>"
    Then I select a firm of "<firmName/Number>"
    And there is a child patient with operation that does not need a consultant but anaesthetist with no GA
    When I follow "Partial bookings waiting list"
    And I select awaiting patient from the waiting list
    Then I expand the "<speciality>" sidebar
    And I add a New Event "<event>"

    Then I select Diagnosis Eyes of "<diagEye>"
    And I select a Diagnosis of "<diag>"
    Then I select Operation Eyes of "<opEye>"
    And I select a Procedure of "<procedure>"

    Then I select No to Consultant required

    And I select a Anaesthetic type "<ASType>"

    Then I select Yes to a Post Operative Stay
    Then I select No to a Post Operative Stay

    And I select a Operation Site of "<opSite>"

    Then I select a Priority of Urgent

    And I select a decision date of "<decDate>"

    Then I select Save and Schedule now

    And I click on available date in the calendar
    And I select available theatre session from the list
    And I press "Confirm slot"
    Then I should see "Operation booking (Scheduled)"

    Examples:

      |uname|pwd  |siteName/Number|firmName/Number|speciality|event    |diagEye|diag     |opEye|procedure|ASType |opSite|decDate|
      |admin|admin|1              |1              |Cataract  |OpBooking|Left   |267626000|Left |41       |Topical|1     |14     |


  Scenario Outline: Route 10: Successfully scheduling a child operation that does not need a consultant but anaesthetist with GA

    Given I enter login credentials "<uname>" and "<pwd>"
    And I select Site "<siteName/Number>"
    Then I select a firm of "<firmName/Number>"
    And there is a child patient with operation that does not need a consultant but anaesthetist with GA
    When I follow "Partial bookings waiting list"
    And I select awaiting patient from the waiting list
    Then I expand the "<speciality>" sidebar
    And I add a New Event "<event>"

    Then I select Diagnosis Eyes of "<diagEye>"
    And I select a Diagnosis of "<diag>"
    Then I select Operation Eyes of "<opEye>"
    And I select a Procedure of "<procedure>"

    Then I select No to Consultant required

    And I select a Anaesthetic type "<ASType>"

    Then I select Yes to a Post Operative Stay
    Then I select No to a Post Operative Stay

    And I select a Operation Site of "<opSite>"

    Then I select a Priority of Urgent

    And I select a decision date of "<decDate>"

    Then I select Save and Schedule now

    And I click on available date in the calendar
    And I select available theatre session from the list
    And I press "Confirm slot"
    Then I should see "Operation booking (Scheduled)"

    Examples:

      |uname|pwd  |siteName/Number|firmName/Number|speciality|event    |diagEye|diag     |opEye|procedure|ASType |opSite|decDate|
      |admin|admin|1              |1              |Cataract  |OpBooking|Left   |267626000|Left |41       |Topical|1     |14     |

#
  Scenario Outline: Route 11: Successfully scheduling a child operation that does need a consultant and anaesthetist with no GA

    Given I enter login credentials "<uname>" and "<pwd>"
    And I select Site "<siteName/Number>"
    Then I select a firm of "<firmName/Number>"
    And there is a child patient with operation that does need a consultant and anaesthetist with no GA
    When I follow "Partial bookings waiting list"
    And I select awaiting patient from the waiting list
    Then I expand the "<speciality>" sidebar
    And I add a New Event "<event>"

    Then I select Diagnosis Eyes of "<diagEye>"
    And I select a Diagnosis of "<diag>"
    Then I select Operation Eyes of "<opEye>"
    And I select a Procedure of "<procedure>"

    Then I select No to Consultant required

    And I select a Anaesthetic type "<ASType>"

    Then I select Yes to a Post Operative Stay
    Then I select No to a Post Operative Stay

    And I select a Operation Site of "<opSite>"

    Then I select a Priority of Urgent

    And I select a decision date of "<decDate>"

    Then I select Save and Schedule now

    And I click on available date in the calendar
    And I select available theatre session from the list
    And I press "Confirm slot"
    Then I should see "Operation booking (Scheduled)"

    Examples:

      |uname|pwd  |siteName/Number|firmName/Number|speciality|event    |diagEye|diag     |opEye|procedure|ASType |opSite|decDate|
      |admin|admin|1              |1              |Cataract  |OpBooking|Left   |267626000|Left |41       |Topical|1     |14     |


  Scenario Outline: Route 12: Successfully scheduling a child operation that does need a consultant and anaesthetist with GA

    Given I enter login credentials "<uname>" and "<pwd>"
    And I select Site "<siteName/Number>"
    Then I select a firm of "<firmName/Number>"
    And there is a child patient with operation that does need a consultant and anaesthetist with GA
    When I follow "Partial bookings waiting list"
    And I select awaiting patient from the waiting list
    Then I expand the "<speciality>" sidebar
    And I add a New Event "<event>"

    Then I select Diagnosis Eyes of "<diagEye>"
    And I select a Diagnosis of "<diag>"
    Then I select Operation Eyes of "<opEye>"
    And I select a Procedure of "<procedure>"

    Then I select No to Consultant required

    And I select a Anaesthetic type "<ASType>"

    Then I select Yes to a Post Operative Stay
    Then I select No to a Post Operative Stay

    And I select a Operation Site of "<opSite>"

    Then I select a Priority of Urgent

    And I select a decision date of "<decDate>"

    Then I select Save and Schedule now

    And I click on available date in the calendar
    And I select available theatre session from the list
    And I press "Confirm slot"
    Then I should see "Operation booking (Scheduled)"

  Examples:

    |uname|pwd  |siteName/Number|firmName/Number|speciality|event    |diagEye|diag     |opEye|procedure|ASType |opSite|decDate|
    |admin|admin|1              |1              |Cataract  |OpBooking|Left   |267626000|Left |41       |Topical|1     |14     |


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
