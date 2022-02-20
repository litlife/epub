<?php

namespace Litlife\Epub;

use Imagick;
use ImagickException;
use Litlife\Url\Url;

class Image extends File
{
    protected Epub $epub;
    protected string $content;
    private Imagick $imagick;
    private string $id;
    private string $href;

    function __construct(&$epub, string $path = null)
    {
        parent::__construct($epub, $path);
    }

    public function isValid(): bool
    {
        try {
            $this->getImagick();
        } catch (ImagickException) {
            return false;
        }
        return true;
    }

    /**
     * @throws \ImagickException
     */
    public function getImagick(): Imagick
    {
        if (empty($this->imagick)) {
            $this->imagick = new Imagick();
            $this->imagick->readImageBlob($this->content);
        }

        return $this->imagick;
    }

    public function setContent($content)
    {
        $this->content = $content;
    }

    /**
     * @throws \ImagickException
     */
    public function getWidth(): int
    {
        return $this->getImagick()->getImageWidth();
    }

    /**
     * @throws \ImagickException
     */
    public function getHeight(): int
    {
        return $this->getImagick()->getImageHeight();
    }

    /**
     * @throws \ImagickException
     */
    public function guessExtension(): string
    {
        return strtolower($this->getImagick()->getImageFormat());
    }

    /**
     * @throws \ImagickException
     * @throws \PhpZip\Exception\ZipEntryNotFoundException
     * @throws \PhpZip\Exception\ZipException
     */
    public function addToManifest()
    {
        $this->epub->opf()
            ->appendToManifest($this->getId(), $this->getHref(), $this->getContentType());
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getHref(): string
    {
        return $this->href;
    }

    public function setHref($href)
    {
        $this->href = $href;
    }

    /**
     * @throws \ImagickException
     */
    public function getContentType(): string
    {
        return $this->getImagick()->getImageMimeType();
    }

    public function rename(string $newName): bool
    {
        $oldPath = $this->getPath();

        $newPath = (string)Url::fromString($this->getPath())->withBasename($newName);

        foreach ($this->epub->getSectionsList() as $section) {

            $imagesNodes = $section->xpath()->query("//*[local-name()='img'][@src]", $section->body());

            foreach ($imagesNodes as $imagesNode) {

                $src = $imagesNode->getAttribute('src');

                $image_url = Url::fromString($src);

                if ($this->getPath() == $image_url->getPathRelativelyToAnotherUrl($section->getPath())->withoutFragment()) {
                    $imagesNode->setAttribute('src', $image_url->withBasename($newName));
                }
            }
        }

        $query = "*[local-name()='item'][@media-type][@href][contains(@media-type,'image')]";

        foreach ($this->epub->opf()->xpath()->query($query, $this->epub->opf()->manifest()) as $node) {

            $image_url = Url::fromString($node->getAttribute('href'));

            if ($this->getPath() == $image_url->getPathRelativelyToAnotherUrl($this->epub->opf()->getPath())->withoutFragment()) {

                $node->setAttribute('href', $image_url->withBasename($newName));
                $node->setAttribute('id', $newName);
            }
        }

        $this->setPath($newPath);

        unset($this->epub->files[$oldPath]);

        return true;
    }

    public function setPath($path)
    {
        $this->path = $path;
        $this->epub->files[$path] = $this;
    }
}
