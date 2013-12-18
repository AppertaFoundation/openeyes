
Feature: Create New Consent Form
  In order to cover every possible route throughout the site
  As an automation tester
  I want to build a template with supporting code for each web page

  Scenario Outline: Login and create a new Consent Form

    Given I am on the OpenEyes "<environment>" homepage
    And I enter login credentials "<username>" and "<password>"
    And I select Site "2"
    Then I select a firm of "3"

    Then I search for hospital number "<hospnumber>"

    Then I select the Latest Event

    Then I expand the Glaucoma sidebar
    And I add a New Event "Consent"
    Then I select Unbooked Procedures
    Then I select Add Consent Form
    And I choose Type "1"

    Then I choose Procedure eye of "Both"
#    And I choose a Procedure of "Laser"
    And I choose an Anaesthetic type of LA
    And I add a common procedure of "127"

    Then I choose Permissions for images No

    And I select the Information leaflet checkbox
#    And I select the Anasethetic leflet checkbox
#    Then I select a Witness Required checkbox
#    And I enter a Witness Name of "Joe Bloggs"
#    Then I select a Interpreter required checkbox
#    And I enter a Interpreter name of "Tom Smith"
#    Then I select a supplementary consent form checkbox

#   !!!! The element not found error crops up again for all of these checkboxes!!!!
    Then I save the Consent Form




  Examples: User details
    | environment   | site      | username | password     | hospnumber   | nhs        | last           | first         | EventType     |
    | master        | 1         | admin    | admin        | 1009465      | 8821388753 | Coffin,        | Violet        | Examination   |
