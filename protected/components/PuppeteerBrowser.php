<?php

use \Nesk\Puphpeteer\Puppeteer;
use Nesk\Puphpeteer\Resources\Browser;
use Nesk\Puphpeteer\Resources\Page;
use Nesk\Rialto\Data\JsFunction;

/**
 * Class PuppeteerBrowser
 * @property Browser $browser
 * @property int $readTimeout
 * @property bool $logBrowserConsole
 * @property string $leftFooterTemplate
 * @property string $middleFooterTemplate
 * @property string $rightFooterTemplate
 * @property string $topMargin
 * @property string $leftMargin
 * @property string $bottomMargin
 * @property string $rightMargin
 */
class PuppeteerBrowser extends CApplicationComponent
{
    protected $_browser;
    private $_readTimeout = 65;
    private $_logBrowserConsole = false;

    protected $_left;
    protected $_middle;
    protected $_right;

    protected $barcodes = array();
    protected $documents = 1;
    protected $docrefs = array();
    protected $patients = array();
    public $custom_tags = array();

    protected $_top_margin;
    protected $_bottom_margin;
    protected $_left_margin;
    protected $_right_margin;
    public $page_size = 'A4';
    public $orientation = 'Portrait';
    public $page_width;
    public $page_height;
    protected $_scale;

    /**
     * Initialise the Puppeteer browser component.
     */
    public function init()
    {
        parent::init();
        $puppeteer = new Puppeteer(
            ['read_timeout' => $this->readTimeout, 'log_browser_console' => $this->logBrowserConsole]
        );
        $this->browser = $puppeteer->launch(
            array('headless' => true, 'args' => array('--no-sandbox', '--window-size=1280,720'))
        );
    }

    /**
     * @param string $footer
     * @param string $left
     * @param string $middle
     * @param string $right
     * @return string
     */
    protected function formatFooter($footer, $left, $middle, $right)
    {
        $patient_names = array();
        $patient_hosnums = array();
        $patient_nhsnums = array();
        $patient_dobs = array();

        foreach ($this->patients as $patient) {
            $patient_names[] = $patient->getHSCICName(true);
            $patient_hosnums[] = $patient->hos_num;
            $patient_nhsnums[] = $patient->nhsnum;
            $patient_dobs[] = date('d-m-Y', strtotime($patient->dob));
        }

        while (count($patient_names) < $this->documents) {
            $patient_names[] = $patient_names[count($patient_names) - 1];
            $patient_hosnums[] = $patient_hosnums[count($patient_hosnums) - 1];
            $patient_nhsnums[] = $patient_nhsnums[count($patient_nhsnums) - 1];
            $patient_dobs[] = $patient_dobs[count($patient_dobs) - 1];
        }

        while (count($this->barcodes) < $this->documents) {
            $this->barcodes[] = $this->barcodes[count($this->barcodes) - 1];
        }

        while (count($this->docrefs) < $this->documents) {
            $this->docrefs[] = $this->docrefs[count($this->docrefs) - 1];
        }

        $footer = str_replace(
            array(
                '{{FOOTER_LEFT}}',
                '{{FOOTER_MIDDLE}}',
                '{{FOOTER_RIGHT}}',
                '{{PATIENT_NAMES}}',
                '{{PATIENT_HOSNUMS}}',
                '{{PATIENT_NHSNUMS}}',
                '{{PATIENT_DOBS}}',
                '{{PATIENT_NAME}}',
                '{{PATIENT_HOSNUM}}',
                '{{PATIENT_NHSNUM}}',
                '{{PATIENT_DOB}}',
                '{{BARCODES}}',
                '{{BARCODE}}',
                '{{DOCREF}}',
                '{{DOCREFS}}',
                '{{DOCUMENTS}}',
                '{{PAGE}}',
                '{{PAGES}}',
                '{{CUSTOM_TAGS}}',
                '{{NHS No}}',
                '{{Hos No}}',
                '{{MARGIN_TOP}}',
                '{{MARGIN_LEFT}}',
                '{{MARGIN_RIGHT}}'
            ),
            array(
                $left,
                $middle,
                $right,
                CJavaScript::encode($patient_names),
                CJavaScript::encode($patient_hosnums),
                CJavaScript::encode($patient_nhsnums),
                CJavaScript::encode($patient_dobs),
                '<span class="patient_name"></span>',
                '<span class="patient_hosnum"></span>',
                '<span class="patient_nhsnum"></span>',
                '<span class="patient_dob"></span>',
                CJavaScript::encode($this->barcodes),
                '<br/><span class="barcode"></span><br/>',
                '<span class="docref"></span>',
                CJavaScript::encode($this->docrefs),
                $this->documents,
                '<span class="pageNumber"></span>',
                '<span class="totalPages"></span>',
                CJavaScript::encode($this->custom_tags),
                \SettingMetadata::model()->getSetting('nhs_num_label'),
                \SettingMetadata::model()->getSetting('hos_num_label'),
                $this->_top_margin,
                $this->_left_margin,
                $this->_right_margin
            ),
            $footer
        );

        return $footer;
    }

    /**
     * @return Page
     */
    protected function newPage()
    {
        return $this->browser->newPage();
    }

    /**
     * @return Browser
     */
    protected function getBrowser()
    {
        return $this->_browser;
    }

    /**
     * @param $browser Browser
     */
    protected function setBrowser($browser)
    {
        $this->_browser = $browser;
    }

    /**
     * @return int
     */
    public function getReadTimeout()
    {
        return $this->_readTimeout;
    }

    /**
     * @param $value int
     */
    public function setReadTimeout($value)
    {
        $this->_readTimeout = $value;
    }

    /**
     * @return bool
     */
    public function getLogBrowserConsole()
    {
        return $this->_logBrowserConsole;
    }

    /**
     * @param $value bool
     */
    public function setLogBrowserConsole($value)
    {
        $this->_logBrowserConsole = $value;
    }

    public function setDocuments($count)
    {
        $this->documents = $count;
    }

    public function setDocref($docref)
    {
        $this->setDocrefs(array($docref));
    }

    public function setDocrefs($docrefs)
    {
        $this->docrefs = $docrefs;
    }

    public function setBarcode($barcode_html)
    {
        $this->setBarcodes(array($barcode_html));
    }

    public function setBarcodes($barcodes)
    {
        $this->barcodes = $barcodes;
    }

    public function setPatient($patient)
    {
        $this->setPatients(array($patient));
    }

    public function setPatients($patients)
    {
        $this->patients = $patients;
    }

    public function setLeftFooterTemplate($left)
    {
        $this->_left = $left;
    }

    public function setMiddleFooterTemplate($middle)
    {
        $this->_middle = $middle;
    }

    public function setRightFooterTemplate($right)
    {
        $this->_right = $right;
    }

    public function setTopMargin($top_margin)
    {
        $this->_top_margin = $top_margin;
    }

    public function setBottomMargin($bottom_margin)
    {
        $this->_bottom_margin = $bottom_margin;
    }

    public function setLeftMargin($left_margin)
    {
        $this->_left_margin = $left_margin;
    }

    public function setRightMargin($right_margin)
    {
        $this->_right_margin = $right_margin;
    }

    public function setScale($scale)
    {
        $this->_scale = $scale;
    }

    public function getScale()
    {
        return $this->_scale;
    }

    /**
     * @param $imageDirectory
     * @param $prefix
     * @param $suffix
     * @param $html
     * @param bool $inject_autoprint_js
     * @param bool $print_footer
     * @param bool $use_cookies
     * @return bool
     * @throws Exception
     */
    public function savePageToPDF(
        $imageDirectory,
        $prefix,
        $suffix,
        $html,
        $inject_autoprint_js = true,
        $print_footer = true,
        $use_cookies = true,
        $event_id = null
    ) {
        $footer = null;

        $this->findOrCreateDirectory($imageDirectory);

        $pdf_file = $suffix
            ? $imageDirectory . DIRECTORY_SEPARATOR . "{$prefix}_$suffix.pdf"
            : $imageDirectory . DIRECTORY_SEPARATOR . "$prefix.pdf";

        $footer_file = $suffix
            ? $imageDirectory . DIRECTORY_SEPARATOR . "footer_$suffix.html"
            : $imageDirectory . DIRECTORY_SEPARATOR . 'footer.html';
        $footer = $this->formatFooter(
            $this->readFile(
                Yii::app()->basePath . DIRECTORY_SEPARATOR
                . 'views'
                . DIRECTORY_SEPARATOR
                . 'print'
                . DIRECTORY_SEPARATOR
                . 'pdf_footer.php'
            ),
            $this->_left,
            $this->_middle,
            $this->_right
        );

        $this->writeFile($footer_file, $footer);

        $page_size = $this->page_size;

        $options = array(
            'path' => $pdf_file,
            'format' => $page_size,
            'printBackground' => true,
            'landscape' => ($this->orientation === 'Landscape'),
            'displayHeaderFooter' => $print_footer,
            'headerTemplate' => '<div></div>',
        );

        if (isset($this->scale)) {
            $options['scale'] = $this->scale;
        }

        if (isset($this->page_width)) {
            $options['page_width'] = $this->page_width;
        }

        if (isset($this->page_height)) {
            $options['page_height'] = $this->page_height;
        }

        $margins = array();

        foreach (array('top', 'left', 'bottom', 'right') as $side) {
            if ($this->{"_{$side}_margin"}) {
                $margins[$side] = $this->{"_{$side}_margin"} === '0mm' ? 0 : $this->{"_{$side}_margin"};
            }
        }

        if (!empty($margins)) {
            $options['margin'] = $margins;
        }
        // Load the footer HTML and add it to the options list.
        // Note that we have to evaluate the subst function so that the footer template is
        // correctly populated using Javascript on the page.
        $footerPage = $this->newPage();
        $footerPage->goto('file://' . $footer_file);
        $footerPage->evaluate(JsFunction::createWithBody('subst();'));
        $options['footerTemplate'] = $footerPage->content();

        $footerPage->close();

        // Save the page to PDF.
        $this->savePageToPDFInternal($html, $options, $use_cookies);

        if (!$this->fileExists($pdf_file) || $this->fileSize($pdf_file) === 0) {
            if ($this->fileSize($pdf_file) === 0) {
                $this->deleteFile($pdf_file);
            }

            throw new Exception("Unable to generate $pdf_file");
        }

        $this->deleteFile($footer_file);

        if ($pdf = $this->getPDFOptions($pdf_file)) {
            if ($inject_autoprint_js) {
                $pdf->injectJS('print(true);');
            }

            $pdf->disablePrintScaling();
            $pdf->write();
        }

        if (isset(Yii::app()->modules['RTFGeneration'])) {
            Yii::app()->db->createCommand()->update('document_instance', array('footer'=>$options['footerTemplate']), 'correspondence_event_id=:event_id', [':event_id'=>$event_id]);
        }

        return true;
    }

    /**
     * @param string $url
     * @param array $options
     * @param bool $use_cookies
     */
    protected function savePageToPDFInternal(string $url, array $options, $use_cookies = false)
    {
        $page = $this->newPage();
        if ($use_cookies) {
            $page->setCookie(
                array('name' => ini_get('session.name'), 'value' => $_COOKIE[ini_get('session.name')], 'url' => $url)
            );
        }
        $page->goto($url);

        // Save the file to PDF then close the page.
        $page->pdf($options);
        $page->close();
    }

    /**
     * @param $path
     * @return OEPDFOptions
     * @throws Exception
     */
    public function getPDFOptions($path)
    {
        return new OEPDFOptions($path);
    }

    /**
     * @param $imageDirectory
     * @param $prefix
     * @param $suffix
     * @param $html
     * @param array $options
     * @param bool $use_cookies
     * @return bool
     * @throws Exception
     */
    public function savePageToImage(
        $imageDirectory,
        $prefix,
        $suffix,
        $html,
        $options = array(),
        $use_cookies = true
    ) {
        $this->findOrCreateDirectory($imageDirectory);

        $image_file = $suffix ? $imageDirectory . DIRECTORY_SEPARATOR . "{$prefix}_$suffix.png" : $imageDirectory . DIRECTORY_SEPARATOR . "$prefix.png";

        $screenshot_options = array(
            'path' => $image_file,
            'type' => 'jpeg',
            'quality' => 75,
            'fullPage' => true,
        );

        if (array_key_exists('quality', $options)) {
            $screenshot_options['quality'] = $options['quality'];
        }

        // Save the screenshot of the page.
        $this->savePageToImageInternal($html, $screenshot_options, $use_cookies);

        if (!$this->fileExists($image_file) || $this->fileSize($image_file) === 0) {
            if ($this->fileSize($image_file) === 0) {
                $this->deleteFile($image_file);
            }

            throw new Exception("Unable to generate $image_file");
        }

        return true;
    }

    /**
     * @param string $url
     * @param array $options
     * @param bool $use_cookies
     */
    protected function savePageToImageInternal($url, $options, $use_cookies = false)
    {
        $page = $this->newPage();
        if ($use_cookies) {
            $page->setCookie(
                array('name' => ini_get('session.name'), 'value' => $_COOKIE[ini_get('session.name')], 'url' => $url)
            );
        }
        $page->goto($url, array(
            'waitUntil' => 'networkidle0'
        ));

        // Save the file to PDF then close the page.
        $page->screenshot($options);
        $page->close();
    }

    /**
     * Creates a directory at the given path if it doesn't already exist
     *
     * @param string $path The path of the directory to create
     * @throws Exception Thrown if the directory could not be made
     */
    public function findOrCreateDirectory($path)
    {
        if (!file_exists($path)) {
            if (!@mkdir($path, 0775, true) || !is_dir($path)) {
                throw new Exception("Unable to create directory: $path: check permissions.");
            }
        }
    }

    /**
     * Reads and returns the contents of a file
     *
     * @param string $path The file to read
     * @return string The contents of the file
     * @throws Exception Thrown if the file could not be found or read
     */
    public function readFile($path)
    {
        $data = @file_get_contents($path);
        if (!$data) {
            throw new Exception("File not found: $path");
        }

        return $data;
    }

    /**
     * Writes a data buffer to the given path
     *
     * @param string $path The
     * @param string $data The file contents
     * @throws Exception Thrown if an error occurred when writing the file
     */
    public function writeFile($path, $data)
    {
        if (!@file_put_contents($path, $data)) {
            throw new Exception("Unable to write to $path: check permissions.");
        }
    }

    /**
     * Deletes the path at the given path
     *
     * @param string $path The file to delete
     * @throws Exception Thrown if the file doesn't exist or could not be deleted
     */
    public function deleteFile($path)
    {
        if (@file_exists($path) && !@unlink($path)) {
            throw new Exception("Unable to delete $path: check permissions.");
        }
    }

    /**
     * Gets a value indicating whether a file exists at the given path
     *
     * @param string $path The path to test
     * @return bool Whether teh file exists or not
     */
    public function fileExists($path)
    {
        return @file_exists($path);
    }

    /**
     * Gets the size of the file in the given path in bytes
     *
     * @param string $path The file path
     * @return int The file size in bytes
     */
    public function fileSize($path)
    {
        return @filesize($path);
    }

    public function setCustomTag($tag_name, $value)
    {
        $this->custom_tags[$tag_name] = $value;
    }
}
