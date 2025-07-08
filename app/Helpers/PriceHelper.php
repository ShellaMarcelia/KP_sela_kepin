<?php

namespace app\Helpers;

class PriceHelper{
    public static function formatPrice($price)
    {
        return 'Rp ' . number_format($price, 0, ',', '.');
    }
}
