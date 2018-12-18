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
    Then I add patient age parameter for ages "<lowerAge>" to "<upperAge>"
    Then I add diagnosis parameter for diagnosed not with "Abdominal aortic atherosclerosis" by "Oncology Service (Oncology)"
    Then I add medication parameter for has not taken "<medication>"
    Then I add allergy parameter for is not allergic to "<allergy>"
    Then I add previous procedure parameter for has not had a  "<procedure>"
    Then I search
    Then I should have results

    Examples:
      |uname|pwd  |lowerAge |upperAge|medication|allergy|procedure|
      |admin|admin|15       |65      |medicaiton|allergy|procedrue|