<?php

use Pebble\Tools\Image;
use PHPUnit\Framework\TestCase;

class ImageTest extends TestCase
{
    private string $dir;

    protected function setUp(): void
    {
        if (!extension_loaded('imagick')) {
            $this->markTestSkipped('ext-imagick is not loaded');
        }

        $this->dir = sys_get_temp_dir() . '/pebble_tools_' . uniqid();
        mkdir($this->dir);
    }

    protected function tearDown(): void
    {
        if (isset($this->dir)) {
            array_map('unlink', glob($this->dir . '/*'));
            rmdir($this->dir);
        }
    }

    /**
     * 20x10 PNG, left half red, right half blue.
     */
    private function png(): Image
    {
        $img = new Image();
        $img->newImage(20, 10, new ImagickPixel('red'));
        $draw = new ImagickDraw();
        $draw->setFillColor('blue');
        $draw->rectangle(10, 0, 19, 9);
        $img->drawImage($draw);
        $img->setImageFormat('png');
        $img->writeImage($this->dir . '/test.png');

        return new Image($this->dir . '/test.png');
    }

    private function color(Image $img, int $x, int $y): string
    {
        return $img->getImagePixelColor($x, $y)->getColorAsString();
    }

    // -------------------------------------------------------------------------
    // File
    // -------------------------------------------------------------------------

    public function testReadsAFile()
    {
        $img = $this->png();

        self::assertSame('test', $img->getName());
        self::assertSame('image/png', $img->getImageMimeType());
        self::assertGreaterThan(0, $img->getFileSize());
        self::assertTrue($img->hasFormat('png'));
        self::assertFalse((new Image())->hasFormat('png'));
    }

    public function testSaveWithAnotherFormatReturnsANewObject()
    {
        $img = $this->png();
        $jpg = $img->toJpeg()->save();

        self::assertNotSame($img, $jpg);
        self::assertSame($this->dir . '/test.jpg', $jpg->getImageFilename());
        self::assertSame('image/jpeg', $jpg->getImageMimeType());
        self::assertSame($img, $img->save());
    }

    // -------------------------------------------------------------------------
    // Resize / crop
    // -------------------------------------------------------------------------

    public function testFitBoundsSwapsBoxToMatchOrientation()
    {
        self::assertTrue($this->png()->isInside(10, 20));
        self::assertFalse($this->png()->isInside(10, 20, true));

        $img = $this->png()->fitBounds(5, 4);
        self::assertSame([5, 3], [$img->getImageWidth(), $img->getImageHeight()]);

        $img = $this->png()->fitBounds(100, 100);
        self::assertSame([20, 10], [$img->getImageWidth(), $img->getImageHeight()]);
    }

    public function testCrop()
    {
        $img = $this->png()->crop(['x' => 8, 'y' => 2, 'width' => 4, 'height' => 5]);

        self::assertSame([4, 5], [$img->getImageWidth(), $img->getImageHeight()]);
        self::assertSame('srgb(255,0,0)', $this->color($img, 0, 0));
        self::assertSame('srgb(0,0,255)', $this->color($img, 3, 0));
    }

    public function testCropKeepsTheVirtualCanvasOffset()
    {
        // No setImagePage()/+repage after cropImage().
        $page = $this->png()->crop(['x' => 8, 'y' => 2, 'width' => 4, 'height' => 5])->getImagePage();

        self::assertSame([20, 10, 8, 2], [$page['width'], $page['height'], $page['x'], $page['y']]);
    }

    // -------------------------------------------------------------------------
    // Hash
    // -------------------------------------------------------------------------

    public function testHashIs64HexDigits()
    {
        $hash = $this->png()->getHash();

        self::assertSame(64, strlen($hash));
        self::assertSame(0, Image::similarHash($hash, $this->png()->getHash()));
    }

    public function testSimilarHashGivesDistanceAndPercent()
    {
        $percent = 0;

        self::assertSame(3, Image::similarHash('8', 'f', $percent));
        self::assertSame(25.0, $percent);
    }

    // -------------------------------------------------------------------------
    // Known bugs (see TODO.md)
    // -------------------------------------------------------------------------

    public function testSimilarHashPadsBitsOnTheWrongSide()
    {
        // BUG: str_pad(..., 4, STR_PAD_LEFT) uses "0" as pad string and pads
        // right: '1' becomes 1000 like '8', so they look identical.
        self::assertSame(0, Image::similarHash('1', '8'));
    }

    public function testCropScaleXFlipsVertically()
    {
        // BUG: scaleX = -1 calls flipImage() (vertical) and scaleY = -1 calls
        // flopImage() (horizontal), the opposite of cropper.js.
        self::assertSame('srgb(255,0,0)', $this->color($this->png()->crop(['scaleX' => -1]), 0, 0));
        self::assertSame('srgb(0,0,255)', $this->color($this->png()->crop(['scaleY' => -1]), 0, 0));
    }

    public function testCropPassesFloatsToCropImage()
    {
        // BUG: x/y/width/height are cast to float, then truncated by cropImage()
        // with an "Implicit conversion" deprecation.
        $deprecations = 0;
        set_error_handler(function () use (&$deprecations) {
            $deprecations++;
            return true;
        }, E_DEPRECATED);

        try {
            $img = $this->png()->crop(['x' => 1.6, 'y' => 0.4, 'width' => 5.7, 'height' => 5.5]);
        } finally {
            restore_error_handler();
        }

        self::assertSame(4, $deprecations);
        self::assertSame([5, 5], [$img->getImageWidth(), $img->getImageHeight()]);
    }
}
