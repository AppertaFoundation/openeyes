@mink:selenium
Feature: Operation Module
	In order to book patients for operations
	As a OpenEyes user
	I need to be able to create, update and view operation bookings

	Background:
		Given I am logged in as "admin:admin:Enoch:Root"
		And patient "1000057" exists

	Scenario: Diagnosis right eye should select procedure right eye
		Given I am on "/patient/episodes/10057"
		When I press "addNewEvent"
		And I follow "OphTrOperation_Create"
		When I select the "ElementDiagnosis_eye_id_2" radio button
		Then the "ElementOperation_eye_id_2" radio should be checked

	Scenario: Start new operation booking and cancel
		Given I am on "/patient/episodes/10057"
		When I press "addNewEvent"
		And I follow "OphTrOperation_Create"
		And I press "Cancel Operation"
		Then I should be on "/patient/episodes/10057"