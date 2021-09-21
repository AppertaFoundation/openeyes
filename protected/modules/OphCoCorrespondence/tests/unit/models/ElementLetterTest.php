<?php

/**
 * Class ElementLetterTest
 * @covers ElementLetter
 * @covers Exportable
 * @method letters($fixtureId)
 */
class ElementLetterTest extends CDbTestCase
{
    protected $fixtures = array(
        'letters' => ElementLetter::class,
        'events' => Event::class,
        'episodes' => Episode::class,
        'patients' => Patient::class,
    );
    private ElementLetter $letter;

    public function setUp()
    {
        parent::setUp();
        Yii::app()->session['selected_firm_id'] = 2;
        Yii::app()->session['selected_institution_id'] = 1;
        $this->letter = $this->letters('letter1');
        file_put_contents('testfile.pdf', '');
    }

    public function tearDown()
    {
        parent::tearDown();
        if (file_exists('testfile.pdf')) {
            unlink('testfile.pdf');
        }
        unset(Yii::app()->session['selected_firm_id'], Yii::app()->session['selected_institution_id']);
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
        // Obtain the PDF path to pass to the export function.
        $pdf_path = 'testfile.pdf';

        // Define the mock SOAP client and its two return values.
        $client = $this->getMockBuilder('SoapClient')
            ->disableOriginalConstructor()
            ->setMethods(array('ReceiveFileByCrn'))
            ->getMock();

        $client->expects(self::at(0))
            ->method('ReceiveFileByCrn')
            ->willReturn(new ExportResult(1));

        $client->expects(self::at(1))
            ->method('ReceiveFileByCrn')
            ->willReturn(new ExportResult(null, 'Error'));

        // Simulate a successful upload.
        $result = $this->letter->export($pdf_path, 'SOAP', $client);
        self::assertInstanceOf('ExportResult', $result);
        self::assertEquals(1, $result->ReceiveFileByCrnResult['DocId']);
        self::assertNull($result->ReceiveFileByCrnResult['ErrorMessage']);

        // Simulate a failed upload.
        $result = $this->letter->export($pdf_path, 'SOAP', $client);
        self::assertInstanceOf('ExportResult', $result);
        self::assertNull($result->ReceiveFileByCrnResult['DocId']);
        self::assertNotNull($result->ReceiveFileByCrnResult['ErrorMessage']);

        // Expect an exception if the export URL is null.
        Yii::app()->params['correspondence_export_url'] = null;
        self::assertNull(Yii::app()->params['correspondence_export_url']);
        $this->expectException(CHttpException::class);
        $this->letter->export($pdf_path);
    }

    /**
     * @throws SoapFault
     */
    public function testInvalidWebServiceType(): void
    {
        // Obtain the PDF path to pass to the export function.
        $pdf_path = 'testfile.pdf';

        // Expect an exception if the web service type is REST
        $this->expectException(CHttpException::class);
        $this->letter->export($pdf_path, 'REST');
    }
}

class ExportResult
{
    public array $ReceiveFileByCrnResult = array();

    public function __construct($DocId, $ErrorMessage = null)
    {
        $this->ReceiveFileByCrnResult = array(
            'DocId' => $DocId,
            'ErrorMessage' => $ErrorMessage
        );
    }
}
