<?php
namespace App\Validator\Validatorfile;

use Symfony\Component\Validator\Constraint;
/**
* @Annotation
*/
class Yourfile extends Constraint
{
    public $message = "le fichier %string% est invalide";
    //l'option taillemax est la taille maximale de l'image en octect.
    public $taillemax = 20000;

    public function validatedBy()
    {
    return 'your_file';
    }
}