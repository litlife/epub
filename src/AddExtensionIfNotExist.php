<?php

namespace Litlife\Epub;

class AddExtensionIfNotExist
{
    private Epub $epub;

    function __construct(&$epub)
    {
        $this->epub = &$epub;
    }

    public function addExtension()
    {
        foreach ($this->epub->getImages() as $image) {

            if (empty($image->getExtension())) {

                $image->rename($image->getFileName() . '.' . mb_strtolower($image->guessExtension()));
            }
        }
    }
}
