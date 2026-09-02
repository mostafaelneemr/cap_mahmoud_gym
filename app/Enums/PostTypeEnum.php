<?php

namespace App\Enums;

enum PostTypeEnum: string
{
    case Transformation = 'transformation';
    case PricingPlan = 'pricing_plan';
    case Faq = 'faq';
    case Testimonial = 'testimonial';
    case Service = 'service';
    case Attribute = 'attribute';
    case WhyUs = 'why_us';
    case Review = 'review';

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
