@Intravitreal @regression
Feature: Create New Intravitreal Event
  In order to cover every possible route throughout the site
  As an automation tester
  I want to build a template with supporting code for each web page

  Scenario: Login and create a New Intravitreal Event

    Given I am on the OpenEyes "master" homepage
    And I enter login credentials "admin" and "admin"
    And I select Site "1"
    Then I select a firm of "1"

    Then I search for hospital number "1009465"

    Then I select the Latest Event

    Then I expand the Cataract sidebar
    And I add a New Event "Intravitreal"

    Then I select Add Right Side
    Then I choose Right Anaesthetic Type of Topical
    Then I choose Right Anaesthetic Type of LA

    Then I choose Right Anaesthetic Delivery of Retrobulbar
    Then I choose Right Anaesthetic Delivery of Peribulbar
    Then I choose Right Anaesthetic Delivery of Subtenons
    Then I choose Right Anaesthetic Delivery of Subconjunctival
    Then I choose Right Anaesthetic Delivery of Topical
    Then I choose Right Anaesthetic Delivery of TopicalandIntracameral
    Then I choose Right Anaesthetic Delivery of Other
    And I choose Right Anaesthetic Agent "5"

    Then I choose Left Anaesthetic Type of Topical
    Then I choose Left Anaesthetic Type of LA

    Then I choose Left Anaesthetic Delivery of Retrobulbar
    Then I choose Left Anaesthetic Delivery of Peribulbar
    Then I choose Left Anaesthetic Delivery of Subtenons
    Then I choose Left Anaesthetic Delivery of Subconjunctival
    Then I choose Left Anaesthetic Delivery of Topical
    Then I choose Left Anaesthetic Delivery of TopicalandIntracameral
    Then I choose Left Anaesthetic Delivery of Other
    And I choose Left Anaesthetic Agent "1"

    Then I choose Right Pre Injection Antiseptic "1"
    Then I choose Right Pre Injection Skin Cleanser "2"
    And I tick the Right Pre Injection IOP Lowering Drops checkbox
    Then I choose Right Pre Injection IOP Lowering Drops "1"
    Then I choose Right Drug "7"
    And I enter "2" number of Right injections
    Then I enter Right batch number "123"

    Then I choose Right Injection Given By "1"
    And I enter a Right Injection time of "09:30"

    Then I choose Left Pre Injection Antiseptic "1"
    Then I choose Left Pre Injection Skin Cleanser "2"
    And I tick the Left Pre Injection IOP Lowering Drops checkbox
    Then I choose Left Pre Injection IOP Lowering Drops "1"
    Then I choose Left Drug "7"
    And I enter "2" number of Left injections
    Then I enter Left batch number "123"

    Then I choose Left Injection Given By "1"
    And I enter a Left Injection time of "09:30"

    Then I choose A Right Lens Status of "1"
    And I choose Right Counting Fingers Checked Yes
    And I choose Right Counting Fingers Checked No
    And I choose Right IOP Needs to be Checked Yes
    And I choose Right IOP Needs to be Checked No
    Then I choose Right Post Injection Drops "1"

    Then I choose A Left Lens Status of "1"
    And I choose Left Counting Fingers Checked Yes
    And I choose Left Counting Fingers Checked No
    And I choose Left IOP Needs to be Checked Yes
    And I choose Left IOP Needs to be Checked No
    Then I choose Left Post Injection Drops "1"

    And I select Right Complications "5"
    And I select Left Complications "5"

    Then I Save the Intravitreal injection
