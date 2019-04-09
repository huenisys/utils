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
        $this->fs->mirror(Trt::bp('g1'), Trt::bp('pg/g1'), null, [
            'override' => true,
            'delete' => true,
        ]);

        $this->g1t1 = Trt::bp('pg/g1/t1.txt');
        $this->g1t2 = Trt::bp('pg/g1/t2.txt');
        $this->g1t3 = Trt::bp('pg/g1/t3.txt');
    }

    protected function sU_g2()
    {
        // playground
        $this->fs->mirror(Trt::bp('g2'), Trt::bp('pg/g2'), null, [
            'override' => true,
            'delete' => true,
        ]);

        $this->g2t1d0_b101 = Trt::bp('pg/g2/file1-depth0-bloom101.txt');
        $this->g2t2d1_b101 = Trt::bp('pg/g2/depth1/file2-depth1-bloom101.txt');
        $this->g2t3d2_b101 = Trt::bp('pg/g2/depth1/depth2/file3-depth2-bloom101.txt');
        $this->g2t1d0_b103 = Trt::bp('pg/g2/file1-depth0-bloom103.txt');
        $this->g2t2d1_b103 = Trt::bp('pg/g2/depth1/file2-depth1-bloom103.txt');
        $this->g2t3d2_b103 = Trt::bp('pg/g2/depth1/depth2/file3-depth2-bloom103.txt');
    }

    public function setUp() :void
    {
        if (! defined('DS')) {
            define('DS', DIRECTORY_SEPARATOR);
        }

        $this->sU_fs(); // setup filesystem
    }

    /** @test **/
    public function initialTextsFromG1_isShowingRight()
    {
        $this->sU_g1(); // setup group1

        $this->assertStringContainsString('Hello World', file_get_contents($this->g1t1));
        $this->assertStringContainsString('Hi Earth', file_get_contents($this->g1t2));
        $this->assertStringContainsString('Lorem Ipsum', file_get_contents($this->g1t3));
    }

    /** @test **/
    public function regexReplaceStub_sourceContentFromAnotherFile()
    {
        $this->sU_g1(); // setup group1

        Trt::rrs('Lorem', 'Ipsum', $this->g1t3, $this->g1t1);
        $this->assertStringContainsString('Ipsum Ipsum', file_get_contents($this->g1t1));
        $this->assertStringNotContainsString('Ipsum1 Ipsum', file_get_contents($this->g1t1));
    }

    /** @test **/
    public function regexReplaceSameStub_withText()
    {
        $this->sU_g1(); // setup group1

        Trt::rrss('World', 'Paul', $this->g1t1);
        $this->assertStringContainsString('Hello Paul', file_get_contents($this->g1t1));
    }

    /** @test **/
    public function regexReplaceFilesContentInDir()
    {
        $this->sU_g1(); // setup group1

        Trt::rrafc('World', 'World1', Trt::bp('pg/g1'));
        $this->assertStringContainsString('Hello World1', file_get_contents($this->g1t1));
        $this->assertStringNotContainsString('Ipsum Ipsum', file_get_contents($this->g1t3));
    }

    /** @test **/
    public function regexReplaceAllFilepaths_contentNotChanged_allDepth_renamed()
    {
        $this->sU_g2(); // setup group2

        Trt::rrafp('bloom101', 'bloom103', Trt::bp('pg/g2'));
        $this->assertFileNotExists($this->g2t1d0_b101);
        $this->assertFileNotExists($this->g2t2d1_b101);
        $this->assertFileNotExists($this->g2t3d2_b101);
        $this->assertFileExists($this->g2t1d0_b103);
        $this->assertFileExists($this->g2t2d1_b103);
        $this->assertFileExists($this->g2t3d2_b103);
        $this->assertStringContainsString('bloom101', file_get_contents($this->g2t1d0_b103));
        $this->assertStringContainsString('bloom101', file_get_contents($this->g2t2d1_b103));
        $this->assertStringContainsString('bloom101', file_get_contents($this->g2t3d2_b103));
    }

    /** @test **/
    public function regexReplaceAllFilepaths_contentNotChanged_Depth0Only_renamed()
    {
        $this->sU_g2(); // setup group2

        Trt::rrafp('bloom101', 'bloom103', Trt::bp('pg/g2'), [
            'depth' => 0
        ]);
        $this->assertFileNotExists($this->g2t1d0_b101);
        $this->assertFileExists($this->g2t2d1_b101);
        $this->assertFileExists($this->g2t3d2_b101);
        $this->assertFileExists($this->g2t1d0_b103);
        $this->assertFileNotExists($this->g2t2d1_b103);
        $this->assertFileNotExists($this->g2t3d2_b103);
        $this->assertStringNotContainsString('bloom101', $this->g2t1d0_b103);
        $this->assertStringNotContainsString('bloom101', $this->g2t2d1_b103);
        $this->assertStringNotContainsString('bloom101', $this->g2t3d2_b103);
    }

    /** @test **/
    public function regexReplaceAllFilepaths_contentNotChanged_Depth1Only_renamed()
    {
        $this->sU_g2(); // setup group2

        Trt::rrafp('bloom101', 'bloom103', Trt::bp('pg/g2'), [
            'depth' => 1
        ]);
        $this->assertFileExists($this->g2t1d0_b101);
        $this->assertFileNotExists($this->g2t2d1_b101);
        $this->assertFileExists($this->g2t3d2_b101);
        $this->assertFileNotExists($this->g2t1d0_b103);
        $this->assertFileExists($this->g2t2d1_b103);
        $this->assertFileNotExists($this->g2t3d2_b103);
        $this->assertStringNotContainsString('bloom101', $this->g2t1d0_b103);
        $this->assertStringNotContainsString('bloom101', $this->g2t2d1_b103);
        $this->assertStringNotContainsString('bloom101', $this->g2t3d2_b103);
    }

    /** @test **/
    public function regexReplaceAllFilepaths_includeContent_allDepth_renamed()
    {
        $this->sU_g2(); // setup group2

        Trt::rrafp('bloom101', 'bloom103', Trt::bp('pg/g2'), [
            'includeContent' => true
        ]);
        $this->assertFileNotExists($this->g2t1d0_b101);
        $this->assertFileNotExists($this->g2t2d1_b101);
        $this->assertFileNotExists($this->g2t3d2_b101);
        $this->assertFileExists($this->g2t1d0_b103);
        $this->assertFileExists($this->g2t2d1_b103);
        $this->assertFileExists($this->g2t3d2_b103);

        $this->assertStringNotContainsString('bloom101', file_get_contents($this->g2t1d0_b103));
        $this->assertStringNotContainsString('bloom101', file_get_contents($this->g2t2d1_b103));
        $this->assertStringNotContainsString('bloom101', file_get_contents($this->g2t3d2_b103));

        $this->assertStringContainsString('bloom103', file_get_contents($this->g2t1d0_b103));
        $this->assertStringContainsString('bloom103', file_get_contents($this->g2t2d1_b103));
        $this->assertStringContainsString('bloom103', file_get_contents($this->g2t3d2_b103));
    }

    /** @test **/
    public function regexReplaceAllFilepaths_includeContent_Depth0Only_renamed()
    {
        $this->sU_g2(); // setup group2

        Trt::rrafp('bloom101', 'bloom103', Trt::bp('pg/g2'), [
            'includeContent' => true,
            'depth' => 0
        ]);
        $this->assertFileNotExists($this->g2t1d0_b101);
        $this->assertFileExists($this->g2t2d1_b101);
        $this->assertFileExists($this->g2t3d2_b101);
        $this->assertFileExists($this->g2t1d0_b103);
        $this->assertFileNotExists($this->g2t2d1_b103);
        $this->assertFileNotExists($this->g2t3d2_b103);

        $this->assertStringContainsString('bloom103', file_get_contents($this->g2t1d0_b103));
        $this->assertStringContainsString('bloom101', file_get_contents($this->g2t2d1_b101));
        $this->assertStringContainsString('bloom101', file_get_contents($this->g2t3d2_b101));
    }

}
