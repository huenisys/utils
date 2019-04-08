<?php

namespace huenisys\Utils\Common;

use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;

trait TraitRegexReplaceFiles {

    public static function rrs()
    {
        return call_user_func_array('static::regexReplaceStub', func_get_args());
    }

    public static function rrss()
    {
        return call_user_func_array('static::regexReplaceSameStub', func_get_args());
    }

    public static function rrsrbp()
    {
        return call_user_func_array('static::regexReplaceStubRelToBasePath', func_get_args());
    }

    public static function rrssrbp()
    {
        return call_user_func_array('static::regexReplaceSameStubRelToBasePath', func_get_args());
    }

    public static function rrafc()
    {
        return call_user_func_array('static::regexReplaceAllFilesContent', func_get_args());
    }

    /**
     * Regex replaces texts in a file
     *
     * @param string $search
     * @param string $replace The new text
     * @param string $pathSource source content
     * @param string $pathDest where the new text will go
     */
    public static function regexReplaceStub($search, $replace, $pathSource, $pathDest)
    {
        $newContent = str_replace(
            $search,
            $replace,
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
     * @param string $search
     * @param string $replace The new text
     * @param string $pathname Relative path to laravel folder
     */
    public static function regexReplaceSameStub($search, $replace, $pathname)
    {
        return static::regexReplaceStub($search, $replace, $pathname, $pathname);
    }

    /**
     * Regex replaces text in a file
     *
     * @param string $search
     * @param string $replace The new text
     * @param string $pathRelToBaseSource Source with path relative path to a base folder
     * @param string $pathRelToBaseDest Destination filepath relative path to a base folder
     */
    public static function regexReplaceStubRelToBasePath($search, $replace, $pathRelToBaseSource, $pathRelToBaseDest)
    {
        return static::regexReplaceStub($search, $replace, static::bp($pathRelToBaseSource), static::bp($pathRelToBaseDest));
    }

    /**
     * Regex replaces text in a file
     *
     * @param string $search
     * @param string $replace The new text
     * @param string $pathRelToBase Relative path to a base folder
     * @param bool $isFolder if folder, we must iterate over all files
     */
    public static function regexReplaceSameStubRelToBasePath($search, $replace, $pathRelToBase, $isFolder = false)
    {
        return static::regexReplaceSameStub($search, $replace, static::bp($pathRelToBase), $isFolder);
    }

    /**
     *  Regex replace files
     *
     * @return mixed
     */
    public static function regexReplaceAllFilesContent($search, $replace, $dirPath, bool $returnSearchedFilesArr = true)
    {
        $fileObjsArr = static::allFiles($dirPath);

        $thisTrait = static::class;

        $pathnamesArr = array_map(function($fileO) use($search, $replace, $thisTrait) {

            $thisTrait::regexReplaceSameStub($search, $replace, $fileO->getPathname());

            return $fileO->getPathname();

        }, $fileObjsArr);

        if ($returnSearchedFilesArr)
            return $pathnamesArr;
    }

    public static function allFiles($dirPath, array $options = []) {

        extract(array_merge(
            $finderOptions = [
                'ignoreDotFiles' => false,
                'depth' => null
            ], Arr::only($options, array_keys($finderOptions))
        ));

        return iterator_to_array(
            Finder::create()->files()->ignoreDotFiles($ignoreDotFiles)->in($dirPath)->depth($depth)->sortByName(),
            false
        );
    }

    /**
     * Regex replaces texts in a file
     *
     * @param string $search
     * @param string $relace
     * @param string $sourceDirPath directory of source files
     * @param array $options
     * @return mixed
     */
    public static function regexReplaceAllFilepaths($search, $replace, $sourceDirPath, array $options = [])
    {
        extract(array_merge(
            $searchOptions = [
                'destDirPath' => null,
                'includeContent' => false,
                'report' => true,
            ], Arr::only($options, array_keys($searchOptions))
        ));

        $thisTrait = static::class;

        if (is_null($destDirPath)) :
            $destDirPath = $sourceDirPath;
        else :
            $filesystem = new Filesystem();
            try {
                $filesystem->mirror($sourceDirPath, $destDirPath);
            } catch (IOExceptionInterface $exception) {
                echo "An error occurred while creating your directory at ".$exception->getPath();
            }
        endif;

        $fileObjsArr = static::allFiles($destDirPath, Arr::only($options, [
            'ignoreDotFiles',
            'depth'
        ]));

        $deltaReportsArr = array_map(function($fileO) use($search, $replace, $thisTrait, $sourceDirPath, $destDirPath, $includeContent) {

            // this makes sure higher level folders arent affected
            $afterPath = Str::after($fileO->getPathname(), $destDirPath);

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
     * Returns path relative to a base path
     *
     * @param string $pathRelToBase
     * @param bool $forceLocal
     * @param string $basePath defaults to tmp folder
     * @return string
     */
    public static function bp(string $pathRelToBase = null, bool $forceLocal = false, string $basePath = null)
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
