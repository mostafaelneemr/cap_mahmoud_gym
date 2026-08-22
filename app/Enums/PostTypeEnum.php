<?php

namespace App\Enums;

enum PostTypeEnum: string
{
    case Transformation = 'transformation';
    case PricingPlan = 'pricing_plan';
    case Faq = 'faq';
    case Testimonial = 'testimonial';

    public static function values(): array
    {
        return array_column(self::cases(), 'value', 'name');
    }

    public static function values_lang(): array
    {
        $data = [];
        foreach (self::cases() as $row) {
            $data[$row->value] = __($row->name);
        }
        return $data;
    }
}
