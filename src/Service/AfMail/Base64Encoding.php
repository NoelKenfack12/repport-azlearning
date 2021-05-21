<?php
/**
* Base64 Encoding class
*/
namespace App\Service\AfMail;

require_once(dirname(__FILE__) . '/iEncoding.php');

class Base64Encoding implements iEncoding
{
    /*
    * Function to encode data using
    * base64 encoding.
    *
    * @param string $input Data to encode
    */
    public function encode($input)
    {
        return rtrim(chunk_split(base64_encode($input), 76, defined('MAIL_MIME_PART_CRLF') ? MAIL_MIME_PART_CRLF : "\r\n"));
    }

    /**
    * Returns type
    */
    public function getType()
    {
        return 'base64';
    }
}
