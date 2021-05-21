<?php
/**
* 7Bit Encoding class
*/
namespace App\Service\AfMail;

require_once(dirname(__FILE__) . '/iEncoding.php');

class SevenBitEncoding implements iEncoding
{
    /*
    * Function to "encode" data using
    * 7bit encoding.
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
        return '7bit';
    }
}
