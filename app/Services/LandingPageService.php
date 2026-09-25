<?php

namespace App\Services;

use App\Models\Post;
use App\Models\Setting;

class LandingPageService
{
    protected ?array $settings = null;

    /**
     * Helper to resolve localized text with Arabic/English support.
     */
    protected function getLocalizedValue(
        ?string $arValue,
        ?string $enValue,
        string $locale = 'ar',
        ?string $genericFallback = null
    ): ?string {
        $ar = filled($arValue) ? trim($arValue) : null;
        $en = filled($enValue) ? trim($enValue) : (filled($genericFallback) ? trim($genericFallback) : null);

        if ($locale === 'ar') {
            return $ar ?? $en;
        }

        return $en ?? $ar;
    }

    /**
     * Get settings key-value array for global brand info.
     */
    protected function getSettings(): array
    {
        if ($this->settings === null) {
            try {
                $this->settings = Setting::pluck('value', 'name')->toArray();
            } catch (\Throwable $e) {
                $this->settings = [];
            }
        }

        return $this->settings;
    }

    protected function formatImageUrl(?string $path): ?string
    {
        if (empty($path)) {
            return null;
        }

        if (filter_var($path, FILTER_VALIDATE_URL)) {
            return $path;
        }

        return asset($path);
    }

    // =========================================================================
    // HOME PAGE SECTIONS
    // =========================================================================
    public function getSectionData(string $type, string $lang = 'ar'): ?array
    {
        $lang = in_array(strtolower($lang), ['ar', 'en']) ? strtolower($lang) : 'ar';
        $type = strtolower(trim($type));

        return match ($type) {
            'hero', 'slider' => $this->getHeroData($lang),
            'about'          => $this->getAboutData($lang),
            'services'       => $this->getServicesData($lang),
            'why_us'         => $this->getWhyUsData($lang),
            'quote'          => $this->getQuoteData($lang),
            'footer'         => $this->getFooterData($lang),
            default          => null,
        };
    }

    protected function getHeroData(string $lang): array
    {
        $post = Post::where('type', 'hero')
            ->where(function ($q) {
                $q->where('status', 'active')->orWhereNull('status');
            })->first();

        return [
            'tagline'     => $this->getLocalizedValue($post?->subtitle_ar, $post?->subtitle_en, $lang, 'GROW STRONGER'),
            'title'       => $this->getLocalizedValue($post?->title_ar, $post?->title_en, $lang, 'Your Fitness Journey Starts Here'),
            'subtitle'    => $this->getLocalizedValue($post?->description_ar, $post?->description_en, $lang, ''),
            'button_text' => $this->getLocalizedValue($post?->btn_text_ar, $post?->btn_text_en, $lang, 'JOIN US'),
            'button_link' => $post?->link ?? '/join-us',
            'image'       => $this->formatImageUrl($post?->image),
        ];
    }

    protected function getAboutData(string $lang): array
    {
        $post = Post::with(['items' => function ($query) {
            $query->where(function ($q) {
                $q->where('status', 'active')->orWhereNull('status');
            })->orderBy('sort', 'asc');
        }])->where('type', 'about')
            ->where(function ($q) {
                $q->where('status', 'active')->orWhereNull('status');
            })->first();

        $features = ($post?->items ?? collect())->map(function ($item) use ($lang) {
            return [
                'id'          => $item->id,
                'title'       => $this->getLocalizedValue($item->title_ar, $item->title_en, $lang, $item->title_en),
                'description' => $this->getLocalizedValue($item->description_ar, $item->description_en, $lang, $item->description_en),
                'icon'        => $item->link ?: 'fa-solid fa-dumbbell',
                'sort'        => (int) $item->sort,
            ];
        })->values()->toArray();

        return [
            'tagline'  => $this->getLocalizedValue($post?->subtitle_ar, $post?->subtitle_en, $lang, 'About'),
            'title'    => $this->getLocalizedValue($post?->title_ar, $post?->title_en, $lang, 'Built For Everyone Powered By Passion'),
            'bio'      => $this->getLocalizedValue($post?->description_ar, $post?->description_en, $lang, ''),
            'image'    => $this->formatImageUrl($post?->image),
            'features' => $features,
        ];
    }

    protected function getServicesData(string $lang): array
    {
        $post = Post::with(['items' => function ($query) {
            $query->where(function ($q) {
                $q->where('status', 'active')->orWhereNull('status');
            })->orderBy('sort', 'asc');
        }])->where('type', 'service')
            ->where(function ($q) {
                $q->where('status', 'active')->orWhereNull('status');
            })->first();

        $items = ($post?->items ?? collect())->map(function ($item) use ($lang) {
            return [
                'id'          => $item->id,
                'title'       => $this->getLocalizedValue($item->title_ar, $item->title_en, $lang),
                'subtitle'    => $this->getLocalizedValue($item->subtitle_ar, $item->subtitle_en, $lang),
                'description' => $this->getLocalizedValue($item->description_ar, $item->description_en, $lang),
                'icon'        => $item->link ?: 'fa-solid fa-dumbbell',
                'image'       => $this->formatImageUrl($item->image),
                'sort'        => (int) $item->sort,
            ];
        })->values()->toArray();

        return [
            'tagline'  => $this->getLocalizedValue($post?->subtitle_ar, $post?->subtitle_en, $lang, 'Our Services'),
            'title'    => $this->getLocalizedValue($post?->title_ar, $post?->title_en, $lang, 'Your Path to the Top'),
            'subtitle' => $this->getLocalizedValue($post?->description_ar, $post?->description_en, $lang, ''),
            'items'    => $items,
        ];
    }

    protected function getWhyUsData(string $lang): array
    {
        $post = Post::with(['items' => function ($query) {
            $query->where(function ($q) {
                $q->where('status', 'active')->orWhereNull('status');
            })->orderBy('sort', 'asc');
        }])->where('type', 'why_us')
            ->where(function ($q) {
                $q->where('status', 'active')->orWhereNull('status');
            })->first();

        $items = ($post?->items ?? collect())->map(function ($item) use ($lang) {
            return [
                'id'       => $item->id,
                'number'   => sprintf('%02d', $item->sort),
                'title'    => $this->getLocalizedValue($item->title_ar, $item->title_en, $lang),
                'subtitle' => $this->getLocalizedValue($item->subtitle_ar, $item->subtitle_en, $lang),
                'icon'     => $item->link ?: 'fa-solid fa-trophy',
                'sort'     => (int) $item->sort,
            ];
        })->values()->toArray();

        return [
            'title' => $this->getLocalizedValue($post?->title_ar, $post?->title_en, $lang, "Here's Why Members Love Us"),
            'items' => $items,
        ];
    }

    protected function getQuoteData(string $lang): array
    {
        $post = Post::where('type', 'quote')
            ->where(function ($q) {
                $q->where('status', 'active')->orWhereNull('status');
            })->first();

        return [
            'title'       => $this->getLocalizedValue($post?->title_ar, $post?->title_en, $lang, 'Turning Your Potential into Performance'),
            'text'        => $this->getLocalizedValue($post?->description_ar, $post?->description_en, $lang, ''),
            'image'       => $this->formatImageUrl($post?->image),
            'button_text' => $this->getLocalizedValue($post?->btn_text_ar, $post?->btn_text_en, $lang, 'Contact Us'),
            'button_link' => $post?->link ?? '/contact',
        ];
    }

    // =========================================================================
    // TRANSFORMATIONS PAGE SECTIONS
    // =========================================================================
    public function getTransformationPageData(string $type, string $lang = 'ar'): ?array
    {
        $lang = in_array(strtolower($lang), ['ar', 'en']) ? strtolower($lang) : 'ar';
        $type = strtolower(trim($type));

        return match ($type) {
            'hero', 'header' => $this->getTransformationsHeroData($lang),
            'list', 'items'  => $this->getTransformationsListData($lang),
            'reviews'        => $this->getReviewsData($lang),
            default          => null,
        };
    }

    protected function getTransformationsHeroData(string $lang): array
    {
        $post = Post::where('type', 'transformation_hero')
            ->where(function ($q) {
                $q->where('status', 'active')->orWhereNull('status');
            })->first();

        return [
            'tagline'     => $this->getLocalizedValue($post?->subtitle_ar, $post?->subtitle_en, $lang, 'GROW STRONGER'),
            'title'       => $this->getLocalizedValue($post?->title_ar, $post?->title_en, $lang, 'The Journey of Transformation'),
            'subtitle'    => $this->getLocalizedValue($post?->description_ar, $post?->description_en, $lang, ''),
            'button_text' => $this->getLocalizedValue($post?->btn_text_ar, $post?->btn_text_en, $lang, 'JOIN US'),
            'button_link' => $post?->link ?? '/join-us',
            'image'       => $this->formatImageUrl($post?->image),
        ];
    }

    protected function getTransformationsListData(string $lang): array
    {
        $post = Post::with(['items' => function ($query) {
            $query->where(function ($q) {
                $q->where('status', 'active')->orWhereNull('status');
            })->orderBy('sort', 'asc');
        }])->where('type', 'transformation')
            ->where(function ($q) {
                $q->where('status', 'active')->orWhereNull('status');
            })->first();

        $items = ($post?->items ?? collect())->map(function ($item) use ($lang) {
            return [
                'id'           => $item->id,
                'trainee_name' => $this->getLocalizedValue($item->title_ar, $item->title_en, $lang),
                'duration'     => $this->getLocalizedValue($item->subtitle_ar, $item->subtitle_en, $lang),
                'before_image' => $this->formatImageUrl($item->extra_image),
                'after_image'  => $this->formatImageUrl($item->image),
                'sort'         => (int) $item->sort,
            ];
        })->values()->toArray();

        return [
            'section_title' => $this->getLocalizedValue($post?->title_ar, $post?->title_en, $lang, "RESULTS don't LIE"),
            'items'         => $items,
        ];
    }

    protected function getReviewsData(string $lang): array
    {
        $post = Post::with(['items' => function ($query) {
            $query->where(function ($q) {
                $q->where('status', 'active')->orWhereNull('status');
            })->orderBy('sort', 'asc');
        }])->where('type', 'review')
            ->where(function ($q) {
                $q->where('status', 'active')->orWhereNull('status');
            })->first();

        $items = ($post?->items ?? collect())->map(function ($item) use ($lang) {
            return [
                'id'      => $item->id,
                'name'    => $this->getLocalizedValue($item->title_ar, $item->title_en, $lang),
                'role'    => $this->getLocalizedValue($item->subtitle_ar, $item->subtitle_en, $lang, 'Trainee'),
                'review'  => $this->getLocalizedValue($item->description_ar, $item->description_en, $lang),
                'rating'  => (float) ($item->rating ?: 5.0),
                'avatar'  => $this->formatImageUrl($item->image),
                'sort'    => (int) $item->sort,
            ];
        })->values()->toArray();

        return [
            'section_title' => $this->getLocalizedValue($post?->title_ar, $post?->title_en, $lang, 'Our Customer Reviews'),
            'items'         => $items,
        ];
    }

    // =========================================================================
    // JOIN US PAGE SECTIONS
    // =========================================================================
    public function getJoinUsPageData(string $type, string $lang = 'ar'): ?array
    {
        $lang = in_array(strtolower($lang), ['ar', 'en']) ? strtolower($lang) : 'ar';
        $type = strtolower(trim($type));

        if ($type === 'hero' || $type === 'header') {
            $post = Post::where('type', 'join_hero')
                ->where(function ($q) {
                    $q->where('status', 'active')->orWhereNull('status');
                })->first();

            return [
                'tagline'     => $this->getLocalizedValue($post?->subtitle_ar, $post?->subtitle_en, $lang, 'GROW STRONGER'),
                'title'       => $this->getLocalizedValue($post?->title_ar, $post?->title_en, $lang, 'Your Fitness Journey Starts Here'),
                'subtitle'    => $this->getLocalizedValue($post?->description_ar, $post?->description_en, $lang, ''),
                'button_text' => $this->getLocalizedValue($post?->btn_text_ar, $post?->btn_text_en, $lang, 'CONTACT US'),
                'button_link' => $post?->link ?? '/contact',
                'image'       => $this->formatImageUrl($post?->image),
            ];
        }

        if ($type === 'form_info') {
            return [
                'form_title' => $lang === 'ar' ? 'جاهز للتغيير؟' : 'Ready to Transform',
            ];
        }

        return null;
    }

    // =========================================================================
    // CONTACT PAGE SECTIONS
    // =========================================================================
    public function getContactPageData(string $type, string $lang = 'ar'): ?array
    {
        $lang = in_array(strtolower($lang), ['ar', 'en']) ? strtolower($lang) : 'ar';
        $type = strtolower(trim($type));

        if ($type === 'hero' || $type === 'header') {
            $post = Post::where('type', 'contact_hero')
                ->where(function ($q) {
                    $q->where('status', 'active')->orWhereNull('status');
                })->first();

            return [
                'tagline'     => $this->getLocalizedValue($post?->subtitle_ar, $post?->subtitle_en, $lang, 'GROW STRONGER'),
                'title'       => $this->getLocalizedValue($post?->title_ar, $post?->title_en, $lang, 'Contact Us'),
                'subtitle'    => $this->getLocalizedValue($post?->description_ar, $post?->description_en, $lang, ''),
                'button_text' => $this->getLocalizedValue($post?->btn_text_ar, $post?->btn_text_en, $lang, 'Join Us'),
                'button_link' => $post?->link ?? '/join-us',
                'image'       => $this->formatImageUrl($post?->image),
            ];
        }

        if ($type === 'info') {
            $settings = $this->getSettings();
            return [
                'header_title' => $lang === 'ar' ? 'هل لديك أسئلة أو تحتاج إلى مساعدة؟' : 'Have questions or need help?',
                'get_in_touch' => [
                    'phone'   => $settings['contact_phone'] ?? ($settings['mobile'] ?? '01144470845'),
                    'message' => $settings['contact_whatsapp'] ?? ($settings['mobile'] ?? '01144470845'),
                ],
            ];
        }

        return null;
    }

    // =========================================================================
    // GLOBAL & FOOTER DATA
    // =========================================================================
    public function getGlobalData(string $lang = 'en'): array
    {
        $lang = in_array(strtolower($lang), ['ar', 'en']) ? strtolower($lang) : 'en';
        $settings = $this->getSettings();
        $link = 'https://www.devhubit.com';
        $whatsappNumber = $settings['mobile'] ?? ($settings['whatsapp'] ?? '');
        $cleanWhatsapp = preg_replace('/[^0-9]/', '', $whatsappNumber);
        $whatsappUrl = !empty($cleanWhatsapp) ? "https://wa.me/{$cleanWhatsapp}" : null;

        return [
            'site_title' => $settings['site_title'] ?? '',
            'site_logo' => $this->formatImageUrl($settings['logo'] ?? null),
            'contact' => [
                'email' => $settings['email'] ?? '' ,
                'phone' => $settings['mobile'] ?? '',
                'whatsapp' => $whatsappUrl,
            ],
            'social_links' => [
                'whatsapp' => $whatsappUrl,
                'instagram' => $settings['instagram'] ?? ($settings['instagram_url'] ?? null),
                'tiktok' => $settings['tiktok'] ?? ($settings['tiktok_url'] ?? null),
                'facebook' => $settings['facebook'] ?? ($settings['facebook_url'] ?? null),
            ],
            'copyright' => 'Copyright© ' . $link . ' . All Right Reserved',
        ];
    }

    protected function getFooterData(string $lang): array
    {
        return $this->getGlobalData($lang);
    }
}
