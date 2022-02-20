<?php /** @noinspection HttpUrlsUsage */

namespace Litlife\Epub;

use DOMDocument;
use DOMElement;
use DOMNodeList;
use DOMXpath;
use Litlife\Url\Url;

class Opf extends File
{
	private DOMDocument $dom;
	private DOMElement $metaData;
	private DOMElement $manifest;
	private DOMElement $spine;
	private DOMXpath $xpath;
	private array $prefixes = [];
	private string $dublinCoreNameSpace = "http://purl.org/dc/elements/1.1/";

    /**
     * @throws \PhpZip\Exception\ZipEntryNotFoundException
     * @throws \PhpZip\Exception\ZipException
     */
    public function __construct(Epub $epub, $path = null)
	{
		parent::__construct($epub, $path);

		if (!is_null($path)) {
			$this->setPath($path);

			$this->dom = new DOMDocument();
			$this->dom->loadXML(trim($epub->zipFile->getEntryContents($path)));

			foreach ($this->xpath()->query('namespace::*', $this->metaData()) as $node) {
				if ($node->prefix == 'dc') {
					$this->dublinCoreNameSpace = $node->namespaceURI;
				}
			}
		} else {
			$this->dom = new DOMDocument('1.0', 'utf-8');
			$this->dom->formatOutput = true;

			$this->createPackage();
			$this->createMetaData();
			$this->createManifest();
			$this->createSpine();
			//$this->createGuide();
		}
	}

	public function setPath($path)
	{
		$this->path = $path;
		$this->epub->files[$path] = $this;
		$this->epub->opf = $this;
	}

	public function xpath(): DOMXpath
    {
		if (empty($this->xpath)) {
			$this->xpath = new DOMXpath($this->dom());
		}

		return $this->xpath;
	}

	function dom(): DOMDocument
    {
		return $this->dom;
	}

	function metaData(): ?DOMElement
    {
		$nodeList = $this->xpath()->query("*[local-name()='metadata']", $this->package());

		if ($nodeList->length) {
			$this->metaData = $nodeList->item(0);
		} else {
			return null;
		}

		foreach ($this->xpath()->query('namespace::*', $this->metaData) as $node) {

			$prefix = $this->metaData->lookupPrefix($node->nodeValue);
			$this->prefixes[$prefix] = $node->nodeValue;
		}

		return $this->metaData;
	}

	public function package(): DOMElement
    {
		return $this->dom()->documentElement;
	}

	public function createPackage()
	{
		$package = $this->dom()->createElementNS('http://www.idpf.org/2007/opf', 'package');
		$package->setAttribute("version", '2.0');
		//$package->setAttribute("unique-identifier", 'BookId');
		$this->dom->appendChild($package);
	}

	public function createMetaData()
	{
		$this->metaData = $this->dom()->createElement('metadata');
		$this->package()->appendChild($this->metaData);

		$this->metaData->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:dc', $this->getDublinCoreNameSpace());
		$this->metaData->setAttributeNS('http://www.w3.org/2000/xmlns/', "xmlns:opf", "http://www.idpf.org/2007/opf");
		$this->metaData->setAttributeNS('http://www.w3.org/2000/xmlns/', "xmlns:calibre", "http://calibre.kovidgoyal.net/2009/metadata");
		$this->metaData->setAttributeNS('http://www.w3.org/2000/xmlns/', "xmlns:xsi", "http://www.w3.org/2001/XMLSchema-instance");
		$this->metaData->setAttributeNS('http://www.w3.org/2000/xmlns/', "xmlns:dcterms", "http://purl.org/dc/terms/");
	}

	public function getDublinCoreNameSpace(): string
    {
		return $this->dublinCoreNameSpace;
	}

	public function createManifest()
	{
		$this->manifest = $this->dom()->createElement('manifest');
		$this->package()->appendChild($this->manifest);
	}

	public function createSpine()
	{
		$this->spine = $this->dom()->createElement('spine');
		$this->package()->appendChild($this->spine);

		$this->spine->setAttribute("toc", "ncx");
	}

	public function createGuide()
	{
		$guide = $this->dom()->createElement('guide');
		$this->package()->appendChild($guide);
	}

	public function getNamespace($prefix)
	{
		return $this->getPrefixes()[$prefix];
	}

	public function getPrefixes(): array
    {
		$this->metaData();
		return $this->prefixes;
	}

	public function appendToMetaData($name, $value)
	{
		$meta = $this->dom->createElement('meta');

		$meta->setAttribute('name', $name);
		$meta->setAttribute('content', $value);

		$this->metaData->appendChild($meta);
	}

	public function appendDublinCode($name, $value, $attributes = [])
	{
		$node = $this->dom->createElementNS($this->getDublinCoreNameSpace(), $name);

		$node->appendChild($this->dom->createTextNode($value));

		foreach ($attributes as $k => $v) {
			$node->setAttribute($k, $v);
		}

		$this->metaData->appendChild($node);
	}

	public function appendToManifest($id, $href, $mediaType)
	{
		$url = Url::fromString($href);

		$item = $this->dom()->createElement('item');
		$item->setAttribute('id', $id);
		$item->setAttribute('href', $url->urlencode());
		$item->setAttribute('media-type', $mediaType);
		$this->manifest()->appendChild($item);
	}

	function manifest(): ?DOMElement
    {
		$nodeList = $this->xpath()->query("*[local-name()='manifest']", $this->package());

		if ($nodeList->length) {
			$this->manifest = $nodeList->item(0);
		} else {
			return null;
		}

		return $this->manifest;
	}

	public function appendToSpine($idref)
	{
		$itemref = $this->dom()->createElement('itemref');
		$itemref->setAttribute('idref', $idref);
		$this->spine()->appendChild($itemref);
	}

	function spine(): ?DOMElement
    {
		$nodeList = $this->xpath()->query("*[local-name()='spine']", $this->package());

		if ($nodeList->length) {
			$this->spine = $nodeList->item(0);
		} else {
			return null;
		}

		return $this->spine;
	}

	public function deleteDublinCoreByName($name)
	{
		foreach ($this->getDublinCoreByName($name) as $node) {
			$node->parentNode->removeChild($node);
		}
	}

	public function getDublinCoreByName($name): DOMNodeList
    {
		return $this->xpath()->query("*[local-name()='" . $name . "']", $this->metaData());
	}

	public function getMetaDataByName($name): DOMNodeList
    {
		$query = '*[local-name()="meta"][@name="' . htmlspecialchars($name) . '"]';
		return $this->xpath()->query($query, $this->metaData());
	}

	public function getMetaDataContentByName($name): ?string
    {
		$nodeList = $this->xpath()->query("*[local-name()='meta'][@name='" . $name . "'][@content]", $this->metaData());
		if ($nodeList->length)
			return $nodeList->item(0)->getAttribute('content');
        return null;
	}

	public function getDublinCoreValueByName($name): ?string
    {
		$nodeList = $this->xpath()->query("*[local-name()='" . $name . "']", $this->metaData());
		if ($nodeList->length)
			return $nodeList->item(0)->nodeValue;
        return null;
	}

	public function getManifestItemById($id): DOMNodeList
    {
		return $this->xpath()->query("*[local-name()='item'][@id='" . $id . "']", $this->manifest());
	}

	public function getContent(): string
    {
		return $this->dom()->saveXML();
	}
}
