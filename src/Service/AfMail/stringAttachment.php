<?php
/**
* Attachment class to handle attachments which are contained
* in a variable.
*/
namespace App\Service\AfMail;
require_once(dirname(__FILE__) . '/attachment.php');
require_once(dirname(__FILE__) . '/Base64Encoding.php');

class stringAttachment extends attachment
{
    /**
    * Constructor
    *
    * @param string $data        File data
    * @param string $name        Name of attachment (filename)
    * @param string $contentType Content type of file
    * @param string $encoding    What encoding to use
    */
    public function __construct($data, $name = '', $contentType = 'application/octet-stream', $encoding = null)
    {
        $encoding = is_null($encoding) ? new Base64Encoding() : $encoding;

        parent::__construct($data, $name, $contentType, $encoding);
    }
}
