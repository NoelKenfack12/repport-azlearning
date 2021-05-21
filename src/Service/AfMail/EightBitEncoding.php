<?php
/**
* 8Bit Encoding class
*/
namespace App\Service\AfMail;

require_once(dirname(__FILE__) . '/iEncoding.php');

class EightBitEncoding implements iEncoding
{
    /*
    * Function to "encode" data using
    * 8bit encoding.
    *
    * @param string $input Data to encode
    */
    public function encode($input)
    {
        return $input;
    }

    /**
    * Returns type
    */
    public function getType()
    {
        return '8bit';
    }
}
