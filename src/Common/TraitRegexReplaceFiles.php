<?php

namespace huenisys\Utils\Common;

use Illuminate\Support\Str;
use Symfony\Component\Finder\Finder;

trait TraitRegexReplaceFiles {

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
     *  Regex replace files
     *
     * @return mixed
     */
    public static function regexReplaceAllFilesContent($search, $replace, $dirPath, bool $returnSearchedFilesArr = true)
    {
        $fileObjsArr = iterator_to_array(
            Finder::create()->files()->ignoreDotFiles(false)->in($dirPath)->sortByName(),
            false
        );

        $thisTrait = static::class;

        $pathnamesArr = array_map(function($fileO) use($search, $replace, $thisTrait) {

            $thisTrait::regexReplaceSameStub($search, $replace, $fileO->getPathname());

            return $fileO->getPathname();

        }, $fileObjsArr);

        if ($returnSearchedFilesArr)
            return $pathnamesArr;
    }

    protected function allFiles($dirPath) {
        return iterator_to_array(
            Finder::create()->files()->ignoreDotFiles(false)->in($dirPath)->sortByName(),
            false
        );
    }

    public static function regexReplaceAllFiles($search, $replace, $sourceDirPath, array $options = [])
    {
        $defOptions = array_merge([
            'destDirPath' => null,
            'includeContent' => false,
            'keepCopy' => true,
            'report' => true,
            'backupDirPath' => null,
        ], $options);

        extract($defOptions);

        $thisTrait = static::class;

        if (is_null($destDirPath))
            $destDirPath = $sourceDirPath;

        $fileObjsArr = static::allFiles($sourceDirPath);

        $curTime = (string) time();

        $deltaReportsArr = array_map(function($fileO) use($search, $replace, $thisTrait, $keepCopy, $sourceDirPath, $destDirPath, $backupDirPath, $includeContent, $curTime) {

            $afterPath = Str::after($fileO->getPathname(), $sourceDirPath);

            if ($keepCopy) :
                $backupDirPath = $backupDirPath ?? $sourceDirPath . '.bak'. DIRECTORY_SEPARATOR . $curTime;

                if (!is_dir($backupDirPath))
                    mkdir($backupDirPath, 0777, true);

                copy($fileO->getPathname(), $backupDirPath . DIRECTORY_SEPARATOR . $afterPath);
            endif;

            $possiblyReplacedAfterPath = str_replace($search, $replace, $afterPath);

            $afterPathChanged = false;

            if ($possiblyReplacedAfterPath !== $afterPath) :
                $afterPathChanged = true;
            endif;

            $possiblyNewPathname = $destDirPath . $possiblyReplacedAfterPath;

            rename($fileO->getPathname(), $possiblyNewPathname);

            if ($includeContent)
                $thisTrait::regexReplaceSameStub($search, $replace, $possiblyNewPathname);

            return [ 'pathnameChanged' => $afterPathChanged, 'new' => $possiblyNewPathname, 'old' => $fileO->getPathname()];

        }, $fileObjsArr);

        if ($report)
            return $deltaReportsArr;
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
        return static::regexReplaceStub($searchText, $replacerText, static::base_path($pathRelToBaseSource), static::base_path($pathRelToBaseDest), $isFolder);
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
        return static::regexReplaceSameStub($searchText, $replacerText, static::base_path($pathRelToBase), $isFolder);
    }

    /**
     * Returns path relative to a base path
     *
     * @param string $pathRelToBase
     * @param bool $forceLocal
     * @param string $basePath defaults to tmp folder
     * @return string
     */
    public static function base_path(string $pathRelToBase = null, bool $forceLocal = false, string $basePath = null)
    {
        $composerPackagePath = __DIR__.'/../..';

        if (function_exists('base_path') && $forceLocal !== true) :
            return base_path($pathRelToBase);
        else :
            $intended = ($basePath ?: $composerPackagePath.'/tests/tmp') . DIRECTORY_SEPARATOR . $pathRelToBase;
            return realpath($intended) ?: $intended;
        endif;
    }
}
