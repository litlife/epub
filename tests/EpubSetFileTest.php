<?php

namespace Litlife\Epub\Tests;

use Litlife\Epub\Epub;
use Litlife\Epub\Image;
use Litlife\Epub\Opf;
use Litlife\Epub\Section;
use PhpZip\ZipFile;

class EpubSetFileTest extends TestCase
{
    /**
     * @throws \PhpZip\Exception\ZipException
     */
    public function testSetFile()
    {
        $epub = new Epub();
        $this->assertInstanceOf(ZipFile::class, $epub->zipFile);

        $epub->setFile(__DIR__ . '/books/test.epub');

        $image = $epub->getFileByPath('OEBPS/Images/test.png');

        $this->assertInstanceOf(Image::class, $image);
        $this->assertNotNull($image->getContent());
    }

    /**
     * @throws \PhpZip\Exception\ZipException
     */
    public function testFindFiles()
    {
        $epub = new Epub();
        $epub->setFile(__DIR__ . '/books/test.epub');
        $array = $epub->findFiles('/\.opf/iu');

        $this->assertContains('OEBPS/content.opf', $array);

        $array = $epub->findFiles('/' . uniqid() . '\.opf/iu');

        $this->assertEquals([], $array);
    }

    /**
     * @throws \PhpZip\Exception\ZipEntryNotFoundException
     * @throws \PhpZip\Exception\ZipException
     */
    public function testOpfDom()
    {
        $epub = new Epub();
        $epub->setFile(__DIR__ . '/books/test.epub');

        $this->assertInstanceOf(Opf::class, $epub->opf());
    }

    /**
     * @throws \PhpZip\Exception\ZipException
     */
    public function testGetSectionById()
    {
        $epub = new Epub();
        $epub->setFile(__DIR__ . '/books/test.epub');

        $this->assertInstanceOf(Section::class, $epub->getSectionByFilePath('OEBPS/Text/Section0001.xhtml'));
    }
}
