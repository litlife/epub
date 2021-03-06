<?php

namespace Litlife\Epub;

use DOMDocument;
use DOMElement;
use DOMImplementation;
use DOMNodeList;
use DOMXpath;
use Litlife\Url\Url;

class Ncx extends File
{
    private string $xp = "";
    private DOMDocument $dom;

    /** @noinspection HttpUrlsUsage */
    public function __construct($epub, $path = null)
    {
        parent::__construct($epub, $path);

        if (!is_null($path)) {
            $this->setPath($path);
            $this->loadXML(trim($epub->zipFile->getEntryContents($path)));
        } else {
            $imp = new DOMImplementation;

            $dtd = $imp->createDocumentType('ncx', '-//NISO//DTD ncx 2005-1//EN', 'http://www.daisy.org/z3986/2005/ncx-2005-1.dtd');

            $this->dom = $imp->createDocument(null, "ncx", $dtd);
            $this->dom->encoding = "utf-8";
            $this->dom->formatOutput = true;

            $ncx = $this->dom->documentElement;

            $ncx->setAttribute("version", "2005-1");
            $ncx->setAttribute("xmlns", "http://www.daisy.org/z3986/2005/ncx/");

            $head = $this->dom->createElement('head');
            $ncx->appendChild($head);

            $docTitle = $this->dom->createElement('docTitle');
            $title = $this->dom->createElement('text');
            $docTitle->appendChild($title);
            $ncx->appendChild($docTitle);

            $navMap = $this->dom->createElement('navMap');
            $ncx->appendChild($navMap);
        }
    }

    public function setPath($path)
    {
        $this->path = $path;
        $this->epub->files[$path] = $this;
        $this->epub->ncx = $this;
    }

    public function loadXML($path)
    {
        $this->dom = new DOMDocument();
        $this->dom->loadXML($path);
    }

    public function head(): ?DOMElement
    {
        $head = $this->xpath()->query("*[local-name()='head']");

        if (empty($head))
            return null;

        return $head->item(0);
    }

    public function xpath(): DOMXpath
    {
        if (empty($this->xpath))
            $this->xpath = new DOMXpath($this->dom());

        return $this->xpath;
    }

    function dom(): DOMDocument
    {
        return $this->dom;
    }

    public function getNavPoints($parent): DOMNodeList
    {
        return $this->xpath()->query("*[local-name()='navPoint']", $parent);
    }

    public function getFileById($id)
    {
        $navPoint = $this->xpath()->query("//*[local-name()='navPoint'][@id='" . $id . "']", $this->navmap());

        $content = $this->xpath()->query("*[local-name()='content']", $navPoint->item(0));

        $src = $content->item(0)->getAttribute('src');

        $fullPath = (string)Url::fromString($src)
            ->getPathRelativelyToAnotherUrl($this->getPath())
            ->withoutFragment();

        return $this->epub->getFileByPath($fullPath);
    }

    public function navmap(): ?DOMElement
    {
        $navMap = $this->xpath()->query("*[local-name()='navMap']");

        if (empty($navMap))
            return null;

        return $navMap->item(0);
    }

    public function getTextById($id): string
    {
        $navPoint = $this->xpath()->query("//*[local-name()='navPoint'][@id='" . $id . "']", $this->navmap());

        $text = $this->xpath()->query("*[local-name()='navLabel']/*[local-name()='text']", $navPoint->item(0))->item(0);

        return $text->nodeValue;
    }

    public function findTitleBySrc($src): string
    {
        $text = $this->xpath()
            ->query("//*[@src='" . $src . "']", $this->navmap())[0]
            ->query("parent::*")[0]
            ->query("*[local-name()='" . $this->xp . "navLabel']/*[local-name()='" . $this->xp . "text']")[0];

        return (string)$text;
    }

    public function findNavPointBySrc($src)
    {
        return $this->xpath()->query("//*[@src='" . $src . "']", $this->navmap())[0]
            ->query("parent::*")[0];
    }

    public function findNavPointByBaseName($baseName)
    {
        foreach ($this->xpath()->query("//[local-name()='navPoint'][@src]", $this->navmap()) as $section) {
            if (preg_match('/' . preg_quote($baseName, '/') . '/iu', $section->attributes()->src, $matches)) {
                return $section;
            }
        }
        return null;
    }

    public function findTitleByFullPath($searchableFullPath): ?string
    {
        foreach ($this->xpath()->query("//*[local-name()='navPoint']", $this->navmap()) as $navPoint) {

            $src = $this->xpath()->query("*[local-name()='content'][@src]", $navPoint)->item(0)->getAttribute('src');

            $fullPath = (string)Url::fromString($src)
                ->getPathRelativelyToAnotherUrl($this->getPath())
                ->withoutFragment();

            if ($fullPath == $searchableFullPath) {
                return $this->xpath()->query("*[local-name()='navLabel']/*[local-name()='text']", $navPoint)->item(0)->nodeValue;
            }
        }
        return null;
    }

    public function getParentSrcBySrc($src): string
    {
        $src = $this->xpath()
            ->query("//*[@src='" . $src . "']", $this->navmap())[0]
            ->xpath("parent::*")[0]
            ->xpath("parent::*")[0]
            ->xpath("*[local-name()='content']")[0]
            ->attributes()->src;

        return (string)$src;
    }

    /** @noinspection PhpUnusedParameterInspection */
    public function appendNavMap($text, $src, $id, $playOrder, $parent = null): DOMElement
    {
        $navPoint = $this->dom->createElement('navPoint');
        $navPoint->setAttribute('id', $id);

        $navLabel = $this->dom->createElement('navLabel');
        $navPoint->appendChild($navLabel);

        $textNode = $this->dom->createElement('text');
        $textNode->appendChild($this->dom->createTextNode($text));
        $navLabel->appendChild($textNode);

        $content = $this->dom->createElement('content');
        $content->setAttribute('src', $src);
        $navPoint->appendChild($content);

        if (empty($parent))
            $this->navmap()->appendChild($navPoint);
        else
            $parent->appendChild($navPoint);

        return $navPoint;
    }

    function getTree(): array
    {
        $navMap = $this->navmap();

        $navPoints = $this->xpath()->query("*[local-name()='navPoint']", $navMap);

        $array = [];

        foreach ($navPoints as $navPoint) {
            $array[] = $this->getSubNavPoints($navPoint);
        }

        return $array;
    }

    private function getSubNavPoints($navPoint): array
    {
        $id = $navPoint->getAttribute('id');
        $text = $this->xpath()->query("*[local-name()='navLabel']/*[local-name()='text']", $navPoint)->item(0)->nodeValue;
        $src = $this->xpath()->query("*[local-name()='content']", $navPoint)->item(0)->getAttribute('src');
        $playOrder = $this->xpath()->query("*[local-name()='content']", $navPoint)->item(0)->getAttribute('playOrder');

        $childs = [];

        $subNavPoints = $this->xpath()->query("*[local-name()='navPoint']", $navPoint);

        if ($subNavPoints->length) {
            foreach ($subNavPoints as $subNavPoint) {
                $childs[] = $this->getSubNavPoints($subNavPoint);
            }
        }

        return [
            'id' => $id,
            'text' => $text,
            'src' => $src,
            'playOrder' => $playOrder,
            'childs' => $childs
        ];
    }

    public function getContent(): string
    {
        return $this->dom()->saveXML();
    }
}
