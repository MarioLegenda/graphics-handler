<?php
/**
 * Created by PhpStorm.
 * User: Mario
 * Date: 25.07.14.
 * Time: 19:56
 */

namespace GraphicsHandler;

use GraphicsHandler\MultipleFilesResizer;

class GraphicsHandler
{
    private $mimeType;
    private $uploadedFile;
    private $resizedCanvas;

    public function __construct($mimeType = null, $uploadedFile = null) {
        if( $mimeType !== null AND $uploadedFile !== null ) {
            $this->mimeType = $mimeType;
            $this->uploadedFile = $uploadedFile;
        }
    }

    public function createMulitpleFilesResizer() {
        return new MultipleFilesResizer($this);
    }

    public function setMimeType($mimeType) {
        $this->mimeType = $mimeType;

        return $this;
    }

    public function setUploadedFile(\SplFileInfo $uploadedFile) {
        $this->uploadedFile = $uploadedFile;

        return $this;
    }

    public function resize(array $sizeRatio, $returnCanvas = false) {
        if ($this->mimeType == 'image/jpeg' OR $this->mimeType == 'image/jpg') {
            $image = imagecreatefromjpeg($this->uploadedFile);
        } else if ($this->mimeType == 'image/png') {
            $image = imagecreatefrompng($this->uploadedFile);
        }

        $width = imagesx($image);
        $height = imagesy($image);

        $canvas = imagecreatetruecolor($sizeRatio[0], $sizeRatio[1]);

        // Create a new transparent color for image
        $color = imagecolorallocatealpha($canvas, 255, 255, 255, 127);

        // Completely fill the background of the new image with allocated color.
        imagefill($canvas, 1, 1, $color);
        imagesavealpha($canvas, true);

        imagecopyresampled($canvas, $image, 0, 0, 0, 0, $sizeRatio[0], $sizeRatio[1], $width, $height);
        @ imagetruecolortopalette($canvas, false, imagecolorstotal($image));

        if( $returnCanvas === true ) {
            imagedestroy($image);
            @ imagedestroy($color);
            return $canvas;
        }

        $this->resizedCanvas = $canvas;

        imagedestroy($image);
        @ imagedestroy($color);

        return $this;
    }

    public function saveResized($path, $fileName) {
        $path = str_replace('\\', '/', $path);
        imagejpeg($this->resizedCanvas, $path . $fileName, 100);

        imagedestroy($this->resizedCanvas);
    }

    public function eraseData() {
        $this->uploadedFile = null;
        $this->mimeType = null;
    }
} 