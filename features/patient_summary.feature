Feature: Patient Summary
	In order to view patient details and episodes
	As a OpenEyes user
	I need to be able to see their summary page

	Background:
		Given I am logged in as "admin:admin:Enoch:Root"

	@sample-data
	Scenario Outline: Confirm correct details for known alive patients
		Given I am on "/"
		When I search for patient "<firstname> <lastname>"
		Then I should be on "<url>"
		And I should see "Create or View Episodes and Events"
		# Personal Details
		And I should see "First name(s): <firstname>" in the "#personal_details" element
		And I should see "Last name: <lastname>" in the "#personal_details" element
		And I should see "Address: <address>" in the "#personal_details" element
		And I should see "Date of Birth: <dob>" in the "#personal_details" element
		And I should see "Age: <age>" in the "#personal_details" element
		And I should see "Gender: <gender>" in the "#personal_details" element
		# Contact Details
		And I should see "Telephone: <phone>" in the "#contact_details" element
		And I should see "Email: <email>" in the "#contact_details" element
		And I should see "Next of Kin: <nextofkin>" in the "#contact_details" element
		# General Practitioner
		And I should see "Name: <gp_name>" in the "#gp_details" element
		And I should see "Address: <gp_address>" in the "#gp_details" element
		And I should see "Telephone: <gp_phone>" in the "#gp_details" element

	Examples:
	| 	firstname	|	lastname	|	url					|	address																|	dob			|	age	|	gender	|	phone			|	email							|	nextofkin	|	gp_name			|	gp_address												|	gp_phone		|
	|	Violet		|	Coffin		|	/patient/view/19434	|	82 Scarisbrick Lane Bethersden West Yorkshire QA88 2GC				|	19 Mar 1942	|	70	|	Female	|	03040 6024378	|	Violet.Coffin@hotmail.com		|	Unknown		|	Dr James Kildare	|	99 Helton Lane, Laide, County Down, MO40 5OY	|	0222 222 2222	|
	|	Iris		|	Treffry		|	/patient/view/10010	|	14 Penberthy Lane Wigtoft Countywide Greater Manchester WG33 1GJ	|	27 Jan 1922	|	90	|	Female	|	02746 8129676	|	Iris.Treffry@hotmail.com		|	Unknown		|	Dr James Kildare	|	99 Helton Lane, Laide, County Down, MO40 5OY	|	0222 222 2222	|
	|	Michael		|	Broadbent	|	/patient/view/10057	|	17 Golding Crescent Welford-on-Avon RD60 8JF						|	10 Oct 1920	|	92	|	Male	|	06881 4873577	|	Michael.Broadbent@hotmail.com	|	Unknown		|	Unknown		|	Unknown	|	0222 222 2222 |

	@sample-data
	Scenario Outline: Confirm correct details for known deceased patients
		Given I am on "/"
		When I search for patient "<firstname> <lastname>"
		Then I should be on "<url>"
		And I should see "This patient is deceased (<dod>)" in the "div.alertBox" element
		# Personal Details
		And I should see "First name(s): <firstname>" in the "#personal_details" element
		And I should see "Last name: <lastname>" in the "#personal_details" element
		And I should see "Address: <address>" in the "#personal_details" element
		And I should see "Date of Birth: <dob>" in the "#personal_details" element
		And I should see "Date of Death: <dod> (Age <age>)" in the "#personal_details" element
		And I should see "Gender: <gender>" in the "#personal_details" element
		# Contact Details
		And I should see "Telephone: <phone>" in the "#contact_details" element
		And I should see "Email: <email>" in the "#contact_details" element
		And I should see "Next of Kin: <nextofkin>" in the "#contact_details" element
		# General Practitioner
		And I should see "Name: <gp_name>" in the "#gp_details" element
		And I should see "Address: <gp_address>" in the "#gp_details" element
		And I should see "Telephone: <gp_phone>" in the "#gp_details" element
		And I should see "Create or View Episodes and Events"

	Examples:
	| 	firstname	|	lastname	|	url					|	address												|	dob			|	dod			|	age	|	gender	|	phone			|	email						|	nextofkin	|	gp_name			|	gp_address										|	gp_phone		|
	|	Muriel		|	Chapman		|	/patient/view/10026	|	105 Bennett Crescent Colgate Hampshire CA85 1FE	| 8 Mar 1952	|	3 Feb 2012 	|	59	|	Female	|	09624 3843325	|	Muriel.Chapman@hotmail.com	|	Unknown		|	Dr James Kildare	|	99 Helton Lane, Laide, County Down, MO40 5OY	|	0444 444 4444 |

	Scenario: Navigate to episode summary page for a known patient
		Given I am on "/patient/view/19434"
		When I follow "Create or View Episodes and Events"
		Then I should be on "/patient/episodes/19434"
