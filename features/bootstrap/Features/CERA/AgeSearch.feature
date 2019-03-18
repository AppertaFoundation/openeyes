Feature: Case Search
  @CaseSearch
  @javascript

  Scenario Outline:
  Route 1: Login and create a near visual acuity:
  Site :  Kings
  Firm :  MR Clinic (Medical Retina)

    Given I am on the OpenEyes "master" homepage
    And I enter login credentials "<uname>" and "<pwd>"

    And I go to nav "Advanced Search"
    And I add patient age parameter for ages "<lowerAge>" to "<upperAge>"
    Then I search
    Then I should have results

    Examples:
      |uname|pwd  |lowerAge |upperAge|
      |admin|admin|15       |65      |
      |admin|admin|null     |65      |
      |admin|admin|15       |null    |