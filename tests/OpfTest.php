<?php

namespace Litlife\Epub\Tests;

use Litlife\Epub\Epub;
use Litlife\Epub\Opf;
use Litlife\Epub\Section;

class OpfTest extends TestCase
{
    /**
     * @throws \PhpZip\Exception\ZipException
     * @throws \PhpZip\Exception\ZipEntryNotFoundException
     */
    /**
     * @throws \PhpZip\Exception\ZipException
     * @throws \PhpZip\Exception\ZipEntryNotFoundException
     */
    public function testOpen()
    {
        $epub = new Epub();

        $epub->setFile(__DIR__ . '/books/test.epub');

        $this->assertInstanceOf(Opf::class, $epub->opf());

        $this->assertNotNull($epub->opf());
    }

    /**
     * @throws \PhpZip\Exception\ZipEntryNotFoundException
     * @throws \PhpZip\Exception\ZipException
     */
    public function testCantFound()
    {
        $epub = new Epub();

        $this->assertNull($epub->opf());
    }

    /**
     * @throws \PhpZip\Exception\ZipEntryNotFoundException
     * @throws \PhpZip\Exception\ZipException
     */
    public function testMetaDataExists()
    {
        $epub = new Epub();

        $epub->setFile(__DIR__ . '/books/test.epub');

        $this->assertNotNull($epub->opf()->metaData());
    }

    /**
     * @throws \PhpZip\Exception\ZipEntryNotFoundException
     * @throws \PhpZip\Exception\ZipException
     */
    public function testNewMetaData()
    {
        $epub = new Epub();

        $opf = new Opf($epub);

        $this->assertNotNull($opf->metaData());

        $s = <<<EOT
<?xml version="1.0" encoding="utf-8"?>
<package xmlns="http://www.idpf.org/2007/opf" version="2.0">
  <metadata xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:opf="http://www.idpf.org/2007/opf" xmlns:calibre="http://calibre.kovidgoyal.net/2009/metadata" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:dcterms="http://purl.org/dc/terms/"/>
  <manifest/>
  <spine toc="ncx"/>
</package>

EOT;

        $this->assertEquals($s, $opf->dom()->saveXML());
    }

    /**
     * @throws \PhpZip\Exception\ZipException
     * @throws \PhpZip\Exception\ZipEntryNotFoundException
     */
    /**
     * @throws \PhpZip\Exception\ZipException
     * @throws \PhpZip\Exception\ZipEntryNotFoundException
     */
    public function testAppendToMetaData()
    {
        $epub = $this->newEpub();

        $epub->opf()->appendToMetaData('cover', 'cover.jpg');

        //  <meta name="cover" content="cover.jpg" />

        $s = <<<EOT
<metadata xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:opf="http://www.idpf.org/2007/opf" xmlns:calibre="http://calibre.kovidgoyal.net/2009/metadata" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:dcterms="http://purl.org/dc/terms/">
  <meta name="cover" content="cover.jpg"/>
</metadata>
EOT;
        $this->assertEquals($s, $epub->opf()->dom()->saveXML($epub->opf()->metaData()));
    }

    /**
     * @throws \PhpZip\Exception\ZipEntryNotFoundException
     * @throws \PhpZip\Exception\ZipException
     */
    public function testGetDublinCoreValueByName()
    {
        $epub = new Epub();
        $epub->setFile(__DIR__ . '/books/test.epub');
        $this->assertEquals('[Title here]', $epub->opf()->getDublinCoreValueByName('title'));
    }

    /**
     * @throws \PhpZip\Exception\ZipException
     * @throws \PhpZip\Exception\ZipEntryNotFoundException
     */
    /**
     * @throws \PhpZip\Exception\ZipException
     * @throws \PhpZip\Exception\ZipEntryNotFoundException
     */
    public function testDeleteDublinCore()
    {
        $epub = new Epub();
        $epub->setFile(__DIR__ . '/books/test.epub');
        $epub->opf()->deleteDublinCoreByName('title');
        $this->assertNull($epub->opf()->getDublinCoreValueByName('title'));
    }

    /**
     * @throws \PhpZip\Exception\ZipEntryNotFoundException
     * @throws \PhpZip\Exception\ZipException
     */
    public function testAppendDublinCore()
    {
        $epub = new Epub();
        $opf = new Opf($epub);
        $title = uniqid();
        $opf->appendDublinCode('title', $title);
        $this->assertEquals($title, $opf->getDublinCoreValueByName('title'));
    }

    /**
     * @throws \PhpZip\Exception\ZipEntryNotFoundException
     * @throws \PhpZip\Exception\ZipException
     */
    public function testGetMetaDataContentByName()
    {
        $epub = new Epub();
        $epub->setFile(__DIR__ . '/books/test.epub');

        $this->assertEquals('SequenceName', $epub->opf()->getMetaDataContentByName('calibre:series'));
    }

    /**
     * @throws \PhpZip\Exception\ZipException
     * @throws \PhpZip\Exception\ZipEntryNotFoundException
     */
    /**
     * @throws \PhpZip\Exception\ZipException
     * @throws \PhpZip\Exception\ZipEntryNotFoundException
     */
    public function testManifestItemById()
    {
        $epub = new Epub();
        $epub->setFile(__DIR__ . '/books/test.epub');

        $ncx = $epub->opf()->getManifestItemById('ncx')->item(0);

        $this->assertEquals('ncx', $ncx->getAttribute('id'));
        $this->assertEquals('toc.ncx', $ncx->getAttribute('href'));
        $this->assertEquals('application/x-dtbncx+xml', $ncx->getAttribute('media-type'));

        $section = $epub->opf()->getManifestItemById('Section0001.xhtml')->item(0);

        $this->assertEquals('Section0001.xhtml', $section->getAttribute('id'));
        $this->assertEquals('Text/Section0001.xhtml', $section->getAttribute('href'));
        $this->assertEquals('application/xhtml+xml', $section->getAttribute('media-type'));
    }

    /**
     * @throws \PhpZip\Exception\ZipException
     * @throws \PhpZip\Exception\ZipEntryNotFoundException
     */
    /**
     * @throws \PhpZip\Exception\ZipException
     * @throws \PhpZip\Exception\ZipEntryNotFoundException
     */
    public function testGetDublinCoreByName()
    {
        $epub = new Epub();
        $epub->setFile(__DIR__ . '/books/test.epub');

        $subjects = $epub->opf()->getDublinCoreByName('subject');
        $subject0 = $subjects->item(0);
        $this->assertEquals('sci_anachem', $subject0->nodeValue);

        $subjects = $epub->opf()->getDublinCoreByName('subject');
        $subject1 = $subjects->item(1);
        $this->assertEquals('music', $subject1->nodeValue);
    }

    /**
     * @throws \PhpZip\Exception\ZipException
     * @throws \PhpZip\Exception\ZipEntryNotFoundException
     */
    /**
     * @throws \PhpZip\Exception\ZipException
     * @throws \PhpZip\Exception\ZipEntryNotFoundException
     */
    public function testGetMetaDataByName()
    {
        $epub = new Epub();
        $epub->setFile(__DIR__ . '/books/test.epub');

        $this->assertEquals('SequenceName', $epub->opf()->getMetaDataByName('calibre:series')
            ->item(0)->getAttribute('content'));

        $this->assertEquals('SequenceName2', $epub->opf()->getMetaDataByName('calibre:series')
            ->item(1)->getAttribute('content'));
    }

    /**
     * @throws \PhpZip\Exception\ZipEntryNotFoundException
     * @throws \PhpZip\Exception\ZipException
     */
    public function testAppendToManifest()
    {
        $epub = new Epub();

        $opf = new Opf($epub);
        $opf->appendToManifest('Section0001.xhtml', 'Text/Section0001.xhtml', 'application/xhtml+xml');

        $s = <<<EOT
<manifest>
  <item id="Section0001.xhtml" href="Text/Section0001.xhtml" media-type="application/xhtml+xml"/>
</manifest>
EOT;
        $this->assertEquals($s, $opf->dom()->saveXML($opf->manifest()));
    }

    /**
     * @throws \PhpZip\Exception\ZipEntryNotFoundException
     * @throws \PhpZip\Exception\ZipException
     */
    public function testAppendToSpine()
    {
        $epub = new Epub();

        $opf = new Opf($epub);
        $opf->appendToSpine('Section0001.xhtml');

        $s = <<<EOT
<spine toc="ncx">
  <itemref idref="Section0001.xhtml"/>
</spine>
EOT;
        $this->assertEquals($s, $opf->dom()->saveXML($opf->spine()));
    }

    /**
     * @throws \PhpZip\Exception\ZipException
     * @throws \PhpZip\Exception\ZipEntryNotFoundException
     */
    /**
     * @throws \PhpZip\Exception\ZipException
     * @throws \PhpZip\Exception\ZipEntryNotFoundException
     */
    public function testCreateNewRightNamespace()
    {
        $epub = new Epub();

        $opf = new Opf($epub);
        $opf->setPath($epub->default_opf_path);
        $epub->opf = $opf;
        $epub->files[$epub->default_opf_path] = $opf;

        $s = <<<EOT
<package xmlns="http://www.idpf.org/2007/opf" version="2.0">
  <metadata xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:opf="http://www.idpf.org/2007/opf" xmlns:calibre="http://calibre.kovidgoyal.net/2009/metadata" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:dcterms="http://purl.org/dc/terms/"/>
  <manifest/>
  <spine toc="ncx"/>
</package>
EOT;

        $this->assertEquals($s, $epub->opf()->dom()->saveXml($epub->opf()->package()));
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
    public function testAppendDublinCoreByName()
    {
        $epub = new Epub();

        $opf = new Opf($epub);
        $opf->setPath($epub->default_opf_path);
        $epub->opf = $opf;
        $epub->files[$epub->default_opf_path] = $opf;

        $epub->opf()->appendDublinCode('title', 'Book Title');

        $this->assertEquals('Book Title', $epub->opf()->getDublinCoreValueByName('title'));

        $s = <<<EOT
<metadata xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:opf="http://www.idpf.org/2007/opf" xmlns:calibre="http://calibre.kovidgoyal.net/2009/metadata" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:dcterms="http://purl.org/dc/terms/">
  <dc:title>Book Title</dc:title>
</metadata>
EOT;
        $this->assertEquals($s, $epub->opf()->dom()->saveXml($epub->opf()->metaData()));
    }

    /**
     * @throws \PhpZip\Exception\ZipException
     * @throws \PhpZip\Exception\ZipEntryNotFoundException
     */
    /**
     * @throws \PhpZip\Exception\ZipException
     * @throws \PhpZip\Exception\ZipEntryNotFoundException
     */
    public function testSaveOpenDublinCore()
    {
        $epub = $this->newEpub();

        $epub->opf()->appendDublinCode('title', 'Book Title');

        $string = $epub->outputAsString();

        $epub = new Epub();
        $epub->setFile($string);

        $this->assertEquals('Book Title', $epub->opf()->getDublinCoreValueByName('title'));
    }

    /**
     * @throws \PhpZip\Exception\ZipException
     * @throws \PhpZip\Exception\ZipEntryNotFoundException
     */
    /**
     * @throws \PhpZip\Exception\ZipException
     * @throws \PhpZip\Exception\ZipEntryNotFoundException
     */
    public function testSaveOpenMetaData()
    {
        $epub = $this->newEpub();

        $epub->opf()->appendToMetaData('cover', 'image.jpg');

        $string = $epub->outputAsString();

        $epub = new Epub();
        $epub->setFile($string);

        $this->assertEquals('image.jpg', $epub->opf()->getMetaDataContentByName('cover'));
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
    public function testManifestHrefUrlEncoded()
    {
        $epub = $this->newEpub();

        $section = new Section($epub);
        $section->setPath('OEBPS/????????????/????????.xhtml');
        $section->setBodyHtml('<p>??????????</p>');

        $epub->opf()->appendToManifest('????????.xhtml', '????????????/????????.xhtml', 'application/xhtml+xml');

        $this->assertEquals('<item id="????????.xhtml" href="%D0%A2%D0%B5%D0%BA%D1%81%D1%82%D1%8B/%D0%A4%D0%B0%D0%B9%D0%BB.xhtml" media-type="application/xhtml+xml"/>',
            $epub->opf()->dom()->saveXML($epub->opf()->getManifestItemById('????????.xhtml')->item(0)));

        $this->assertNotNull($epub->getFileByPath('OEBPS/????????????/????????.xhtml'));

        $string = $epub->outputAsString();

        $epub = new Epub();
        $epub->setFile($string);

        $this->assertNotNull($epub->getFileByPath('OEBPS/????????????/????????.xhtml'));
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
    public function testSpecialChars()
    {
        $s = '&<>"\' ???';

        $epub = $this->newEpub();
        $epub->opf()->appendToMetaData($s, $s);
        $epub->opf()->appendDublinCode('test', $s, ['test' => $s]);

        $xml = <<<EOT
<metadata xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:opf="http://www.idpf.org/2007/opf" xmlns:calibre="http://calibre.kovidgoyal.net/2009/metadata" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:dcterms="http://purl.org/dc/terms/">
  <meta name="&amp;&lt;&gt;&quot;' ???" content="&amp;&lt;&gt;&quot;' ???"/>
  <dc:test test="&amp;&lt;&gt;&quot;' ???">&amp;&lt;&gt;"' ???</dc:test>
</metadata>
EOT;

        $this->assertEquals($xml, $epub->opf()->dom()->saveXML($epub->opf()->metaData()));

        $this->assertEquals($s, $epub->opf()->getDublinCoreByName('test')->item(0)->getAttribute('test'));
        $this->assertEquals($s, $epub->opf()->getDublinCoreByName('test')->item(0)->nodeValue);
        $this->assertEquals($s, $epub->opf()->getDublinCoreValueByName('test'));
        $this->assertNull($epub->opf()->getMetaDataByName($s)->item(0));
    }
}
