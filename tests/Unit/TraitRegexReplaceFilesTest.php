<?php

namespace huenisys\Utils\Tests;

use huenisys\Utils\Common\TraitRegexReplaceFiles as TheTrait;
use PHPUnit\Framework\TestCase;

class TraitRegexReplaceFilesTest extends TestCase
{
    public $filepath1;
    public $filepath2;
    public $filename1;
    public $filename2;

    public function setUp() :void
    {
        $this->filename1 = 'test1.txt';
        $this->filename2 = 'test2.txt';
        $this->filepath1 = TheTrait::base_path($this->filename1);
        $this->filepath2 = TheTrait::base_path($this->filename2);

        var_dump(TheTrait::base_path($this->filename1));

        // always start with hello world
        file_put_contents($this->filepath1, 'Hello World');
        file_put_contents($this->filepath2, 'Hello World');
    }

    /** @test **/
    public function initialTextIsHelloWorld()
    {
        $this->assertStringContainsString('Hello World', file_get_contents($this->filepath1));
    }

    /** @test **/
    public function replaceSameStubWithText()
    {
       TheTrait::regexReplaceSameStub('World', 'Paul', $this->filepath1);

        $this->assertStringContainsString('Hello Paul', file_get_contents($this->filepath1));
    }

    /** @test **/
    public function replaceStubWithSourceContentFromAnotherFile()
    {
        TheTrait::regexReplaceStub('Lorem', 'Ipsum', TheTrait::base_path('ipsum-source.txt'), $this->filepath1);

        $this->assertStringContainsString('Ipsum Ipsum', file_get_contents($this->filepath1));
        $this->assertStringNotContainsString('Ipsum1 Ipsum', file_get_contents($this->filepath1));
    }

    /** @test **/
    public function regexReplaceFilesInDir()
    {
        TheTrait::regexReplaceAllFilesContent('World', 'World1', TheTrait::base_path());

        $this->assertStringContainsString('World1', file_get_contents($this->filepath1));
        $this->assertStringNotContainsString('Ipsum Ipsum', file_get_contents($this->filepath2));
    }

    /** @test **/
    public function fxn()
    {
        $result = TheTrait::regexReplaceAllFiles('bloom1', 'bloom10', TheTrait::base_path('files-source'), [
            'backupDirPath'=>TheTrait::base_path('files-backup'),
        ]);
        var_dump($result);
    }
}
