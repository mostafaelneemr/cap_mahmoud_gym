@extends('system.layout')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card card-flush bg-body shadow-sm">
            <!--begin::Card Header with Tabs-->
            <div class="card-header border-0 pt-6">
                <div class="card-title">
                    <h3 class="fw-bold text-gray-900 m-0">
                        <i class="fa-solid fa-layer-group text-primary fs-3 me-2"></i>
                        {{ __('Website Landing Page Engine') }}
                    </h3>
                </div>
            </div>

            <div class="card-body pt-0">
                <!--begin::Nav Tabs (6 Tabs)-->
                <ul class="nav nav-tabs nav-line-tabs nav-line-tabs-2x mb-8 fs-5 fw-semibold" id="websiteNavTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <a class="nav-link active text-active-primary d-flex align-items-center pb-4" data-bs-toggle="tab" href="#tab_hero" role="tab">
                            <i class="fa-solid fa-wand-magic-sparkles me-2"></i> {{ __('Hero & Page Banners') }}
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link text-active-primary d-flex align-items-center pb-4" data-bs-toggle="tab" href="#tab_about" role="tab">
                            <i class="fa-solid fa-user-tie me-2"></i> {{ __('About & Attributes') }}
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link text-active-primary d-flex align-items-center pb-4" data-bs-toggle="tab" href="#tab_services" role="tab">
                            <i class="fa-solid fa-dumbbell me-2"></i> {{ __('Our Services') }}
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link text-active-primary d-flex align-items-center pb-4" data-bs-toggle="tab" href="#tab_transformations" role="tab">
                            <i class="fa-solid fa-arrows-split-up-and-left me-2"></i> {{ __('Transformations') }}
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link text-active-primary d-flex align-items-center pb-4" data-bs-toggle="tab" href="#tab_why_us" role="tab">
                            <i class="fa-solid fa-trophy me-2"></i> {{ __('Why Us & Motivation') }}
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link text-active-primary d-flex align-items-center pb-4" data-bs-toggle="tab" href="#tab_reviews" role="tab">
                            <i class="fa-solid fa-star me-2"></i> {{ __('Reviews & Global Socials') }}
                        </a>
                    </li>
                </ul>
                <!--end::Nav Tabs-->

                <!--begin::Tab Content-->
                <div class="tab-content" id="landingTabContent">

                    <!-- ========================================================================= -->
                    <!-- TAB 1: HERO & PAGE BANNERS                                                -->
                    <!-- ========================================================================= -->
                    <div class="tab-pane fade show active" id="tab_hero" role="tabpanel">

                        <!-- Sub-Navigation for Banners -->
                        <ul class="nav nav-pills nav-pills-custom mb-7 gap-3" role="tablist">
                            <li class="nav-item" role="presentation">
                                <a class="nav-link active btn btn-outline btn-outline-dashed btn-outline-primary fw-bold px-4 py-3" data-bs-toggle="pill" href="#banner_main_hero">
                                    <i class="fa-solid fa-house-chimney me-2"></i> {{ __('Main Home Page') }}
                                </a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link btn btn-outline btn-outline-dashed btn-outline-primary fw-bold px-4 py-3" data-bs-toggle="pill" href="#banner_trans_hero">
                                    <i class="fa-solid fa-arrows-rotate me-2"></i> {{ __('Transformation Page') }}
                                </a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link btn btn-outline btn-outline-dashed btn-outline-primary fw-bold px-4 py-3" data-bs-toggle="pill" href="#banner_join_hero">
                                    <i class="fa-solid fa-user-plus me-2"></i> {{ __('Join Us Page') }}
                                </a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link btn btn-outline btn-outline-dashed btn-outline-primary fw-bold px-4 py-3" data-bs-toggle="pill" href="#banner_contact_hero">
                                    <i class="fa-solid fa-headset me-2"></i> {{ __('Contact Us Page') }}
                                </a>
                            </li>
                        </ul>

                        <div class="tab-content">
                            <!-- 1.1 Main Home Hero -->
                            <div class="tab-pane fade show active" id="banner_main_hero" role="tabpanel">
                                <div class="card border border-dashed border-gray-300 p-6 mb-6">
                                    <div class="card-header border-0 p-0 mb-5">
                                        <h4 class="fw-bold text-gray-900 m-0"><i class="fa-solid fa-house-chimney text-primary me-2"></i> {{ __('Main Home Landing Hero') }}</h4>
                                    </div>
                                    {!! Form::open([
                                        'id' => 'form-hero',
                                        'onsubmit' => 'FormSubmit("' . route('system.website.section.update', 'hero') . '", "form-hero"); return false;',
                                        'files' => true,
                                        'method' => 'POST'
                                    ]) !!}
                                    <div id="form-hero-alert"></div>
                                    <div class="row g-5">
                                        <div class="col-md-6">
                                            <label class="form-label required fw-bold">{{ __('Tagline / Subtext (English)') }}</label>
                                            {!! Form::text('subtitle_en', $hero->subtitle_en ?? '', ['class' => 'form-control form-control-solid', 'placeholder' => 'GROW STRONGER']) !!}
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label required fw-bold">{{ __('Tagline / Subtext (Arabic)') }}</label>
                                            {!! Form::text('subtitle_ar', $hero->subtitle_ar ?? '', ['class' => 'form-control form-control-solid', 'placeholder' => 'كن أقوى']) !!}
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label required fw-bold">{{ __('Main Title (English)') }}</label>
                                            {!! Form::text('title_en', $hero->title_en ?? '', ['class' => 'form-control form-control-solid', 'placeholder' => 'Your Fitness Journey Starts Here']) !!}
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label required fw-bold">{{ __('Main Title (Arabic)') }}</label>
                                            {!! Form::text('title_ar', $hero->title_ar ?? '', ['class' => 'form-control form-control-solid', 'placeholder' => 'رحلتك الرياضية تبدأ من هنا']) !!}
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label fw-bold">{{ __('Description (English)') }}</label>
                                            {!! Form::textarea('description_en', $hero->description_en ?? '', ['class' => 'form-control form-control-solid', 'rows' => 3]) !!}
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold">{{ __('Description (Arabic)') }}</label>
                                            {!! Form::textarea('description_ar', $hero->description_ar ?? '', ['class' => 'form-control form-control-solid', 'rows' => 3]) !!}
                                        </div>

                                        <div class="col-md-4">
                                            <label class="form-label fw-bold">{{ __('Button Text (English)') }}</label>
                                            {!! Form::text('btn_text_en', $hero->btn_text_en ?? '', ['class' => 'form-control form-control-solid', 'placeholder' => 'JOIN US']) !!}
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label fw-bold">{{ __('Button Text (Arabic)') }}</label>
                                            {!! Form::text('btn_text_ar', $hero->btn_text_ar ?? '', ['class' => 'form-control form-control-solid', 'placeholder' => 'انضم إلينا']) !!}
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label fw-bold">{{ __('Button Link') }}</label>
                                            {!! Form::text('link', $hero->link ?? '', ['class' => 'form-control form-control-solid', 'placeholder' => '/join-us']) !!}
                                        </div>

                                        <div class="col-md-12">
                                            <label class="form-label fw-bold">{{ __('Hero Background Banner Photo') }}</label>
                                            <div class="d-flex align-items-center gap-5">
                                                <div class="image-input image-input-outline" data-kt-image-input="true">
                                                    @php $heroImg = !empty($hero?->image) ? asset($hero->image) : asset('assets/media/svg/avatars/blank.svg'); @endphp
                                                    <div class="image-input-wrapper w-175px h-100px rounded shadow-sm border" id="hero_preview" style="background-image: url('{{ $heroImg }}'); background-size: cover; background-position: center;"></div>
                                                </div>
                                                <div class="flex-grow-1">
                                                    {!! Form::file('image', ['class' => 'form-control form-control-solid', 'accept' => '.png, .jpg, .jpeg, .webp', 'onchange' => 'previewImage(this, "#hero_preview")']) !!}
                                                    <div class="text-muted fs-7 mt-2">{{ __('Allowed formats: PNG, JPG, JPEG, WEBP. Recommended size: 1920x1080px.') }}</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-end mt-6">
                                        <button type="submit" class="btn btn-primary submit">
                                            <span class="indicator-label"><i class="fa-solid fa-check-circle me-1"></i> {{ __('Save') }}</span>
                                            <span class="indicator-progress">{{ __('Please wait') }}... <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                                        </button>
                                    </div>
                                    {!! Form::close() !!}
                                </div>
                            </div>

                            <!-- 1.2 Transformation Page Hero -->
                            <div class="tab-pane fade" id="banner_trans_hero" role="tabpanel">
                                <div class="card border border-dashed border-gray-300 p-6 mb-6">
                                    <div class="card-header border-0 p-0 mb-5">
                                        <h4 class="fw-bold text-gray-900 m-0"><i class="fa-solid fa-arrows-rotate text-primary me-2"></i> {{ __('Transformations Page Hero Banner') }}</h4>
                                    </div>
                                    {!! Form::open([
                                        'id' => 'form-trans-hero',
                                        'onsubmit' => 'FormSubmit("' . route('system.website.section.update', 'transformation_hero') . '", "form-trans-hero"); return false;',
                                        'files' => true,
                                        'method' => 'POST'
                                    ]) !!}
                                    <div id="form-trans-hero-alert"></div>
                                    <div class="row g-5">
                                        <div class="col-md-6">
                                            <label class="form-label required fw-bold">{{ __('Tagline / Subtext (English)') }}</label>
                                            {!! Form::text('subtitle_en', $transformationHero->subtitle_en ?? '', ['class' => 'form-control form-control-solid', 'placeholder' => 'GROW STRONGER']) !!}
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label required fw-bold">{{ __('Tagline / Subtext (Arabic)') }}</label>
                                            {!! Form::text('subtitle_ar', $transformationHero->subtitle_ar ?? '', ['class' => 'form-control form-control-solid', 'placeholder' => 'كن أقوى']) !!}
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label required fw-bold">{{ __('Main Title (English)') }}</label>
                                            {!! Form::text('title_en', $transformationHero->title_en ?? '', ['class' => 'form-control form-control-solid', 'placeholder' => 'The Journey of Transformation']) !!}
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label required fw-bold">{{ __('Main Title (Arabic)') }}</label>
                                            {!! Form::text('title_ar', $transformationHero->title_ar ?? '', ['class' => 'form-control form-control-solid', 'placeholder' => 'رحلة التحول والنتائج']) !!}
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label fw-bold">{{ __('Description (English)') }}</label>
                                            {!! Form::textarea('description_en', $transformationHero->description_en ?? '', ['class' => 'form-control form-control-solid', 'rows' => 3]) !!}
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold">{{ __('Description (Arabic)') }}</label>
                                            {!! Form::textarea('description_ar', $transformationHero->description_ar ?? '', ['class' => 'form-control form-control-solid', 'rows' => 3]) !!}
                                        </div>

                                        <div class="col-md-4">
                                            <label class="form-label fw-bold">{{ __('Button Text (English)') }}</label>
                                            {!! Form::text('btn_text_en', $transformationHero->btn_text_en ?? '', ['class' => 'form-control form-control-solid', 'placeholder' => 'JOIN US']) !!}
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label fw-bold">{{ __('Button Text (Arabic)') }}</label>
                                            {!! Form::text('btn_text_ar', $transformationHero->btn_text_ar ?? '', ['class' => 'form-control form-control-solid', 'placeholder' => 'انضم إلينا']) !!}
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label fw-bold">{{ __('Button Link') }}</label>
                                            {!! Form::text('link', $transformationHero->link ?? '', ['class' => 'form-control form-control-solid', 'placeholder' => '/join-us']) !!}
                                        </div>

                                        <div class="col-md-12">
                                            <label class="form-label fw-bold">{{ __('Hero Banner Photo') }}</label>
                                            <div class="d-flex align-items-center gap-5">
                                                <div class="image-input image-input-outline" data-kt-image-input="true">
                                                    @php $transHeroImg = !empty($transformationHero?->image) ? asset($transformationHero->image) : asset('assets/media/svg/avatars/blank.svg'); @endphp
                                                    <div class="image-input-wrapper w-175px h-100px rounded shadow-sm border" id="trans_hero_preview" style="background-image: url('{{ $transHeroImg }}'); background-size: cover; background-position: center;"></div>
                                                </div>
                                                <div class="flex-grow-1">
                                                    {!! Form::file('image', ['class' => 'form-control form-control-solid', 'accept' => '.png, .jpg, .jpeg, .webp', 'onchange' => 'previewImage(this, "#trans_hero_preview")']) !!}
                                                    <div class="text-muted fs-7 mt-2">{{ __('Allowed formats: PNG, JPG, JPEG, WEBP.') }}</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-end mt-6">
                                        <button type="submit" class="btn btn-primary submit">
                                            <span class="indicator-label"><i class="fa-solid fa-check-circle me-1"></i> {{ __('Save') }}</span>
                                            <span class="indicator-progress">{{ __('Please wait') }}... <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                                        </button>
                                    </div>
                                    {!! Form::close() !!}
                                </div>
                            </div>

                            <!-- 1.3 Join Us Page Hero -->
                            <div class="tab-pane fade" id="banner_join_hero" role="tabpanel">
                                <div class="card border border-dashed border-gray-300 p-6 mb-6">
                                    <div class="card-header border-0 p-0 mb-5">
                                        <h4 class="fw-bold text-gray-900 m-0"><i class="fa-solid fa-user-plus text-primary me-2"></i> {{ __('Join Us Page Hero Banner') }}</h4>
                                    </div>
                                    {!! Form::open([
                                        'id' => 'form-join-hero',
                                        'onsubmit' => 'FormSubmit("' . route('system.website.section.update', 'join_hero') . '", "form-join-hero"); return false;',
                                        'files' => true,
                                        'method' => 'POST'
                                    ]) !!}
                                    <div id="form-join-hero-alert"></div>
                                    <div class="row g-5">
                                        <div class="col-md-6">
                                            <label class="form-label required fw-bold">{{ __('Tagline / Subtext (English)') }}</label>
                                            {!! Form::text('subtitle_en', $joinHero->subtitle_en ?? '', ['class' => 'form-control form-control-solid', 'placeholder' => 'GROW STRONGER']) !!}
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label required fw-bold">{{ __('Tagline / Subtext (Arabic)') }}</label>
                                            {!! Form::text('subtitle_ar', $joinHero->subtitle_ar ?? '', ['class' => 'form-control form-control-solid', 'placeholder' => 'كن أقوى']) !!}
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label required fw-bold">{{ __('Main Title (English)') }}</label>
                                            {!! Form::text('title_en', $joinHero->title_en ?? '', ['class' => 'form-control form-control-solid', 'placeholder' => 'Your Fitness Journey Starts Here']) !!}
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label required fw-bold">{{ __('Main Title (Arabic)') }}</label>
                                            {!! Form::text('title_ar', $joinHero->title_ar ?? '', ['class' => 'form-control form-control-solid', 'placeholder' => 'ابدأ خطتك التدريبية اليوم']) !!}
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label fw-bold">{{ __('Description (English)') }}</label>
                                            {!! Form::textarea('description_en', $joinHero->description_en ?? '', ['class' => 'form-control form-control-solid', 'rows' => 3]) !!}
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold">{{ __('Description (Arabic)') }}</label>
                                            {!! Form::textarea('description_ar', $joinHero->description_ar ?? '', ['class' => 'form-control form-control-solid', 'rows' => 3]) !!}
                                        </div>

                                        <div class="col-md-4">
                                            <label class="form-label fw-bold">{{ __('Button Text (English)') }}</label>
                                            {!! Form::text('btn_text_en', $joinHero->btn_text_en ?? '', ['class' => 'form-control form-control-solid', 'placeholder' => 'CONTACT US']) !!}
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label fw-bold">{{ __('Button Text (Arabic)') }}</label>
                                            {!! Form::text('btn_text_ar', $joinHero->btn_text_ar ?? '', ['class' => 'form-control form-control-solid', 'placeholder' => 'تواصل معنا']) !!}
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label fw-bold">{{ __('Button Link') }}</label>
                                            {!! Form::text('link', $joinHero->link ?? '', ['class' => 'form-control form-control-solid', 'placeholder' => '/contact']) !!}
                                        </div>

                                        <div class="col-md-12">
                                            <label class="form-label fw-bold">{{ __('Hero Banner Photo') }}</label>
                                            <div class="d-flex align-items-center gap-5">
                                                <div class="image-input image-input-outline" data-kt-image-input="true">
                                                    @php $joinHeroImg = !empty($joinHero?->image) ? asset($joinHero->image) : asset('assets/media/svg/avatars/blank.svg'); @endphp
                                                    <div class="image-input-wrapper w-175px h-100px rounded shadow-sm border" id="join_hero_preview" style="background-image: url('{{ $joinHeroImg }}'); background-size: cover; background-position: center;"></div>
                                                </div>
                                                <div class="flex-grow-1">
                                                    {!! Form::file('image', ['class' => 'form-control form-control-solid', 'accept' => '.png, .jpg, .jpeg, .webp', 'onchange' => 'previewImage(this, "#join_hero_preview")']) !!}
                                                    <div class="text-muted fs-7 mt-2">{{ __('Allowed formats: PNG, JPG, JPEG, WEBP.') }}</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-end mt-6">
                                        <button type="submit" class="btn btn-primary submit">
                                            <span class="indicator-label"><i class="fa-solid fa-check-circle me-1"></i> {{ __('Save') }}</span>
                                            <span class="indicator-progress">{{ __('Please wait') }}... <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                                        </button>
                                    </div>
                                    {!! Form::close() !!}
                                </div>
                            </div>

                            <!-- 1.4 Contact Us Page Hero -->
                            <div class="tab-pane fade" id="banner_contact_hero" role="tabpanel">
                                <div class="card border border-dashed border-gray-300 p-6 mb-6">
                                    <div class="card-header border-0 p-0 mb-5">
                                        <h4 class="fw-bold text-gray-900 m-0"><i class="fa-solid fa-headset text-primary me-2"></i> {{ __('Contact Us Page Hero Banner') }}</h4>
                                    </div>
                                    {!! Form::open([
                                        'id' => 'form-contact-hero',
                                        'onsubmit' => 'FormSubmit("' . route('system.website.section.update', 'contact_hero') . '", "form-contact-hero"); return false;',
                                        'files' => true,
                                        'method' => 'POST'
                                    ]) !!}
                                    <div id="form-contact-hero-alert"></div>
                                    <div class="row g-5">
                                        <div class="col-md-6">
                                            <label class="form-label required fw-bold">{{ __('Tagline / Subtext (English)') }}</label>
                                            {!! Form::text('subtitle_en', $contactHero->subtitle_en ?? '', ['class' => 'form-control form-control-solid', 'placeholder' => 'GROW STRONGER']) !!}
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label required fw-bold">{{ __('Tagline / Subtext (Arabic)') }}</label>
                                            {!! Form::text('subtitle_ar', $contactHero->subtitle_ar ?? '', ['class' => 'form-control form-control-solid', 'placeholder' => 'كن أقوى']) !!}
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label required fw-bold">{{ __('Main Title (English)') }}</label>
                                            {!! Form::text('title_en', $contactHero->title_en ?? '', ['class' => 'form-control form-control-solid', 'placeholder' => 'Contact Us']) !!}
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label required fw-bold">{{ __('Main Title (Arabic)') }}</label>
                                            {!! Form::text('title_ar', $contactHero->title_ar ?? '', ['class' => 'form-control form-control-solid', 'placeholder' => 'تواصل معنا']) !!}
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label fw-bold">{{ __('Description (English)') }}</label>
                                            {!! Form::textarea('description_en', $contactHero->description_en ?? '', ['class' => 'form-control form-control-solid', 'rows' => 3]) !!}
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold">{{ __('Description (Arabic)') }}</label>
                                            {!! Form::textarea('description_ar', $contactHero->description_ar ?? '', ['class' => 'form-control form-control-solid', 'rows' => 3]) !!}
                                        </div>

                                        <div class="col-md-4">
                                            <label class="form-label fw-bold">{{ __('Button Text (English)') }}</label>
                                            {!! Form::text('btn_text_en', $contactHero->btn_text_en ?? '', ['class' => 'form-control form-control-solid', 'placeholder' => 'Join Us']) !!}
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label fw-bold">{{ __('Button Text (Arabic)') }}</label>
                                            {!! Form::text('btn_text_ar', $contactHero->btn_text_ar ?? '', ['class' => 'form-control form-control-solid', 'placeholder' => 'انضم إلينا']) !!}
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label fw-bold">{{ __('Button Link') }}</label>
                                            {!! Form::text('link', $contactHero->link ?? '', ['class' => 'form-control form-control-solid', 'placeholder' => '/join-us']) !!}
                                        </div>

                                        <div class="col-md-12">
                                            <label class="form-label fw-bold">{{ __('Hero Banner Photo') }}</label>
                                            <div class="d-flex align-items-center gap-5">
                                                <div class="image-input image-input-outline" data-kt-image-input="true">
                                                    @php $contactHeroImg = !empty($contactHero?->image) ? asset($contactHero->image) : asset('assets/media/svg/avatars/blank.svg'); @endphp
                                                    <div class="image-input-wrapper w-175px h-100px rounded shadow-sm border" id="contact_hero_preview" style="background-image: url('{{ $contactHeroImg }}'); background-size: cover; background-position: center;"></div>
                                                </div>
                                                <div class="flex-grow-1">
                                                    {!! Form::file('image', ['class' => 'form-control form-control-solid', 'accept' => '.png, .jpg, .jpeg, .webp', 'onchange' => 'previewImage(this, "#contact_hero_preview")']) !!}
                                                    <div class="text-muted fs-7 mt-2">{{ __('Allowed formats: PNG, JPG, JPEG, WEBP.') }}</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-end mt-6">
                                        <button type="submit" class="btn btn-primary submit">
                                            <span class="indicator-label"><i class="fa-solid fa-check-circle me-1"></i> {{ __('Save') }}</span>
                                            <span class="indicator-progress">{{ __('Please wait') }}... <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                                        </button>
                                    </div>
                                    {!! Form::close() !!}
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- ========================================================================= -->
                    <!-- TAB 2: ABOUT & 4 KEY ATTRIBUTES                                           -->
                    <!-- ========================================================================= -->
                    <div class="tab-pane fade" id="tab_about" role="tabpanel">
                        <div class="card border border-dashed border-gray-300 p-6 mb-8">
                            <div class="card-header border-0 p-0 mb-5">
                                <h4 class="fw-bold text-gray-900 m-0"><i class="fa-solid fa-user-tie text-primary me-2"></i> {{ __('About Coach Section') }}</h4>
                            </div>

                            {!! Form::open([
                                'id' => 'form-about',
                                'onsubmit' => 'FormSubmit("' . route('system.website.section.update', 'about') . '", "form-about"); return false;',
                                'files' => true,
                                'method' => 'POST'
                            ]) !!}
                            <div id="form-about-alert"></div>

                            <div class="row g-5">
                                <div class="col-md-6">
                                    <label class="form-label required fw-bold">{{ __('About Tagline (English)') }}</label>
                                    {!! Form::text('subtitle_en', $about->subtitle_en ?? '', ['class' => 'form-control form-control-solid', 'placeholder' => 'About']) !!}
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label required fw-bold">{{ __('About Tagline (Arabic)') }}</label>
                                    {!! Form::text('subtitle_ar', $about->subtitle_ar ?? '', ['class' => 'form-control form-control-solid', 'placeholder' => 'عن الكابتن']) !!}
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label required fw-bold">{{ __('Main Title (English)') }}</label>
                                    {!! Form::text('title_en', $about->title_en ?? '', ['class' => 'form-control form-control-solid', 'placeholder' => 'Built For Everyone Powered By Passion']) !!}
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label required fw-bold">{{ __('Main Title (Arabic)') }}</label>
                                    {!! Form::text('title_ar', $about->title_ar ?? '', ['class' => 'form-control form-control-solid', 'placeholder' => 'صُممت للجميع ومبنية على الشغف والاحتراف']) !!}
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label required fw-bold">{{ __('Bio / Description (English)') }}</label>
                                    {!! Form::textarea('description_en', $about->description_en ?? '', ['class' => 'form-control form-control-solid', 'rows' => 4, 'placeholder' => 'Dedicated to transforming lives through personalized fitness coaching...']) !!}
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label required fw-bold">{{ __('Bio / Description (Arabic)') }}</label>
                                    {!! Form::textarea('description_ar', $about->description_ar ?? '', ['class' => 'form-control form-control-solid', 'rows' => 4, 'placeholder' => 'نبذة عن الكابتن وفلسفة التدريب والتغذية...']) !!}
                                </div>

                                <div class="col-md-12">
                                    <label class="form-label fw-bold">{{ __('Coach Photo') }}</label>
                                    <div class="d-flex align-items-center gap-5">
                                        <div class="image-input image-input-outline" data-kt-image-input="true">
                                            @php $aboutImg = !empty($about?->image) ? asset($about->image) : asset('assets/media/svg/avatars/blank.svg'); @endphp
                                            <div class="image-input-wrapper w-125px h-125px rounded shadow-sm border" id="about_preview" style="background-image: url('{{ $aboutImg }}'); background-size: cover; background-position: center;"></div>
                                        </div>
                                        <div class="flex-grow-1">
                                            {!! Form::file('image', ['class' => 'form-control form-control-solid', 'accept' => '.png, .jpg, .jpeg, .webp', 'onchange' => 'previewImage(this, "#about_preview")']) !!}
                                            <div class="text-muted fs-7 mt-2">{{ __('Allowed formats: PNG, JPG, JPEG, WEBP. Max size: 5MB.') }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end mt-6">
                                <button type="submit" class="btn btn-primary submit">
                                    <span class="indicator-label"><i class="fa-solid fa-check-circle me-1"></i> {{ __('Save') }}</span>
                                    <span class="indicator-progress">{{ __('Please wait') }}... <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                                </button>
                            </div>
                            {!! Form::close() !!}
                        </div>

                        <!-- Dynamic Key Attributes Badges -->
                        <div class="d-flex justify-content-between align-items-center mb-6">
                            <div>
                                <h4 class="fw-bold text-gray-900 mb-1"><i class="fa-solid fa-list-check text-primary me-2"></i> {{ __('Key Attributes & Badges (posts)') }}</h4>
                                <div class="text-muted fs-7">{{ __('Key selling points displayed in About section (e.g., Custom Plans, Data Driven, Elite Coaching)') }}</div>
                            </div>
                            <button type="button" class="btn btn-primary" onclick="openPostModal('about', {{ $about?->id ?? 'null' }})">
                                <i class="fa-solid fa-plus me-1"></i> {{ __('Add Attribute Badge') }}
                            </button>
                        </div>

                        <div class="table-responsive">
                            <table class="table align-middle table-row-dashed fs-6 gy-4">
                                <thead>
                                    <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                                        <th class="w-80px">{{ __('Icon') }}</th>
                                        <th>{{ __('Title (EN / AR)') }}</th>
                                        <th>{{ __('Description (EN / AR)') }}</th>
                                        <th class="w-80px">{{ __('Sort') }}</th>
                                        <th class="w-100px">{{ __('Status') }}</th>
                                        <th class="text-end w-125px">{{ __('Actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="text-gray-700 fw-semibold">
                                @if(!empty($about?->items))
                                    @forelse($about->items as $item)
                                        <tr>
                                            <td>
                                                <div class="symbol symbol-40px symbol-light-primary">
                                                    <span class="symbol-label"><i class="{{ $item->link ?: 'fa-solid fa-dumbbell' }} fs-4 text-primary"></i></span>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="text-gray-900 fw-bold">{{ $item->title_en }}</div>
                                                <div class="text-muted fs-7">{{ $item->title_ar }}</div>
                                            </td>
                                            <td>
                                                <div class="fs-7 text-gray-800">{{ Str::limit($item->description_en, 50) }}</div>
                                                <div class="fs-8 text-muted">{{ Str::limit($item->description_ar, 50) }}</div>
                                            </td>
                                            <td>{{ $item->sort }}</td>
                                            <td>
                                                <span class="badge {{ $item->status === 'active' ? 'badge-light-success' : 'badge-light-danger' }}">
                                                    {{ ucfirst($item->status) }}
                                                </span>
                                            </td>
                                            <td class="text-end">
                                                <button type="button" class="btn btn-icon btn-light-primary btn-sm me-2" onclick="editPostItem({{ json_encode($item) }}, 'about')">
                                                    <i class="fa-solid fa-pen-to-square"></i>
                                                </button>
                                                <button type="button" class="btn btn-icon btn-light-danger btn-sm" onclick="deletePostItem({{ $item->id }})">
                                                    <i class="fa-solid fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center text-muted py-8">{{ __('No key attribute badges found.') }}</td>
                                        </tr>
                                    @endforelse
                                @else
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-8">{{ __('No key attribute badges found.') }}</td>
                                    </tr>
                                @endif
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- ========================================================================= -->
                    <!-- TAB 3: OUR SERVICES                                                       -->
                    <!-- ========================================================================= -->
                    <div class="tab-pane fade" id="tab_services" role="tabpanel">
                        <div class="card border border-dashed border-gray-300 p-6 mb-8">
                            <div class="card-header border-0 p-0 mb-5">
                                <h4 class="fw-bold text-gray-900 m-0"><i class="fa-solid fa-dumbbell text-primary me-2"></i> {{ __('Services Section Header') }}</h4>
                            </div>

                            {!! Form::open([
                                'id' => 'form-service-header',
                                'onsubmit' => 'FormSubmit("' . route('system.website.section.update', 'service') . '", "form-service-header"); return false;',
                                'files' => true,
                                'method' => 'POST'
                            ]) !!}
                            <div id="form-service-header-alert"></div>

                            <div class="row g-5">
                                <div class="col-md-6">
                                    <label class="form-label required fw-bold">{{ __('Tagline / Subtitle (English)') }}</label>
                                    {!! Form::text('subtitle_en', $service->subtitle_en ?? '', ['class' => 'form-control form-control-solid', 'placeholder' => 'Our Services']) !!}
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label required fw-bold">{{ __('Tagline / Subtitle (Arabic)') }}</label>
                                    {!! Form::text('subtitle_ar', $service->subtitle_ar ?? '', ['class' => 'form-control form-control-solid', 'placeholder' => 'خدماتنا']) !!}
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label required fw-bold">{{ __('Section Title (English)') }}</label>
                                    {!! Form::text('title_en', $service->title_en ?? '', ['class' => 'form-control form-control-solid', 'placeholder' => 'Your Path to the Top']) !!}
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label required fw-bold">{{ __('Section Title (Arabic)') }}</label>
                                    {!! Form::text('title_ar', $service->title_ar ?? '', ['class' => 'form-control form-control-solid', 'placeholder' => 'طريقك نحو القمة والتميز']) !!}
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold">{{ __('Description (English)') }}</label>
                                    {!! Form::textarea('description_en', $service->description_en ?? '', ['class' => 'form-control form-control-solid', 'rows' => 3]) !!}
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">{{ __('Description (Arabic)') }}</label>
                                    {!! Form::textarea('description_ar', $service->description_ar ?? '', ['class' => 'form-control form-control-solid', 'rows' => 3]) !!}
                                </div>
                            </div>

                            <div class="d-flex justify-content-end mt-6">
                                <button type="submit" class="btn btn-primary submit">
                                    <span class="indicator-label"><i class="fa-solid fa-check-circle me-1"></i> {{ __('Save') }}</span>
                                    <span class="indicator-progress">{{ __('Please wait') }}... <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                                </button>
                            </div>
                            {!! Form::close() !!}
                        </div>

                        <!-- Services Post Items Table -->
                        <div class="d-flex justify-content-between align-items-center mb-6">
                            <div>
                                <h4 class="fw-bold text-gray-900 mb-1"><i class="fa-solid fa-dumbbell text-primary me-2"></i> {{ __('Services Offered (posts)') }}</h4>
                                <div class="text-muted fs-7">{{ __('Training, nutrition, and tracking service cards offered to trainees') }}</div>
                            </div>
                            <button type="button" class="btn btn-primary" onclick="openPostModal('service', {{ $service?->id ?? 'null' }})">
                                <i class="fa-solid fa-plus me-1"></i> {{ __('Add Service Card') }}
                            </button>
                        </div>

                        <div class="table-responsive">
                            <table class="table align-middle table-row-dashed fs-6 gy-4">
                                <thead>
                                    <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                                        <th class="w-60px">#</th>
                                        <th class="w-80px">{{ __('Icon / Image') }}</th>
                                        <th>{{ __('Service Title') }}</th>
                                        <th>{{ __('Subtitle') }}</th>
                                        <th>{{ __('Description') }}</th>
                                        <th class="w-80px">{{ __('Sort') }}</th>
                                        <th class="w-100px">{{ __('Status') }}</th>
                                        <th class="text-end w-125px">{{ __('Actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="text-gray-700 fw-semibold">
                                @if(!empty($service?->items))
                                    @forelse($service->items as $item)
                                        <tr>
                                            <td>{{ $item->id }}</td>
                                            <td>
                                                @if(!empty($item->image))
                                                    <img src="{{ asset($item->image) }}" class="w-45px h-45px rounded object-fit-cover border" alt="Service">
                                                @else
                                                    <div class="symbol symbol-40px symbol-light-primary">
                                                        <span class="symbol-label"><i class="{{ $item->link ?: 'fa-solid fa-dumbbell' }} fs-4 text-primary"></i></span>
                                                    </div>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="text-gray-900 fw-bold">{{ $item->title_en ?: $item->title }}</div>
                                                <div class="text-muted fs-7">{{ $item->title_ar }}</div>
                                            </td>
                                            <td>
                                                <div>{{ $item->subtitle_en ?: $item->subtitle ?: '-' }}</div>
                                                <div class="text-muted fs-7">{{ $item->subtitle_ar }}</div>
                                            </td>
                                            <td>
                                                <div class="fs-7 text-muted">{{ Str::limit($item->description_en ?: $item->description, 50) }}</div>
                                            </td>
                                            <td>{{ $item->sort }}</td>
                                            <td>
                                                <span class="badge {{ $item->status === 'active' ? 'badge-light-success' : 'badge-light-danger' }}">
                                                    {{ ucfirst($item->status) }}
                                                </span>
                                            </td>
                                            <td class="text-end">
                                                <button type="button" class="btn btn-icon btn-light-primary btn-sm me-2" onclick="editPostItem({{ json_encode($item) }}, 'service')">
                                                    <i class="fa-solid fa-pen-to-square"></i>
                                                </button>
                                                <button type="button" class="btn btn-icon btn-light-danger btn-sm" onclick="deletePostItem({{ $item->id }})">
                                                    <i class="fa-solid fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center text-muted py-8">{{ __('No service cards found.') }}</td>
                                        </tr>
                                    @endforelse
                                @else
                                    <tr>
                                        <td colspan="8" class="text-center text-muted py-8">{{ __('No service cards found.') }}</td>
                                    </tr>
                                @endif
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- ========================================================================= -->
                    <!-- TAB 4: TRANSFORMATIONS                                                    -->
                    <!-- ========================================================================= -->
                    <div class="tab-pane fade" id="tab_transformations" role="tabpanel">
                        <div class="card border border-dashed border-gray-300 p-6 mb-8">
                            <div class="card-header border-0 p-0 mb-5">
                                <h4 class="fw-bold text-gray-900 m-0"><i class="fa-solid fa-arrows-split-up-and-left text-primary me-2"></i> {{ __('Transformations Section Header') }}</h4>
                            </div>

                            {!! Form::open([
                                'id' => 'form-transformation-header',
                                'onsubmit' => 'FormSubmit("' . route('system.website.section.update', 'transformation') . '", "form-transformation-header"); return false;',
                                'files' => true,
                                'method' => 'POST'
                            ]) !!}
                            <div id="form-transformation-header-alert"></div>

                            <div class="row g-5">
                                <div class="col-md-6">
                                    <label class="form-label required fw-bold">{{ __('Section Title (English)') }}</label>
                                    {!! Form::text('title_en', $transformation->title_en ?? '', ['class' => 'form-control form-control-solid', 'placeholder' => "RESULTS don't LIE"]) !!}
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label required fw-bold">{{ __('Section Title (Arabic)') }}</label>
                                    {!! Form::text('title_ar', $transformation->title_ar ?? '', ['class' => 'form-control form-control-solid', 'placeholder' => 'النتائج لا تكذب']) !!}
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">{{ __('Tagline / Subtitle (English)') }}</label>
                                    {!! Form::text('subtitle_en', $transformation->subtitle_en ?? '', ['class' => 'form-control form-control-solid', 'placeholder' => 'Transformations']) !!}
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">{{ __('Tagline / Subtitle (Arabic)') }}</label>
                                    {!! Form::text('subtitle_ar', $transformation->subtitle_ar ?? '', ['class' => 'form-control form-control-solid', 'placeholder' => 'قصص نجاح']) !!}
                                </div>
                            </div>

                            <div class="d-flex justify-content-end mt-6">
                                <button type="submit" class="btn btn-primary submit">
                                    <span class="indicator-label"><i class="fa-solid fa-check-circle me-1"></i> {{ __('Save') }}</span>
                                    <span class="indicator-progress">{{ __('Please wait') }}... <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                                </button>
                            </div>
                            {!! Form::close() !!}
                        </div>

                        <!-- Transformations Post Items Table -->
                        <div class="d-flex justify-content-between align-items-center mb-6">
                            <div>
                                <h4 class="fw-bold text-gray-900 mb-1"><i class="fa-solid fa-images text-primary me-2"></i> {{ __('Client Transformation Stories (posts)') }}</h4>
                                <div class="text-muted fs-7">{{ __('Before and After photos showcasing trainee fitness transformations') }}</div>
                            </div>
                            <button type="button" class="btn btn-primary" onclick="openPostModal('transformation', {{ $transformation?->id ?? 'null' }})">
                                <i class="fa-solid fa-plus me-1"></i> {{ __('Add Transformation Card') }}
                            </button>
                        </div>

                        <div class="table-responsive">
                            <table class="table align-middle table-row-dashed fs-6 gy-4">
                                <thead>
                                    <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                                        <th class="w-60px">#</th>
                                        <th class="w-90px">{{ __('Before Photo') }}</th>
                                        <th class="w-90px">{{ __('After Photo') }}</th>
                                        <th>{{ __('Trainee Name (EN / AR)') }}</th>
                                        <th>{{ __('Duration (EN / AR)') }}</th>
                                        <th class="w-80px">{{ __('Sort') }}</th>
                                        <th class="w-100px">{{ __('Status') }}</th>
                                        <th class="text-end w-125px">{{ __('Actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="text-gray-700 fw-semibold">
                                @if(!empty($transformation?->items))
                                    @forelse($transformation->items as $item)
                                        <tr>
                                            <td>{{ $item->id }}</td>
                                            <td>
                                                @if(!empty($item->extra_image))
                                                    <img src="{{ asset($item->extra_image) }}" class="w-55px h-55px rounded object-fit-cover border shadow-sm" alt="Before">
                                                @else
                                                    <span class="badge badge-light">{{ __('No Image') }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if(!empty($item->image))
                                                    <img src="{{ asset($item->image) }}" class="w-55px h-55px rounded object-fit-cover border shadow-sm" alt="After">
                                                @else
                                                    <span class="badge badge-light">{{ __('No Image') }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="text-gray-900 fw-bold">{{ $item->title_en ?: $item->title }}</div>
                                                <div class="text-muted fs-7">{{ $item->title_ar }}</div>
                                            </td>
                                            <td>
                                                <div>{{ $item->subtitle_en ?: $item->subtitle ?: '-' }}</div>
                                                <div class="text-muted fs-7">{{ $item->subtitle_ar }}</div>
                                            </td>
                                            <td>{{ $item->sort }}</td>
                                            <td>
                                                <span class="badge {{ $item->status === 'active' ? 'badge-light-success' : 'badge-light-danger' }}">
                                                    {{ ucfirst($item->status) }}
                                                </span>
                                            </td>
                                            <td class="text-end">
                                                <button type="button" class="btn btn-icon btn-light-primary btn-sm me-2" onclick="editPostItem({{ json_encode($item) }}, 'transformation')">
                                                    <i class="fa-solid fa-pen-to-square"></i>
                                                </button>
                                                <button type="button" class="btn btn-icon btn-light-danger btn-sm" onclick="deletePostItem({{ $item->id }})">
                                                    <i class="fa-solid fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center text-muted py-8">{{ __('No transformation cards found.') }}</td>
                                        </tr>
                                    @endforelse
                                @else
                                    <tr>
                                        <td colspan="8" class="text-center text-muted py-8">{{ __('No transformation cards found.') }}</td>
                                    </tr>
                                @endif
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- ========================================================================= -->
                    <!-- TAB 5: WHY US & MOTIVATION                                                -->
                    <!-- ========================================================================= -->
                    <div class="tab-pane fade" id="tab_why_us" role="tabpanel">

                        <!-- Why Us Header -->
                        <div class="card border border-dashed border-gray-300 p-6 mb-8">
                            <div class="card-header border-0 p-0 mb-5">
                                <h4 class="fw-bold text-gray-900 m-0"><i class="fa-solid fa-trophy text-primary me-2"></i> {{ __('Why Members Love Us Section Header') }}</h4>
                            </div>

                            {!! Form::open([
                                'id' => 'form-whyus-header',
                                'onsubmit' => 'FormSubmit("' . route('system.website.section.update', 'why_us') . '", "form-whyus-header"); return false;',
                                'files' => true,
                                'method' => 'POST'
                            ]) !!}
                            <div id="form-whyus-header-alert"></div>

                            <div class="row g-5">
                                <div class="col-md-6">
                                    <label class="form-label required fw-bold">{{ __('Section Title (English)') }}</label>
                                    {!! Form::text('title_en', $whyUs->title_en ?? '', ['class' => 'form-control form-control-solid', 'placeholder' => "Here's Why Members Love Us"]) !!}
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label required fw-bold">{{ __('Section Title (Arabic)') }}</label>
                                    {!! Form::text('title_ar', $whyUs->title_ar ?? '', ['class' => 'form-control form-control-solid', 'placeholder' => 'لهذا السبب يفضلنا المتدربون']) !!}
                                </div>
                            </div>

                            <div class="d-flex justify-content-end mt-6">
                                <button type="submit" class="btn btn-primary submit">
                                    <span class="indicator-label"><i class="fa-solid fa-check-circle me-1"></i> {{ __('Save') }}</span>
                                    <span class="indicator-progress">{{ __('Please wait') }}... <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                                </button>
                            </div>
                            {!! Form::close() !!}
                        </div>

                        <!-- Motivation Quote & Trainer Action Photo -->
                        <div class="card border border-dashed border-gray-300 p-6 mb-8">
                            <div class="card-header border-0 p-0 mb-5">
                                <h4 class="fw-bold text-gray-900 m-0"><i class="fa-solid fa-quote-left text-primary me-2"></i> {{ __('Motivation Quote & Trainer Action Photo') }}</h4>
                            </div>

                            {!! Form::open([
                                'id' => 'form-quote',
                                'onsubmit' => 'FormSubmit("' . route('system.website.section.update', 'quote') . '", "form-quote"); return false;',
                                'files' => true,
                                'method' => 'POST'
                            ]) !!}
                            <div id="form-quote-alert"></div>

                            <div class="row g-5">
                                <div class="col-md-6">
                                    <label class="form-label required fw-bold">{{ __('Quote Title (English)') }}</label>
                                    {!! Form::text('title_en', $quote->title_en ?? '', ['class' => 'form-control form-control-solid', 'placeholder' => 'Turning Your Potential into Performance']) !!}
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label required fw-bold">{{ __('Quote Title (Arabic)') }}</label>
                                    {!! Form::text('title_ar', $quote->title_ar ?? '', ['class' => 'form-control form-control-solid', 'placeholder' => 'تحويل طاقتك الكامنة إلى أداء استثنائي']) !!}
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold">{{ __('Quote Text / Description (English)') }}</label>
                                    {!! Form::textarea('description_en', $quote->description_en ?? '', ['class' => 'form-control form-control-solid', 'rows' => 3]) !!}
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">{{ __('Quote Text / Description (Arabic)') }}</label>
                                    {!! Form::textarea('description_ar', $quote->description_ar ?? '', ['class' => 'form-control form-control-solid', 'rows' => 3]) !!}
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label fw-bold">{{ __('Button Text (English)') }}</label>
                                    {!! Form::text('btn_text_en', $quote->btn_text_en ?? '', ['class' => 'form-control form-control-solid', 'placeholder' => 'Contact Us']) !!}
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">{{ __('Button Text (Arabic)') }}</label>
                                    {!! Form::text('btn_text_ar', $quote->btn_text_ar ?? '', ['class' => 'form-control form-control-solid', 'placeholder' => 'تواصل معنا']) !!}
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">{{ __('Button Link') }}</label>
                                    {!! Form::text('link', $quote->link ?? '', ['class' => 'form-control form-control-solid', 'placeholder' => '/contact']) !!}
                                </div>

                                <div class="col-md-12">
                                    <label class="form-label fw-bold">{{ __('Trainer Action Photo') }}</label>
                                    <div class="d-flex align-items-center gap-5">
                                        <div class="image-input image-input-outline" data-kt-image-input="true">
                                            @php $quoteImg = !empty($quote?->image) ? asset($quote->image) : asset('assets/media/svg/avatars/blank.svg'); @endphp
                                            <div class="image-input-wrapper w-150px h-125px rounded shadow-sm border" id="quote_preview" style="background-image: url('{{ $quoteImg }}'); background-size: cover; background-position: center;"></div>
                                        </div>
                                        <div class="flex-grow-1">
                                            {!! Form::file('image', ['class' => 'form-control form-control-solid', 'accept' => '.png, .jpg, .jpeg, .webp', 'onchange' => 'previewImage(this, "#quote_preview")']) !!}
                                            <div class="text-muted fs-7 mt-2">{{ __('Allowed formats: PNG, JPG, JPEG, WEBP.') }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end mt-6">
                                <button type="submit" class="btn btn-primary submit">
                                    <span class="indicator-label"><i class="fa-solid fa-check-circle me-1"></i> {{ __('Save') }}</span>
                                    <span class="indicator-progress">{{ __('Please wait') }}... <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                                </button>
                            </div>
                            {!! Form::close() !!}
                        </div>

                        <!-- Numbered Why Us Cards (01, 02, 03, 04) -->
                        <div class="d-flex justify-content-between align-items-center mb-6">
                            <div>
                                <h4 class="fw-bold text-gray-900 mb-1"><i class="fa-solid fa-list-ol text-primary me-2"></i> {{ __('Numbered Feature Cards (01, 02, 03, 04) (posts)') }}</h4>
                                <div class="text-muted fs-7">{{ __('Numbered cards highlighting gym advantages with sort index') }}</div>
                            </div>
                            <button type="button" class="btn btn-primary" onclick="openPostModal('why_us', {{ $whyUs?->id ?? 'null' }})">
                                <i class="fa-solid fa-plus me-1"></i> {{ __('Add Why Us Card') }}
                            </button>
                        </div>

                        <div class="table-responsive">
                            <table class="table align-middle table-row-dashed fs-6 gy-4">
                                <thead>
                                    <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                                        <th class="w-60px">{{ __('#') }}</th>
                                        <th class="w-80px">{{ __('Icon') }}</th>
                                        <th>{{ __('Title (EN / AR)') }}</th>
                                        <th>{{ __('Subtext / Description (EN / AR)') }}</th>
                                        <th class="w-80px">{{ __('Sort') }}</th>
                                        <th class="w-100px">{{ __('Status') }}</th>
                                        <th class="text-end w-125px">{{ __('Actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="text-gray-700 fw-semibold">
                                @if(!empty($whyUs?->items))
                                    @forelse($whyUs->items as $item)
                                        <tr>
                                            <td>
                                                <span class="badge badge-light-warning fw-bold fs-6">
                                                    {{ sprintf('%02d', $item->sort ?: 1) }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="symbol symbol-40px symbol-light-warning">
                                                    <span class="symbol-label"><i class="{{ $item->link ?: 'fa-solid fa-trophy' }} fs-4 text-warning"></i></span>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="text-gray-900 fw-bold">{{ $item->title_en ?: $item->title }}</div>
                                                <div class="text-muted fs-7">{{ $item->title_ar }}</div>
                                            </td>
                                            <td>
                                                <div>{{ $item->subtitle_en ?: $item->subtitle ?: Str::limit($item->description_en, 40) ?: '-' }}</div>
                                                <div class="text-muted fs-7">{{ $item->subtitle_ar ?: Str::limit($item->description_ar, 40) }}</div>
                                            </td>
                                            <td>{{ $item->sort }}</td>
                                            <td>
                                                <span class="badge {{ $item->status === 'active' ? 'badge-light-success' : 'badge-light-danger' }}">
                                                    {{ ucfirst($item->status) }}
                                                </span>
                                            </td>
                                            <td class="text-end">
                                                <button type="button" class="btn btn-icon btn-light-primary btn-sm me-2" onclick="editPostItem({{ json_encode($item) }}, 'why_us')">
                                                    <i class="fa-solid fa-pen-to-square"></i>
                                                </button>
                                                <button type="button" class="btn btn-icon btn-light-danger btn-sm" onclick="deletePostItem({{ $item->id }})">
                                                    <i class="fa-solid fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center text-muted py-8">{{ __('No Why Us cards found.') }}</td>
                                        </tr>
                                    @endforelse
                                @else
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-8">{{ __('No Why Us cards found.') }}</td>
                                    </tr>
                                @endif
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- ========================================================================= -->
                    <!-- TAB 6: REVIEWS & GLOBAL SOCIALS                                           -->
                    <!-- ========================================================================= -->
                    <div class="tab-pane fade" id="tab_reviews" role="tabpanel">

                        <!-- Reviews Header Form -->
                        <div class="card border border-dashed border-gray-300 p-6 mb-8">
                            <div class="card-header border-0 p-0 mb-5">
                                <h4 class="fw-bold text-gray-900 m-0"><i class="fa-solid fa-star text-warning me-2"></i> {{ __('Customer Reviews Section Header') }}</h4>
                            </div>

                            {!! Form::open([
                                'id' => 'form-review-header',
                                'onsubmit' => 'FormSubmit("' . route('system.website.section.update', 'review') . '", "form-review-header"); return false;',
                                'files' => true,
                                'method' => 'POST'
                            ]) !!}
                            <div id="form-review-header-alert"></div>

                            <div class="row g-5">
                                <div class="col-md-6">
                                    <label class="form-label required fw-bold">{{ __('Section Title (English)') }}</label>
                                    {!! Form::text('title_en', $review->title_en ?? '', ['class' => 'form-control form-control-solid', 'placeholder' => 'Our Customer Reviews']) !!}
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label required fw-bold">{{ __('Section Title (Arabic)') }}</label>
                                    {!! Form::text('title_ar', $review->title_ar ?? '', ['class' => 'form-control form-control-solid', 'placeholder' => 'آراء وتقييمات عملائنا']) !!}
                                </div>
                            </div>

                            <div class="d-flex justify-content-end mt-6">
                                <button type="submit" class="btn btn-primary submit">
                                    <span class="indicator-label"><i class="fa-solid fa-check-circle me-1"></i> {{ __('Save') }}</span>
                                    <span class="indicator-progress">{{ __('Please wait') }}... <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                                </button>
                            </div>
                            {!! Form::close() !!}
                        </div>

                        <!-- Trainee Reviews Post Items Table -->
                        <div class="d-flex justify-content-between align-items-center mb-6">
                            <div>
                                <h4 class="fw-bold text-gray-900 mb-1"><i class="fa-solid fa-comments text-primary me-2"></i> {{ __('Trainee Reviews & Testimonials (posts)') }}</h4>
                                <div class="text-muted fs-7">{{ __('Trainee feedback, star ratings, and review content') }}</div>
                            </div>
                            <button type="button" class="btn btn-primary" onclick="openPostModal('review', {{ $review?->id ?? 'null' }})">
                                <i class="fa-solid fa-plus me-1"></i> {{ __('Add Review') }}
                            </button>
                        </div>

                        <div class="table-responsive mb-10">
                            <table class="table align-middle table-row-dashed fs-6 gy-4">
                                <thead>
                                    <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                                        <th class="w-60px">#</th>
                                        <th class="w-70px">{{ __('Avatar') }}</th>
                                        <th>{{ __('Reviewer Name (EN / AR)') }}</th>
                                        <th>{{ __('Role / Status') }}</th>
                                        <th>{{ __('Rating') }}</th>
                                        <th>{{ __('Review Content (EN / AR)') }}</th>
                                        <th class="w-80px">{{ __('Sort') }}</th>
                                        <th class="w-100px">{{ __('Status') }}</th>
                                        <th class="text-end w-125px">{{ __('Actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="text-gray-700 fw-semibold">
                                @if(!empty($review?->items))
                                    @forelse($review->items as $item)
                                        <tr>
                                            <td>{{ $item->id }}</td>
                                            <td>
                                                @if(!empty($item->image))
                                                    <img src="{{ asset($item->image) }}" class="w-45px h-45px rounded-circle object-fit-cover border" alt="Avatar">
                                                @else
                                                    <div class="symbol symbol-40px symbol-circle symbol-light-primary">
                                                        <span class="symbol-label fw-bold">{{ substr($item->title_en ?: $item->title ?: 'R', 0, 1) }}</span>
                                                    </div>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="text-gray-900 fw-bold">{{ $item->title_en ?: $item->title }}</div>
                                                <div class="text-muted fs-7">{{ $item->title_ar }}</div>
                                            </td>
                                            <td>
                                                <div>{{ $item->subtitle_en ?: $item->subtitle ?: 'Trainee' }}</div>
                                                <div class="text-muted fs-7">{{ $item->subtitle_ar }}</div>
                                            </td>
                                            <td>
                                                <span class="badge badge-light-warning fw-bold">
                                                    <i class="fa-solid fa-star text-warning me-1"></i> {{ $item->rating ?: $item->price ?: '5.0' }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="fs-7 text-gray-800">{{ Str::limit($item->description_en ?: $item->description, 50) }}</div>
                                                <div class="fs-8 text-muted">{{ Str::limit($item->description_ar, 50) }}</div>
                                            </td>
                                            <td>{{ $item->sort }}</td>
                                            <td>
                                                <span class="badge {{ $item->status === 'active' ? 'badge-light-success' : 'badge-light-danger' }}">
                                                    {{ ucfirst($item->status) }}
                                                </span>
                                            </td>
                                            <td class="text-end">
                                                <button type="button" class="btn btn-icon btn-light-primary btn-sm me-2" onclick="editPostItem({{ json_encode($item) }}, 'review')">
                                                    <i class="fa-solid fa-pen-to-square"></i>
                                                </button>
                                                <button type="button" class="btn btn-icon btn-light-danger btn-sm" onclick="deletePostItem({{ $item->id }})">
                                                    <i class="fa-solid fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="9" class="text-center text-muted py-8">{{ __('No reviews found.') }}</td>
                                        </tr>
                                    @endforelse
                                @else
                                    <tr>
                                        <td colspan="9" class="text-center text-muted py-8">{{ __('No reviews found.') }}</td>
                                    </tr>
                                @endif
                                </tbody>
                            </table>
                        </div>

                        <div class="separator separator-dashed my-8"></div>

                        <!-- Global Contact & Social Links Form -->
                        <div class="card border border-dashed border-gray-300 p-6">
                            <div class="card-header border-0 p-0 mb-5">
                                <h4 class="fw-bold text-gray-900 m-0"><i class="fa-solid fa-globe text-primary me-2"></i> {{ __('Global Branding, Contact Details & Social Links') }}</h4>
                            </div>

                            {!! Form::open([
                                'id' => 'form-settings',
                                'onsubmit' => 'FormSubmit("' . route('system.website.settings.update') . '", "form-settings"); return false;',
                                'files' => true,
                                'method' => 'POST'
                            ]) !!}
                            <div id="form-settings-alert"></div>

                            <div class="row g-5">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">{{ __('Website Title / Brand Name') }}</label>
                                    {!! Form::text('site_title', $settings['site_title'] ?? '', ['class' => 'form-control form-control-solid', 'placeholder' => 'Coach Mahmoud Shaltout']) !!}
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">{{ __('Contact Email Address') }}</label>
                                    {!! Form::email('email', $settings['email'] ?? '', ['class' => 'form-control form-control-solid', 'placeholder' => 'contact@example.com']) !!}
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold">{{ __('Contact Phone Number') }}</label>
                                    {!! Form::text('mobile', $settings['mobile'] ?? ($settings['contact_phone'] ?? ''), ['class' => 'form-control form-control-solid', 'placeholder' => '01144470845']) !!}
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">{{ __('WhatsApp Number (for direct chat link)') }}</label>
                                    {!! Form::text('whatsapp', $settings['whatsapp'] ?? ($settings['contact_whatsapp'] ?? ''), ['class' => 'form-control form-control-solid', 'placeholder' => '01144470845']) !!}
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label fw-bold"><i class="fa-brands fa-instagram text-danger me-1"></i> {{ __('Instagram Profile URL') }}</label>
                                    {!! Form::text('instagram', $settings['instagram'] ?? ($settings['instagram_url'] ?? ''), ['class' => 'form-control form-control-solid', 'placeholder' => 'https://instagram.com/...']) !!}
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold"><i class="fa-brands fa-tiktok text-dark me-1"></i> {{ __('TikTok Profile URL') }}</label>
                                    {!! Form::text('tiktok', $settings['tiktok'] ?? ($settings['tiktok_url'] ?? ''), ['class' => 'form-control form-control-solid', 'placeholder' => 'https://tiktok.com/@...']) !!}
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold"><i class="fa-brands fa-facebook text-primary me-1"></i> {{ __('Facebook Page URL') }}</label>
                                    {!! Form::text('facebook', $settings['facebook'] ?? ($settings['facebook_url'] ?? ''), ['class' => 'form-control form-control-solid', 'placeholder' => 'https://facebook.com/...']) !!}
                                </div>

                                <div class="col-md-12">
                                    <label class="form-label fw-bold">{{ __('Website Logo') }}</label>
                                    <div class="d-flex align-items-center gap-5">
                                        <div class="image-input image-input-outline" data-kt-image-input="true">
                                            @php $logoImg = !empty($settings['logo']) ? asset($settings['logo']) : asset('assets/media/svg/avatars/blank.svg'); @endphp
                                            <div class="image-input-wrapper w-125px h-100px rounded shadow-sm border" id="logo_preview" style="background-image: url('{{ $logoImg }}'); background-size: contain; background-repeat: no-repeat; background-position: center;"></div>
                                        </div>
                                        <div class="flex-grow-1">
                                            {!! Form::file('logo', ['class' => 'form-control form-control-solid', 'accept' => '.png, .jpg, .jpeg, .svg, .webp', 'onchange' => 'previewImage(this, "#logo_preview")']) !!}
                                            <div class="text-muted fs-7 mt-2">{{ __('Allowed formats: PNG, SVG, JPG, WEBP. Transparent background recommended.') }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end mt-6">
                                <button type="submit" class="btn btn-primary submit">
                                    <span class="indicator-label"><i class="fa-solid fa-check-circle me-1"></i> {{ __('Save Global Settings') }}</span>
                                    <span class="indicator-progress">{{ __('Please wait') }}... <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                                </button>
                            </div>
                            {!! Form::close() !!}
                        </div>

                    </div>

                </div>
                <!--end::Tab Content-->
            </div>
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- UNIFIED ITEM MODAL (ADD / EDIT CHILD ITEM FOR post_items)                 -->
<!-- ========================================================================= -->
<div class="modal fade" id="modal-post-item" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content bg-body">
            <div class="modal-header border-0 pb-0">
                <h3 class="fw-bold text-gray-900" id="modal-post-item-title">{{ __('Add Item') }}</h3>
                <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal" aria-label="Close">
                    <i class="fa-solid fa-xmark fs-4"></i>
                </div>
            </div>

            {!! Form::open([
                'id' => 'form-post-item',
                'files' => true,
                'method' => 'POST'
            ]) !!}
            <div class="modal-body py-6 px-lg-10">
                <div id="form-post-item-alert"></div>
                {!! Form::hidden('post_id', '', ['id' => 'modal_post_id']) !!}
                {!! Form::hidden('post_type', '', ['id' => 'modal_post_type']) !!}

                <div class="row g-5">
                    <!-- Title Fields -->
                    <div class="col-md-6" id="field-item-title-en">
                        <label class="form-label required fw-bold" id="label-item-title-en">{{ __('Title (English)') }}</label>
                        {!! Form::text('title_en', '', ['class' => 'form-control form-control-solid', 'id' => 'title_en-form-input']) !!}
                        <div class="invalid-feedback" id="title_en-form-error"></div>
                    </div>

                    <div class="col-md-6" id="field-item-title-ar">
                        <label class="form-label required fw-bold" id="label-item-title-ar">{{ __('Title (Arabic)') }}</label>
                        {!! Form::text('title_ar', '', ['class' => 'form-control form-control-solid', 'id' => 'title_ar-form-input']) !!}
                        <div class="invalid-feedback" id="title_ar-form-error"></div>
                    </div>

                    <!-- Subtitle / Role / Duration Fields -->
                    <div class="col-md-6" id="field-item-subtitle-en">
                        <label class="form-label fw-bold" id="label-item-subtitle-en">{{ __('Subtitle / Duration / Role (English)') }}</label>
                        {!! Form::text('subtitle_en', '', ['class' => 'form-control form-control-solid', 'id' => 'subtitle_en-form-input']) !!}
                        <div class="invalid-feedback" id="subtitle_en-form-error"></div>
                    </div>

                    <div class="col-md-6" id="field-item-subtitle-ar">
                        <label class="form-label fw-bold" id="label-item-subtitle-ar">{{ __('Subtitle / Duration / Role (Arabic)') }}</label>
                        {!! Form::text('subtitle_ar', '', ['class' => 'form-control form-control-solid', 'id' => 'subtitle_ar-form-input']) !!}
                        <div class="invalid-feedback" id="subtitle_ar-form-error"></div>
                    </div>

                    <!-- Rating Field -->
                    <div class="col-md-6" id="field-item-rating">
                        <label class="form-label fw-bold" id="label-item-rating">{{ __('Rating (e.g. 5.0)') }}</label>
                        {!! Form::text('rating', '5.0', ['class' => 'form-control form-control-solid', 'id' => 'rating-form-input', 'placeholder' => '5.0']) !!}
                        <div class="invalid-feedback" id="rating-form-error"></div>
                    </div>

                    <!-- Icon / Link Field -->
                    <div class="col-md-6" id="field-item-link">
                        <label class="form-label fw-bold" id="label-item-link">{{ __('FontAwesome Icon Class') }}</label>
                        {!! Form::text('link', '', ['class' => 'form-control form-control-solid', 'id' => 'link-form-input', 'placeholder' => 'fa-solid fa-dumbbell']) !!}
                        <div class="text-muted fs-8 mt-1">{{ __('Example: fa-solid fa-dumbbell, fa-solid fa-heart-pulse, fa-solid fa-trophy') }}</div>
                        <div class="invalid-feedback" id="link-form-error"></div>
                    </div>

                    <!-- Description Fields -->
                    <div class="col-12" id="field-item-description-en">
                        <label class="form-label fw-bold" id="label-item-description-en">{{ __('Description (English)') }}</label>
                        {!! Form::textarea('description_en', '', ['class' => 'form-control form-control-solid', 'rows' => 3, 'id' => 'description_en-form-input']) !!}
                        <div class="invalid-feedback" id="description_en-form-error"></div>
                    </div>

                    <div class="col-12" id="field-item-description-ar">
                        <label class="form-label fw-bold" id="label-item-description-ar">{{ __('Description (Arabic)') }}</label>
                        {!! Form::textarea('description_ar', '', ['class' => 'form-control form-control-solid', 'rows' => 3, 'id' => 'description_ar-form-input']) !!}
                        <div class="invalid-feedback" id="description_ar-form-error"></div>
                    </div>

                    <!-- Before Image (Transformations) -->
                    <div class="col-md-6" id="field-item-extra-image">
                        <label class="form-label fw-bold">{{ __('Before Image') }}</label>
                        {!! Form::file('extra_image', [
                            'class' => 'form-control form-control-solid',
                            'id' => 'extra_image-form-input',
                            'accept' => '.png, .jpg, .jpeg, .webp'
                        ]) !!}
                        <div class="invalid-feedback" id="extra_image-form-error"></div>
                    </div>

                    <!-- Main Image / Avatar / After Image -->
                    <div class="col-md-6" id="field-item-image">
                        <label class="form-label fw-bold" id="label-item-image">{{ __('Image / Avatar / After Photo') }}</label>
                        {!! Form::file('image', [
                            'class' => 'form-control form-control-solid',
                            'id' => 'image-form-input',
                            'accept' => '.png, .jpg, .jpeg, .webp'
                        ]) !!}
                        <div class="invalid-feedback" id="image-form-error"></div>
                    </div>

                    <!-- Sort Order & Status -->
                    <div class="col-md-6">
                        <label class="form-label fw-bold">{{ __('Sort') }}</label>
                        {!! Form::number('sort', 0, ['class' => 'form-control form-control-solid', 'id' => 'sort-form-input', 'min' => 0]) !!}
                        <div class="invalid-feedback" id="sort-form-error"></div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label required fw-bold">{{ __('Status') }}</label>
                        {!! Form::select('status', ['active' => __('Active'), 'inactive' => __('Inactive')], 'active', [
                            'class' => 'form-select form-select-solid',
                            'id' => 'status-form-input'
                        ]) !!}
                        <div class="invalid-feedback" id="status-form-error"></div>
                    </div>
                </div>
            </div>

            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                <button type="submit" class="btn btn-primary submit">
                    <span class="indicator-label"><i class="fa-solid fa-check me-1"></i> {{ __('Save Item') }}</span>
                    <span class="indicator-progress">{{ __('Please wait') }}... <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                </button>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>

@endsection

@section('footer')
<script>
    // Live Image Preview Helper
    function previewImage(input, previewSelector) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                $(previewSelector).css('background-image', 'url(' + e.target.result + ')');
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    // Modal Visibility & Labels Configuration based on Item Type
    function configureModalForType(type) {
        $('#modal_post_type').val(type);

        // Reset visibility of all dynamic fields
        $('#field-item-subtitle-en').hide();
        $('#field-item-subtitle-ar').hide();
        $('#field-item-description-en').hide();
        $('#field-item-description-ar').hide();
        $('#field-item-rating').hide();
        $('#field-item-link').hide();
        $('#field-item-extra-image').hide();
        $('#field-item-image').hide();

        if (type === 'about') {
            $('#modal-post-item-title').text('{{ __("Add Attribute Badge") }}');
            $('#label-item-title-en').text('{{ __("Badge Title (English)") }}');
            $('#label-item-title-ar').text('{{ __("Badge Title (Arabic)") }}');
            $('#field-item-description-en').show();
            $('#field-item-description-ar').show();
            $('#label-item-description-en').text('{{ __("Short Description (English)") }}');
            $('#label-item-description-ar').text('{{ __("Short Description (Arabic)") }}');
            $('#field-item-link').show();
            $('#label-item-link').text('{{ __("FontAwesome Icon Class (e.g. fa-solid fa-dumbbell)") }}');
        } else if (type === 'service') {
            $('#modal-post-item-title').text('{{ __("Add Service Card") }}');
            $('#label-item-title-en').text('{{ __("Service Title (English)") }}');
            $('#label-item-title-ar').text('{{ __("Service Title (Arabic)") }}');
            $('#field-item-subtitle-en').show();
            $('#field-item-subtitle-ar').show();
            $('#label-item-subtitle-en').text('{{ __("Service Subtitle (English)") }}');
            $('#label-item-subtitle-ar').text('{{ __("Service Subtitle (Arabic)") }}');
            $('#field-item-description-en').show();
            $('#field-item-description-ar').show();
            $('#label-item-description-en').text('{{ __("Service Description (English)") }}');
            $('#label-item-description-ar').text('{{ __("Service Description (Arabic)") }}');
            $('#field-item-link').show();
            $('#label-item-link').text('{{ __("Icon Class (e.g. fa-solid fa-heart-pulse)") }}');
            $('#field-item-image').show();
            $('#label-item-image').text('{{ __("Service Card Image") }}');
        } else if (type === 'transformation') {
            $('#modal-post-item-title').text('{{ __("Add Transformation Card") }}');
            $('#label-item-title-en').text('{{ __("Trainee Name (English)") }}');
            $('#label-item-title-ar').text('{{ __("Trainee Name (Arabic)") }}');
            $('#field-item-subtitle-en').show();
            $('#field-item-subtitle-ar').show();
            $('#label-item-subtitle-en').text('{{ __("Duration / Period (English)") }}');
            $('#label-item-subtitle-ar').text('{{ __("Duration / Period (Arabic)") }}');
            $('#field-item-extra-image').show();
            $('#field-item-image').show();
            $('#label-item-image').text('{{ __("After Photo") }}');
        } else if (type === 'why_us') {
            $('#modal-post-item-title').text('{{ __("Add Why Us Feature Card") }}');
            $('#label-item-title-en').text('{{ __("Feature Title (English)") }}');
            $('#label-item-title-ar').text('{{ __("Feature Title (Arabic)") }}');
            $('#field-item-subtitle-en').show();
            $('#field-item-subtitle-ar').show();
            $('#label-item-subtitle-en').text('{{ __("Short Subtext (English)") }}');
            $('#label-item-subtitle-ar').text('{{ __("Short Subtext (Arabic)") }}');
            $('#field-item-link').show();
            $('#label-item-link').text('{{ __("Icon Class (e.g. fa-solid fa-trophy)") }}');
        } else if (type === 'review') {
            $('#modal-post-item-title').text('{{ __("Add Trainee Review") }}');
            $('#label-item-title-en').text('{{ __("Reviewer Name (English)") }}');
            $('#label-item-title-ar').text('{{ __("Reviewer Name (Arabic)") }}');
            $('#field-item-subtitle-en').show();
            $('#field-item-subtitle-ar').show();
            $('#label-item-subtitle-en').text('{{ __("Role / Title (English)") }}');
            $('#label-item-subtitle-ar').text('{{ __("Role / Title (Arabic)") }}');
            $('#field-item-description-en').show();
            $('#field-item-description-ar').show();
            $('#label-item-description-en').text('{{ __("Review Content (English)") }}');
            $('#label-item-description-ar').text('{{ __("Review Content (Arabic)") }}');
            $('#field-item-rating').show();
            $('#field-item-image').show();
            $('#label-item-image').text('{{ __("Reviewer Avatar Photo") }}');
        }
    }

    // Open Modal for New Record
    function openPostModal(type, postId) {
        configureModalForType(type);
        $('#form-post-item')[0].reset();
        $('#modal_post_type').val(type);
        $('#modal_post_id').val(postId || '');
        $('#form-post-item').attr('onsubmit', 'FormSubmit("{{ route("system.website.items.store") }}", "form-post-item"); return false;');
        $('#modal-post-item').modal('show');
    }

    // Open Modal for Edit
    function editPostItem(item, type) {
        configureModalForType(type);
        $('#modal-post-item-title').text('{{ __("Edit Item") }} #' + item.id);

        $('#modal_post_id').val(item.post_id || '');
        $('#modal_post_type').val(type);

        $('#title_en-form-input').val(item.title_en || item.title || '');
        $('#title_ar-form-input').val(item.title_ar || '');
        $('#subtitle_en-form-input').val(item.subtitle_en || item.subtitle || '');
        $('#subtitle_ar-form-input').val(item.subtitle_ar || '');
        $('#description_en-form-input').val(item.description_en || item.description || '');
        $('#description_ar-form-input').val(item.description_ar || '');
        $('#rating-form-input').val(item.rating || item.price || '5.0');
        $('#link-form-input').val(item.link || '');
        $('#sort-form-input').val(item.sort !== undefined ? item.sort : 0);
        $('#status-form-input').val(item.status || 'active');

        var updateUrl = "{{ url('system/website/items') }}/" + item.id;
        $('#form-post-item').attr('onsubmit', 'FormSubmit("' + updateUrl + '", "form-post-item"); return false;');
        $('#modal-post-item').modal('show');
    }

    // Delete Record via AJAX
    function deletePostItem(id) {
        if (confirm('{{ __("Are you sure you want to delete this item?") }}')) {
            $.ajax({
                url: "{{ url('system/website/items') }}/" + id,
                type: 'DELETE',
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.status) {
                        location.reload();
                    } else {
                        alert(response.message || '{{ __("Error deleting item.") }}');
                    }
                },
                error: function() {
                    alert('{{ __("Something went wrong.") }}');
                }
            });
        }
    }

    // Retain Active Tab on Refresh / Navigation
    $(document).ready(function() {
        var hash = window.location.hash;
        if (hash) {
            var triggerEl = document.querySelector('#websiteNavTabs a[href="' + hash + '"]');
            if (triggerEl) {
                var tab = new bootstrap.Tab(triggerEl);
                tab.show();
            }
        }

        $('#websiteNavTabs a[data-bs-toggle="tab"]').on('shown.bs.tab', function(e) {
            window.location.hash = e.target.getAttribute('href');
        });
    });
</script>
@endsection
