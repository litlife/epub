<?php

namespace Litlife\Epub;

class UnifyImagesNames
{
    /**
     * @var \Litlife\Epub\Epub
     */
    private Epub $epub;
    /**
     * @var string
     */
    private string $prefix;
    /**
     * @var int|mixed
     */
    private mixed $current_id;

    function __construct($epub)
	{
		$this->epub = &$epub;

		$this->prefix = 'image_';

		$this->current_id = $this->getMaxId();
	}

	public function getMaxId()
	{
		foreach ($this->epub->getImages() as $image) {
			if (mb_substr(mb_strtolower($image->getFileName()), 0, strlen($this->prefix)) == $this->prefix) {

				$id = mb_substr(mb_strtolower($image->getFileName()), strlen($this->prefix));

				if (is_numeric($id)) {
					$ids[] = $id;
				}
			}
		}

		return empty($ids) ? 0 : max($ids);
	}

	public function createFileName(): string
    {
		return $this->prefix . $this->current_id;
	}

	public function unify(): array
    {
		$imagesNames = [];
		$imagesForRename = [];

		foreach ($this->epub->getImages() as $image) {

			if (in_array(mb_strtolower($image->getBaseName()), $imagesNames)) {
				$imagesForRename[] = $image;
			} else {
				$imagesNames[] = mb_strtolower($image->getBaseName());
			}
		}

		foreach ($imagesForRename as $image) {
			$this->current_id++;
			$image->rename($this->prefix . $this->current_id . '.' . $image->getExtension());
		}

		return $imagesForRename;
	}

}
