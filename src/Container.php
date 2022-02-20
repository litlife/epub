<?php

namespace Litlife\Epub;

use DOMDocument;
use DOMElement;
use DOMNode;
use DOMXpath;

class Container extends File
{
    private DOMDocument $dom;

    /**
     * @throws \PhpZip\Exception\ZipEntryNotFoundException
     * @throws \PhpZip\Exception\ZipException
     */
    public function __construct(Epub $epub, $path = null)
    {
        parent::__construct($epub, $path);

        if (!empty($path)) {
            $this->dom = new DOMDocument();
            $this->dom->loadXML(trim($epub->zipFile->getEntryContents($path)));
        } else {
            $this->dom = new DOMDocument("1.0", "utf-8");
            $this->dom->formatOutput = true;

            $container = $this->dom->createElement('container');
            $container->setAttribute('version', '1.0');
            $container->setAttribute('xmlns', 'urn:oasis:names:tc:opendocument:xmlns:container');
            $this->dom->appendChild($container);

            $rootfiles = $this->dom->createElement('rootfiles');
            $container->appendChild($rootfiles);
        }
    }

    public function setPath($path)
    {
        $this->path = $path;
        $this->epub->files[$path] = $this;
        $this->epub->container = $this;
    }

    public function xpath(): DOMXpath
    {
        return new DOMXpath($this->dom());
    }

    public function dom(): DOMDocument
    {
        return $this->dom;
    }

    public function container(): DOMElement
    {
        return $this->dom()->getElementsByTagName('container')->item(0);
    }

    public function appendRootFile($fullPath, $mediaType): DOMNode
    {
        $file = $this->dom()->createElement('rootfile');
        $file->setAttribute('full-path', $fullPath);
        $file->setAttribute('media-type', $mediaType);
        return $this->rootfiles()->appendChild($file);
    }

    public function rootfiles(): DOMElement
    {
        return $this->dom()->getElementsByTagName('rootfiles')->item(0);
    }

    public function getContent(): string
    {
        return $this->dom()->saveXML();
    }
}
