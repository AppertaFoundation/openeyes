@rbac @regression
Feature: Open Eyes Login RBAC user levels

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

  Scenario: Route 2: Level 2 RBAC access: Login access and view only rights to all information

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

  Scenario: Route 3: Level 3 RBAC access: Login access,view only rights and Printing allowed

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
