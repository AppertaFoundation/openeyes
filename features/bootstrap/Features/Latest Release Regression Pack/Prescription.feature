@prescription @regression
Feature: Create New Prescription
@Prescription
         Regression coverage of this event is 100%

  @Prescription_Route_1
  Scenario Outline: Route 1: Login and create a new Prescription
            Site 1:Queens
            Firm 3:Anderson Glaucoma

    Given I am on the OpenEyes "master" homepage
    And I enter login credentials "<uname>" and "<pwd>"
    And I select Site "<siteName/Number>"
    Then I select a firm of "<firmName/Number>"

    Then I search for hospital number "<hospNumber>"

    Then I select Create or View Episodes and Events

    Then I expand the "<speciality>" sidebar
    And I add a New Event "<event>"
    
    Then I select a Common Drug "<commonDrug>"
    And I select a Standard Set of "<standardSet>"

    Then I enter a Dose of "<dose>" drops
    And I enter a route of "<route>"

    And I enter a frequency of "<frequency>"
    Then I enter a duration of "<duration>"
    Then I enter a eyes option "1"

    Then I enter a item two eyes option of "1"
    Then I enter a item three eyes option of "1"

    And I add Prescription comments of "<presComm>"

    Then I Save the Prescription Draft and confirm it has been created successfully

    Examples:
    |uname|pwd  |siteName/Number|firmName/Number      |hospNumber|speciality|event       |commonDrug|standardSet|dose|route|frequency|duration|presComm|
    |admin|admin|Bridge Lane    |Amit Blann (Glaucoma)|1009465   |Glaucoma  |Prescription|7         |10         |2   |1    |4        |3       |TEST    |



  @Prescription_Route_2
  Scenario Outline: Route 2: Login and create a new Prescription
            Site 2:Kings
            Firm 3:Anderson Cataract

    Given I am on the OpenEyes "master" homepage
    And I enter login credentials "<uname>" and "<pwd>"
    And I select Site "<siteName/Number>"
    Then I select a firm of "<firmName/Number>"

    Then I search for hospital number "<hospNumber>"

    Then I select Create or View Episodes and Events

    Then I expand the "<speciality>" sidebar
    And I add a New Event "<event>"

    #code for popup
    Then I check prescription already exists


    Then I choose to filter by type "<filterType>"
    And I select the No preservative checkbox

    Then I select a Common Drug "<commonDrug>"

    Then I enter a Dose of "<dose>" drops
    And I enter a route of "<route>"
    Then I enter a eyes option "<eyesOption>"

    And I enter a frequency of "<frequency>"
    Then I enter a duration of "<duration>"

    And I add Prescription comments of "<presComm>"

    Then I Save the Prescription Draft and confirm it has been created successfully

    Examples:
      |uname|pwd  |siteName/Number|firmName/Number         |hospNumber|speciality|event       |filterType|commonDrug                                  |standardSet|dose|route|eyesOption|frequency|duration|presComm|
      |admin|admin|Ealing         |Coral Johnson (Cataract)|1009465   |Cataract  |Prescription|32        |adrenaline 0.01% eye drops (No Preservative)|10         |3   |1    |1         |4        |3       |TEST    |

  @Prescription_Route_3
  Scenario Outline: Route 3: Login and create a new Prescription
            Site 2:Kings
            Firm 4:Anderson Medical Retinal
            Add two Tapers, Remove Taper

    Given I am on the OpenEyes "master" homepage
    And I enter login credentials "<uname>" and "<pwd>"
    And I select Site "<siteName/Number>"
    Then I select a firm of "<firmName/Number>"

    Then I search for hospital number "<hospNumber>"

    Then I select Create or View Episodes and Events

    Then I expand the "<speciality>" sidebar
    And I add a New Event "<event>"

    #code for popup
    Then I check prescription already exists

    Then I select a Common Drug "<commonDrug>"

    Then I enter a Dose of "<dose>" drops
    And I enter a route of "<route>"

    And I enter a frequency of "<frequency>"
    Then I enter a duration of "<duration>"

    Then I add a Taper
    And I enter a first Taper dose of "<taperDose>"
    Then I enter a first Taper frequency of "<taperFreq>"
    And I enter a first Taper duration of "<taperDuration>"

    Then I add a Taper
    And I enter a second Taper dose of "<taperDose-2>"
    Then I enter a second Taper frequency of "<taperFreq-2>"
    And I enter a second Taper duration of "<taperDuration-2>"

    Then I add a Taper
    Then I remove the last Taper

    And I add Prescription comments of "<presComm>"

    Then I Save the Prescription Draft and confirm it has been created successfully

  Examples:
  |uname|pwd  |siteName/Number|firmName/Number                |hospNumber|speciality     |event       |filterType|commonDrug                                  |standardSet|dose|route|eyesOption|frequency|duration|taperDose|taperFreq|taperDuration|taperDose-2|taperFreq-2|taperDuration-2|presComm|
  |admin|admin|Bridge Lane    |Angela Glasby (Medical Retinal)|1009465   |Medical Retinal|Prescription|32        |adrenaline 0.01% eye drops (No Preservative)|10         |2   |3    |1         |5        |3       |4        |2        |6            |3          |7          |2              |TEST    |

  @Prescription_Route_4
  Scenario Outline: Route 4: Login and create a new Prescription
            Site 1:Queens
            Firm 2:Broom Glaucoma

    Given I am on the OpenEyes "master" homepage
    And I enter login credentials "<uname>" and "<pwd>"
    And I select Site "<siteName/Number>"
    Then I select a firm of "<firmName/Number>"

    Then I search for hospital number "<hospNumber>"

    Then I select Create or View Episodes and Events

    Then I expand the "<speciality>" sidebar
    And I add a New Event "<event>"

    #code for popup
    Then I check prescription already exists

    Then I select a Common Drug "<commonDrug>"
    
    Then I choose to filter by type "<filterType>"
    
    And I select the No preservative checkbox

    Then I enter a Dose of "<dose>" drops
    And I enter a route of "<route>"

    And I enter a frequency of "<frequency>"
    Then I enter a duration of "<duration>"

    And I add Prescription comments of "<presComm>"

    Then I Save the Prescription Draft and confirm it has been created successfully

  Examples:
  |uname|pwd  |siteName/Number|firmName/Number      |hospNumber|speciality|event       |commonDrug                                  |filterType|standardSet|dose|route|eyesOption|frequency|duration|taperDose|taperFreq|taperDuration|taperDose-2|taperFreq-2|taperDuration-2|presComm|
  |admin|admin|Barking        |Amit Blann (Glaucoma)|1009465   |Glaucoma  |Prescription|adrenaline 0.01% eye drops (No Preservative)|32        |10         |2   |17   |1         |6        |6       |4        |2        |6            |3          |7          |2              |TEST    |



  @Prescription_Route_5 @OEM-495 @postRelease_v1.11
  Scenario Outline: Prescription previously saved
    Given I am on the OpenEyes "<page>" homepage
    And I enter login credentials "<username>" and "<password>"
    And I select Site "<siteName/Number>"
    Then I select a firm of "<firmName/Number>"

    Then I search for hospital number "<searchItem>"

    Then I select Create or View Episodes and Events

    Then I expand the "<speciality>" sidebar
    And I add a New Event "<event>"

    #code for popup
    Then I check prescription already exists

    Then I choose to filter by type "<filterType>"
    And I select the No preservative checkbox

    Then I select a Common Drug "<commonDrug>"
    Then I enter a Dose of "<dose>" drops
    And I enter a route of "<route>"
    Then I enter a eyes option "<eyesOption>"

    And I enter a frequency of "<frequency>"
    Then I enter a duration of "<duration>"

    And I add Prescription comments of "<presComm>"

    Then I Save the Prescription Draft and confirm it has been created successfully

    Then I expand the "<speciality>" sidebar
    And I add a New Event "<event>"
    #code for popup
    Then I check prescription already exists

    Then I select Repeat Prescription
#    And I should see the drug from the previous prescription

    Examples:
    |page  |username|password|siteName/Number|firmName/Number      |searchItem|speciality|event       |filterType|commonDrug                                  |dose|route|eyesOption|frequency|duration|presComm|
    |master|admin   |admin   |Barking        |Amit Blann (Glaucoma)|1009465   |Glaucoma  |Prescription|32        |adrenaline 0.01% eye drops (No Preservative)|3   |1    |1         |4        |3       |TEST    |



  @OE-5667 @sprint25 @test1 @Prescription_Route_6
  Scenario Outline: To check if the warning message is shown if the prescription already exists
    Given I am on the OpenEyes "<page>" homepage
    And I enter login credentials "<username>" and "<password>"
    And I select Site "<siteName/Number>"
    Then I select a firm of "<firmName/Number>"

    Then I search for hospital number "<searchItem>"

    Then I select Create or View Episodes and Events

    Then I delete all the previous prescription events created
    Then I expand the "<speciality>" sidebar
    And I add a New Event "<event>"

    Then I check prescription already exists

    #Then I choose to filter by type "<type>"
    #And I select the No preservative checkbox

    Then I select a Common Drug "<commonDrug>"
    Then I enter a Dose of "<dose>" drops
    And I enter a route of "<route>"
    Then I enter a eyes option "<eyesOption>"

    And I enter a frequency of "<frequency>"
    Then I enter a duration of "<duration>"

    And I add Prescription comments of "<presComm>"

    Then I Save the Prescription Draft and confirm it has been created successfully

    Then I expand the "<speciality>" sidebar
    And I add a New Event "<event>"
    #Validation1
    Then I should see prescription already exists message

    #Validation2
    Then I click on "<warningOption>" and should see respective pages


    Examples:
      |page  |username|password|siteName/Number|firmName/Number      |searchItem|speciality|event       |type|commonDrug                                  |dose|route|eyesOption|frequency|duration|presComm|warningOption|
      |master|admin   |admin   |Barking        |Amit Blann (Glaucoma)|1009465   |Glaucoma  |Prescription|32  |adrenaline 0.01% eye drops (No Preservative)|3   |1    |1         |4        |3       |TEST    |no           |




#Assuming that the patient does not have prescription for the present day
  @OE-5667_2 @sprint22
  Scenario Outline: To check if the warning message is shown if the prescription already exists
    Given I am on the OpenEyes "<page>" homepage
    And I enter login credentials "<username>" and "<password>"
    And I select Site "<siteName/Number>"
    Then I select a firm of "<firmName/Number>"

    Then I search for hospital number "<searchItem>"

    Then I select Create or View Episodes and Events

    Then I expand the "<speciality>" sidebar
    And I add a New Event "<event>"

    Then I check prescription already exists

    Then I choose to filter by type "<filterType>"
    And I select the No preservative checkbox

    Then I select a Common Drug "<commonDrug>"
    Then I enter a Dose of "<dose>" drops
    And I enter a route of "<route>"
    Then I enter a eyes option "<eyesOption>"

    And I enter a frequency of "<frequency>"
    Then I enter a duration of "<duration>"

    And I add Prescription comments of "<presComm>"

    Then I Save the Prescription Draft and confirm it has been created successfully

    Then I expand the "<speciality>" sidebar
    And I add a New Event "<event>"
    Then I should see prescription already exists message


    Examples:
      |page|username|password|siteName/Number|firmName/Number         |searchItem|speciality|event       |filterType|commonDrug                                  |dose|route|eyesOption|frequency|duration|presComm|
      |master|admin |admin   |Barking        |Cataract firm (Cataract)|1009465   |Cataract  |Prescription|32        |adrenaline 0.01% eye drops (No Preservative)|3   |1    |1         |4        |1       |TEST    |


