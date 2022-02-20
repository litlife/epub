<?php

namespace Litlife\Epub\Tests;

use Litlife\Epub\Container;
use Litlife\Epub\Epub;

class ContainerXmlTest extends TestCase
{
    /**
     * @throws \PhpZip\Exception\ZipEntryNotFoundException
     * @throws \PhpZip\Exception\ZipException
     */
    public function testOpen()
	{
		$epub = new Epub();
		$epub->setFile(__DIR__ . '/books/test.epub');

		$container = $epub->container()->container();

		$this->assertEquals('urn:oasis:names:tc:opendocument:xmlns:container', $container->getAttribute('xmlns'));
		$this->assertEquals('1.0', $container->getAttribute('version'));
	}

    /**
     * @throws \PhpZip\Exception\ZipEntryNotFoundException
     * @throws \PhpZip\Exception\ZipException
     */
    public function testAddRootFile()
	{
		$epub = new Epub();

		$container = new Container($epub);
		$container->appendRootFile('OEBPS/content.opf', 'application/oebps-package+xml');

		$s = <<<EOT
<?xml version="1.0" encoding="utf-8"?>
<container xmlns="urn:oasis:names:tc:opendocument:xmlns:container" version="1.0">
  <rootfiles>
    <rootfile full-path="OEBPS/content.opf" media-type="application/oebps-package+xml"/>
  </rootfiles>
</container>

EOT;

		$this->assertEquals($s, $container->dom()->saveXML());
	}

    /**
     * @throws \PhpZip\Exception\ZipEntryNotFoundException
     * @throws \PhpZip\Exception\ZipException
     */
    public function testCreate()
    {
        $epub = new Epub();
        $epub->createContainer();

        $this->assertContains('META-INF/container.xml', $epub->getAllFilesList());
    }
}
