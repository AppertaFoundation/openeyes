Feature: Patient Search
	In order to manage a patient
	As a OpenEyes user
	I need to be able to search for them

	Background:
		Given I am logged in as "admin:admin:Enoch:Root"

	Scenario: Blank patient name search
		Given I am on "/"
		When I press "Search"
		Then I should see "Please enter either a hospital number or a firstname and lastname."

	Scenario: Blank hospital number search
		Given I am on "/"
		When I press "findPatient_id"
		Then I should see "Please enter either a hospital number or a firstname and lastname."

	@testing
	Scenario: TEST
		Given patient "1009465" has a scheduled operation booking

	@regression @regression:3 @sample-data
	Scenario: 3.15 Searching with no first name
		Given I am logged in as "admin:admin:Enoch:Root"
		When I fill in "Patient[last_name]" with "coffin"
		And I press "findPatient_details"
		Then I should see "Please enter either a hospital number or a firstname and lastname."

	@regression @regression:3 @sample-data
	Scenario: 3.16 Searching with no last name
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

	@regression @regression:3 @sample-data
	Scenario: 3.9 Searching for patient that doesn't exist by hospital number
		Given I am on "/"
		When I fill in "Patient[hos_num]" with "1"
		And I press "findPatient_id"
		Then I should see "Sorry, No patients found for that search"

	@regression @regression:3 @sample-data
	Scenario: 3.1 Search screen has correct fields and links
		Given I am on "/"
		Then I should see an "#selected_firm_id" element
		And I should see "Home"
		And I should see "Theatre Diaries"
		And I should see "Partial bookings waiting list"
		And I should see "Logout"
		And I should see "Patient search"
		And I should see "Search by hospital number:"
		And I should see an "#Patient_hos_num" element
		And I should see "Find patient" in the "#findPatient_id" element
		And I should see "Last name:"
		And I should see "First name:"
		And I should see an "#Patient_last_name" element
		And I should see an "#Patient_first_name" element
		And I should see "Find patient" in the "#findPatient_details" element

	@regression @regression:3 @sample-data
	Scenario: 3.5|3.6 Searching for a patient by hospital number
		Given I am on "/"
		When I fill in "Patient[hos_num]" with "1009465"
		And I press "findPatient_id"
		Then I should be on "/patient/view/19434"
		And I should see "Coffin, Violet" in the "#patientID" element

	@regression @regression:3 @sample-data
	Scenario: 3.8 Searching for a patient that does exists by name
		Given I am on "/"
		When I fill in "Patient[last_name]" with "coffin"
		And I fill in "Patient[first_name]" with "violet"
		And I press "findPatient_details"
		Then I should be on "/patient/view/19434"
		And I should see "Coffin, Violet" in the "#patientID" element




