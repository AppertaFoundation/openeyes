<?php

/**
 * Class ElementLetterTest
 * @covers ElementLetter
 * @method letters($fixtureId)
 */
class ElementLetterTest extends ActiveRecordTestCase
{
    protected $fixtures = array(
        'letters' => ElementLetter::class,
        'events' => Event::class,
        'episodes' => Episode::class,
        'patients' => Patient::class,
        'users' => User::class,
    );
    private ElementLetter $letter;

    protected array $columns_to_skip = ['date'];

    public function getModel()
    {
        // TODO: Implement getModel() method.
        return ElementLetter::model();
    }

    public function setUp()
    {
        parent::setUp();
        $path = Yii::app()->getRuntimePath() . '/test.pdf';

        if (!file_exists($path)) {
            $file = fopen($path, 'wb') or die;
            fclose($file);
        }
        Yii::app()->configure(
            array(
                'components' => array(
                    'user' => array(
                        'class' => 'DummyUser',
                    )
                )
            )
        );
        Yii::app()->session['selected_site_id'] = 1;
        Yii::app()->session['selected_firm_id'] = 2;
        Yii::app()->session['selected_institution_id'] = 1;
        $this->letter = $this->letters('letter1');
    }

    public function tearDown()
    {
        parent::tearDown();

        $path = Yii::app()->getRuntimePath() . '/test.pdf';

        if (file_exists($path)) {
            unlink($path);
        }

        Yii::app()->configure(
            array(
                'components' => array(
                    'user' => array(
                        'class' => 'OEWebUser',
                    )
                )
            )
        );
        unset(Yii::app()->session['selected_site_id']);
    }

    public function testGetExportUrl(): void
    {
        self::assertEquals(Yii::app()->params['correspondence_export_url'], $this->letter->getExportUrl());
    }

    /**
     * @throws CHttpException
     * @throws SoapFault
     * @throws Exception
     */
    public function testExport(): void
    {
        // Define the mock SOAP client and its two return values.
        $path = Yii::app()->getRuntimePath() . '/test.pdf';
        $client = $this->getMockBuilder('SoapClient')
            ->disableOriginalConstructor()
            ->setMethods(array('StoreDocument'))
            ->getMock();

        $client->expects(self::at(0))
            ->method('StoreDocument')
            ->willReturn(new ExportResult(true, 1, 1));

        $client->expects(self::at(1))
            ->method('StoreDocument')
            ->willReturn(new ExportResult(false, null, null));

        // Simulate a successful upload.
        $result = $this->letter->export($path, 'SOAP', $client);
        self::assertInstanceOf('ExportResult', $result);
        self::assertTrue($result->StoreDocumentResponse['Success']);
        self::assertEquals(1, $result->StoreDocumentResponse['DocumentId']);
        self::assertEquals(1, $result->StoreDocumentResponse['DocumentSupersessionSetId']);

        // Simulate a failed upload.
        $result = $this->letter->export($path, 'SOAP', $client);
        self::assertInstanceOf('ExportResult', $result);
        self::assertFalse($result->StoreDocumentResponse['Success']);
        self::assertNull($result->StoreDocumentResponse['DocumentId']);
        self::assertNull($result->StoreDocumentResponse['DocumentSupersessionSetId']);

        // Expect an exception if the export URL is null.
        Yii::app()->params['correspondence_export_url'] = null;
        self::assertNull(Yii::app()->params['correspondence_export_url']);
        $this->expectException(CHttpException::class);
        $this->letter->export($path);
    }

    /**
     * @throws SoapFault
     */
    public function testInvalidWebServiceType(): void
    {
        // Expect an exception if the web service type is REST
        $path = Yii::app()->getRuntimePath() . '/test.pdf';
        $this->expectException(CHttpException::class);
        $this->letter->export($path, 'REST');
    }
}

class ExportResult
{
    public array $StoreDocumentResponse = array();

    public function __construct($Success, $DocumentId, $SupersessionSetId)
    {
        $this->StoreDocumentResponse = array(
            'Success' => $Success,
            'DocumentId' => $DocumentId,
            'DocumentSupersessionSetId' => $SupersessionSetId,
        );
    }
}

class DummyUser extends OEWebUser
{
    public function getId()
    {
        return 1;
    }

    public function getIsGuest()
    {
        return false;
    }
}
