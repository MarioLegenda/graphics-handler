<?php
/**
 * Created by PhpStorm.
 * User: Mario
 * Date: 17.10.14.
 * Time: 14:44
 */

namespace GraphicsHandler\Exception;


class FileException extends \Exception
{
    public function __construct($message) {
        $this->message = $message;
    }
} 