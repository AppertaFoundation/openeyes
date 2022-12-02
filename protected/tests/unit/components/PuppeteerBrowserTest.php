<?php

/**
 * Class PuppeteerBrowserTest
 *
 * @method patients($fixture_id)
 */
class PuppeteerBrowserTest extends OEDbTestCase
{
    protected $instance;
    protected $fixtures = array(
        'patients' => Patient::class,
        'events' => Event::class,
    );

    public function setUp(): void
    {
        parent::setUp();
        $this->instance = new PuppeteerBrowser();
        $this->instance->init();
    }

    public function tearDown(): void
    {
        parent::tearDown();
        unset($this->instance);
    }

    /**
     * @covers PuppeteerBrowser
     */
    public function testInit()
    {
        $this->instance->init();
        $this->assertTrue($this->instance->isInitialized);
    }

    /**
     * @covers PuppeteerBrowser
     */
    public function testSetLogBrowserConsole()
    {
        // The function is not being explicitly used here because we want to test that the magic method works as expected.
        $this->instance->logBrowserConsole = true;
        $this->assertTrue($this->instance->logBrowserConsole);
    }

    /**
     * @covers PuppeteerBrowser
     */
    public function testSetReadTimeout()
    {
        // The function is not being explicitly used here because we want to test that the magic method works as expected.
        $this->instance->readTimeout = 30;
        $this->assertEquals(30, $this->instance->readTimeout);
    }

    /**
     * @covers PuppeteerBrowser
     */
    public function testGetReadTimeout()
    {
        // The function is not being explicitly used here because we want to test that the magic method works as expected.
        $this->instance->readTimeout = 30;
        $this->assertEquals(30, $this->instance->readTimeout);
    }

    /**
     * @covers PuppeteerBrowser
     * @throws Exception
     */
    public function testSavePageToImage()
    {
        file_put_contents(Yii::app()->getRuntimePath() . DIRECTORY_SEPARATOR . 'input.html', '<html lang="en"><body>Test data</body></html>');
        $html = 'file://' . Yii::app()->getRuntimePath() . DIRECTORY_SEPARATOR . 'input.html';
        $this->instance->savePageToImage(
            Yii::app()->getRuntimePath(),
            'testfile',
            null,
            $html,
            array(),
            false
        );
        $this->assertFileExists(Yii::app()->getRuntimePath() . DIRECTORY_SEPARATOR . 'testfile.png');
        unlink(Yii::app()->getRuntimePath() . DIRECTORY_SEPARATOR . 'testfile.png');
        unlink(Yii::app()->getRuntimePath() . DIRECTORY_SEPARATOR . 'input.html');
    }

    /**
     * @covers PuppeteerBrowser
     */
    public function testGetLogBrowserConsole()
    {
        // The function is not being explicitly used here because we want to test that the magic method works as expected.
        $this->instance->logBrowserConsole = true;
        $this->assertTrue($this->instance->logBrowserConsole);
    }

    /**
     * @covers PuppeteerBrowser
     * @throws Exception
     */
    public function testSavePageToPDF()
    {
        $this->instance->setPatient($this->patients('patient1'));
        $this->instance->setBarcode('');
        $this->instance->setDocRef('');
        file_put_contents(Yii::app()->getRuntimePath() . DIRECTORY_SEPARATOR . 'input.html', '<html lang="en"><body>Test data</body></html>');
        $this->instance->savePageToPDF(Yii::app()->getRuntimePath(), 'testfile', null, 'file://' . Yii::app()->getRuntimePath() . DIRECTORY_SEPARATOR . 'input.html', false, false, false);
        $this->assertFileExists(Yii::app()->getRuntimePath() . DIRECTORY_SEPARATOR . 'testfile.pdf');
        unlink(Yii::app()->getRuntimePath() . DIRECTORY_SEPARATOR . 'testfile.pdf');
        unlink(Yii::app()->getRuntimePath() . DIRECTORY_SEPARATOR . 'input.html');
    }

    /**
     * @covers PuppeteerBrowser
     * @throws Exception
     */
    public function testFindOrCreateDirectory()
    {
        $this->instance->findOrCreateDirectory('./test_directory');
        $this->assertDirectoryExists('./test_directory');
        rmdir('./test_directory');
        $this->assertDirectoryDoesNotExist('./test_directory');
    }

    /**
     * @covers PuppeteerBrowser
     * @throws Exception
     */
    public function testReadFile()
    {
        file_put_contents('./test.txt', 'Test');
        $contents = $this->instance->readFile('./test.txt');
        $this->assertEquals($contents, 'Test');
        unlink('./test.txt');
        $this->assertFileDoesNotExist('./test.txt');

        $this->expectException('Exception');
        $this->instance->readFile('./nofile.txt');
    }

    /**
     * @covers PuppeteerBrowser
     */
    public function testFileSize()
    {
        $expected = file_put_contents('./test.txt', 'Test');
        $actual = $this->instance->fileSize('./test.txt');
        $this->assertEquals($expected, $actual);
        unlink('./test.txt');
        $this->assertFileDoesNotExist('./test.txt');
    }

    /**
     * @covers PuppeteerBrowser
     * @throws Exception
     */
    public function testWriteFile()
    {
        $this->instance->writeFile('./test.txt', 'Test');
        $this->assertFileExists('./test.txt');
        unlink('./test.txt');
        $this->assertFileDoesNotExist('./test.txt');
    }

    /**
     * @covers PuppeteerBrowser
     * @throws Exception
     */
    public function testDeleteFile()
    {
        file_put_contents('./test.txt', 'Test');
        $this->instance->deleteFile('./test.txt');
        $this->assertFileDoesNotExist('./test.txt');

        $this->instance->deleteFile('./nofile.txt');
    }

    /**
     * @covers PuppeteerBrowser
     */
    public function testFileExists()
    {
        file_put_contents('./test.txt', 'Test');
        $this->assertTrue($this->instance->fileExists('./test.txt'));
        $this->assertFalse($this->instance->fileExists('./nofile.txt'));
        unlink('./test.txt');
        $this->assertFileDoesNotExist('./test.txt');
    }
}
