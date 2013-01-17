Feature: Patient Episodes
	In order to manage patients in OpenEyes
	As a OpenEyes user
	I need to be able to create, view and edit events and episodes

	Background:
		Given I am logged in as "admin:admin:Enoch:Root"

	Scenario: Patient with no episodes
		Given I am on "/patient/view/10057"
		When I follow "Create or View Episodes and Events"
		Then I should see "There are currently no episodes for this patient, please add a new event to open an episode." in the "div.alertBox" element
		And I should not see an "#episodes_sidebar .episode_nav" element
		And I should see "No Episodes for this patient"

	Scenario: Patient with episodes
		Given I am on "/patient/view/19434"
		When I follow "Create or View Episodes and Events"
		Then I should not see an "div.alertBox" element
		And I should see an "#episodes_sidebar .episode_nav" element
		And I should not see "No Episodes for this patient"

	Scenario: Add new event dropdown open
		Given I am on "/patient/view/10057"
		When I follow "Create or View Episodes and Events"
		And I press "addNewEvent"
		Then I should see an "#add-event-select-type" element on screen

	Scenario: Add new event dropdown close
		Given I am on "/patient/view/10057"
		When I follow "Create or View Episodes and Events"
		And I press "addNewEvent"
		And I wait "0.5" seconds
		And I press "addNewEvent"
		And I wait "0.5" seconds
		Then I should not see an "#add-event-select-type" element on screen
