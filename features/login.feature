Feature: Login
	In order to use the OpenEyes application
	As a OpenEyes user
	I need to be able to login

	Scenario: Login with no username
		Given I am on "/"
		And I am logged out
		When I fill in "LoginForm[password]" with "admin"
		And I press "Login"
		Then I should see "Invalid login."

	Scenario: Login with no password
        Given I am on "/"
        And I am logged out
        When I fill in "LoginForm[username]" with "admin"
        And I press "Login"
        Then I should see "Invalid login."

	Scenario: Login with non-existing user
		Given I am on "/"
		And I am logged out
        When I fill in "LoginForm[username]" with "ad"
        And I fill in "LoginForm[password]" with "ad"
        And I select "City Road" from "LoginForm[siteId]"
        And I press "Login"
		Then I should see "Invalid login."

	Scenario: Login as default admin user
		Given I am on "/"
		And I am logged out
		When I fill in "LoginForm[username]" with "admin"
		And I fill in "LoginForm_password" with "admin"
		And I select "City Road" from "LoginForm[siteId]"
		And I press "Login"
		Then I should see "You are logged in as admin. So this is OpenEyes Goldenrod Edition"

	Scenario: Logout as admin user
		Given I am on "/"
		And I am logged in as "admin:admin"
		When I follow "Logout"
		Then I should see "Please login"
