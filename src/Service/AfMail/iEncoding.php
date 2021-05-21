<?php
/**
*
*/
/**
* Encoding interface
*/
namespace App\Service\AfMail;

interface iEncoding
{
    public function encode($input);
    public function getType();
}
