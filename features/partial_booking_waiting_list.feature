Feature: Partial Booking Waiting List
	In order to view partial patient bookings
	As a OpenEyes user
	I need to be able to get to the partial booking waiting list

	Background:
		Given I am logged in as "admin:admin:Enoch:Root"

	@regression @regression:1 @sample-data @javascript
	Scenario Outline: 1.13|1.17 PBWL defaults change depending on selected firm
		Given I am on "/"
		When I select "<firmselect>" firm
		And I wait "0.5" seconds # Fix for selenium webdriver moving too fast
		And I follow "Partial bookings waiting list"
		And I wait "0.5" seconds # Fix for selenium webdriver moving too fast
		Then I should see "<service>" in the "#subspecialty-id" dropdown
		And I should see "<firm>" in the "#firm-id" dropdown
		And I should see an "#waitingList" element

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