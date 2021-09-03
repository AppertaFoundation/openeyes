<?php
/**
 * (C) OpenEyes Foundation, 2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
class OEPDFOptionsTest extends CTestCase
{
    /**
     * @covers OEPDFOptions
     * @throws Exception
     */
    public function testInject_Catalog_OneLine_WithoutNames()
    {
        $tmp_dir = sys_get_temp_dir();

        $im_a_pdf = "%PDF-1.4
1 0 obj
<<
/Title My Test PDF
/Creator MW
/Producer was awesome
/CreationDate timeless
>>
endobj
2 0 obj
<<
I'm an object :D
>>
endobj
3 0 obj
34234325
endobj
4 0 obj
<< /Type /Catalog /Pages 2 0 R /Outlines 37 0 R /PageMode /UseOutlines /Dests 36 0 R >>
endobj
xref
0 4
0000000000 65535 f
0000000009 00000 n
0000000106 00000 n
0000000144 00000 n
0000000168 00000 n
trailer
<<
/Size 4
/Info 1 0 R
/Root 4 0 R
>>
startxref
271
%%EOF
";

        $tmp_file = $tmp_dir.'/testInject_Catalog_OneLine_WithoutNames_'.time().'.pdf';

        file_put_contents($tmp_file, $im_a_pdf);

        $pdf = new OEPDFOptions($tmp_file);
        $pdf->injectJS('print(true);');
        $pdf->write();

        $output = file_get_contents($tmp_file);
        @unlink($tmp_file);

        $this->assertEquals("%PDF-1.4
1 0 obj
<<
/Title My Test PDF
/Creator MW
/Producer was awesome
/CreationDate timeless
>>
endobj
2 0 obj
<<
I'm an object :D
>>
endobj
3 0 obj
34234325
endobj
4 0 obj
<< /Type /Catalog /Pages 2 0 R /Outlines 37 0 R /PageMode /UseOutlines /Dests 36 0 R /Names << /JavaScript 5 0 R >> >>
endobj
5 0 obj
<< /Names [ (EmbeddedJS) 6 0 R ] >>
endobj
6 0 obj
<< /S /JavaScript /JS (\xfe\xff\0p\0r\0i\0n\0t\0(\0t\0r\0u\0e\0)\0;) >>
endobj
xref
0 6
0000000000 65535 f
0000000009 00000 n
0000000106 00000 n
0000000144 00000 n
0000000168 00000 n
0000000302 00000 n
0000000353 00000 n
trailer
<< /Size 5 /Root 4 0 R /Info 1 0 R >>
startxref
422
%%EOF
", $output);
    }

    /**
     * @covers OEPDFOptions
     * @throws Exception
     */
    public function testInject_Catalog_OneLine_WithNames()
    {
        $tmp_dir = sys_get_temp_dir();

        $im_a_pdf = "%PDF-1.4
1 0 obj
<<
/Title My Test PDF
/Creator MW
/Producer was awesome
/CreationDate timeless
>>
endobj
2 0 obj
<<
I'm an object :D
>>
endobj
3 0 obj
34234325
endobj
4 0 obj
<< /Type /Catalog /Names << /Rubbish 2 0 R >> /Pages 2 0 R /Outlines 37 0 R /PageMode /UseOutlines /Dests 36 0 R >>
endobj
xref
0 4
0000000000 65535 f
0000000009 00000 n
0000000106 00000 n
0000000144 00000 n
0000000168 00000 n
trailer
<<
/Size 4
/Info 1 0 R
/Root 4 0 R
>>
startxref
271
%%EOF
";

        $tmp_file = $tmp_dir.'/testInject_Catalog_OneLine_WithNames_'.time().'.pdf';

        file_put_contents($tmp_file, $im_a_pdf);

        $pdf = new OEPDFOptions($tmp_file);
        $pdf->injectJS('print(true);');
        $pdf->write();

        $output = file_get_contents($tmp_file);
        @unlink($tmp_file);

        $this->assertEquals("%PDF-1.4
1 0 obj
<<
/Title My Test PDF
/Creator MW
/Producer was awesome
/CreationDate timeless
>>
endobj
2 0 obj
<<
I'm an object :D
>>
endobj
3 0 obj
34234325
endobj
4 0 obj
<< /Type /Catalog /Names << /JavaScript 5 0 R /Rubbish 2 0 R >> /Pages 2 0 R /Outlines 37 0 R /PageMode /UseOutlines /Dests 36 0 R >>
endobj
5 0 obj
<< /Names [ (EmbeddedJS) 6 0 R ] >>
endobj
6 0 obj
<< /S /JavaScript /JS (\xfe\xff\0p\0r\0i\0n\0t\0(\0t\0r\0u\0e\0)\0;) >>
endobj
xref
0 6
0000000000 65535 f
0000000009 00000 n
0000000106 00000 n
0000000144 00000 n
0000000168 00000 n
0000000317 00000 n
0000000368 00000 n
trailer
<< /Size 5 /Root 4 0 R /Info 1 0 R >>
startxref
437
%%EOF
", $output);
    }

    /**
     * @covers OEPDFOptions
     * @throws Exception
     */
    public function testInject_Catalog_MultiLine_WithoutNames()
    {
        $tmp_dir = sys_get_temp_dir();

        $im_a_pdf = "%PDF-1.4
1 0 obj
<<
/Title My Test PDF
/Creator MW
/Producer was awesome
/CreationDate timeless
>>
endobj
2 0 obj
<<
I'm an object :D
>>
endobj
3 0 obj
34234325
endobj
4 0 obj
<<
/Type /Catalog
/Pages 2 0 R
/Outlines 37 0 R
/PageMode
/UseOutlines
/Dests 36 0 R
>>
endobj
xref
0 4
0000000000 65535 f
0000000009 00000 n
0000000106 00000 n
0000000144 00000 n
0000000168 00000 n
trailer
<<
/Size 4
/Info 1 0 R
/Root 4 0 R
>>
startxref
271
%%EOF
";

        $tmp_file = $tmp_dir.'/testInject_Catalog_MultiLine_WithoutNames_'.time().'.pdf';

        file_put_contents($tmp_file, $im_a_pdf);

        $pdf = new OEPDFOptions($tmp_file);
        $pdf->injectJS('print(true);');
        $pdf->write();

        $output = file_get_contents($tmp_file);
        @unlink($tmp_file);

        $this->assertEquals("%PDF-1.4
1 0 obj
<<
/Title My Test PDF
/Creator MW
/Producer was awesome
/CreationDate timeless
>>
endobj
2 0 obj
<<
I'm an object :D
>>
endobj
3 0 obj
34234325
endobj
4 0 obj
<<
/Type /Catalog
/Pages 2 0 R
/Outlines 37 0 R
/PageMode
/UseOutlines
/Dests 36 0 R
/Names << /JavaScript 5 0 R >>
>>
endobj
5 0 obj
<< /Names [ (EmbeddedJS) 6 0 R ] >>
endobj
6 0 obj
<< /S /JavaScript /JS (\xfe\xff\0p\0r\0i\0n\0t\0(\0t\0r\0u\0e\0)\0;) >>
endobj
xref
0 6
0000000000 65535 f
0000000009 00000 n
0000000106 00000 n
0000000144 00000 n
0000000168 00000 n
0000000302 00000 n
0000000353 00000 n
trailer
<< /Size 5 /Root 4 0 R /Info 1 0 R >>
startxref
422
%%EOF
", $output);
    }

    /**
     * @covers OEPDFOptions
     * @throws Exception
     */
    public function testInject_Catalog_MultiLine_WithNames()
    {
        $tmp_dir = sys_get_temp_dir();

        $im_a_pdf = "%PDF-1.4
1 0 obj
<<
/Title My Test PDF
/Creator MW
/Producer was awesome
/CreationDate timeless
>>
endobj
2 0 obj
<<
I'm an object :D
>>
endobj
3 0 obj
34234325
endobj
4 0 obj
<<
/Type /Catalog
/Pages 2 0 R
/Names << /Rubbish 2 0 R >>
/Outlines 37 0 R
/PageMode
/UseOutlines
/Dests 36 0 R
>>
endobj
xref
0 4
0000000000 65535 f
0000000009 00000 n
0000000106 00000 n
0000000144 00000 n
0000000168 00000 n
trailer
<<
/Size 4
/Info 1 0 R
/Root 4 0 R
>>
startxref
271
%%EOF
";

        $tmp_file = $tmp_dir.'/testInject_Catalog_MultiLine_WithNames_'.time().'.pdf';

        file_put_contents($tmp_file, $im_a_pdf);

        $pdf = new OEPDFOptions($tmp_file);
        $pdf->injectJS('print(true);');
        $pdf->write();

        $output = file_get_contents($tmp_file);
        @unlink($tmp_file);

        $this->assertEquals("%PDF-1.4
1 0 obj
<<
/Title My Test PDF
/Creator MW
/Producer was awesome
/CreationDate timeless
>>
endobj
2 0 obj
<<
I'm an object :D
>>
endobj
3 0 obj
34234325
endobj
4 0 obj
<<
/Type /Catalog
/Pages 2 0 R
/Names << /JavaScript 5 0 R /Rubbish 2 0 R >>
/Outlines 37 0 R
/PageMode
/UseOutlines
/Dests 36 0 R
>>
endobj
5 0 obj
<< /Names [ (EmbeddedJS) 6 0 R ] >>
endobj
6 0 obj
<< /S /JavaScript /JS (\xfe\xff\0p\0r\0i\0n\0t\0(\0t\0r\0u\0e\0)\0;) >>
endobj
xref
0 6
0000000000 65535 f
0000000009 00000 n
0000000106 00000 n
0000000144 00000 n
0000000168 00000 n
0000000317 00000 n
0000000368 00000 n
trailer
<< /Size 5 /Root 4 0 R /Info 1 0 R >>
startxref
437
%%EOF
", $output);
    }

    /**
     * @covers OEPDFOptions
     * @throws Exception
     */
    public function testDisablePrintScalingtest_Catalog_OneLine()
    {
        $tmp_dir = sys_get_temp_dir();

        $im_a_pdf = "%PDF-1.4
1 0 obj
<<
/Title My Test PDF
/Creator MW
/Producer was awesome
/CreationDate timeless
>>
endobj
2 0 obj
<<
I'm an object :D
>>
endobj
3 0 obj
34234325
endobj
4 0 obj
<< /Type /Catalog /Pages 2 0 R /Outlines 37 0 R /PageMode /UseOutlines /Dests 36 0 R >>
endobj
xref
0 4
0000000000 65535 f
0000000009 00000 n
0000000106 00000 n
0000000144 00000 n
0000000168 00000 n
trailer
<<
/Size 4
/Info 1 0 R
/Root 4 0 R
>>
startxref
271
%%EOF
";

        $tmp_file = $tmp_dir.'/testInject_Catalog_OneLine_WithoutNames_'.time().'.pdf';

        file_put_contents($tmp_file, $im_a_pdf);

        $pdf = new OEPDFOptions($tmp_file);
        $pdf->disablePrintScaling();
        $pdf->write();

        $output = file_get_contents($tmp_file);
        @unlink($tmp_file);

        $this->assertEquals("%PDF-1.4
1 0 obj
<<
/Title My Test PDF
/Creator MW
/Producer was awesome
/CreationDate timeless
>>
endobj
2 0 obj
<<
I'm an object :D
>>
endobj
3 0 obj
34234325
endobj
4 0 obj
<< /Type /Catalog /Pages 2 0 R /Outlines 37 0 R /PageMode /UseOutlines /Dests 36 0 R /ViewerPreferences << /Direction/L2R/PrintScaling/None >> >>
endobj
xref
0 4
0000000000 65535 f
0000000009 00000 n
0000000106 00000 n
0000000144 00000 n
0000000168 00000 n
trailer
<< /Size 4 /Root 4 0 R /Info 1 0 R >>
startxref
329
%%EOF
", $output);
    }

    /**
     * @covers OEPDFOptions
     * @throws Exception
     */
    public function testDisablePrintScaling_Catalog_MultiLine_WithoutNames()
    {
        $tmp_dir = sys_get_temp_dir();

        $im_a_pdf = "%PDF-1.4
1 0 obj
<<
/Title My Test PDF
/Creator MW
/Producer was awesome
/CreationDate timeless
>>
endobj
2 0 obj
<<
I'm an object :D
>>
endobj
3 0 obj
34234325
endobj
4 0 obj
<<
/Type /Catalog
/Pages 2 0 R
/Outlines 37 0 R
/PageMode
/UseOutlines
/Dests 36 0 R
>>
endobj
xref
0 4
0000000000 65535 f
0000000009 00000 n
0000000106 00000 n
0000000144 00000 n
0000000168 00000 n
trailer
<<
/Size 4
/Info 1 0 R
/Root 4 0 R
>>
startxref
271
%%EOF
";

        $tmp_file = $tmp_dir.'/testInject_Catalog_MultiLine_WithoutNames_'.time().'.pdf';

        file_put_contents($tmp_file, $im_a_pdf);

        $pdf = new OEPDFOptions($tmp_file);
        $pdf->disablePrintScaling();
        $pdf->write();

        $output = file_get_contents($tmp_file);
        @unlink($tmp_file);

        $this->assertEquals("%PDF-1.4
1 0 obj
<<
/Title My Test PDF
/Creator MW
/Producer was awesome
/CreationDate timeless
>>
endobj
2 0 obj
<<
I'm an object :D
>>
endobj
3 0 obj
34234325
endobj
4 0 obj
<<
/Type /Catalog
/Pages 2 0 R
/Outlines 37 0 R
/PageMode
/UseOutlines
/Dests 36 0 R
/ViewerPreferences << /Direction/L2R/PrintScaling/None >>
>>
endobj
xref
0 4
0000000000 65535 f
0000000009 00000 n
0000000106 00000 n
0000000144 00000 n
0000000168 00000 n
trailer
<< /Size 4 /Root 4 0 R /Info 1 0 R >>
startxref
329
%%EOF
", $output);
    }
}
