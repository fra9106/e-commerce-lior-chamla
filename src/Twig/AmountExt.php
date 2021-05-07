<?php 

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class AmountExt extends AbstractExtension
{
    public function getFilters()
    {
        return [
            new TwigFilter('amount', [ $this, 'amount'])
        ];
    }

    public function amount($value)
    {
        $finalResult = $value / 100;

        $finalResult = number_format($finalResult, 2, ',', ' ');
        
        return $finalResult . '€';
    }
}