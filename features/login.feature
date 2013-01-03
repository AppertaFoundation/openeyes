Feature: Login
	In order to use the OpenEyes application
	As a OpenEyes user
	I need to be able to login

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
		When I follow "Logout"

	Examples:
	|	username	|	password		|	firstname	|	lastname	|
	| angunawelar | openeyesdevel | Romesh |  Angunawela |
	|	admin		|	admin			|	Enoch		|	Root		|
	|	RAIP |	openeyesdevel	|	Poornima |	Rai |
	|	WATSONM2 |	openeyesdevel	|	Martin |	Watson |
	|	OsborneS |	openeyesdevel	|	Sarah |	Osborne |
	|	RAJENDRAMR1 |	openeyesdevel	|	Ranjan |	Rajendram |
	|	STROUTHIDISN |	openeyesdevel	|	Nicholas |	Strouthidis |

	@regression @regression:1 @sample-data
	Scenario: 1.12 Search screen loads after login
		Given I am logged in as "admin:admin:Enoch:Root"
		Then I should see "Find a patient by Hospital Number, NHS Number, Firstname Surname or Surname, Firstname."
		And I should see an "#query" element

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
