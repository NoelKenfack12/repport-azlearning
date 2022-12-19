<?php
namespace App\Validator\Validatortext;

use Symfony\Component\Validator\Constraint;
/**
* @Annotation
*/
class Telormail extends Constraint
{
    public $message = "Email ou t�l�phone %string% invalide";

    public function validatedBy()
    {
    return 'teloremail_user';
    }
}