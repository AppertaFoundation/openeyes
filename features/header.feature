Feature: Header
	In order to navigate OpenEyes
	As an OpenEyes user
	I need a header containing links

	Scenario Outline: Header displays logged in users first and last name
		Given I am on "/"
		When I log in as "<username>:<password>"
		Then I should see "Hi <firstname> <lastname>" in the "#user_id" element

	Examples:
	|	username	|	password	|	firstname	|	lastname	|
	|	admin		|	admin		|	Enoch		|	Root		|
	|	username	|	username	|	User		|	User		|

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
	|	Home							|	/theatre	|	/				|	Patient search					|
	|	Theatre Diaries					|	/			|	/theatre		|	Theatre Schedules				|
	|	Partial bookings waiting list	|	/			|	/waitingList	|	Partial bookings waiting List	|

