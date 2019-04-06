<?php

namespace huenisys\Utils\Tests;

use huenisys\Utils\UseTraitStaticRegexReplaceStubFxns;
use PHPUnit\Framework\TestCase;

if (! function_exists('base_path')) {

    function base_path(string $path = '')
    {
        return __DIR__.'/../tmp' . ( ($path != '') ? '/'.$path : '' );
    }
}

class UseTraitStaticRegexReplaceStubFxnsTest extends TestCase
{
    public $filepath;
    public $filename;

    public function setUp() :void
    {
        $this->filename = 'test.txt';
        $this->filepath = base_path($this->filename);

        file_put_contents($this->filepath, 'Hello World');
    }

    /** @test **/
    public function initialTextIsHelloWorld()
    {
        $this->assertStringContainsString('Hello World', file_get_contents($this->filepath));
    }

    /** @test **/
    public function replaceSameStubWithText()
    {
       UseTraitStaticRegexReplaceStubFxns::regexReplaceSameStub('World', 'Paul', $this->filepath);

        $this->assertStringContainsString('Hello Paul', file_get_contents($this->filepath));
    }

    /** @test **/
    public function replaceStubWithSourceContentFromAnotherFile()
    {
        UseTraitStaticRegexReplaceStubFxns::regexReplaceStub('Lorem', 'Ipsum', base_path('ipsum-source.txt'), $this->filepath);

        $this->assertStringContainsString('Ipsum Ipsum', file_get_contents($this->filepath));
        $this->assertStringNotContainsString('Ipsum1 Ipsum', file_get_contents($this->filepath));
    }
}
