@mink:selenium
Feature: Patient Search
	In order to manage a patient
	As a OpenEyes user
	I need to be able to search for them

	Background:
		Given I am logged in as "admin:admin:Enoch:Root"

	Scenario: Blank patient name search
		Given I am on "/"
		When I press "findPatient_details"
		Then I should see "Please enter either a hospital number or a firstname and lastname."

	Scenario: Blank hospital number search
		Given I am on "/"
		When I press "findPatient_id"
		Then I should see "Please enter either a hospital number or a firstname and lastname."

	Scenario: Searching with no first name
		Given I am logged in as "admin:admin:Enoch:Root"
		When I fill in "Patient[last_name]" with "coffin"
		And I press "findPatient_details"
		Then I should see "Please enter either a hospital number or a firstname and lastname."

	Scenario: Searching with no last name
        Given I am on "/"
        When I fill in "Patient[first_name]" with "violet"
        And I press "findPatient_details"
        Then I should see "Please enter either a hospital number or a firstname and lastname."

	Scenario: Searching for a patient that doesn't exist by name
		Given I am on "/"
        When I fill in "Patient[last_name]" with "cof"
        And I fill in "Patient[first_name]" with "vio"
        And I press "findPatient_details"
		Then I should see "Sorry, No patients found for that search."

	Scenario: Searching for a patient that does exists by name
		Given I am on "/"
		When I fill in "Patient[last_name]" with "coffin"
		And I fill in "Patient[first_name]" with "violet"
		And I press "findPatient_details"
		Then I should be on "/patient/view/19434"

	Scenario: Searching for patient that doesn't exist by hospital number
		Given I am on "/"
		When I fill in "Patient[hos_num]" with "1"
		And I press "findPatient_id"
		Then I should see "Sorry, No patients found for that search"

	Scenario: Searching for a patient by hospital number
		Given I am on "/"
		When I fill in "Patient[hos_num]" with "1009465"
		And I press "findPatient_id"
		Then I should be on "/patient/view/19434"
