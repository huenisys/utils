<?php

namespace huenisys\Utils\Common;

trait TraitStaticRegexReplaceStubFxns {

    /**
     * Regex replaces texts in a file
     *
     * @param string $searchText
     * @param string $replacerText The new text
     * @param string $pathSource source content
     * @param string $pathDest where the new text will go
     * @param bool $isFolder if folder, we must iterate over all files
     */
    public static function regexReplaceStub($searchText, $replacerText, $pathSource, $pathDest, $isFolder = false)
    {
        $newContent = str_replace(
            $searchText,
            $replacerText,
            file_get_contents($pathSource)
        );

        file_put_contents(
            $pathDest,
            $newContent
        );
    }

    /**
     * Regex replaces text in a file
     *
     * @param string $searchText
     * @param string $replacerText The new text
     * @param string $pathname Relative path to laravel folder
     * @param bool $isFolder if folder, we must iterate over all files
     */
    public static function regexReplaceSameStub($searchText, $replacerText, $pathname, $isFolder = false)
    {
        return static::regexReplaceStub($searchText, $replacerText, $pathname, $pathname, $isFolder);
    }

    /**
     * Regex replaces text in a file
     *
     * @param string $searchText
     * @param string $replacerText The new text
     * @param string $pathRelToBaseSource Source with path relative path to a base folder
     * @param string $pathRelToBaseDest Destination filepath relative path to a base folder
     * @param bool $isFolder if folder, we must iterate over all files
     */
    public static function regexReplaceStubRelToBasePath($searchText, $replacerText, $pathRelToBaseSource, $pathRelToBaseDest, $isFolder = false)
    {
        if (! function_exists('base_path')) :
            throw new \Exception('No base_path method exists');
        endif;

        return static::regexReplaceStub($searchText, $replacerText, base_path($pathRelToBaseSource), base_path($pathRelToBaseDest), $isFolder);
    }

    /**
     * Regex replaces text in a file
     *
     * @param string $searchText
     * @param string $replacerText The new text
     * @param string $pathRelToBase Relative path to a base folder
     * @param bool $isFolder if folder, we must iterate over all files
     */
    public static function regexReplaceSameStubRelToBasePath($searchText, $replacerText, $pathRelToBase, $isFolder = false)
    {
        return static::regexReplaceStubRelToBasePath($searchText, $replacerText, $pathRelToBase, $isFolder);
    }
}
