<?php

namespace Litlife\Epub;

use Litlife\Url\Url;

class EpubDescription extends Epub
{
    /**
     * @throws \PhpZip\Exception\ZipException
     * @throws \PhpZip\Exception\ZipEntryNotFoundException
     */
    public function getCover(): ?Image
    {
		$id = $this->opf()->getMetaDataContentByName('cover');

		$nodeList = $this->opf()->getManifestItemById($id);

		if ($nodeList->length) {
			$href = urldecode($nodeList->item(0)->getAttribute('href'));

			return new Image($this, Url::fromString($href)->getPathRelativelyToAnotherUrl($this->opf()->getPath()));
		}

		return null;
	}

    /**
     * @throws \PhpZip\Exception\ZipException
     * @throws \PhpZip\Exception\ZipEntryNotFoundException
     */
    public function getTitle(): string
    {
		$title = $this->opf()->getDublinCoreValueByName('title');

		if (empty($title)) {
			$title = $this->opf()->getMetaDataContentByName('calibre:title_sort');
		}

		if (empty($title)) {
			$title = $this->opf()->getMetaDataContentByName('FB2.publish-info.book-name');
		}

		return trim($title);
	}

    /**
     * @throws \PhpZip\Exception\ZipEntryNotFoundException
     * @throws \PhpZip\Exception\ZipException
     */
    public function getPublisher(): string
    {
		return trim($this->opf()->getDublinCoreValueByName('publisher') ?? '');
	}

    /**
     * @throws \PhpZip\Exception\ZipEntryNotFoundException
     * @throws \PhpZip\Exception\ZipException
     */
    public function getPublishCity(): string
    {
		return trim($this->opf()->getMetaDataContentByName('FB2.publish-info.city') ?? '');
	}

    /**
     * @throws \PhpZip\Exception\ZipException
     * @throws \PhpZip\Exception\ZipEntryNotFoundException
     */
    public function getPublishYear(): ?int
    {
		$year = $this->opf()->getMetaDataContentByName('FB2.publish-info.year');

		if (empty($year)) {
			foreach ($this->opf()->getDublinCoreByName('date') as $node) {
				if ($node->hasAttribute('opf:event') and $node->getAttribute('opf:event') == 'original-publication') {
					$year = (string)$node->nodeValue;
				}
			}
		}

		if (empty($year)) {
			foreach ($this->opf()->getDublinCoreByName('date') as $node) {
				if ($node->hasAttribute('opf:event') and $node->getAttribute('opf:event') != 'modification') {
					$year = (string)$node->nodeValue;
				}
			}
		}

		if (!empty($year)) {

		    $year = trim($year);

			if (!is_numeric($year)) {
				$year = date_parse($year)['year'];
			}

			return (integer)$year;
		} else
			return null;
	}

    /**
     * @throws \PhpZip\Exception\ZipEntryNotFoundException
     * @throws \PhpZip\Exception\ZipException
     */
    public function getLanguage(): string
    {
		return mb_strtolower($this->opf()->getDublinCoreValueByName('language'));
	}

    /**
     * @throws \PhpZip\Exception\ZipEntryNotFoundException
     * @throws \PhpZip\Exception\ZipException
     */
    public function getAnnotation(): ?string
    {
		return $this->opf()->getDublinCoreValueByName('description');
	}

    /**
     * @throws \PhpZip\Exception\ZipEntryNotFoundException
     * @throws \PhpZip\Exception\ZipException
     */
    public function getRightsHolder(): ?string
    {
		return $this->opf()->getDublinCoreValueByName('rightsholder');
	}

    /**
     * @throws \PhpZip\Exception\ZipEntryNotFoundException
     * @throws \PhpZip\Exception\ZipException
     */
    public function getCreatedDate(): ?int
    {
		$year = $this->opf()->getDublinCoreValueByName('created');

		if (!empty($year))
			return (integer)$year;
		else
			return null;
	}

    /**
     * @throws \PhpZip\Exception\ZipEntryNotFoundException
     * @throws \PhpZip\Exception\ZipException
     */
    public function getISBN()
	{
		foreach ($this->opf()->getDublinCoreByName('identifier') as $node) {

			if ($node->hasAttribute('id') and $node->getAttribute('id') == 'pub-identifier') {

				preg_match('/isbn:([\-0-9]+)/iu', $node->nodeValue, $matches);

				return $matches[1] ?? '';
			}
		}

        return null;
	}

    /**
     * @throws \PhpZip\Exception\ZipEntryNotFoundException
     * @throws \PhpZip\Exception\ZipException
     */
    public function getAuthors(): array
    {
		$authors = [];

		foreach ($this->opf()->getDublinCoreByName('creator') as $node) {

			if ($node->hasAttribute('opf:role') and $node->getAttribute('opf:role') == 'aut') {
				$authors[] = $node->nodeValue;
			}
		}

		if (empty($authors)) {
			foreach ($this->opf()->getDublinCoreByName('creator') as $node) {
				$authors[] = $node->nodeValue;
			}
		}

		return empty($authors) ? [] : $authors;
	}

    /**
     * @throws \PhpZip\Exception\ZipException
     * @throws \PhpZip\Exception\ZipEntryNotFoundException
     */
    public function getTranslators(): array
    {
		$translators = [];

		foreach ($this->opf()->getDublinCoreByName('contributor') as $node) {
			if ($node->hasAttribute('opf:role') and $node->getAttribute('opf:role') == 'trl') {
				$translators[] = $node->nodeValue;
			}
		}

		foreach ($this->opf()->getDublinCoreByName('creator') as $node) {
			if ($node->hasAttribute('opf:role') and $node->getAttribute('opf:role') == 'trl') {
				$translators[] = $node->nodeValue;
			}
		}

		foreach ($this->opf()->getMetaDataByName('FB2.book-info.translator') as $node) {
			if ($node->hasAttribute('content')) {
				$translators[] = $node->getAttribute('content');
			}
		}

		foreach ($this->opf()->getMetaDataByName('FB2.title-info.translator') as $node) {
			if ($node->hasAttribute('content')) {
				$translators[] = $node->getAttribute('content');
			}
		}

		$translators = array_unique($translators);

		return empty($translators) ? [] : $translators;
	}

    /**
     * @throws \PhpZip\Exception\ZipEntryNotFoundException
     * @throws \PhpZip\Exception\ZipException
     */
    public function getGenres(): array
    {
		foreach ($this->opf()->getDublinCoreByName('subject') as $node) {
			$genres[] = $node->nodeValue;
		}

		return empty($genres) ? [] : $genres;
	}

    /**
     * @throws \PhpZip\Exception\ZipException
     * @throws \PhpZip\Exception\ZipEntryNotFoundException
     */
    public function getSequences(): array
    {
		$c = 0;
		$sequences = [];

		$query = "*[local-name()='meta'][@content][@name='calibre:series']";

		foreach ($this->opf()->xpath()->query($query, $this->opf()->metaData()) as $node) {

			$name = $node->getAttribute('content');

			foreach ($this->opf()->xpath()->query("following-sibling::*[local-name()='meta']", $node) as $sibling) {
				if ($sibling->getAttribute('name') == 'calibre:series') {
					break;
				}

				if ($sibling->getAttribute('name') == 'calibre:series_index') {
					$number = $sibling->getAttribute('content');
				}
			}

			$sequences[$c]['name'] = trim($name);

			if (!empty($number))
				$sequences[$c]['number'] = trim($number);

			$c++;
			unset($name);
			unset($number);
		}

		foreach ($this->opf()->getMetaDataByName('FB2.book-info.sequence') as $node) {
			if ($node->hasAttribute('content')) {
				preg_match('/(.*);(?:.*)number=([0-9]+)/iu', $node->getAttribute('content'), $match);

				if (!empty($match[1])) {
					$sequences[$c] = [
						'name' => trim($match[1]),
						'number' => intval($match[2]) ?? null
					];

					$c++;
				}
			}
		}

		return empty($sequences) ? [] : $sequences;
	}
}
