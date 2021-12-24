<?php
//(c) Noel Kenfack   Novembre 2014 | Update 2020
namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class CustomTwigExtensions extends AbstractExtension
{
	public function getFilters()
    {
        return array(
            // add more if you want like this new \Twig_SimpleFilter('replaceNonAlphaNumerics', array($this, 'replaceNonAlphaNumerics')),
			new TwigFilter('numberFormat', array($this, 'numberFormat')),
        );
    }

    public function getFunctions()
    {
        return array(
            // add more like this if you want new \Twig_SimpleFunction('is_mobile', array($this, 'is_mobile')),
        );
    }


	public function numberFormat($number)
	{
		return number_format($number);
	}

	
    public function getName()
    {
        return 'TwigExtensions';
    }
}
