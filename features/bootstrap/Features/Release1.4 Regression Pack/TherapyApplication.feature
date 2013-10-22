@therapy @regression
Feature: Create New Anaesthetic Satisfaction Audit
  In order to cover every possible route throughout the site
  As an automation tester
  I want to build a template with supporting code for each web page

  Scenario: Login and create a new Therapy Application

    Given I am on the OpenEyes "master" homepage
    And I enter login credentials "admin" and "admin"
    And I select Site "2"
    Then I select a firm of "4"

    Then I search for hospital number "1009465"

    Then I select the Latest Event

    Then I expand the Medical Retinal sidebar
    And I add a New Event "Therapy"

    And I select a Right Side Diagnosis of "75971007"
    Then I select a Right Secondary To of "267718000"

    And I select a Left Side Diagnosis of "75971007"
    Then I select a Left Secondary To of "267718000"

    Then I select a Right Treatment of "1"

    Then I select a Left Treatment of "1"

    Then I select Cerebrovascular accident Yes
    Then I select Cerebrovascular accident No
    Then I select Ischaemic attack Yes
    Then I select Ischaemic attack No
    Then I select Myocardial infarction Yes
    Then I select Myocardial infarction No

    And I select a Consultant of "4"

    Then I Save the Therapy Application


