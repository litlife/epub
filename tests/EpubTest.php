<?php

namespace Litlife\Epub\Tests;

use Litlife\Epub\Container;
use Litlife\Epub\Epub;
use Litlife\Epub\Image;
use Litlife\Epub\Ncx;
use Litlife\Epub\Opf;
use Litlife\Epub\Section;

class EpubTest extends TestCase
{
    /**
     * @throws \PhpZip\Exception\ZipEntryNotFoundException
     * @throws \PhpZip\Exception\ZipException
     */
    public function testGetSectionsList()
    {
        $this->newEpub();

        $epub = $this->getEpub();

        $section = new Section($epub);
        $section->setPath($epub->default_folder . '/Text/Section0001.xhtml');
        $section->setBodyHtml('<p>text</p>');
        $epub->opf()->appendToManifest('Section0001.xhtml', 'Text/Section0001.xhtml', 'application/xhtml+xml');

        $epub->files[$epub->default_folder . '/Text/Section0001.xhtml'] = $section;

        $section = new Section($epub);
        $section->setPath($epub->default_folder . '/Text/Section0002.xhtml');
        $section->setBodyHtml('<p>text</p>');
        $epub->opf()->appendToManifest('Section0002.xhtml', 'Text/Section0002.xhtml', 'application/xhtml+xml');

        $epub->files[$epub->default_folder . '/Text/Section0002.xhtml'] = $section;

        $epub->opf()->appendToSpine('Section0002.xhtml');
        $epub->opf()->appendToSpine('Section0001.xhtml');

        $s = <<<EOT
<spine toc="ncx">
  <itemref idref="Section0002.xhtml"/>
  <itemref idref="Section0001.xhtml"/>
</spine>
EOT;

        $this->assertEquals($s,
            $epub->opf()->dom()->saveXML($epub->opf()->spine()));

        $sections = $epub->getSectionsListInOrder();

        $this->assertEquals($epub->default_folder . '/Text/Section0002.xhtml', $sections[0]->getPath());
        $this->assertEquals($epub->default_folder . '/Text/Section0001.xhtml', $sections[1]->getPath());
    }

    /**
     * @throws \PhpZip\Exception\ZipException
     */
    public function testGetImages()
    {
        $epub = new Epub();
        $epub->setFile(__DIR__ . '/books/test.epub');

        $images = $epub->getImages();

        $this->assertInstanceOf(Image::class, $images['OEBPS/Images/test.png']);
    }

    /**
     * @throws \PhpZip\Exception\ZipException
     */
    public function testGetSectionByFilePath()
    {
        $epub = new Epub();
        $epub->setFile(__DIR__ . '/books/test.epub');

        $this->assertInstanceOf(Section::class, $epub->getSectionByFilePath('OEBPS/Text/Section0001.xhtml'));
    }

    /**
     * @throws \PhpZip\Exception\ZipException
     */
    public function testGetImageByFilePath()
    {
        $epub = new Epub();
        $epub->setFile(__DIR__ . '/books/test.epub');

        $this->assertInstanceOf(Image::class, $epub->getImageByFilePath('OEBPS/Images/test.png'));
    }

    /**
     * @throws \PhpZip\Exception\ZipException
     */
    public function testGetFilesList()
    {
        $epub = new Epub();
        $epub->setFile(__DIR__ . '/books/test.epub');

        $this->assertContains('OEBPS/toc.ncx', $epub->getAllFilesList());
        $this->assertContains('OEBPS/Text/Section0001.xhtml', $epub->getAllFilesList());
        $this->assertContains('OEBPS/Text/Section0002.xhtml', $epub->getAllFilesList());
        $this->assertContains('OEBPS/Images/test.png', $epub->getAllFilesList());
    }

    /**
     * @throws \PhpZip\Exception\ZipEntryNotFoundException
     * @throws \PhpZip\Exception\ZipException
     */
    public function testLoadFiles()
    {
        $epub = new Epub();
        $epub->setFile(__DIR__ . '/books/test.epub');

        $epub->loadFiles();

        $this->assertInstanceOf(Ncx::class, $epub->files['OEBPS/toc.ncx']);
        $this->assertInstanceOf(Section::class, $epub->files['OEBPS/Text/Section0001.xhtml']);
        $this->assertInstanceOf(Section::class, $epub->files['OEBPS/Text/Section0002.xhtml']);
        $this->assertInstanceOf(Image::class, $epub->files['OEBPS/Images/test.png']);
    }

    /**
     * @throws \PhpZip\Exception\ZipException
     */
    public function testGetFileByPath()
    {
        $epub = new Epub();
        $epub->setFile(__DIR__ . '/books/test.epub');

        $this->assertInstanceOf(Ncx::class, $epub->getFileByPath('OEBPS/toc.ncx'));
        $this->assertInstanceOf(Section::class, $epub->getFileByPath('OEBPS/Text/Section0001.xhtml'));
        $this->assertInstanceOf(Section::class, $epub->getFileByPath('OEBPS/Text/Section0002.xhtml'));
        $this->assertInstanceOf(Image::class, $epub->getFileByPath('OEBPS/Images/test.png'));
    }

    /**
     * @throws \PhpZip\Exception\ZipEntryNotFoundException
     * @throws \PhpZip\Exception\ZipException
     */
    public function testCreateOpfContainer()
    {
        $epub = new Epub();
        $epub->createContainer();
        $epub->createOpf();

        $this->assertContains('META-INF/container.xml', $epub->getAllFilesList());
        $this->assertContains('OEBPS/content.opf', $epub->getAllFilesList());
    }

    /**
     * @throws \PhpZip\Exception\ZipException
     * @throws \PhpZip\Exception\ZipEntryNotFoundException
     */
    /**
     * @throws \PhpZip\Exception\ZipException
     * @throws \PhpZip\Exception\ZipEntryNotFoundException
     */
    public function testSaveOpenContainerOpfExist()
    {
        $epub = new Epub();
        $epub->createContainer();
        $epub->createOpf();

        $this->assertInstanceOf(Container::class, $epub->getFileByPath('META-INF/container.xml'));
        $this->assertInstanceOf(Opf::class, $epub->getFileByPath('OEBPS/content.opf'));

        $string = $epub->outputAsString();

        $epub = new Epub();
        $epub->setFile($string);

        $this->assertTrue($epub->zipFile->hasEntry('META-INF/container.xml'));
        $this->assertTrue($epub->zipFile->hasEntry('OEBPS/content.opf'));

        $this->assertContains('META-INF/container.xml', $epub->getAllFilesList());
        $this->assertContains('OEBPS/content.opf', $epub->getAllFilesList());
    }

    /**
     * @throws \PhpZip\Exception\ZipException
     */
    public function testCyrylicFileNames()
    {
        $epub = new Epub();
        $epub->setFile(__DIR__ . '/books/test_cyrylic_names.epub');

        $this->assertContains('OEBPS/Images/??????????????????????.png', $epub->getAllFilesList());
        $this->assertContains('OEBPS/Text/??????????2.xhtml', $epub->getAllFilesList());
    }

    /**
     * @throws \PhpZip\Exception\ZipException
     * @throws \PhpZip\Exception\ZipEntryNotFoundException
     */
    /**
     * @throws \PhpZip\Exception\ZipException
     * @throws \PhpZip\Exception\ZipEntryNotFoundException
     */
    /**
     * @throws \PhpZip\Exception\ZipException
     * @throws \PhpZip\Exception\ZipEntryNotFoundException
     */
    public function testOutputFileHasMimetypeFile()
    {
        $epub = new Epub();
        $epub->createContainer();
        $epub->createOpf();

        $string = $epub->outputAsString();

        $epub = new Epub();
        $epub->setFile($string);

        $this->assertTrue($epub->zipFile->hasEntry('mimetype'));
        $this->assertEquals('application/epub+zip', $epub->zipFile->getEntryContents('mimetype'));
        $this->assertEquals('mimetype', $epub->zipFile->getListFiles()[0]);
    }

    /**
     * @throws \PhpZip\Exception\ZipEntryNotFoundException
     * @throws \PhpZip\Exception\ZipException
     */
    /**
     * @throws \PhpZip\Exception\ZipEntryNotFoundException
     * @throws \PhpZip\Exception\ZipException
     */
    /**
     * @throws \PhpZip\Exception\ZipEntryNotFoundException
     * @throws \PhpZip\Exception\ZipException
     */
    /**
     * @throws \PhpZip\Exception\ZipEntryNotFoundException
     * @throws \PhpZip\Exception\ZipException
     */
    /**
     * @throws \PhpZip\Exception\ZipEntryNotFoundException
     * @throws \PhpZip\Exception\ZipException
     */
    public function test2()
    {
        $epub = new Epub();
        $epub->createContainer();

        $opf = new Opf($epub);
        $opf->setPath('content.opf');
        $epub->container()->appendRootFile('content.opf', "application/oebps-package+xml");

        $content = $this->mockImage()->getImageBlob();

        $image = new Image($epub);
        $image->setPath('cover.jpeg');
        $image->setContent($content);
        $epub->opf()->appendToManifest('cover.jpeg', 'cover.jpeg', 'image/jpeg');

        $string = $epub->outputAsString();

        $epub = new Epub();
        $epub->setFile($string);

        $this->assertEquals('<rootfile full-path="content.opf" media-type="application/oebps-package+xml"/>',
            $epub->container()->dom()->saveXML($epub->container()->dom()->getElementsByTagName('rootfile')->item(0)));

        $this->assertEquals('<manifest>' . "\n" . '    <item id="cover.jpeg" href="cover.jpeg" media-type="image/jpeg"/>' . "\n" . '  </manifest>',
            $epub->opf()->dom()->saveXML($epub->opf()->manifest()));
    }
}
