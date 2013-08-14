@automation
Feature: Authentication

  Scenario: Successfully logging in with valid credentials
    Given I am on homepage
    When I fill in "Username" with "admin"
    And I fill in "Password" with "admin"
    And I press "Login"
    Then I should see "You are logged in"