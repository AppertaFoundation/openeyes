@NewDiagnosis
Feature: Open Eyes Login and Patient Diagnosis Screen Template
  In order to cover every possible route throughout the site
  As an automation tester
  I want to build a template with supporting code for each web page

  Scenario Outline: Login and add Opthamlmic Diagnosis, Systemic Diagnosis, CVI, Medication and other Allergies

    Given I am on the OpenEyes "<environment>" homepage
    And I enter login credentials "<username>" and "<password>"
    And I select Site "<site>"
    Then I select a firm of "1"

    Then I search for hospital number "<hospnumber>"
    #Then I search for patient name last name "<last>" and first name "<first>"
    #Then I search for NHS number "<nhs>"

    Then I Add an Ophthalmic Diagnosis selection of "<OphtDiagnosis>"
    And I select that it affects eye "<eye>"
    And I select a Opthalmic Diagnosis date of day "<day>" month "<month>" year "<year>"
    Then I save the new Opthalmic Diagnosis

    Then I Add an Systemic Diagnosis selection of "<SystDiagnosis>"
    And I select that it affects Systemic side "<side>"
    And I select a Systemic Diagnosis date of day "<day>" month "<month>" year "<year>"
    Then I save the new Systemic Diagnosis

    Then I Add a Previous Operation of "1"
    And I select that it affects Operation side "<side>"
    And I select a Previous Operation date of day "<day>" month "<month>" year "<year>"
    Then I save the new Previous Operation

    And I Add Medication details medication "<medication>" route "<route>" frequency "<frequency>" date from "<datefrom>" and Save

    Then I edit the CVI Status "<CVIstatus>"
    And I select a CVI Status date of day "<day>" month "<month>" year "<year>"
    Then I save the new CVI status

    Then I Add Allergy "<allergy>" and Save

    And I Add a Family History of relative "relative" side "<side>" condition "condition" and comments "comments" and Save
    #Then I choose to close the browser

  Examples: User details
    | environment   | site      | username | password     | hospnumber   | nhs        | last    | first  |OphtDiagnosis | eye   | day | month | year | SystDiagnosis | side | allergy | CVIstatus | medication | route | frequency | datefrom |
    | master        | 1         | admin    | admin        | 1009465      | 8821388753 | Coffin, | Violet |193570009     | Both  | 18  |  6    | 2012 | 195967001     | Left | 5       |   4       |   3        |  2    |  7        | 12       |


  # Site ID's:
  # Queens - 1

  # Firm 1 = Abderson Firm (Cataract)

  # Last name to include a comma after to match search criteria i.e Coffin,

  # Ophthalmic Diagnosis  (Using SNOMED codes)
  # Cataract - 193570009

  # Systemic Diagnosis  (Using SNOMED codes)
  # Asthma - 195967001

  # Allergy (Values need to be defined not as 1,2,3 etc)
  # Sulphonamides - 2
  # Fluorescin - 13
  # Acetazolamide - 9
  # Tetracycline - 5


