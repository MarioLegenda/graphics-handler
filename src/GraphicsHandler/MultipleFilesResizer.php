<?php
/**
 * Created by PhpStorm.
 * User: Mario
 * Date: 17.10.14.
 * Time: 14:37
 */

namespace GraphicsHandler;

use GraphicsHandler\Exception\FileException;

class MultipleFilesResizer
{
    private $graphicsHandler;

    private $uploadedFiles = array();
    private $canvases = array();

    public function __construct(GraphicsHandler $graphicsHandler) {
        $this->graphicsHandler = $graphicsHandler;
    }

    public function setFiles( array $uploadedFiles ) {
        foreach( $uploadedFiles AS $file ) {
            if( ! $file instanceof \SplFileInfo ) {
                throw new FileException('$uploadedFiles has to be of type SplFileInfo');
            }
        }

        $this->uploadedFiles = $uploadedFiles;

        return $this;
    }

    public function resize(array $sizeRatio) {
        foreach( $this->uploadedFiles AS $file ) {
            $this->canvases[] = $this->graphicsHandler
                                    ->setMimeType($file->getClientMimeType())
                                    ->setUploadedFile($file)
                                    ->resize($sizeRatio, true);

            $this->graphicsHandler->eraseData();
        }

        return $this;
    }

    public function saveResized(array $resizeInfo) {
        if( ! array_key_exists('path', $resizeInfo) AND ! array_key_exists('filenames', $resizeInfo) ) {
            throw new FileException('Wrong data keys sent to saveResized. Keys are \'path\' and \'filenames\'');
        }

        $counter = 0;
        $filenames = $resizeInfo['filenames'];
        foreach( $this->uploadedFiles AS $file ) {
            $path = str_replace('\\', '/', $resizeInfo['path']);
            imagejpeg($this->canvases[$counter], $path . $filenames[$counter], 100);

            imagedestroy($this->canvases[$counter]);

            $counter += 1;
        }
    }


} 