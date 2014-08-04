
Feature: Create New Anaesthetic Satisfaction Audit
  In order to cover every possible route throughout the site
  As an automation tester
  I want to build a template with supporting code for each web page

  Scenario Outline: Login and create a Anaesthetic Satisfaction Audit

    Given I am on the OpenEyes "<environment>" homepage
    And I enter login credentials "<username>" and "<password>"
    And I select Site "2"
    Then I select a firm of "3"

    Then I search for hospital number "<hospnumber>"

    Then I select the Latest Event
    Then I expand the Glaucoma sidebar
    And I add a New Event "<EventType>"

    #!!! THIS DATABASE SET DOESNT CONTAIN ANY LASERSITE OR LAZERS SO DO NOT RUN THIS FEATURE!!!

    Then I select a Laser site ID "<LaserSite>"
    And I select a Laser of "<Laser>"
    And I select a Laser Operator of "<Surgeon>"
    Then I select a Right Procedure of "62"
    Then I select a Left Procedure of "363"





  Examples: User details
    | environment   | site      | username | password     | hospnumber   | nhs        | last    | first  | EventType     | LaserSite | Laser | Surgeon |
    | master        | 1         | admin    | admin        | 1009465      | 8821388753 | Coffin, | Violet | Laser         | 2m        | 3     | 111     |

  #Laser Site ID
  # 2 = Bedford
  # 1 = City Road
  # 3= Ealing
  # 6 = Mile End
  # 4 = Northwick Park
  # 9 = St Ann's
  # 5 = St George's

  #Laser (City Road site)
  # 3 = HGM YAG Laser
  # 4 = Coherent Novus Omni
  # 5 = HGM Lightlas
  # 6 = Lasag YAG
  # 7 = HGM SPECTRUM
  # 8 = LPULSA Q-YAG
  # 9 = PDT
  # 10 = HGM Elite
  # 19 = Pascal Photocoagulator
  # 20 = HGM 532
  # 21 = Surgical Designs Ellex YAG
  # 24 = HGM Elite (Mackellar)
  # 25 = HGM Elite (Clinic15)
  # 26 = Coherent Ultima 2000 (JSS)
  # 31 = Litechnica LightLas 532 (14.5)
  # 32 = Luminus Selector Trio
  # 33 = Ellex 2RT
  # 35 = HGM Elite (Cumb)

  #Surgeon
  # 111 = Abou-Rayyah Yassir
  # 112 = Acheson James
  # 113 = Adams Gill
  # 117 = Addison Peter
  # 2 = Ali Nadeem
  # 3 = Allan Bruce

  #Procedure
  # 62 = Capsulotomy (YAG)
  # 363 = Cycloablation
  # 364 = Focal laser photocoagulation
  # 365 = Laser demarcation
  # 366 = Laser gonioplasty
  # 367 = Laser hyaloidotomy
  # 368 = Laser iridoplasty
  # 128 = Laser iridotomy
  # 176 = Laser retinopexy
  # 369 = Laser to chorioretinal lesion
  # 129 = Laser trabeculoplasty
  # 370 = Laser vitreolysis
  # 371 = Macular grid
  # 177 = Panretinal photocoagulation
  # 372 = Selective laser trabeculoplasty
  # 373 = Suture lysis

