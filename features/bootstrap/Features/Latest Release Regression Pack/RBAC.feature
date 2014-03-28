@rbac @regression
Feature: Open Eyes Login RBAC user levels

  Scenario: PREPARATION LASER EVENT: Login and create a Phasing Event

    Given I am on the OpenEyes "master" homepage
    And I enter login credentials "admin" and "admin"
    And I select Site "1"
    Then I select a firm of "1"

    Then I search for hospital number "1009465 "

    Then I select the Latest Event

    Then I expand the Cataract sidebar
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

  Scenario: Route 0: Level 0 RBAC access: User with no login rights

    Given I am on the OpenEyes "master" homepage
    And I enter login credentials "level0" and "password"
    And I confirm that an Invalid Login error message is displayed

  Scenario: Route 1: Level 1 RBAC access: Login access and only able to view patient demographics

    Given I am on the OpenEyes "master" homepage
    And I enter login credentials "level1" and "password"
    And I select Site "1"
    Then I select a firm of "1"
    Then I search for hospital number "1009465"

    Then a check is made to confirm that Patient details information is displayed
    Then a check is made to confirm the user has correct level one access

  Scenario: Route 2: Level 2 RBAC access: Login access and view only, no printing

    Given I am on the OpenEyes "master" homepage
    And I enter login credentials "level2" and "password"
    And I select Site "1"
    Then I select a firm of "1"
    Then I search for hospital number "1009465"

    Then a check is made to confirm that Patient details information is displayed

    Then a check is made to confirm the user has correct level two access

    Then I select the Latest Event

    Then additional checks are made for correct level two access

    And a check to see printing has been disabled


  Scenario: Route 3: Level 3 RBAC access: Login access,view only rights and Printing Events

    Given I am on the OpenEyes "master" homepage
    And I enter login credentials "level3" and "password"
    And I select Site "1"
    Then I select a firm of "1"
    Then I search for hospital number "1009465"

    Then a check is made to confirm that Patient details information is displayed

    Then a check is made to confirm the user has correct level two access

    Then I select the Latest Event

    Then additional checks are made for correct level two access

    And a check to see printing has been enabled

  Scenario: Route 4 (Prep): Login and create a new Prescription
  Site 1:Queens
  Firm 3:Anderson Glaucoma

    Given I am on the OpenEyes "master" homepage
    And I enter login credentials "admin" and "admin"
    And I select Site "1"
    Then I select a firm of "3"

    Then I search for hospital number "1009465"

    Then I select the Latest Event

    Then I expand the Glaucoma sidebar
    And I add a New Event "Prescription"

    Then I select a Common Drug "75"
    And I select a Standard Set of "10"

    Then I enter a Dose of "2" drops
    And I enter a route of "1"

    And I enter a frequency of "4"
    Then I enter a duration of "1"
    Then I enter a eyes option "1"

    Then I enter a item two eyes option of "1"
    Then I enter a item three eyes option of "1"

    And I add Prescription comments of "TEST COMMENTS"

    Then I Save the Prescription Draft and confirm it has been created successfully

  Scenario: Route 4: Level 4 RBAC access: Login access, edit rights, Prescription event blocked

    Given I am on the OpenEyes "master" homepage
    And I enter login credentials "level4" and "password"
    And I select Site "1"
    Then I select a firm of "3"
    Then I search for hospital number "1009465"

    Then a check is made to confirm that Patient details information is displayed

    Then I select the Latest Event

    Then I expand the Glaucoma sidebar

    Then a check is made to confirm the user has correct level four access
    #level 4 Prescription event disabled

#  Scenario: Route 5: Level 5 RBAC access: Login access, edit rights, Prescription event allowed
#
#    Given I am on the OpenEyes "master" homepage
#    And I enter login credentials "level4" and "password"
#    And I select Site "1"
#    Then I select a firm of "3"
#    Then I search for hospital number "1009465"
#
#    Then a check is made to confirm that Patient details information is displayed
#
#    Then I select the Latest Event


#  Scenario: Route 6: Level 6 RBAC access: TBC
#
#    Given I am on the OpenEyes "master" homepage
#    And I enter login credentials "level6" and "password"
#    And I select Site "1"
#    Then I select a firm of "3"
#    Then I search for hospital number "1009465"
#
#    Then a check is made to confirm that Patient details information is displayed
#
#    Then I select the Latest Event


