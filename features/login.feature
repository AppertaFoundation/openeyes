Feature: Login
	In order to use the OpenEyes application
	As a OpenEyes user
	I need to be able to login

	####
	# 1.2 - not testable
	# 1.3 - not testable
	# 1.8 - don't understand
	# 1.9 - need to emulate connection throttling (charles does it)
	# 1.11 - skipping
	# 1.13 / 1.17 - Found in PBWL feature
	# 1.14 / 1.18 / 1.19 - Found in Theatre Diary feature
	# 1.15 / 1.16 / 1.22 - Found in Header feature
	# 
	####

	@regression @regression:1
	Scenario: 1.1 Login fields are present
		Given I am logged out
		When I go to "/"
		Then I should see an "#LoginForm_password" element
		And I should see an "#LoginForm_username" element
		And I should see an "#LoginForm_siteId" element
		And I should see "Login" in the "button" element

	@regression @regression:1
	Scenario: 1.4 Login with non-existing user
		Given I am on "/"
		And I am logged out
        When I fill in "LoginForm[username]" with "ad"
        And I fill in "LoginForm[password]" with "ad"
        And I select "City Road" from "LoginForm[siteId]"
        And I press "Login"
		Then I should see "Invalid login."

	@regression @regression:1 @sample-data
	Scenario: 1.5 Login with correct username, wrong password
		Given I am on "/"
		And I am logged out
        When I fill in "LoginForm[username]" with "admin"
        And I fill in "LoginForm[password]" with "ad"
        And I select "City Road" from "LoginForm[siteId]"
        And I press "Login"
		Then I should see "Invalid login."

	@regression @regression:1 @sample-data
	Scenario: 1.6 Login with wrong username, correct password
		Given I am on "/"
		And I am logged out
        When I fill in "LoginForm[username]" with "ad"
        And I fill in "LoginForm[password]" with "admin"
        And I select "City Road" from "LoginForm[siteId]"
        And I press "Login"
		Then I should see "Invalid login."

	@regression @regression:1 @sample-data
	Scenario: 1.7 Login as default admin user
		Given I am on "/"
		And I am logged out
		When I fill in "LoginForm[username]" with "admin"
		And I fill in "LoginForm_password" with "admin"
		And I select "City Road" from "LoginForm[siteId]"
		And I press "Login"
		Then I should see "You are logged in as admin. So this is OpenEyes Goldenrod Edition"

	@regression @regression:1 @sample-data
	Scenario Outline: 1.10 Check header for correct users first and last name
		Given I am on "/"
		And I am logged out
		When I fill in "LoginForm[username]" with "<username>"
		And I fill in "LoginForm_password" with "<password>"
		And I select "City Road" from "LoginForm[siteId]"
		And I press "Login"
		Then I should see "Hi <firstname> <lastname>" in the "#user_id" element

	Examples:
	|	username	|	password		|	site		|	firstname	|	lastname	|
	|	admin		|	admin			|	City Road	|	Enoch		|	Root		|
	|	username	|	username		|	Bedford		|	User		|	User		|
	|	kahnj		|	openeyesdevel	|	Boots		|	Jaheed		|	Khan		|
	|	jonese		|	openeyesdevel	|	Bridge lane	|	Emma		|	Jones		|
	|	childc		|	openeyesdevel	|	Croydon		|	Chris		|	Child		|
	|	wilkinsm	|	openeyesdevel	|	Ealing		|	Mark		|	Wilkins		|
	|	verityd		|	openeyesdevel	|	Harlow		|	David		|	Verity		|
	|	tufts		|	openeyesdevel	|	Homerton	|	Stephen		|	Tuft		|

	@regression @regression:1 @sample-data
	Scenario: 1.12 Search screen loads after login
		Given I am logged in as "admin:admin:Enoch:Root"
		Then I should see "Patient search"
		And I should see "Find a patient. Either by hospital number or by personal details. You must know their surname."
		And I should see "Search by hospital number:"
		And I should see an "#Patient_hos_num" element
		And I should see "Find patient" in the "#findPatient_id" element
		And I should see "Last name:"
		And I should see "First name:"
		And I should see an "#Patient_last_name" element
		And I should see an "#Patient_first_name" element
		And I should see "Find patient" in the "#findPatient_details" element

	@sample-data
	Scenario: Login with no username
		Given I am on "/"
		And I am logged out
		When I fill in "LoginForm[password]" with "admin"
		And I press "Login"
		Then I should see "Invalid login."

	@sample-data
	Scenario: Login with no password
        Given I am on "/"
        And I am logged out
        When I fill in "LoginForm[username]" with "admin"
        And I press "Login"
        Then I should see "Invalid login."

    @regression @regression:1 @sample-data
	Scenario: 1.20 Login and logout as admin user
		Given I am on "/"
		And I am logged in as "admin:admin"
		When I follow "Logout"
		Then I should see "Please login"
