<?php
class _WrapperContactBehavior extends BaseActiveRecord
{
    public Contact $contact;
    public string $fax = '09876 543210';

    public function tableName()
    {
        return 'contact';
    }

    public function behaviors()
    {
        return array(
            'ContactBehavior' => array(
                'class' => 'application.behaviors.ContactBehavior',
            ),
        );
    }

    public function getTelephone()
    {
        return '01234 567890';
    }

    public function getPrefix()
    {
        return "Excuse me I'm a tad unwell";
    }
}

class _WrapperContactBehavior2 extends BaseActiveRecord
{
    public Contact $contact;
    public string $date_of_death = '2013-04-01';

    public int $is_deceased = 1;

    public function tableName()
    {
        return 'contact';
    }

    public function behaviors()
    {
        return array(
            'ContactBehavior' => array(
                'class' => 'application.behaviors.ContactBehavior',
            ),
        );
    }

    public function getTelephone()
    {
        return '01234 567890';
    }

    public function getFax()
    {
        return '09876 543210';
    }

    public function getPrefix()
    {
        return "Excuse me I'm a tad unwell";
    }

    public function getLetterArray()
    {
        return array("Four calling birds","Three french hens","Two turtle doves","Pheasant in a tree that grows pears");
    }

    public function getCorrespondenceName()
    {
        return 'General Sir Anthony Cecil Hogmanay Melchett';
    }

    public function getSalutationName()
    {
        return 'Kleptocrat';
    }
}

class _WrapperContactBehavior3 extends BaseActiveRecord
{
    public $contact;

    public function tableName()
    {
        return 'contact';
    }

    public function behaviors()
    {
        return array(
            'ContactBehavior' => array(
                'class' => 'application.behaviors.ContactBehavior',
            ),
        );
    }

    public function getTelephone()
    {
        return '01234 567890';
    }

    public function getFax()
    {
        return '09876 543210';
    }

    public function getPrefix()
    {
        return "Excuse me I'm a tad unwell";
    }

    public function getLetterArray()
    {
        return array("Four calling birds","Three french hens","Two turtle doves","Pheasant in a tree that grows pears");
    }

    public function getCorrespondenceName()
    {
        return array('PHP','Is','Fantastic');
    }
}

class ContactBehaviorTest extends CDbTestCase
{
    private _WrapperContactBehavior $model;
    private _WrapperContactBehavior2 $model2;
    private _WrapperContactBehavior3 $model3;
    public $fixtures = array(
        'contact' => 'Contact',
        'address' => 'Address',
        'country' => 'Country',
        'patient' => 'Patient',
        'episode' => 'Episode',
        'event' => 'Event',
        'user'  => 'User'
    );

    /**
     * @throws ReflectionException
     */
    public function setUp()
    {
        parent::setUp();

        $this->model = new _WrapperContactBehavior();

        $this->address = new Address();
        $this->address->attributes = array(
            'address1' => 'Line 1',
            'address2' => 'Line 2',
            'city' => 'City',
            'postcode' => 'Postcode',
            'county' => 'County',
            'country_id' => 1,
        );

        $label = new ContactLabel();
        $label->name = 'Test Label';

        /**
         * @var $contact Contact
         */
        $contact = ComponentStubGenerator::generate(
            'Contact',
            array(
                'address' => $this->address,
                'label' => $label,
                'fullName' => 'Henry Krinkle',
                'reversedFullName' => 'Krinkle Henry',
            )
        );

        $this->model->contact = $contact;

        $this->model2 = new _WrapperContactBehavior2();
        $this->model2->contact = $contact;

        $contact3 = ComponentStubGenerator::generate(
            'Contact',
            array(
                'address' => $this->address,
                'label' => $label,
                'fullName' => 'Henry Krinkle',
                'nick_name' => 'Jimbob',
            )
        );

        $this->model3 = new _WrapperContactBehavior3();
        $this->model3->contact = $contact3;
    }

    /**
     * @covers ContactBehavior
     */
    public function testGetLetterAddressNoParams()
    {
        $this->assertEquals(
            array(
                'Line 1',
                'Line 2',
                'City',
                'County',
                'Postcode',
            ),
            $this->model->getLetterAddress()
        );
    }

    /**
     * @covers ContactBehavior
     */
    public function testGetLetterAddressWithLabel()
    {
        $this->assertEquals(
            array(
                'Test Label',
                'Line 1',
                'Line 2',
                'City',
                'County',
                'Postcode',
            ),
            $this->model->getLetterAddress(array('include_label'=>true))
        );
    }

    /**
     * @covers ContactBehavior
     */
    public function testGetLetterAddressWithCountry()
    {
        $this->assertEquals(
            array(
                'Line 1',
                'Line 2',
                'City',
                'County',
                'Postcode',
                'United States',
            ),
            $this->model->getLetterAddress(array('include_country'=>true))
        );
    }

    /**
     * @covers ContactBehavior
     */
    public function testGetLetterAddressWithName()
    {
        $this->assertEquals(
            array(
                'Henry Krinkle',
                'Line 1',
                'Line 2',
                'City',
                'County',
                'Postcode',
            ),
            $this->model->getLetterAddress(array('include_name'=>true))
        );
    }

    /**
     * @covers ContactBehavior
     */
    public function testGetLetterAddressWithTelephone()
    {
        $this->assertEquals(
            array(
                'Line 1',
                'Line 2',
                'City',
                'County',
                'Postcode',
                'Tel: 01234 567890',
            ),
            $this->model->getLetterAddress(array('include_telephone'=>true))
        );
    }

    /**
     * @covers ContactBehavior
     */
    public function testGetLetterAddressWithFax()
    {
        $this->assertEquals(
            array(
                'Line 1',
                'Line 2',
                'City',
                'County',
                'Postcode',
                'Fax: 09876 543210',
            ),
            $this->model->getLetterAddress(array('include_fax'=>true))
        );
    }

    /**
     * @covers ContactBehavior
     */
    public function testGetLetterAddressWithDelimiter()
    {
        $this->assertEquals(
            'Line 1 *COUGH* Line 2 *COUGH* City *COUGH* County *COUGH* Postcode',
            $this->model->getLetterAddress(array('delimiter'=>' *COUGH* '))
        );
    }

    /**
     * @covers ContactBehavior
     */
    public function testGetLetterAddressWithPrefix()
    {
        $this->assertEquals(
            "Excuse me I'm a tad unwell: Line 1 *COUGH* Line 2 *COUGH* City *COUGH* County *COUGH* Postcode",
            $this->model->getLetterAddress(array(
                'delimiter'=>' *COUGH* ',
                'include_prefix' => "Excuse me I'm a tad unwell",
            ))
        );
    }

    /**
     * @covers ContactBehavior
     */
    public function testGetLetterAddressWithOwnerGetLetterArray()
    {
        $this->assertEquals(
            array(
                'Four calling birds',
                'Three french hens',
                'Two turtle doves',
                'Pheasant in a tree that grows pears',
            ),
            $this->model2->getLetterAddress()
        );
    }

    /**
     * @covers ContactBehavior
     */
    public function testGetLetterAddressWithCorrespondenceNameAsString()
    {
        $this->assertEquals(
            array(
                'General Sir Anthony Cecil Hogmanay Melchett',
                'Four calling birds',
                'Three french hens',
                'Two turtle doves',
                'Pheasant in a tree that grows pears',
            ),
            $this->model2->getLetterAddress(array('include_name' => true))
        );
    }

    /**
     * @covers ContactBehavior
     */
    public function testGetLetterAddressWithCorrespondenceNameAsArray()
    {
        $this->assertEquals(
            array(
                'PHP',
                'Is',
                'Fantastic',
                'Four calling birds',
                'Three french hens',
                'Two turtle doves',
                'Pheasant in a tree that grows pears',
            ),
            $this->model3->getLetterAddress(array('include_name' => true))
        );
    }

    /**
     * @covers ContactBehavior
     */
    public function testGetLetterAddressWithAllTheTrimmings()
    {
        $this->assertEquals(
            array(
                'Henry Krinkle',
                'Test Label',
                'Line 1',
                'Line 2',
                'City',
                'County',
                'Postcode',
                'United States',
                'Tel: 01234 567890',
                'Fax: 09876 543210',
            ),
            $this->model->getLetterAddress(array(
                'include_country' => true,
                'include_label' => true,
                'include_name' => true,
                'include_telephone' => true,
                'include_fax' => true,
            ))
        );
    }

    /**
     * @covers ContactBehavior
     */
    public function testGetLetterIntroductionNoParams()
    {
        $this->assertEquals('Dear Sir/Madam,', $this->model->getLetterIntroduction());
    }

    /**
     * @covers ContactBehavior
     */
    public function testGetLetterIntroductionNicknameNotPresent()
    {
        $this->assertEquals('Dear Sir/Madam,', $this->model->getLetterIntroduction(array('nickname' => true)));
    }

    /**
     * @covers ContactBehavior
     */
    public function testGetLetterIntroductionWithSalutationName()
    {
        $this->assertEquals('Dear Kleptocrat,', $this->model2->getLetterIntroduction());
    }

    /**
     * @covers ContactBehavior
     */
    public function testGetLetterIntroductionNickname()
    {
        $this->assertEquals('Dear Jimbob,', $this->model3->getLetterIntroduction(array('nickname' => true)));
    }

    /**
     * @covers ContactBehavior
     */
    public function testGetFullName()
    {
        $this->assertEquals('Henry Krinkle', $this->model->getFullName());
    }

    /**
     * @covers ContactBehavior
     */
    public function testGetReversedFullName()
    {
        $this->assertEquals('Krinkle Henry', $this->model->getReversedFullName());
    }

    /**
     * @covers ContactBehavior
     */
    public function testGetSalutationName()
    {
        $this->assertEquals('Dear Kleptocrat,', $this->model2->getLetterIntroduction());
    }

    /**
     * @covers ContactBehavior
     */
    public function testIsDeceasedNull()
    {
        $this->assertEquals(null, $this->model->isDeceased());
    }

    /**
     * @covers ContactBehavior
     */
    public function testIsDeceasedPatientDied()
    {
        $this->assertEquals(true, $this->model2->isDeceased());
    }

    /**
     * @covers ContactBehavior
     */
    public function testGetPrefix()
    {
        $this->assertEquals("Excuse me I'm a tad unwell", $this->model->getPrefix());
    }

    /**
     * @covers ContactBehavior
     */
    public function testAfterDelete()
    {
        $patient = $this->patient('patient10');
        $contact_id = $patient->contact_id;
        $patient->delete();

        $this->assertEquals(0, Yii::app()->db->createCommand('select count(*) from contact where id = ?')->queryScalar(array($contact_id)));
        $this->assertEquals(0, Yii::app()->db->createCommand('select count(*) from address where contact_id = ?')->queryScalar(array($contact_id)));
    }
}
