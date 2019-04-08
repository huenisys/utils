<?php

namespace huenisys\Utils\Tests;

use huenisys\Utils\Common\TraitRegexReplaceFiles as Trt;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;

class TraitRegexReplaceFilesTest extends TestCase
{

    protected function sU_fs()
    {
        $this->fs = new Filesystem();
    }

    protected function sU_g1()
    {
        // playground
        $this->fs->mirror(Trt::bp('g1'), Trt::bp('pg/g1'));

        $this->g1t1 = Trt::bp('pg/g1/t1.txt');
        $this->g1t2 = Trt::bp('pg/g1/t2.txt');
        $this->g1t3 = Trt::bp('pg/g1/t3.txt');

        file_put_contents($this->g1t1, 'Hello World');
        file_put_contents($this->g1t2, 'Hi Earth');
        file_put_contents($this->g1t3, 'Lorem Ipsum');
    }

    public function setUp() :void
    {
        if (! defined('DS')) {
            define('DS', DIRECTORY_SEPARATOR);
        }

        $this->sU_fs(); // setup filesystem
        $this->sU_g1(); // setup group1
    }

    /** @test **/
    public function initialTextsFromG1_isShowingRight()
    {
        $this->assertStringContainsString('Hello World', file_get_contents($this->g1t1));
        $this->assertStringContainsString('Hi Earth', file_get_contents($this->g1t2));
        $this->assertStringContainsString('Lorem Ipsum', file_get_contents($this->g1t3));
    }

    /** @test **/
    public function regexReplaceStub_sourceContentFromAnotherFile()
    {
        Trt::rrs('Lorem', 'Ipsum', $this->g1t3, $this->g1t1);
        $this->assertStringContainsString('Ipsum Ipsum', file_get_contents($this->g1t1));
        $this->assertStringNotContainsString('Ipsum1 Ipsum', file_get_contents($this->g1t1));
    }

    /** @test **/
    public function regexReplaceSameStub_withText()
    {
        Trt::rrss('World', 'Paul', $this->g1t1);
        $this->assertStringContainsString('Hello Paul', file_get_contents($this->g1t1));
    }

    public function regexReplaceFilesContentInDir()
    {
        Trt::rrafc('World', 'World1', Trt::bp('pg/g1'));
        $this->assertStringContainsString('Hello World1', file_get_contents($this->g1t1));
        $this->assertStringNotContainsString('Ipsum Ipsum', file_get_contents($this->g1t3));
    }

}
