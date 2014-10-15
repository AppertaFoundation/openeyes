<?php
/**
 * (C) OpenEyes Foundation, 2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

class WKHtmlToPDFTest extends CTestCase
{
	public $wkhtmltopdf;

	public function setUp()
	{
		parent::setUp();
		$this->wkhtmltopdf = Yii::app()->params['wkhtmltopdf_path'];
	}

	public function tearDown()
	{
		Yii::app()->params['wkhtmltopdf_path'] = $this->wkhtmltopdf;
	}

	public function testConstruct_InvalidPath()
	{
		Yii::app()->params['wkhtmltopdf_path'] = '2r9hy21dw8s9h8x32dh89y3deqw';

		$this->setExpectedException('Exception','2r9hy21dw8s9h8x32dh89y3deqw is missing.');

		$wk = new WKHtmlToPDF;
	}

	public function testConstruct_ValidPath()
	{
		Yii::app()->params['wkhtmltopdf_path'] = __FILE__;

		$wk = new WKHtmlToPDF;
	}

	public function testConstruct_ValidPath_ReducedFunctionality()
	{
		Yii::app()->params['wkhtmltopdf_path'] = __FILE__;

		$wk = $this->getMockBuilder('WKHtmlToPDF')
			->setMethods(array('execute'))
			->getMock();

		$wk->expects($this->once())
			->method('execute')
			->with(__FILE__.' 2>&1')
			->will($this->returnValue('reduced functionality'));

		$this->setExpectedException('Exception','wkhtmltopdf has not been compiled with patched QT and so cannot be used.');

		$wk->__construct();
	}
	
	public function testRemapAssetPaths()
	{
		$html = '<a href="/assets/blah1">test</a><script type="text/javascript" src="/assets/blah2"></script>';

		$wk = $this->getMockBuilder('WKHtmlToPDF')
			->disableOriginalConstructor()
			->setMethods(array('getAssetManager'))
			->getMock();

		$asset_manager = new stdClass;
		$asset_manager->basePath = '/test/one/two';

		$wk->expects($this->any())
			->method('getAssetManager')
			->will($this->returnValue($asset_manager));

		$this->assertEquals('<a href="/test/one/two/blah1">test</a><script type="text/javascript" src="/test/one/two/blah2"></script>',$wk->remapAssetPaths($html));
	}

	public function testGenerateDocRef()
	{
		$wk = $this->getMockBuilder('WKHtmlToPDF')
			->setMethods(array('readFile'))
			->disableOriginalConstructor()
			->getMock();

		$docref = $wk->generateDocRef(123);

		$this->assertRegExp('/^E:123\/([A-Z0-9]+)\/\{\{PAGE\}\}$/',$docref);
	}

	public function testFormatFooter_Left()
	{
		$wk = $this->getMockBuilder('WKHtmlToPDF')
			->setMethods(array('readFile'))
			->disableOriginalConstructor()
			->getMock();

		$patient = new Patient;
		$patient->contact = new Contact;

		$this->assertEquals('test blah one two three',$wk->formatFooter('test {{FOOTER_LEFT}} one two three','blah',0,0,$patient,0,0));
	}

	public function testFormatFooter_Middle()
	{
		$wk = $this->getMockBuilder('WKHtmlToPDF')
			->setMethods(array('readFile'))
			->disableOriginalConstructor()
			->getMock();

		$patient = new Patient;
		$patient->contact = new Contact;

		$this->assertEquals('test blah one two three',$wk->formatFooter('test {{FOOTER_MIDDLE}} one two three',0,'blah',0,$patient,0,0));
	}

	public function testFormatFooter_Right()
	{
		$wk = $this->getMockBuilder('WKHtmlToPDF')
			->setMethods(array('readFile'))
			->disableOriginalConstructor()
			->getMock();

		$patient = new Patient;
		$patient->contact = new Contact;

		$this->assertEquals('test blah one two three',$wk->formatFooter('test {{FOOTER_RIGHT}} one two three',0,0,'blah',$patient,0,0));
	}

	public function testFormatFooter_PatientName()
	{
		$wk = $this->getMockBuilder('WKHtmlToPDF')
			->setMethods(array('readFile'))
			->disableOriginalConstructor()
			->getMock();

		$patient = new Patient;
		$patient->contact = new Contact;
		$patient->contact->first_name = 'Fred';
		$patient->contact->last_name = 'Smith';
		$patient->contact->title = 'Lord';

		$this->assertEquals('test '.$patient->getHSCICName(true).' one two three',$wk->formatFooter('test {{PATIENT_NAME}} one two three',0,0,0,$patient,0,0));
	}

	public function testFormatFooter_PatientHosNum()
	{
		$wk = $this->getMockBuilder('WKHtmlToPDF')
			->setMethods(array('readFile'))
			->disableOriginalConstructor()
			->getMock();

		$patient = new Patient;
		$patient->contact = new Contact;
		$patient->hos_num = 3423435;

		$this->assertEquals('test '.$patient->hos_num.' one two three',$wk->formatFooter('test {{PATIENT_HOSNUM}} one two three',0,0,0,$patient,0,0));
	}

	public function testFormatFooter_PatientNHSNum()
	{
		$wk = $this->getMockBuilder('WKHtmlToPDF')
			->setMethods(array('readFile'))
			->disableOriginalConstructor()
			->getMock();

		$patient = new Patient;
		$patient->contact = new Contact;
		$patient->nhs_num = 3423435;

		$this->assertEquals('test '.$patient->nhs_num.' one two three',$wk->formatFooter('test {{PATIENT_NHSNUM}} one two three',0,0,0,$patient,0,0));
	}

	public function testFormatFooter_Barcode()
	{ 
		$wk = $this->getMockBuilder('WKHtmlToPDF')
			->setMethods(array('readFile'))
			->disableOriginalConstructor()
			->getMock();

		$patient = new Patient;
		$patient->contact = new Contact;

		$this->assertEquals('test 923r8f2e9wfuwef9e one two three',$wk->formatFooter('test {{BARCODE}} one two three',0,0,0,$patient,'923r8f2e9wfuwef9e',0));
	}

	public function testFormatFooter_DocRef()
	{
		$wk = $this->getMockBuilder('WKHtmlToPDF')
			->setMethods(array('readFile'))
			->disableOriginalConstructor()
			->getMock();

		$patient = new Patient;
		$patient->contact = new Contact;

		$this->assertEquals('test 923r8f2e9wfuwef9e one two three',$wk->formatFooter('test {{DOCREF}} one two three',0,0,0,$patient,0,'923r8f2e9wfuwef9e'));
	}

	public function testFormatFooter_Page()
	{
		$wk = $this->getMockBuilder('WKHtmlToPDF')
			->setMethods(array('readFile'))
			->disableOriginalConstructor()
			->getMock();

		$patient = new Patient;
		$patient->contact = new Contact;

		$this->assertEquals('test <span class="page"></span> one two three',$wk->formatFooter('test {{PAGE}} one two three',0,0,0,$patient,0,0));
	}

	public function testFormatFooter_Pages()
	{
		$wk = $this->getMockBuilder('WKHtmlToPDF')
			->setMethods(array('readFile'))
			->disableOriginalConstructor()
			->getMock();

		$patient = new Patient;
		$patient->contact = new Contact;

		$this->assertEquals('test <span class="topage"></span> one two three',$wk->formatFooter('test {{PAGES}} one two three',0,0,0,$patient,0,0));
	}

	public function testFormatFooter_Cascade()
	{
		$wk = $this->getMockBuilder('WKHtmlToPDF')
			->setMethods(array('readFile'))
			->disableOriginalConstructor()
			->getMock();

		$patient = new Patient;
		$patient->hos_num = '12345';
		$patient->nhs_num = '54321';
		$patient->contact = new Contact;
		$patient->contact->first_name = 'Henry';
		$patient->contact->last_name = 'Krinkle';
		$patient->contact->title = 'Mr';

		$left = 'This is the left, {{PATIENT_NAME}} {{BARCODE}} {{PAGE}}';
		$middle = 'This is the middle, {{PATIENT_HOSNUM}} {{PATIENT_NHSNUM}} {{PAGES}}';
		$right = 'This is the right, {{DOCREF}} {{PAGE}} {{PAGES}}';

		$this->assertEquals('This is the left, '.$patient->getHSCICName(true).' barc0de <span class="page"></span> This is the middle, 12345 54321 <span class="topage"></span> This is the right, d0cr3f <span class="page"></span> <span class="topage"></span>',$wk->formatFooter('{{FOOTER_LEFT}} {{FOOTER_MIDDLE}} {{FOOTER_RIGHT}}',$left,$middle,$right,$patient,'barc0de','d0cr3f'));
	}

	public function testGetPDFInject()
	{
		$wk = $this->getMockBuilder('WKHtmlToPDF')
			->setMethods(array('readFile'))
			->disableOriginalConstructor()
			->getMock();

		$this->setExpectedException('Exception','Invalid file or not a PDF.');

		$wk->getPDFInject(__FILE__);
	}

	public function getTestEvent()
	{
		$event = $this->getMockBuilder('Event')
			->setMethods(array('getImageDirectory'))
			->getMock();

		$patient = new Patient;
		$patient->hos_num = '12345';
		$patient->nhs_num = '54321';
		$patient->contact = new Contact;
		$patient->contact->first_name = 'Henry';
		$patient->contact->last_name = 'Krinkle';
		$patient->contact->title = 'Mr';

		$event->episode = new Episode;
		$event->episode->patient = $patient;
		$event->id = 9999991;

		return $event;
	}

	public function testGenerateEventPDF()
	{
		$wk = $this->getMockBuilder('WKHtmlToPDF')
			->disableOriginalConstructor()
			->setMethods(array('remapAssetPaths','generateDocRef','findOrCreateDirectory','writeFile','formatFooter','execute','getPDFInject','getAssetManager','deleteFile','fileExists','fileSize'))
			->getMock();

		Yii::app()->params['wkhtmltopdf_path'] = __FILE__;

		$wk->__construct();

		$asset_manager = new stdClass;
		$asset_manager->basePath = 'testing123';

		$wk->expects($this->any())
			->method('getAssetManager')
			->will($this->returnValue($asset_manager));

		$event = $this->getTestEvent();

		$event->expects($this->any())
			->method('getImageDirectory')
			->will($this->returnValue('/blah/one/two/seven'));

		$wk->expects($this->once())
			->method('remapAssetPaths')
			->with('test')
			->will($this->returnValue('test2'));

		$wk->expects($this->once())
			->method('generateDocRef')
			->with('9999991')
			->will($this->returnValue('X3C4499JJ'));

		$wk->expects($this->once())
			->method('findOrCreateDirectory')
			->with('/blah/one/two/seven');

		$wk->expects($this->once())
			->method('execute')
			->with(__FILE__ . " --footer-html '/blah/one/two/seven/footer.html' --print-media-type '/blah/one/two/seven/event.html' '/blah/one/two/seven/event.pdf' 2>&1");

		$wk->expects($this->any())
			->method('fileExists')
			->will($this->returnValue(false));

		$wk->expects($this->any())
			->method('fileSize')
			->will($this->returnValue(1));

		$result = $wk->generateEventPDF($event, 'test', false, false);

		$this->assertFalse($result);
	}

	public function testGenerateEventPDF_FileSize()
	{
		$wk = $this->getMockBuilder('WKHtmlToPDF')
			->disableOriginalConstructor()
			->setMethods(array('remapAssetPaths','generateDocRef','findOrCreateDirectory','writeFile','formatFooter','execute','getPDFInject','getAssetManager','deleteFile','fileExists','fileSize'))
			->getMock();

		$wk->expects($this->any())
			->method('fileExists')
			->will($this->returnValue(true));

		$wk->expects($this->any())
			->method('fileSize')
			->will($this->returnValue(0));

		$result = $wk->generateEventPDF($this->getTestEvent(), 'test', false, false);

		$this->assertFalse($result);
	}

	public function testGenerateEventPDF_Success()
	{
		$wk = $this->getMockBuilder('WKHtmlToPDF')
			->disableOriginalConstructor()
			->setMethods(array('remapAssetPaths','generateDocRef','findOrCreateDirectory','writeFile','formatFooter','execute','getPDFInject','getAssetManager','deleteFile','fileExists','fileSize'))
			->getMock();

		$wk->expects($this->any())
			->method('fileExists')
			->will($this->returnValue(true));

		$wk->expects($this->any())
			->method('fileSize')
			->will($this->returnValue(1));

		$result = $wk->generateEventPDF($this->getTestEvent(), 'test', false, false);

		$this->assertTrue($result);
	}

	public function testGenerateEventPDF_OutputHTML()
	{
		$wk = $this->getMockBuilder('WKHtmlToPDF')
			->disableOriginalConstructor()
			->setMethods(array('remapAssetPaths','generateDocRef','findOrCreateDirectory','writeFile','formatFooter','execute','getPDFInject','getAssetManager','deleteFile','fileExists','fileSize'))
			->getMock();

		$wk->expects($this->once())
			->method('remapAssetPaths')
			->with('test')
			->will($this->returnValue('dsiofjdsifjsdf ksdfj sdiof jsdiofj oisdfjiosdfjosd'));

		$wk->expects($this->once())
			->method('formatFooter')
			->will($this->returnValue('FOOTER39 4u10239ru89ef298 dfu29 8uef9wu9ew8fu 98wfu9ewuf we'));

		$wk->expects($this->any())
			->method('fileExists')
			->will($this->returnValue(true));

		$wk->expects($this->any())
			->method('fileSize')
			->will($this->returnValue(1));

		ob_start();
		$result = $wk->generateEventPDF($this->getTestEvent(), 'test', true, false);
		$html = ob_get_contents();
		ob_end_clean();

		$this->assertTrue($result);

		$this->assertEquals("dsiofjdsifjsdf ksdfj sdiof jsdiofj oisdfjiosdfjosdFOOTER39 4u10239ru89ef298 dfu29 8uef9wu9ew8fu 98wfu9ewuf we", $html);
	}

	public function testGenerateEventPDF_JSInject()
	{
		$wk = $this->getMockBuilder('WKHtmlToPDF')
			->disableOriginalConstructor()
			->setMethods(array('remapAssetPaths','generateDocRef','findOrCreateDirectory','writeFile','formatFooter','execute','getPDFInject','getAssetManager','deleteFile','fileExists','fileSize'))
			->getMock();

		$wk->expects($this->any())
			->method('fileExists')
			->will($this->returnValue(true));

		$wk->expects($this->any())
			->method('fileSize')
			->will($this->returnValue(1));

		$pdf = $this->getMockBuilder('OEPDFInject')
			->disableOriginalConstructor()
			->setMethods(array('inject'))
			->getMock();

		$pdf->expects($this->once())
			->method('inject')
			->with('print(true);');

		$wk->expects($this->once())
			->method('getPDFInject')
			->with('/event.pdf')
			->will($this->returnValue($pdf));

		$wk->generateEventPDF($this->getTestEvent(), 'test', false, true);
	}
}
