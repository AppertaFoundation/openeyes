Feature: Header
	In order to navigate OpenEyes
	As an OpenEyes user
	I need a header containing links

	@sample-data
	Scenario: Header displays firm dropdown
		Given I am on "/"
		When I log in as "admin:admin:Enoch:Root"
		Then I should see an "#selected_firm_id" element

	Scenario Outline: Selecting menu items changes url, page titles and menu state
		Given I am logged in as "admin:admin:Enoch:Root"
		And I am on "<current_url>"
		When I follow "<item>"
		Then the "#user_nav li span.selected" element should contain "<item>"
		And I should be on "<target_url>"
		And the "#content > h2" element should contain "<page_title>"

	Examples:
	|	item							|	current_url	|	target_url		|	page_title						|
	|	Home							|	/theatre	|	/				|	Search 		|
	|	Theatre Diaries					|	/			|	/theatre		|	Theatre Schedules				|
	|	Partial bookings waiting list	|	/			|	/waitingList	|	Partial bookings waiting List	|

	@regression @regression:1 @regression:2 @sample-data @javascript
	Scenario Outline: 1.22|2.6 Firm selection is sticky as you navigate
		Given I am logged in as "admin:admin:Enoch:Root"
		And firm "Minihan Miriam (Vitreoretinal)" is selected
		When I wait "0.5" seconds
		And I go to "<url>"
		Then I should see "Minihan Miriam (Vitreoretinal)" in the "#selected_firm_id" dropdown

	Examples:
	|	url						|
	|	/theatre				|
	|	/waitingList			|
	|	/patient/view/19434		|
	|	/patient/episodes/19434	|

	@regression @regression:1 @sample-data @javascript
	Scenario Outline: 1.15|1.21 Check firm selection is saved after logout and log back in
		Given I am logged in as "admin:admin:Enoch:Root"
		And I am on "/"
		When I select "<firmselect>" firm
		And I follow "Logout"
		And I log in as "admin:admin:Enoch:Root"
		Then I should see "<firmselect>" in the "#selected_firm_id" dropdown

	Examples:
	|	firmselect							|
	|	Abou-Rayyah Yassir (Adnexal)		|
	|	Aylward Bill (Accident & Emergency)	|
	|	Bessant David (Primary Care)		|
	|	Child Christopher (Strabismus)		|
	|	Cunningham Carol (Cataract)			|
	|	Dart John (External)				|
	|	Ezra Eric (Paediatrics)				|
	|	Ficker Linda (Refractive)			|
	|	Horgan Simon (Medical Retinal)		|
	|	Minihan Miriam (Vitreoretinal)		|
	|	Okhravi Narciss (Uveitis)			|
	|	Viswanathan Ananth (Glaucoma)		|

	@regression @regression:1 @sample-data @javascript
	Scenario Outline: 1.16 Select firm and make sure it stays the same
		Given I am logged in as "admin:admin:Enoch:Root"
		And I am on "/"
		When I select "<firmselect>" firm
		Then I should see "<firmselect>" in the "#selected_firm_id" dropdown

	Examples:
	|	firmselect							|
	|	Abou-Rayyah Yassir (Adnexal)		|
	|	Aylward Bill (Accident & Emergency)	|
	|	Bessant David (Primary Care)		|
	|	Child Christopher (Strabismus)		|
	|	Cunningham Carol (Cataract)			|
	|	Dart John (External)				|
	|	Ezra Eric (Paediatrics)				|
	|	Ficker Linda (Refractive)			|
	|	Horgan Simon (Medical Retinal)		|
	|	Minihan Miriam (Vitreoretinal)		|
	|	Okhravi Narciss (Uveitis)			|
	|	Viswanathan Ananth (Glaucoma)		|



