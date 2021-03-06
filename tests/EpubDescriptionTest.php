<?php

namespace Litlife\Epub\Tests;

use Litlife\Epub\EpubDescription;
use Litlife\Epub\Image;
use Litlife\Epub\Opf;

class EpubDescriptionTest extends TestCase
{
    /**
     * @throws \PhpZip\Exception\ZipException
     * @throws \PhpZip\Exception\ZipEntryNotFoundException
     */
    public function testCover()
    {
        $epub = new EpubDescription();
        $epub->setFile(__DIR__ . '/books/test.epub');
        $this->assertInstanceOf(Image::class, $epub->getCover());
        $this->assertEquals('OEBPS/Images/test.png', $epub->getCover()->getPath());
    }

    /**
     * @throws \PhpZip\Exception\ZipEntryNotFoundException
     * @throws \PhpZip\Exception\ZipException
     */
    public function testTitle()
    {
        $epub = new EpubDescription();
        $epub->setFile(__DIR__ . '/books/test.epub');
        $this->assertEquals('[Title here]', $epub->getTitle());
    }

    /**
     * @throws \PhpZip\Exception\ZipEntryNotFoundException
     * @throws \PhpZip\Exception\ZipException
     */
    public function testPublisher()
    {
        $epub = new EpubDescription();
        $epub->setFile(__DIR__ . '/books/test.epub');
        $this->assertEquals('Publisher', $epub->getPublisher());
    }

    /**
     * @throws \PhpZip\Exception\ZipEntryNotFoundException
     * @throws \PhpZip\Exception\ZipException
     */
    public function testPublishCity()
    {
        $epub = new EpubDescription();
        $epub->setFile(__DIR__ . '/books/test.epub');
        $this->assertEquals('City', $epub->getPublishCity());
    }

    /**
     * @throws \PhpZip\Exception\ZipEntryNotFoundException
     * @throws \PhpZip\Exception\ZipException
     */
    public function testPublishYear()
    {
        $epub = new EpubDescription();
        $epub->setFile(__DIR__ . '/books/test.epub');
        $this->assertEquals('2002', $epub->getPublishYear());
    }

    /**
     * @throws \PhpZip\Exception\ZipEntryNotFoundException
     * @throws \PhpZip\Exception\ZipException
     */
    public function testLanguage()
    {
        $epub = new EpubDescription();
        $epub->setFile(__DIR__ . '/books/test.epub');
        $this->assertEquals('en', $epub->getLanguage());
    }

    /**
     * @throws \PhpZip\Exception\ZipEntryNotFoundException
     * @throws \PhpZip\Exception\ZipException
     */
    public function testAnnotation()
    {
        $epub = new EpubDescription();
        $epub->setFile(__DIR__ . '/books/test.epub');
        $this->assertEquals('Annotation', $epub->getAnnotation());
    }

    /**
     * @throws \PhpZip\Exception\ZipEntryNotFoundException
     * @throws \PhpZip\Exception\ZipException
     */
    public function testRightsholder()
    {
        $epub = new EpubDescription();
        $epub->setFile(__DIR__ . '/books/test.epub');
        $this->assertEquals('rightsholder', $epub->getRightsholder());
    }

    /**
     * @throws \PhpZip\Exception\ZipEntryNotFoundException
     * @throws \PhpZip\Exception\ZipException
     */
    public function testCreatedDate()
    {
        $epub = new EpubDescription();
        $epub->setFile(__DIR__ . '/books/test.epub');
        $this->assertEquals('2001', $epub->getCreatedDate());
    }

    /**
     * @throws \PhpZip\Exception\ZipEntryNotFoundException
     * @throws \PhpZip\Exception\ZipException
     */
    public function testISBN()
    {
        $epub = new EpubDescription();
        $epub->setFile(__DIR__ . '/books/test.epub');
        $this->assertEquals('111-1-111-11111-1', $epub->getISBN());
    }

    /**
     * @throws \PhpZip\Exception\ZipEntryNotFoundException
     * @throws \PhpZip\Exception\ZipException
     */
    public function testAuthors()
    {
        $epub = new EpubDescription();
        $epub->setFile(__DIR__ . '/books/test.epub');
        $this->assertEquals(['Author First Name', 'Author2 First2 Name2'], $epub->getAuthors());
    }

    /**
     * @throws \PhpZip\Exception\ZipEntryNotFoundException
     * @throws \PhpZip\Exception\ZipException
     */
    public function testTranslators()
    {
        $epub = new EpubDescription();
        $epub->setFile(__DIR__ . '/books/test.epub');
        $this->assertEquals(['Translator First Name'], $epub->getTranslators());
    }

    /**
     * @throws \PhpZip\Exception\ZipEntryNotFoundException
     * @throws \PhpZip\Exception\ZipException
     */
    public function testGenres()
    {
        $epub = new EpubDescription();
        $epub->setFile(__DIR__ . '/books/test.epub');
        $this->assertEquals(['sci_anachem', 'music'], $epub->getGenres());
    }

    /**
     * @throws \PhpZip\Exception\ZipEntryNotFoundException
     * @throws \PhpZip\Exception\ZipException
     */
    public function testSequences()
    {
        $epub = new EpubDescription();
        $epub->setFile(__DIR__ . '/books/test.epub');

        $array = [
            ['name' => 'SequenceName', 'number' => '1'],
            ['name' => 'SequenceName2']
        ];

        $this->assertEquals($array, $epub->getSequences());
    }

    /**
     * @throws \PhpZip\Exception\ZipEntryNotFoundException
     * @throws \PhpZip\Exception\ZipException
     */
    public function testMetaData1()
    {
        $epub = new EpubDescription();

        $s = <<<EOT
<?xml version="1.0" encoding="utf-8"?>
<package version="2.0" unique-identifier="uuid_id" xmlns="http://www.idpf.org/2007/opf">
  <metadata xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:dcterms="http://purl.org/dc/terms/" xmlns:calibre="http://calibre.kovidgoyal.net/2009/metadata" xmlns:opf="http://www.idpf.org/2007/opf" xmlns:dc="http://purl.org/dc/elements/1.1/">
    <dc:title>?????????????? ????????</dc:title>
    <dc:creator opf:role="aut" opf:file-as="????????????, ??????????????????">?????????????????? ????????????</dc:creator>
    <dc:date>0101-01-01T00:00:00+00:00</dc:date>
    <dc:contributor opf:role="bkp">calibre (1.48.0) [http://calibre-ebook.com]</dc:contributor>
    <dc:identifier opf:scheme="uuid" id="uuid_id">b271d443-dc22-4358-a461-79a1941eec0a</dc:identifier>
    <dc:subject>sf_fantasy</dc:subject>
    <dc:language>ru</dc:language>
    <dc:identifier opf:scheme="calibre">f3d648ae-4169-497f-ba94-7c75a32c1111</dc:identifier>
    <meta name="calibre:title_sort" content="?????????????????? ???????????? - ?????????????? ????????" />
    <meta name="calibre:timestamp" content="2015-06-03T18:54:18.734000+00:00" />
    <meta name="cover" content="cover" />
    <meta name="calibre:author_link_map" content="{&quot;?????????????????? ????????????&quot;: &quot;&quot;}" />
    <meta name="calibre:user_categories" content="{}" />
    <meta name="Sigil version" content="0.9.9" />
    <dc:date opf:event="modification" xmlns:opf="http://www.idpf.org/2007/opf">2018-04-06</dc:date>
  </metadata>
</package>
EOT;
        $opf = new Opf($epub);
        $opf->dom()->loadXML($s);
        $epub->opf = $opf;

        $this->assertEquals('?????????????? ????????', $epub->getTitle());
        $this->assertEquals(['?????????????????? ????????????'], $epub->getAuthors());
        $this->assertNull($epub->getCover());
        $this->assertEquals('', $epub->getPublisher());
        $this->assertEquals('', $epub->getPublishCity());
        $this->assertEquals(null, $epub->getPublishYear());
        $this->assertEquals('ru', $epub->getLanguage());
        $this->assertEquals('', $epub->getAnnotation());
        $this->assertEquals('', $epub->getRightsholder());
        $this->assertEquals(null, $epub->getCreatedDate());
        $this->assertEquals('', $epub->getISBN());
        $this->assertEquals([], $epub->getTranslators());
        $this->assertEquals(['sf_fantasy'], $epub->getGenres());
        $this->assertEquals([], $epub->getSequences());
    }

    /**
     * @throws \PhpZip\Exception\ZipEntryNotFoundException
     * @throws \PhpZip\Exception\ZipException
     */
    public function testMetaData2()
    {
        $epub = new EpubDescription();

        $s = <<<EOT
<?xml version="1.0" encoding="utf-8"?>
<package xmlns="http://www.idpf.org/2007/opf" version="2.0" unique-identifier="BookId">
  <metadata xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:opf="http://www.idpf.org/2007/opf" xmlns:calibre="http://calibre.kovidgoyal.net/2009/metadata" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:dcterms="http://purl.org/dc/terms/">
    <dc:language xsi:type="dcterms:RFC3066">RU</dc:language>
    <dc:creator opf:role="aut" opf:file-as="???????????? ??????????????????">?????????????????? ????????????</dc:creator>
    <dc:creator opf:role="trl" opf:file-as="?????????????????? ???????????? ????????????????">?????????????????? ???????????? ????????????????</dc:creator>
    <dc:creator opf:role="trl" opf:file-as="?????? ??????????????">?????? ??????????????</dc:creator>
    <meta name="calibre:series" content="???????????????????? ??????????????????????"/>
    <meta name="calibre:series_index" content="12"/>
    <meta name="calibre:title_sort" content="?????????????? ????????"/>
  </metadata>
</package>
EOT;
        $opf = new Opf($epub);
        $opf->dom()->loadXML($s);
        $epub->opf = $opf;

        $this->assertEquals('?????????????? ????????', $epub->getTitle());
        $this->assertEquals(['?????????????????? ????????????'], $epub->getAuthors());
        $this->assertNull($epub->getCover());
        $this->assertEquals('', $epub->getPublisher());
        $this->assertEquals('', $epub->getPublishCity());
        $this->assertEquals(null, $epub->getPublishYear());
        $this->assertEquals('ru', $epub->getLanguage());
        $this->assertEquals('', $epub->getAnnotation());
        $this->assertEquals('', $epub->getRightsholder());
        $this->assertEquals(null, $epub->getCreatedDate());
        $this->assertEquals('', $epub->getISBN());
        $this->assertEquals(['?????????????????? ???????????? ????????????????', '?????? ??????????????'], $epub->getTranslators());
        $this->assertEquals([], $epub->getGenres());

        $array = [
            ['name' => '???????????????????? ??????????????????????', 'number' => '12']
        ];

        $this->assertEquals($array, $epub->getSequences());
    }

    /**
     * @throws \PhpZip\Exception\ZipEntryNotFoundException
     * @throws \PhpZip\Exception\ZipException
     */
    public function testMetaData3()
    {
        $epub = new EpubDescription();

        $s = <<<EOT
<package xmlns="http://www.idpf.org/2007/opf" prefix="ibooks: http://vocabulary.itunes.apple.com/rdf/ibooks/vocabulary-extensions-1.0/" unique-identifier="PrimaryID" version="3.0" xml:lang="en">
   <metadata xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:dcterms="http://purl.org/dc/terms/" xmlns:opf="http://www.idpf.org/2007/opf" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
      <meta content="coverimg" name="cover"/>
      <meta property="dcterms:modified">2017-10-13T11:22:14Z</meta>
      <dc:title>?????????????? ????????</dc:title>
      <dc:creator id="creator">?????????????????? ????????????</dc:creator>
      <meta property="role" refines="#creator" scheme="marc:relators">aut</meta>
      <meta property="file-as" refines="#creator">????????????, ??????????????????</meta>
      <meta property="display-seq" refines="#creator">1</meta>
      <dc:language>eng</dc:language>
      <dc:rights>Copyright ???? 2017 by ?????????????????? ????????????</dc:rights>
      <dc:date>2017-11-14</dc:date>
      <dc:identifier id="PrimaryID">9780553448200</dc:identifier>
      <dc:source id="src-id">urn:isbn:9780553448100</dc:source>
      <meta property="ibooks:specified-fonts">true</meta>
   <meta content="2017-10-13" name="epubcheckdate"/><meta content="4.0.1" name="epubcheckversion"/></metadata>
</package>
EOT;
        $opf = new Opf($epub);
        $opf->dom()->loadXML($s);
        $epub->opf = $opf;

        $this->assertEquals('?????????????? ????????', $epub->getTitle());
        $this->assertEquals(['?????????????????? ????????????'], $epub->getAuthors());
        $this->assertNull($epub->getCover());
        $this->assertEquals('', $epub->getPublisher());
        $this->assertEquals('', $epub->getPublishCity());
        $this->assertEquals(null, $epub->getPublishYear());
        $this->assertEquals('eng', $epub->getLanguage());
        $this->assertEquals('', $epub->getAnnotation());
        $this->assertEquals('', $epub->getRightsholder());
        $this->assertEquals(null, $epub->getCreatedDate());
        $this->assertEquals(null, $epub->getISBN());
        $this->assertEquals([], $epub->getTranslators());
        $this->assertEquals([], $epub->getGenres());
        $this->assertEquals([], $epub->getSequences());
    }

    /**
     * @throws \PhpZip\Exception\ZipEntryNotFoundException
     * @throws \PhpZip\Exception\ZipException
     */
    public function testMetaData4()
    {
        $epub = new EpubDescription();

        $s = <<<EOT
<?xml version="1.0" encoding="utf-8"?>
<package version="2.0" unique-identifier="FB2BookID" xmlns="http://www.idpf.org/2007/opf">
  <metadata xmlns="http://www.idpf.org/2007/opf" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:dcterms="http://purl.org/dc/terms/" xmlns:dc="http://purl.org/dc/elements/1.1/">
    <dc:title>?????????????? ???????? -0</dc:title>
    <dc:title>Mertvie Dushi</dc:title>
    <dc:language xsi:type="dcterms:RFC3066">RU</dc:language>
    <dc:language xsi:type="dcterms:RFC3066">en</dc:language>
    <dc:identifier xmlns:opf="http://www.idpf.org/2007/opf" opf:scheme="URI" id="FB2BookID">urn:uuid:4780</dc:identifier>
    <dc:date xmlns:opf="http://www.idpf.org/2007/opf" opf:event="original-publication">1788</dc:date>
    <dc:creator xmlns:opf="http://www.idpf.org/2007/opf" opf:role="aut" opf:file-as="????????????, ??????????????????">?????????????????? ????????????</dc:creator>
    <dc:creator xmlns:opf="http://www.idpf.org/2007/opf" opf:role="aut" opf:file-as=" Gogol, Aleksandr">Aleksandr Gogol</dc:creator>
    <dc:contributor xmlns:opf="http://www.idpf.org/2007/opf" opf:role="trl" opf:file-as="????????????, ??.">??. ????????????</dc:contributor>
    <dc:contributor xmlns:opf="http://www.idpf.org/2007/opf" opf:role="adp" opf:file-as="Maier">maier</dc:contributor>
    <meta name="cover" content="cover.jpg" />
    <meta name="Sigil version" content="0.9.9" />
    <dc:date xmlns:opf="http://www.idpf.org/2007/opf" opf:event="modification">2018-04-08</dc:date>
  </metadata>
</package>
EOT;
        $opf = new Opf($epub);
        $opf->dom()->loadXML($s);
        $epub->opf = $opf;

        $this->assertEquals('?????????????? ???????? -0', $epub->getTitle());
        $this->assertEquals(['?????????????????? ????????????', 'Aleksandr Gogol'], $epub->getAuthors());
        $this->assertNull($epub->getCover());
        $this->assertEquals('', $epub->getPublisher());
        $this->assertEquals('', $epub->getPublishCity());
        $this->assertEquals(1788, $epub->getPublishYear());
        $this->assertEquals('ru', $epub->getLanguage());
        $this->assertEquals('', $epub->getAnnotation());
        $this->assertEquals('', $epub->getRightsholder());
        $this->assertEquals(null, $epub->getCreatedDate());
        $this->assertEquals(null, $epub->getISBN());
        $this->assertEquals(['??. ????????????'], $epub->getTranslators());
        $this->assertEquals([], $epub->getGenres());
        $this->assertEquals([], $epub->getSequences());
    }

    /**
     * @throws \PhpZip\Exception\ZipEntryNotFoundException
     * @throws \PhpZip\Exception\ZipException
     */
    public function testMetaData5()
    {
        $epub = new EpubDescription();

        $s = <<<EOT
<?xml version="1.0" encoding="utf-8"?>
<package version="2.0" unique-identifier="bookid" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:opf="http://www.idpf.org/2007/opf" xmlns:dcterms="http://purl.org/dc/terms/" xmlns="http://www.idpf.org/2007/opf">
  <metadata>
    <dc:creator>?????? ??????????????</dc:creator>
    <dc:language>ru</dc:language>
    <dc:description>?????????? ??????????????????</dc:description>
    <meta content="1988??" name="FB2.book-info.date" />
    <meta content="F4C4E673-D1F9-4711-80EC-D7BE89EAF450" name="FB2.document-info.id" />
    <meta content="1.2" name="FB2.document-info.version" />
    <meta content="1988??" name="FB2.document-info.date" />
    <dc:publisher>????????????????? ??????????????????????? (1933-1963 ????????????)</dc:publisher>
    <meta content="??????????????" name="FB2.publish-info.book-name" />
    <meta content="????????????" name="FB2.publish-info.city" />
    <meta content="1988" name="FB2.publish-info.year" />
    <dc:date>1988</dc:date>
    <meta name="cover" content="id1" />
    <meta content="0.9.9" name="Sigil version" />
  </metadata>
</package>
EOT;
        $opf = new Opf($epub);
        $opf->dom()->loadXML($s);
        $epub->opf = $opf;

        $this->assertEquals('??????????????', $epub->getTitle());
        $this->assertEquals(['?????? ??????????????'], $epub->getAuthors());
        $this->assertNull($epub->getCover());
        $this->assertEquals('????????????????? ??????????????????????? (1933-1963 ????????????)', $epub->getPublisher());
        $this->assertEquals('????????????', $epub->getPublishCity());
        $this->assertEquals(1988, $epub->getPublishYear());
        $this->assertEquals('ru', $epub->getLanguage());
        $this->assertEquals('?????????? ??????????????????', $epub->getAnnotation());
        $this->assertEquals('', $epub->getRightsholder());
        $this->assertEquals(null, $epub->getCreatedDate());
        $this->assertEquals(null, $epub->getISBN());
        $this->assertEquals([], $epub->getTranslators());
        $this->assertEquals([], $epub->getGenres());
        $this->assertEquals([], $epub->getSequences());
    }

    /**
     * @throws \PhpZip\Exception\ZipException
     * @throws \PhpZip\Exception\ZipEntryNotFoundException
     */
    public function testMetaData6()
    {
        $epub = new EpubDescription();

        $s = <<<EOT
<?xml version="1.0" encoding="utf-8"?>
<package version="2.0" unique-identifier="bookid" xmlns:opf="http://www.idpf.org/2007/opf" xmlns="http://www.idpf.org/2007/opf" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:dcterms="http://purl.org/dc/terms/" xmlns:dc="http://purl.org/dc/elements/1.1/">
  <metadata>
    <meta content="Translator1" name="FB2.book-info.translator" />
    <meta content="Translator2" name="FB2.book-info.translator" />
    <dc:language>ru</dc:language>
    <meta content="2014-04-21" name="FB2.book-info.date" />
    <meta content="???????????????? ??????????; number=5" name="FB2.book-info.sequence" />
    <dc:publisher>?????? http://vk.com/vincent_series_sea_breeze</dc:publisher>
    <meta content="2014" name="FB2.publish-info.year" />
    <dc:date>2014</dc:date>
    <dc:date xmlns:opf="http://www.idpf.org/2007/opf" opf:event="modification">2018-04-07</dc:date>
  </metadata>
</package>
EOT;
        $opf = new Opf($epub);
        $opf->dom()->loadXML($s);
        $epub->opf = $opf;

        $array = [
            ['name' => '???????????????? ??????????', 'number' => '5']
        ];

        $this->assertEquals($array, $epub->getSequences());
        $this->assertEquals(['Translator1', 'Translator2'], $epub->getTranslators());
    }

    /**
     * @throws \PhpZip\Exception\ZipEntryNotFoundException
     * @throws \PhpZip\Exception\ZipException
     */
    public function testMetaData7()
    {
        $epub = new EpubDescription();

        $s = <<<EOT
<?xml version="1.0" encoding="utf-8"?>
<package version="2.0" unique-identifier="FB2BookID" xmlns="http://www.idpf.org/2007/opf">
  <metadata xmlns:opf="http://www.idpf.org/2007/opf" xmlns:calibre="http://calibre.kovidgoyal.net/2009/metadata" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:dcterms="http://purl.org/dc/terms/" xmlns:dc="http://purl.org/dc/elements/1.1/">
    <meta name="cover" content="cover.jpg" />
    <meta content="???????????????????? ??????????????????????" name="calibre:series" />
    <meta content="2" name="calibre:series_index" />
    <meta content="???????????????????? ????????????????????" name="calibre:series" />
    <meta content="3" name="calibre:series_index" />
  </metadata>
</package>
EOT;
        $opf = new Opf($epub);
        $opf->dom()->loadXML($s);
        $epub->opf = $opf;

        $array = [
            ['name' => '???????????????????? ??????????????????????', 'number' => '2'],
            ['name' => '???????????????????? ????????????????????', 'number' => '3']
        ];

        $this->assertEquals($array, $epub->getSequences());
    }

    /**
     * @throws \PhpZip\Exception\ZipEntryNotFoundException
     * @throws \PhpZip\Exception\ZipException
     */
    public function testMetaData8()
    {
        $epub = new EpubDescription();

        $s = <<<EOT
<?xml version="1.0" encoding="utf-8"?>
<package version="2.0" unique-identifier="FB2BookID" xmlns="http://www.idpf.org/2007/opf">
  <metadata xmlns:opf="http://www.idpf.org/2007/opf" xmlns:calibre="http://calibre.kovidgoyal.net/2009/metadata" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:dcterms="http://purl.org/dc/terms/" xmlns:dc="http://purl.org/dc/elements/1.1/">
    <meta content="???????????????????? ??????????????????????" name="calibre:series" />
    <meta content="???????????????????? ????????????????????" name="calibre:series" />
    <meta content="3" name="calibre:series_index" />
  </metadata>
</package>
EOT;
        $opf = new Opf($epub);
        $opf->dom()->loadXML($s);
        $epub->opf = $opf;

        $array = [
            ['name' => '???????????????????? ??????????????????????'],
            ['name' => '???????????????????? ????????????????????', 'number' => '3']
        ];

        $this->assertEquals($array, $epub->getSequences());
    }

    /**
     * @throws \PhpZip\Exception\ZipEntryNotFoundException
     * @throws \PhpZip\Exception\ZipException
     */
    public function testPublishYearParseDate()
    {
        $epub = new EpubDescription();

        $s = <<<EOT
<?xml version="1.0" encoding="utf-8"?>
<package version="2.0" unique-identifier="bookid" xmlns:opf="http://www.idpf.org/2007/opf" xmlns="http://www.idpf.org/2007/opf" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:dcterms="http://purl.org/dc/terms/" xmlns:dc="http://purl.org/dc/elements/1.1/">
  <metadata>
    <dc:date xmlns:opf="http://www.idpf.org/2007/opf" opf:event="original-publication">2010-04-07</dc:date>
  </metadata>
</package>
EOT;
        $opf = new Opf($epub);
        $opf->dom()->loadXML($s);
        $epub->opf = $opf;

        $this->assertEquals(2010, $epub->getPublishYear());
    }
}
