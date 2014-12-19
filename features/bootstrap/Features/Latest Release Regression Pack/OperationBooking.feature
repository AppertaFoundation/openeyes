@operationbooking @regression
Feature: Create New Operation Booking Event
         Regression coverage of this event is approx 50%

  Scenario: Route 1: Login and create a Operation Booking Anderson Glaucoma

    Given I am logged in as "admin" with site "Kings" and firm "Anderson Firm (Glaucoma)"

    Then I search for hospital number "1009465"

    Then I select the Latest Event

    Then I expand the Glaucoma sidebar
    And I add a New Event "OpBooking"

    Then I select Diagnosis Eyes of "Left"
    And I select a Diagnosis of "30041005"
    Then I select Operation Eyes of "Left"
    And I select a Procedure of "41"

    Then I select No to Consultant required
    And I select "No" for "Any other doctor to do"

    And I select "No" for "Does the patient require pre-op assessment by an anaesthetist"

    And I select a Anaesthetic type "Topical"

    And I select "Patient preference" for "Anaesthetic choice"

    And I select "No" for "Patient needs to stop medication"

    Then I select No to a Post Operative Stay

    And I select a Operation Site of "1"

    Then I select a Priority of Urgent

    And I select a decision date of "14"

    And I select "Yes" for "Admission discussed with patient"

    And I select "As soon as possible" for "Schedule options"

    Then I select Save and Schedule now

    And I select OK to Duplicate procedure if requested

    And I select an Available theatre slot date
    And I select an Available session time

#    Then I select a Ward of "2"
    And enter an admission time of "11:20"
    Then I add Session comments of "Session Comments Session Comments Session Comments Session Comments Session Comments"
    And I add Operation comments of "Operation Comments Operation Comments Operation Comments Operation Comments Operation Comments"
    And enter RTT comments of "RTT Comments RTT Comments RTT Comments RTT Comments RTT Comments RTT Comments RTT Comments"

    Then I confirm the operation slot

  Scenario: Route 2: Login and create a Operation Booking Anderson Cataract

    Given I am logged in as "admin" with site "Queens" and firm "Anderson Firm (Cataract)"

    Then I search for hospital number "1009465"

    Then I select the Latest Event

    Then I expand the Cataract sidebar
    And I add a New Event "OpBooking"

    Then I select Diagnosis Eyes of "Right"
    And I select a Diagnosis of "24010005"
    Then I select Operation Eyes of "Right"
    And I select a Procedure of "79"

    Then I select Yes to Consultant required
    And I select "Anderson Andrew" for "Named Consultant"

    And I select "No" for "Any other doctor to do"

    And I select "No" for "Does the patient require pre-op assessment by an anaesthetist"

    And I select a Anaesthetic type "LA"

    And I select "Patient preference" for "Anaesthetic choice"

    And I select "No" for "Patient needs to stop medication"

    Then I select Yes to a Post Operative Stay

    And I select a Operation Site of "2"

    Then I select a Priority of Routine

    And I select a decision date of "10"

    Then I add comments of "Insert test comments here"

    And I select "Yes" for "Admission discussed with patient"

    And I select "As soon as possible" for "Schedule options"

    Then I select Save and Schedule now

    And I select OK to Duplicate procedure if requested

    Then I select Next Month

    And I select an Available theatre slot date
    And I select an Available session time

    Then I add Session comments of "Insert session comments here"
    And I add Operation comments of "Insert operation comments here"

    Then I confirm the operation slot

  Scenario: Route 3: Login and create a Operation Booking Anderson Medical Retinal

    Given I am logged in as "admin" with site "Kings" and firm "Anderson Firm (Medical Retinal)"

    Then I search for hospital number "1009465"

    Then I select the Latest Event

    Then I expand the Medical Retinal sidebar
    And I add a New Event "OpBooking"

    Then I select Diagnosis Eyes of "Both"
    And I select a Diagnosis of "255024002"
    Then I select Operation Eyes of "Both"
    And I select a Procedure of "327"

    Then I select No to Consultant required
    And I select "No" for "Any other doctor to do"

    And I select "No" for "Does the patient require pre-op assessment by an anaesthetist"

    And I select a Anaesthetic type "LAC"

    And I select "Patient preference" for "Anaesthetic choice"

    And I select "No" for "Patient needs to stop medication"

    Then I select No to a Post Operative Stay

    And I select a Operation Site of "2"

    Then I select a Priority of Urgent

    And I select a decision date of "3"

    Then I add comments of "Insert test comments here"

    And I select "Yes" for "Admission discussed with patient"

    And I select "As soon as possible" for "Schedule options"

    Then I select Save and Schedule now

    And I select OK to Duplicate procedure if requested

    Then I change the Viewing Schedule to Emergency List

    Then I select an Available theatre slot date of next "Saturday"
    And I select an Available session time

    Then I add Session comments of "Insert session comments here"
    And I add Operation comments of "Insert operation comments here"

    Then I confirm the operation slot
