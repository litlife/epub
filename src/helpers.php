<?php

if (!function_exists("mb_str_replace")) {
    function mb_str_replace($needle, $replace_text, $haystack): string
    {
        return implode($replace_text, mb_split($needle, $haystack));
    }
}
