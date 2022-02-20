<?php

namespace Litlife\Epub;

class File
{
    protected string $path;
    protected string $content;
    protected Epub $epub;

    function __construct(Epub &$epub, $path = null)
    {
        $this->epub = &$epub;

        if (!empty($path))
            $this->setPath($path);
    }

    public function getStream()
    {

    }

    public function getPathinfo()
    {
        return pathinfo($this->getPath());
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function setPath($path)
    {
        $this->path = $path;
        $this->epub->files[$path] = $this;
    }

    public function getDirname(): string
    {
        return dirname($this->getPath());
    }

    public function getBaseName(): string
    {
        return basename($this->getPath());
    }

    public function getFileName()
    {
        return pathinfo($this->getPath(), PATHINFO_FILENAME);
    }

    public function getExtension()
    {
        return pathinfo($this->getPath(), PATHINFO_EXTENSION);
    }

    public function getSize(): int
    {
        return strlen($this->getContent());
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent($content)
    {
        $this->content = $content;
    }

    public function isExists(): bool
    {
        return isset($this->epub->files[$this->getPath()]);
    }

    public function isFoundInZip(): bool
    {
        return $this->epub->zipFile->hasEntry($this->getPath());
    }

    public function delete()
    {
        //$this->epub->zipFile->deleteFromName($this->path);
        unset($this->epub->files[$this->getPath()]);
    }

    public function getMd5(): string
    {
        return md5($this->getContent());
    }

    /**
     * @throws \PhpZip\Exception\ZipException
     */
    public function rename(string $newName)
    {
        $from = $this->getPath();

        $this->epub->zipFile->rename($from, $newName);

        $this->setPath($newName);
    }

    /**
     * @throws \PhpZip\Exception\ZipException
     */
    public function save()
    {
        $this->epub->zipFile->addFromString($this->getPath(), $this->getContent());
    }

    /**
     * @throws \PhpZip\Exception\ZipEntryNotFoundException
     * @throws \PhpZip\Exception\ZipException
     */
    public function loadContent()
    {
        $this->content = $this->epub->zipFile->getEntryContents($this->path);
    }

    /**
     * @throws \PhpZip\Exception\ZipException
     */
    public function writeInArchive()
    {
        $this->epub->zipFile->addFromString($this->getPath(), $this->content);
    }

    public function getEpub(): Epub
    {
        return $this->epub;
    }
}
