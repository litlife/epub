<?php

namespace Litlife\Epub\Tests;

use DOMDocument;
use DOMNode;
use Litlife\Epub\Epub;
use Litlife\Epub\Section;

class SectionTest extends TestCase
{
    /**
     * @throws \PhpZip\Exception\ZipException
     */
    public function testGetSectionsList()
    {
        $epub = new Epub();
        $epub->setFile(__DIR__ . '/books/test.epub');

        $list = $epub->getSectionsList();
        $this->assertTrue(is_array($list));
        $this->assertCount(2, $list);
        $this->assertInstanceOf(Section::class, $list['OEBPS/Text/Section0001.xhtml']);
    }

    /**
     * @throws \PhpZip\Exception\ZipException
     */
    public function testSection()
    {
        $epub = new Epub();
        $epub->setFile(__DIR__ . '/books/test.epub');

        $section = $epub->getSectionByFilePath('OEBPS/Text/Section0001.xhtml');

        $this->assertInstanceOf(DOMNode::class, $section->head());
        $this->assertEquals('', $section->title()->nodeValue);
        $this->assertEquals('<p>Porro hic libero <a href="../Text/Section0002.xhtml">note</a> dolorem. Dolor <a id="anchor1">note</a> quia impedit et corrupti. Laborum quos sit facere ut at illum. Nobis accusantium libero <a href="../Text/Section0002.xhtml#section_20">sit</a> eos. Sunt quia nulla quibusdam dolores. Mollitia dolorum quisquam voluptatum aperiam. Aut voluptatum accusantium alias voluptatem rerum quis illo et. Reiciendis ab minima aut suscipit. Mollitia velit eligendi quidem est. Facere rerum qui ut recusandae explicabo temporibus. Animi aut architecto eos rerum aut. Amet est explicabo minima nulla. Consequatur esse voluptatem vel voluptatem. Molestiae ad omnis magni amet. Aliquam voluptates odit dolorem praesentium nulla ullam. Totam consectetur cupiditate laborum sequi esse. Exercitationem velit dolores ut natus accusamus. Non nulla error voluptatum qui eum nam. Voluptate fuga facere odio autem maiores. <img alt="test" src="../Images/test.png"/> </p>', $section->getBodyContent());
    }

    /**
     * @throws \PhpZip\Exception\ZipEntryNotFoundException
     * @throws \PhpZip\Exception\ZipException
     */
    public function testNewSection()
    {
        $epub = new Epub();

        $section = new Section($epub);
        $section->setPath('OEBPS/Text/Section1.xhtml');
        $section->setBodyHtml('<p>123</p>');

        $s = <<<EOT
<?xml version="1.0" encoding="utf-8"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
  </head>
  <body>
    <p>123</p>
  </body>
</html>

EOT;

        $this->assertEquals($s, $section->getContent());
        $this->assertEquals('<p>123</p>', $section->getBodyContent());
    }

    /**
     * @throws \PhpZip\Exception\ZipEntryNotFoundException
     * @throws \PhpZip\Exception\ZipException
     */
    public function testSetBodyId()
    {
        $epub = new Epub();

        $section = new Section($epub);
        $section->setPath('OEBPS/Text/Section1.xhtml');
        $section->setBodyId('body_id');
        $section->setBodyHtml('<p>123</p>');
        $this->assertEquals('body_id', $section->body()->getAttribute('id'));
    }

    /**
     * @throws \PhpZip\Exception\ZipEntryNotFoundException
     * @throws \PhpZip\Exception\ZipException
     */
    public function testGetBodyId()
    {
        $epub = new Epub();

        $section = new Section($epub);
        $section->setPath('OEBPS/Text/Section1.xhtml');
        $section->setBodyId('body_id');
        $section->setBodyHtml('<p>123</p>');
        $this->assertEquals('body_id', $section->getBodyId());
    }

    /**
     * @throws \PhpZip\Exception\ZipEntryNotFoundException
     * @throws \PhpZip\Exception\ZipException
     */
    public function testNbspEntities()
    {
        $epub = $this->newEpub();

        $html = '<p>text &nbsp; &amp; <img alt="test" src="../Images/test.png"/>text &nbsp;</p>';

        $section = new Section($epub);
        $section->setPath('OEBPS/Text/Section1.xhtml');
        $section->setBodyId('body_id');
        $section->setBodyHtml($html);

        $this->assertEquals('<p>text &amp; <img alt="test" src="../Images/test.png"/>text </p>',
            $section->getBodyContent());
    }

    /**
     * @throws \PhpZip\Exception\ZipEntryNotFoundException
     * @throws \PhpZip\Exception\ZipException
     */
    public function testCariage()
    {
        $epub = $this->newEpub();

        $section = new Section($epub);
        $section->setPath('OEBPS/Text/Section1.xhtml');
        $section->setBodyId('body_id');
        $section->setBodyHtml("test \r test \r test");

        $this->assertEquals('<p>test test test</p>',
            $section->getBodyContent());
    }

    /**
     * @throws \PhpZip\Exception\ZipEntryNotFoundException
     * @throws \PhpZip\Exception\ZipException
     */
    public function testSvg()
    {
        $html = <<<EOT
<?xml version="1.0" encoding="utf-8"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
  <title>Cover</title>
</head>
<body>
  <div style="text-align: center; padding: 0pt; margin: 0pt;">
    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" height="100%" preserveAspectRatio="xMidYMid meet" version="1.1" viewBox="0 0 340 332" width="100%">
      <image width="340" height="332" xlink:href="../Images/??????????????????????.png"/>
    </svg>
  </div>
</body>
</html>

EOT;

        $epub = $this->newEpub();

        $section = new Section($epub);
        $section->setPath('OEBPS/Text/Section1.xhtml');
        $section->loadXml($html);

        $this->assertEquals($html, $section->dom()->saveXML());
    }

    public function testXML()
    {
        $html = <<<EOT
<?xml version="1.0" encoding="utf-8"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
  <title>Cover</title>
</head>
<body>
  <div style="text-align: center; padding: 0pt; margin: 0pt;">
    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" height="100%" preserveAspectRatio="xMidYMid meet" version="1.1" viewBox="0 0 340 332" width="100%">
      <image width="340" height="332" xlink:href="../Images/??????????????????????.png"/>
    </svg>
  </div>
</body>
</html>

EOT;

        $dom = new DOMDocument();
        $dom->loadXML($html);

        $dom2 = new DOMDocument();
        $dom2->loadXML($dom->saveXML());

        $this->assertEquals($html, $dom2->saveXML());
    }

    /**
     * @throws \PhpZip\Exception\ZipEntryNotFoundException
     * @throws \PhpZip\Exception\ZipException
     */
    public function testTitle()
    {
        $html = <<<EOT
<?xml version="1.0" encoding="utf-8"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
  <title>Cover</title>
</head>
<body>
  <div style="text-align: center; padding: 0pt; margin: 0pt;">
    <svg xmlns="http://www.w3.org/2000/svg" height="100%" preserveAspectRatio="xMidYMid meet" version="1.1" viewBox="0 0 340 332" width="100%" xmlns:xlink="http://www.w3.org/1999/xlink">
      <image width="340" height="332" xlink:href="../Images/??????????????????????.png"/>
    </svg>
  </div>
</body>
</html>
EOT;
        $epub = $this->newEpub();

        $section = new Section($epub);
        $section->setPath('OEBPS/Text/Section1.xhtml');
        $section->loadXml($html);

        $this->assertNotNull($section->dom()->getElementsByTagName('title')->item(0));
        $this->assertEquals('Cover', $section->dom()->getElementsByTagName('title')->item(0)->nodeValue);
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
    public function testLoadXmlGetBodyContent()
    {
        $html = <<<EOT
<?xml version="1.0" encoding="utf-8"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
  <title>Cover</title>
</head>
<body>
  <p>??????????</p>
</body>
</html>

EOT;

        $epub = $this->newEpub();

        $section = new Section($epub);
        $section->setPath('OEBPS/Text/Section1.xhtml');
        $section->loadXml($html);

        $epub->opf()->appendToManifest('Section1.xhtml', 'Text/Section1.xhtml', 'application/xhtml+xml');
        $epub->opf()->appendToSpine('Section1.xhtml');

        $this->assertEquals('<p>??????????</p>', $section->getBodyContent());
        $this->assertInstanceOf(Section::class, $epub->getFileByPath('OEBPS/Text/Section1.xhtml'));

        $string = $epub->outputAsString();

        $epub = new Epub();
        $epub->setFile($string);

        $this->assertInstanceOf(Section::class, $epub->getFileByPath('OEBPS/Text/Section1.xhtml'));
        $this->assertEquals('<p>??????????</p>', $epub->getFileByPath('OEBPS/Text/Section1.xhtml')->getBodyContent());
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
    public function testSetBodyXmlGetBodyContent()
    {
        $html = '<p>??????????</p>';

        $epub = $this->newEpub();

        $section = new Section($epub);
        $section->setPath('OEBPS/Text/Section1.xhtml');
        $section->setBodyHtml($html);

        $epub->opf()->appendToManifest('Section1.xhtml', 'Text/Section1.xhtml', 'application/xhtml+xml');
        $epub->opf()->appendToSpine('Section1.xhtml');

        $this->assertEquals($html, $section->getBodyContent());
        $this->assertInstanceOf(Section::class, $epub->getFileByPath('OEBPS/Text/Section1.xhtml'));

        $string = $epub->outputAsString();

        $epub = new Epub();
        $epub->setFile($string);

        $this->assertInstanceOf(Section::class, $epub->getFileByPath('OEBPS/Text/Section1.xhtml'));
        $this->assertEquals($html, $epub->getFileByPath('OEBPS/Text/Section1.xhtml')->getBodyContent());
    }

    /**
     * @throws \PhpZip\Exception\ZipEntryNotFoundException
     * @throws \PhpZip\Exception\ZipException
     */
    public function testSetBodyHtmlTagsWithoutParent()
    {
        $epub = $this->newEpub();

        $section = new Section($epub);
        $section->setPath('OEBPS/Text/Section1.xhtml');
        $section->setBodyId('body_id');
        $section->setBodyHtml('<p>???????????? ??????????</p>');
        $section->setBodyHtml('<p><strong>??????????</strong> <i>??????????</i></p><p><strong>??????????</strong> <i>??????????</i></p>');

        $this->assertEquals('<p><strong>??????????</strong> <i>??????????</i></p><p><strong>??????????</strong> <i>??????????</i></p>', $section->getBodyContent());
        $this->assertEquals('body_id', $section->getBodyId());
    }

    /**
     * @throws \PhpZip\Exception\ZipEntryNotFoundException
     * @throws \PhpZip\Exception\ZipException
     */
    public function testFixHtmlSelfClosedTags()
    {
        $epub = $this->newEpub();

        libxml_use_internal_errors(false);

        $section = new Section($epub);
        $section->setPath('OEBPS/Text/Section1.xhtml');
        $section->setBodyHtml('<p>?????????? <img src="/image.jpg"> ??????????</p>');

        $this->assertEquals('<p>?????????? <img src="/image.jpg"/> ??????????</p>',
            $section->getBodyContent());

        libxml_use_internal_errors(true);

        $section = new Section($epub);
        $section->setPath('OEBPS/Text/Section1.xhtml');
        $section->setBodyHtml('<p>?????????? <img src="/image.jpg"> ??????????</p>');

        $this->assertEquals('<p>?????????? <img src="/image.jpg"/> ??????????</p>',
            $section->getBodyContent());
    }

    /**
     * @throws \PhpZip\Exception\ZipEntryNotFoundException
     * @throws \PhpZip\Exception\ZipException
     */
    public function testDoubleQuote()
    {
        $xhtml = '<p>&amp;&lt;&gt;&quot;\'</p>';

        $epub = $this->newEpub();

        $section = new Section($epub);
        $section->setPath('OEBPS/Text/Section1.xhtml');
        $section->setBodyHtml($xhtml);

        $this->assertEquals('<p>&amp;&lt;&gt;"\'</p>', $section->getBodyContent());
    }

    /**
     * @throws \PhpZip\Exception\ZipEntryNotFoundException
     * @throws \PhpZip\Exception\ZipException
     */
    public function testFormatOutput()
    {
        $epub = $this->newEpub();

        $section = new Section($epub);
        $section->setPath('OEBPS/Text/Section1.xhtml');
        $section->setBodyHtml('<ul><li>??????????</li><li>??????????</li></ul>');

        $xhtmlFormatOutput = <<<EOT
    <ul>
      <li>??????????</li>
      <li>??????????</li>
    </ul>
EOT;

        $xhtmlNotFormatOutput = <<<EOT
<ul><li>??????????</li><li>??????????</li></ul>
EOT;

        $this->assertStringContainsString($xhtmlFormatOutput, $section->getContent(true));
        $this->assertStringNotContainsString($xhtmlNotFormatOutput, $section->getContent(true));

        $this->assertStringNotContainsString($xhtmlFormatOutput, $section->getContent(false));
        $this->assertStringContainsString($xhtmlNotFormatOutput, $section->getContent(false));
    }

    /**
     * @throws \PhpZip\Exception\ZipEntryNotFoundException
     * @throws \PhpZip\Exception\ZipException
     */
    public function testTitleSetGet()
    {
        $epub = $this->newEpub();

        $s = '???????? & < > ? & ';

        $section = new Section($epub);
        $section->setPath('OEBPS/Text/Section1.xhtml');
        $section->title($s);

        $this->assertEquals($s, $section->title()->nodeValue);
    }

    /**
     * @throws \PhpZip\Exception\ZipEntryNotFoundException
     * @throws \PhpZip\Exception\ZipException
     */
    public function testLoadXmlNewLinesBeforeXML()
    {
        $epub = $this->newEpub();

        $section = new Section($epub);
        $section->setPath('OEBPS/Text/Section1.xhtml');
        $section->setBodyHtml('<ul><li>??????????</li><li>??????????</li></ul>');

        $content = "\r\n\r\n\r\n\r\n\r\n\r\n\r\n" . $section->getContent();

        $section = new Section($epub);
        $section->loadXml($content);
        $content = $section->getBodyContent();

        $this->assertEquals('<ul> <li>??????????</li> <li>??????????</li> </ul>', $content);
    }

    /**
     * @throws \PhpZip\Exception\ZipEntryNotFoundException
     * @throws \PhpZip\Exception\ZipException
     */
    public function testImportXhtml()
    {
        $epub = $this->newEpub();

        $section = new Section($epub);
        $section->setPath('OEBPS/Text/Section1.xhtml');

        $nodes = $section->importXhtml('<p>?????????? <b>??????????</b></p><p>??????????2 <b>??????????2</b></p>');

        $this->assertEquals('<p>?????????? <b>??????????</b></p>', $section->dom()->saveXML($nodes->item(0)));
        $this->assertEquals('<p>??????????2 <b>??????????2</b></p>', $section->dom()->saveXML($nodes->item(1)));
    }

    /**
     * @throws \PhpZip\Exception\ZipEntryNotFoundException
     * @throws \PhpZip\Exception\ZipException
     */
    public function testClearBody()
    {
        $epub = $this->newEpub();

        $html = <<<EOT
<?xml version="1.0" encoding="utf-8"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<body>
  <p>123</p>
  <p>456</p>
</body>
</html>
EOT;
        $section = new Section($epub);
        $section->setPath('OEBPS/Text/Section1.xhtml');
        $section->loadXml($html);

        $this->assertEquals("<p>123</p><p>456</p>", $section->getBodyContent());

        $section->clearBody();

        $this->assertEquals("", $section->getBodyContent());
    }

    /**
     * @throws \PhpZip\Exception\ZipEntryNotFoundException
     * @throws \PhpZip\Exception\ZipException
     */
    public function testPrependBodyXhtml()
    {
        $epub = $this->newEpub();

        $section = new Section($epub);
        $section->setPath('OEBPS/Text/Section1.xhtml');

        $section->prependBodyXhtml('<p>??????????</p>');

        $this->assertEquals('<p>??????????</p>', $section->getBodyContent());

        $section->prependBodyXhtml('<p>??????????2</p>');

        $this->assertEquals('<p>??????????2</p><p>??????????</p>', $section->getBodyContent());

        $section->prependBodyXhtml('<p>??????????4</p><p>??????????3</p>');

        $this->assertEquals('<p>??????????4</p><p>??????????3</p><p>??????????2</p><p>??????????</p>', $section->getBodyContent());
    }

    /**
     * @throws \PhpZip\Exception\ZipEntryNotFoundException
     * @throws \PhpZip\Exception\ZipException
     */
    public function testGetTitleV1()
    {
        $epub = $this->newEpub();

        $section = new Section($epub);
        $section->setPath('OEBPS/Text/Section1.xhtml');
        $section->setBodyHtml('<h1 class="title">??????????????????</h1><p>??????????</p>');

        $this->assertEquals('??????????????????', $section->getTitle());
        $this->assertEquals('<h1 class="title">??????????????????</h1><p>??????????</p>', $section->getBodyContent());
    }

    /**
     * @throws \PhpZip\Exception\ZipEntryNotFoundException
     * @throws \PhpZip\Exception\ZipException
     */
    public function testGetTitleV2()
    {
        $epub = $this->newEpub();

        $section = new Section($epub);
        $section->setPath('OEBPS/Text/Section1.xhtml');
        $section->setBodyHtml('<p class="title">??????????????????</p><p>??????????</p>');

        $this->assertEquals('??????????????????', $section->getTitle());
        $this->assertEquals('<p class="title">??????????????????</p><p>??????????</p>', $section->getBodyContent());
    }

    /**
     * @throws \PhpZip\Exception\ZipEntryNotFoundException
     * @throws \PhpZip\Exception\ZipException
     */
    public function testGetTitleV3()
    {
        $epub = $this->newEpub();

        $section = new Section($epub);
        $section->setPath('OEBPS/Text/Section1.xhtml');
        $section->setBodyHtml('<h1>??????????????????</h1><p>??????????</p>');

        $this->assertEquals('??????????????????', $section->getTitle());
        $this->assertEquals('<h1>??????????????????</h1><p>??????????</p>', $section->getBodyContent());
    }

    /**
     * @throws \PhpZip\Exception\ZipEntryNotFoundException
     * @throws \PhpZip\Exception\ZipException
     */
    public function testGetTitleV4()
    {
        $epub = $this->newEpub();

        $xhtml = '<p>?????????? ?????????? ?????????? ?????????? ?????????? ?????????? ?????????? ?????????? ?????????? ?????????? ?????????? ?????????? ?????????? ?????????? ?????????? ?????????? ?????????? </p>';

        $section = new Section($epub);
        $section->setPath('OEBPS/Text/Section1.xhtml');
        $section->setBodyHtml($xhtml);

        $this->assertEquals('?????????? ?????????? ?????????? ?????????? ??????????...',
            $section->getTitle());

        $this->assertEquals($xhtml,
            $section->getBodyContent());
    }

    /**
     * @throws \PhpZip\Exception\ZipEntryNotFoundException
     * @throws \PhpZip\Exception\ZipException
     */
    public function testGetTitleV5()
    {
        $epub = $this->newEpub();

        $section = new Section($epub);
        $section->setPath('OEBPS/Text/Section1.xhtml');

        $this->assertEquals('Section1.xhtml',
            $section->getTitle());

        $this->assertEquals('',
            $section->getBodyContent());
    }

    /**
     * @throws \PhpZip\Exception\ZipEntryNotFoundException
     * @throws \PhpZip\Exception\ZipException
     */
    public function testTitleHandler()
    {
        $epub = $this->newEpub();

        $section = new Section($epub);

        $this->assertEquals('??????????  ??????????', $section->titleHandler('??????????       ??????????'));
        $this->assertEquals('?????????? & ??????????', $section->titleHandler('?????????? &amp; ??????????'));
        $this->assertEquals('??????????  ??????????  ??????????', $section->titleHandler("?????????? \r\n\r\n\r\n ?????????? \n\n ??????????"));
        $this->assertEquals('??????????', $section->titleHandler("    ??????????        "));
        $this->assertEquals('??????????', $section->titleHandler("??????????"));

        $title = '??????????????????????????'; // asc 194
        $this->assertStringContainsString(chr(194), $title);
        $title = $section->titleHandler($title);
        $this->assertStringNotContainsString(chr(194), $title);
    }

    /**
     * @throws \PhpZip\Exception\ZipException
     */
    public function testIsLinear()
    {
        $epub = new Epub();
        $epub->setFile(__DIR__ . '/books/test_with_linear_no.epub');

        $epub->getSectionsList();

        $this->assertEquals(null, $epub->getFileByPath('OEBPS/Text/Section0001.xhtml')->getLinear());
        $this->assertEquals('no', $epub->getFileByPath('OEBPS/Text/Section0002.xhtml')->getLinear());
    }

    /**
     * @throws \PhpZip\Exception\ZipException
     */
    public function testGetTitleId()
    {
        $epub = new Epub();
        $epub->setFile(__DIR__ . '/books/test_header_anchor.epub');

        $epub->getSectionsList();

        $this->assertEquals('?????????? 1', $epub->getFileByPath('OEBPS/Text/Section0001.xhtml')->getTitle());
        $this->assertEquals('header1', $epub->getFileByPath('OEBPS/Text/Section0001.xhtml')->getTitleId());
        $this->assertEquals('<h1 id="header1">?????????? 1</h1><p>?????????? ???????????? ?????????? <a href="../Text/Section0002.xhtml#header2">????????????</a></p>',
            $epub->getFileByPath('OEBPS/Text/Section0001.xhtml')->getBodyContent());

        $this->assertEquals('?????????? 2', $epub->getFileByPath('OEBPS/Text/Section0002.xhtml')->getTitle());
        $this->assertEquals('header2', $epub->getFileByPath('OEBPS/Text/Section0002.xhtml')->getTitleId());
        $this->assertEquals('<h1 id="header2">?????????? 2</h1><p>?????????? ???????????? ?????????? <a href="../Text/Section0001.xhtml#header1">????????????</a></p>',
            $epub->getFileByPath('OEBPS/Text/Section0002.xhtml')->getBodyContent());

        $epub->getFileByPath('OEBPS/Text/Section0001.xhtml')->setTitleId('test');

        $this->assertEquals('test', $epub->getFileByPath('OEBPS/Text/Section0001.xhtml')->getTitleId());
    }
}
