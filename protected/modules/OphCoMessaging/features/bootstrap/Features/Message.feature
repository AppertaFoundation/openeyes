@messaging
Feature: Messaging

  Scenario: Create a new message
    Given I am on the OpenEyes "master" homepage
    And I enter login credentials "admin" and "admin"
    And I select Site "1"
    Then I select a firm of "3"

    Then I search for hospital number "1009465"

    Then I select the Latest Event Page

    And I add a New Event "Message" for "Glaucoma"

    And I Save the Event

    Then the application returns a validation error containing 'For the attention of cannot be blank.'

    And the application returns a validation error containing 'Type cannot be blank.'

    And the application returns a validation error containing 'Text cannot be blank.'

    Then I type "Level Th" into the for the attention of field

    And I select option "1" from the autocomplete list

    Then "Level Three" is displayed as the selected user

    Then I select "General" for the type of message

    And I type "This is a sample message" into the message box

    Then I Save the Message and confirm it has been created successfully

    And I confirm that fao user is "Level Three"

    And I confirm the message type is "General"

    And I confirm that the message text is "This is a sample message"

    Then I Edit the displayed event

    And I confirm I cannot change the FAO user

    And I type "This is a different sample message" into the message box

    Then I Save the Message

    And I confirm that fao user is "Level Three"

    And I confirm the message type is "General"

    And I confirm that the message text is "This is a different sample message"

    And I bookmark the current page as "myMessage"

    Then I logout

    And I enter login credentials "level3" and "password"

    And I select Site "1"
    Then I select a firm of "3"

    And I see I have messages in the messages dashboard

    And there's a row for the bookmark "myMessage"



