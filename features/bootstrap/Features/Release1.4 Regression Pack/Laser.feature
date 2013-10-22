@NewLaser
Feature: Create New Anaesthetic Satisfaction Audit
  In order to cover every possible route throughout the site
  As an automation tester
  I want to build a template with supporting code for each web page

  Scenario: Login and create a Anaesthetic Satisfaction Audit

    Given I am on the OpenEyes "master" homepage
    And I enter login credentials "admin" and "admin"
    And I select Site "2"
    Then I select a firm of "3"

    Then I search for hospital number "1009465"

    Then I select the Latest Event
    Then I expand the Glaucoma sidebar
    And I add a New Event "Laser "

    #!!! THIS DATABASE SET DOESNT CONTAIN ANY LASERSITE OR LAZERS SO DO NOT RUN THIS FEATURE!!!

    Then I select a Laser site ID "2m"
    And I select a Laser of "3"
    And I select a Laser Surgeon of "111"
    Then I select a Right Procedure of "62"
    Then I select a Left Procedure of "363"

