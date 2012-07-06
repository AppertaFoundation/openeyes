Feature: Theatre Diary
	In order to manage operation bookings
	As a OpenEyes user
	I need to be able to get to the theatre diary

	Background:
		Given I am logged in as "admin:admin:Enoch:Root"

	# Need to work out a way to test the sample data with this
	# @regression @regression:1 @sample-data @javascript
	# Scenario: 1.19 Select patient from diary and change firm  

	# Having issues trying to select a procedure
	# @regression @regression:1 @sample-data @javascript
	# Scenario: 1.23|1.24|1.25 Selected firm will be default on reschdule page
	# 	Given firm "Aylward Bill (Vitreoretinal)" is selected
	# 	And patient "1009000" has a scheduled operation booking

	@regression @regression:1 @sample-data @javascript
	Scenario Outline: 1.14|1.18 Theatre Diary defaults change depending on selected firm
		Given I am on "/"
		When I select "<firmselect>" firm
		And I wait "0.5" seconds # Fix for selenium webdriver moving too fast
		And I follow "Theatre Diaries"
		And I wait "0.5" seconds # Fix for selenium webdriver moving too fast
		Then I should see "<service>" in the "#subspecialty-id" dropdown
		And I should see "<firm>" in the "#firm-id" dropdown

	Examples:
	|	firmselect							|	service					|	firm				|
	|	Abou-Rayyah Yassir (Adnexal)		|	Adnexal					|	Abou-Rayyah Yassir	|
	|	Aylward Bill (Accident & Emergency)	|	Accident & Emergency	|	Aylward Bill		|
	|	Bessant David (Primary Care)		|	Primary Care			|	Bessant David		|
	|	Child Christopher (Strabismus)		|	Strabismus				|	Child Christopher	|
	|	Cunningham Carol (Cataract)			|	Cataract				|	Cunningham Carol	|
	|	Dart John (External)				|	External				|	Dart John			|
	|	Ezra Eric (Paediatrics)				|	Paediatrics				|	Ezra Eric			|
	|	Ficker Linda (Refractive)			|	Refractive				|	Ficker Linda		|
	|	Horgan Simon (Medical Retinal)		|	Medical Retinal			|	Horgan Simon		|
	|	Minihan Miriam (Vitreoretinal)		|	Vitreoretinal			|	Minihan Miriam		|
	|	Okhravi Narciss (Uveitis)			|	Uveitis					|	Okhravi Narciss		|
	|	Viswanathan Ananth (Glaucoma)		|	Glaucoma				|	Viswanathan Ananth	|