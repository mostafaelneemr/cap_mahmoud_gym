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
                        {{ __('Cap Mahmoud Shaltout Landing Page Engine') }}
                    </h3>
                </div>
            </div>

            <div class="card-body pt-0">
                <!--begin::Nav Tabs (6 Tabs)-->
                <ul class="nav nav-tabs nav-line-tabs nav-line-tabs-2x mb-8 fs-5 fw-semibold" role="tablist">
                    <li class="nav-item" role="presentation">
                        <a class="nav-link active text-active-primary d-flex align-items-center pb-4" data-bs-toggle="tab" href="#tab_hero" role="tab">
                            <i class="fa-solid fa-wand-magic-sparkles me-2"></i> {{ __('Hero & Branding') }}
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
                            <i class="fa-solid fa-star me-2"></i> {{ __('Reviews & Socials') }}
                        </a>
                    </li>
                </ul>
                <!--end::Nav Tabs-->

                <!--begin::Tab Content-->
                <div class="tab-content" id="landingTabContent">

                    <!-- ========================================== -->
                    <!-- TAB 1: HERO & BRANDING                     -->
                    <!-- ========================================== -->
                    <div class="tab-pane fade show active" id="tab_hero" role="tabpanel">
                        {!! Form::open([
                            'id' => 'hero-form',
                            'onsubmit' => 'FormSubmit("' . route('system.website.settings.update') . '", "hero-form"); return false;',
                            'files' => true,
                            'method' => 'POST'
                        ]) !!}

                        <div id="hero-form-alert"></div>

                        <div class="row g-6 mb-8">
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-gray-800">{{ __('Hero Tagline (e.g. GROW STRONGER)') }}</label>
                                {!! Form::text('hero_tagline', $settings['hero_tagline'] ?? 'GROW STRONGER', [
                                    'class' => 'form-control form-control-solid',
                                    'id' => 'hero_tagline-form-input'
                                ]) !!}
                                <div class="invalid-feedback" id="hero_tagline-form-error"></div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label required fw-bold text-gray-800">{{ __('H1 Main Title (English)') }}</label>
                                {!! Form::text('hero_title_en', $settings['hero_title_en'] ?? ($settings['hero_title'] ?? 'Your Fitness Journey Starts Here'), [
                                    'class' => 'form-control form-control-solid',
                                    'id' => 'hero_title_en-form-input'
                                ]) !!}
                                <div class="invalid-feedback" id="hero_title_en-form-error"></div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label required fw-bold text-gray-800">{{ __('H1 Main Title (Arabic)') }}</label>
                                {!! Form::text('hero_title_ar', $settings['hero_title_ar'] ?? 'رحلتك الرياضية تبدأ هنا', [
                                    'class' => 'form-control form-control-solid',
                                    'id' => 'hero_title_ar-form-input'
                                ]) !!}
                                <div class="invalid-feedback" id="hero_title_ar-form-error"></div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-gray-800">{{ __('CTA Button Text (e.g. JOIN US)') }}</label>
                                {!! Form::text('hero_button_text', $settings['hero_button_text'] ?? ($settings['hero_cta_text'] ?? 'JOIN US'), [
                                    'class' => 'form-control form-control-solid',
                                    'id' => 'hero_button_text-form-input'
                                ]) !!}
                                <div class="invalid-feedback" id="hero_button_text-form-error"></div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold text-gray-800">{{ __('Hero Subtext (English)') }}</label>
                                {!! Form::textarea('hero_subtitle_en', $settings['hero_subtitle_en'] ?? ($settings['hero_subtitle'] ?? 'Customized fitness and nutrition programs for your peak performance'), [
                                    'class' => 'form-control form-control-solid',
                                    'rows' => 3,
                                    'id' => 'hero_subtitle_en-form-input'
                                ]) !!}
                                <div class="invalid-feedback" id="hero_subtitle_en-form-error"></div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-gray-800">{{ __('Hero Subtext (Arabic)') }}</label>
                                {!! Form::textarea('hero_subtitle_ar', $settings['hero_subtitle_ar'] ?? 'برامج تدريب وتغذية مخصصة للوصول لأفضل أداء', [
                                    'class' => 'form-control form-control-solid',
                                    'rows' => 3,
                                    'id' => 'hero_subtitle_ar-form-input'
                                ]) !!}
                                <div class="invalid-feedback" id="hero_subtitle_ar-form-error"></div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold text-gray-800">{{ __('CTA Button Link') }}</label>
                                {!! Form::text('hero_button_link', $settings['hero_button_link'] ?? ($settings['hero_cta_link'] ?? '#pricing'), [
                                    'class' => 'form-control form-control-solid',
                                    'id' => 'hero_button_link-form-input'
                                ]) !!}
                                <div class="invalid-feedback" id="hero_button_link-form-error"></div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold text-gray-800">{{ __('Hero Background / Banner Image') }}</label>
                                <div class="d-flex align-items-center gap-5">
                                    <div class="image-input image-input-outline" data-kt-image-input="true">
                                        @php
                                            $heroImg = !empty($settings['hero_image']) ? asset($settings['hero_image']) : asset('assets/media/svg/avatars/blank.svg');
                                        @endphp
                                        <div class="image-input-wrapper w-175px h-100px rounded shadow-sm border" id="hero_preview" style="background-image: url('{{ $heroImg }}'); background-size: cover; background-position: center;"></div>
                                    </div>
                                    <div class="flex-grow-1">
                                        {!! Form::file('hero_image', [
                                            'class' => 'form-control form-control-solid',
                                            'id' => 'hero_image-form-input',
                                            'accept' => '.png, .jpg, .jpeg, .webp',
                                            'onchange' => 'previewImage(this, "#hero_preview")'
                                        ]) !!}
                                        <div class="text-muted fs-7 mt-2">{{ __('Allowed formats: PNG, JPG, JPEG, WEBP. Max size: 5MB.') }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary submit">
                                <span class="indicator-label"><i class="fa-solid fa-check-circle me-1"></i> {{ __('Save Hero Section') }}</span>
                                <span class="indicator-progress">{{ __('Please wait') }}... <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                            </button>
                        </div>
                        {!! Form::close() !!}
                    </div>

                    <!-- ========================================== -->
                    <!-- TAB 2: ABOUT & 4 KEY ATTRIBUTES            -->
                    <!-- ========================================== -->
                    <div class="tab-pane fade" id="tab_about" role="tabpanel">
                        {!! Form::open([
                            'id' => 'about-form',
                            'onsubmit' => 'FormSubmit("' . route('system.website.settings.update') . '", "about-form"); return false;',
                            'files' => true,
                            'method' => 'POST'
                        ]) !!}

                        <div id="about-form-alert"></div>

                        <div class="row g-6 mb-8">
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-gray-800">{{ __('About Tagline (e.g. ABOUT CAP MAHMOUD)') }}</label>
                                {!! Form::text('about_tagline', $settings['about_tagline'] ?? 'ABOUT CAP MAHMOUD', [
                                    'class' => 'form-control form-control-solid',
                                    'id' => 'about_tagline-form-input'
                                ]) !!}
                            </div>
                            <div class="col-md-6">
                                <label class="form-label required fw-bold text-gray-800">{{ __('About Title (e.g. Built For Everyone Powered By Passion)') }}</label>
                                {!! Form::text('about_title_en', $settings['about_title_en'] ?? ($settings['about_title'] ?? 'Built For Everyone Powered By Passion'), [
                                    'class' => 'form-control form-control-solid',
                                    'id' => 'about_title_en-form-input'
                                ]) !!}
                            </div>

                            <div class="col-md-6">
                                <label class="form-label required fw-bold text-gray-800">{{ __('Coach Bio Quote (English)') }}</label>
                                {!! Form::textarea('about_bio_en', $settings['about_bio_en'] ?? ($settings['about_bio'] ?? ''), [
                                    'class' => 'form-control form-control-solid',
                                    'rows' => 4,
                                    'id' => 'about_bio_en-form-input',
                                    'placeholder' => 'Dedicated to transforming lives through personalized fitness coaching...'
                                ]) !!}
                            </div>
                            <div class="col-md-6">
                                <label class="form-label required fw-bold text-gray-800">{{ __('Coach Bio Quote (Arabic)') }}</label>
                                {!! Form::textarea('about_bio_ar', $settings['about_bio_ar'] ?? ($settings['about_description'] ?? ''), [
                                    'class' => 'form-control form-control-solid',
                                    'rows' => 4,
                                    'id' => 'about_bio_ar-form-input',
                                    'placeholder' => 'نبذة عن الكابتن وفلسفة التدريب والتغذية...'
                                ]) !!}
                            </div>

                            <div class="col-md-12">
                                <h5 class="fw-bold text-primary mb-4"><i class="fa-solid fa-list-check me-2"></i> {{ __('4 Key Attributes Badges') }}</h5>
                                <div class="row g-4 border p-4 rounded bg-light">
                                    <!-- Attribute 1: Custom Plans -->
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">1. {{ __('Custom Plans Title') }}</label>
                                        {!! Form::text('attr_custom_plans_title', $settings['attr_custom_plans_title'] ?? 'Custom Plans', ['class' => 'form-control form-control-solid mb-2']) !!}
                                        {!! Form::text('attr_custom_plans_desc', $settings['attr_custom_plans_desc'] ?? 'Tailored training & diet plans for your body', ['class' => 'form-control form-control-solid', 'placeholder' => 'Short Description']) !!}
                                    </div>
                                    <!-- Attribute 2: Data Driven -->
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">2. {{ __('Data Driven Title') }}</label>
                                        {!! Form::text('attr_data_driven_title', $settings['attr_data_driven_title'] ?? 'Data Driven', ['class' => 'form-control form-control-solid mb-2']) !!}
                                        {!! Form::text('attr_data_driven_desc', $settings['attr_data_driven_desc'] ?? 'Accurate metric tracking and analytics', ['class' => 'form-control form-control-solid', 'placeholder' => 'Short Description']) !!}
                                    </div>
                                    <!-- Attribute 3: Elite Coaching -->
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">3. {{ __('Elite Coaching Title') }}</label>
                                        {!! Form::text('attr_elite_coaching_title', $settings['attr_elite_coaching_title'] ?? 'Elite Coaching', ['class' => 'form-control form-control-solid mb-2']) !!}
                                        {!! Form::text('attr_elite_coaching_desc', $settings['attr_elite_coaching_desc'] ?? 'Direct 1-on-1 guidance by Cap Mahmoud', ['class' => 'form-control form-control-solid', 'placeholder' => 'Short Description']) !!}
                                    </div>
                                    <!-- Attribute 4: 24/7 Support -->
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">4. {{ __('24/7 Support Title') }}</label>
                                        {!! Form::text('attr_support_247_title', $settings['attr_support_247_title'] ?? '24/7 Support', ['class' => 'form-control form-control-solid mb-2']) !!}
                                        {!! Form::text('attr_support_247_desc', $settings['attr_support_247_desc'] ?? 'Continuous WhatsApp support & check-ins', ['class' => 'form-control form-control-solid', 'placeholder' => 'Short Description']) !!}
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <label class="form-label fw-bold text-gray-800">{{ __('Coach Trainer Photo') }}</label>
                                <div class="d-flex align-items-center gap-5">
                                    <div class="image-input image-input-outline" data-kt-image-input="true">
                                        @php
                                            $aboutImg = !empty($settings['about_image']) ? asset($settings['about_image']) : asset('assets/media/svg/avatars/blank.svg');
                                        @endphp
                                        <div class="image-input-wrapper w-125px h-125px rounded shadow-sm border" id="about_preview" style="background-image: url('{{ $aboutImg }}'); background-size: cover; background-position: center;"></div>
                                    </div>
                                    <div class="flex-grow-1">
                                        {!! Form::file('about_image', [
                                            'class' => 'form-control form-control-solid',
                                            'id' => 'about_image-form-input',
                                            'accept' => '.png, .jpg, .jpeg, .webp',
                                            'onchange' => 'previewImage(this, "#about_preview")'
                                        ]) !!}
                                        <div class="text-muted fs-7 mt-2">{{ __('Allowed formats: PNG, JPG, JPEG, WEBP. Max size: 5MB.') }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary submit">
                                <span class="indicator-label"><i class="fa-solid fa-check-circle me-1"></i> {{ __('Save About Section') }}</span>
                                <span class="indicator-progress">{{ __('Please wait') }}... <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                            </button>
                        </div>
                        {!! Form::close() !!}
                    </div>

                    <!-- ========================================== -->
                    <!-- TAB 3: OUR SERVICES                        -->
                    <!-- ========================================== -->
                    <div class="tab-pane fade" id="tab_services" role="tabpanel">
                        <div class="d-flex justify-content-between align-items-center mb-6">
                            <div>
                                <h4 class="fw-bold text-gray-900 mb-1">{{ __('Services Offered') }}</h4>
                                <div class="text-muted fs-7">{{ __('Manage training, nutrition, and tracking services offered to trainees') }}</div>
                            </div>
                            <button type="button" class="btn btn-primary" onclick="openPostModal('service')">
                                <i class="fa-solid fa-plus me-1"></i> {{ __('Add Service Card') }}
                            </button>
                        </div>

                        <div class="table-responsive">
                            <table class="table align-middle table-row-dashed fs-6 gy-4">
                                <thead>
                                    <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                                        <th>#</th>
                                        <th>{{ __('Icon') }}</th>
                                        <th>{{ __('Service Title') }}</th>
                                        <th>{{ __('Subtitle') }}</th>
                                        <th>{{ __('Description') }}</th>
                                        <th>{{ __('Sort') }}</th>
                                        <th>{{ __('Status') }}</th>
                                        <th class="text-end">{{ __('Actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="text-gray-700 fw-semibold">
                                @if(!empty($services))
                                @forelse($services as $item)
                                        <tr>
                                            <td>{{ $item->id }}</td>
                                            <td><i class="{{ $item->link ?: 'fa-solid fa-dumbbell' }} fs-3 text-primary"></i></td>
                                            <td class="text-gray-900 fw-bold">{{ $item->title }}</td>
                                            <td>{{ $item->subtitle ?: '-' }}</td>
                                            <td class="fs-7 text-muted">{{ Str::limit($item->description, 60) }}</td>
                                            <td>{{ $item->sort_order }}</td>
                                            <td>
                                                <span class="badge {{ $item->status === 'active' ? 'badge-light-success' : 'badge-light-danger' }}">
                                                    {{ ucfirst($item->status) }}
                                                </span>
                                            </td>
                                            <td class="text-end">
                                                <button type="button" class="btn btn-icon btn-light-primary btn-sm me-2" onclick="editPost({{ json_encode($item) }})">
                                                    <i class="fa-solid fa-pen-to-square"></i>
                                                </button>
                                                <button type="button" class="btn btn-icon btn-light-danger btn-sm" onclick="deletePost({{ $item->id }})">
                                                    <i class="fa-solid fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center text-muted py-8">{{ __('No service cards found.') }}</td>
                                        </tr>
                                    @endforelse
                                @endif
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- ========================================== -->
                    <!-- TAB 4: TRANSFORMATIONS                     -->
                    <!-- ========================================== -->
                    <div class="tab-pane fade" id="tab_transformations" role="tabpanel">
                        <div class="d-flex justify-content-between align-items-center mb-6">
                            <div>
                                <h4 class="fw-bold text-gray-900 mb-1">{{ __('Client Transformations (RESULTS don\'t LIE)') }}</h4>
                                <div class="text-muted fs-7">{{ __('Before and After cards showcasing trainee results') }}</div>
                            </div>
                            <button type="button" class="btn btn-primary" onclick="openPostModal('transformation')">
                                <i class="fa-solid fa-plus me-1"></i> {{ __('Add Transformation') }}
                            </button>
                        </div>

                        <div class="table-responsive">
                            <table class="table align-middle table-row-dashed fs-6 gy-4">
                                <thead>
                                    <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                                        <th>#</th>
                                        <th>{{ __('Before Image') }}</th>
                                        <th>{{ __('After Image') }}</th>
                                        <th>{{ __('Trainee Name') }}</th>
                                        <th>{{ __('Duration') }}</th>
                                        <th>{{ __('Sort') }}</th>
                                        <th>{{ __('Status') }}</th>
                                        <th class="text-end">{{ __('Actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="text-gray-700 fw-semibold">
                                @if(!empty($transformations))
                                    @forelse($transformations as $item)
                                        <tr>
                                            <td>{{ $item->id }}</td>
                                            <td>
                                                @if($item->extra_image_url)
                                                    <img src="{{ $item->extra_image_url }}" class="w-50px h-50px rounded object-fit-cover border" alt="Before">
                                                @else
                                                    <span class="badge badge-light">{{ __('No Image') }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($item->image_url)
                                                    <img src="{{ $item->image_url }}" class="w-50px h-50px rounded object-fit-cover border" alt="After">
                                                @else
                                                    <span class="badge badge-light">{{ __('No Image') }}</span>
                                                @endif
                                            </td>
                                            <td class="text-gray-900 fw-bold">{{ $item->title }}</td>
                                            <td>{{ $item->subtitle ?: '-' }}</td>
                                            <td>{{ $item->sort_order }}</td>
                                            <td>
                                                <span class="badge {{ $item->status === 'active' ? 'badge-light-success' : 'badge-light-danger' }}">
                                                    {{ ucfirst($item->status) }}
                                                </span>
                                            </td>
                                            <td class="text-end">
                                                <button type="button" class="btn btn-icon btn-light-primary btn-sm me-2" onclick="editPost({{ json_encode($item) }})">
                                                    <i class="fa-solid fa-pen-to-square"></i>
                                                </button>
                                                <button type="button" class="btn btn-icon btn-light-danger btn-sm" onclick="deletePost({{ $item->id }})">
                                                    <i class="fa-solid fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center text-muted py-8">{{ __('No transformation cards found.') }}</td>
                                        </tr>
                                    @endforelse
                                @endif
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- ========================================== -->
                    <!-- TAB 5: WHY US & MOTIVATION                 -->
                    <!-- ========================================== -->
                    <div class="tab-pane fade" id="tab_why_us" role="tabpanel">
                        <!-- Numbered Why Us Cards -->
                        <div class="d-flex justify-content-between align-items-center mb-6">
                            <div>
                                <h4 class="fw-bold text-gray-900 mb-1">{{ __('Why Members Love Us (01, 02, 03, 04)') }}</h4>
                                <div class="text-muted fs-7">{{ __('Numbered feature cards highlighting gym advantages') }}</div>
                            </div>
                            <button type="button" class="btn btn-primary" onclick="openPostModal('why_us')">
                                <i class="fa-solid fa-plus me-1"></i> {{ __('Add Why Us Card') }}
                            </button>
                        </div>

                        <div class="table-responsive mb-10">
                            <table class="table align-middle table-row-dashed fs-6 gy-4">
                                <thead>
                                    <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                                        <th>#</th>
                                        <th>{{ __('Icon') }}</th>
                                        <th>{{ __('Title') }}</th>
                                        <th>{{ __('Subtext / Description') }}</th>
                                        <th>{{ __('Sort') }}</th>
                                        <th>{{ __('Status') }}</th>
                                        <th class="text-end">{{ __('Actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="text-gray-700 fw-semibold">
                                @if(!empty($whyUsItems))
                                    @forelse($whyUsItems as $item)
                                        <tr>
                                            <td>{{ $item->id }}</td>
                                            <td><i class="{{ $item->link ?: 'fa-solid fa-trophy' }} fs-3 text-warning"></i></td>
                                            <td class="text-gray-900 fw-bold">{{ $item->title }}</td>
                                            <td class="fs-7 text-muted">{{ Str::limit($item->subtitle ?: $item->description, 80) }}</td>
                                            <td>{{ $item->sort_order }}</td>
                                            <td>
                                                <span class="badge {{ $item->status === 'active' ? 'badge-light-success' : 'badge-light-danger' }}">
                                                    {{ ucfirst($item->status) }}
                                                </span>
                                            </td>
                                            <td class="text-end">
                                                <button type="button" class="btn btn-icon btn-light-primary btn-sm me-2" onclick="editPost({{ json_encode($item) }})">
                                                    <i class="fa-solid fa-pen-to-square"></i>
                                                </button>
                                                <button type="button" class="btn btn-icon btn-light-danger btn-sm" onclick="deletePost({{ $item->id }})">
                                                    <i class="fa-solid fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center text-muted py-6">{{ __('No Why Us items found.') }}</td>
                                        </tr>
                                    @endforelse
                                    @endif
                                </tbody>
                            </table>
                        </div>

                        <div class="separator separator-dashed mb-8"></div>

                        <!-- Motivation Quote Form -->
                        <h4 class="fw-bold text-gray-900 mb-6"><i class="fa-solid fa-quote-left text-primary me-2"></i> {{ __('Motivation Quote & Final CTA Settings') }}</h4>

                        {!! Form::open([
                            'id' => 'motivation-form',
                            'onsubmit' => 'FormSubmit("' . route('system.website.settings.update') . '", "motivation-form"); return false;',
                            'files' => true,
                            'method' => 'POST'
                        ]) !!}
                        <div id="motivation-form-alert"></div>

                        <div class="row g-6 mb-8">
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-gray-800">{{ __('Motivation Quote Title') }}</label>
                                {!! Form::text('quote_title', $settings['quote_title'] ?? 'Turning Your Potential into Performance', [
                                    'class' => 'form-control form-control-solid'
                                ]) !!}
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-gray-800">{{ __('Motivation Quote Text') }}</label>
                                {!! Form::textarea('quote_text', $settings['quote_text'] ?? 'Fitness is not a destination; it is a lifestyle that creates the impossible.', [
                                    'class' => 'form-control form-control-solid', 'rows' => 2
                                ]) !!}
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold text-gray-800">{{ __('Final CTA Banner Title') }}</label>
                                {!! Form::text('final_cta_title', $settings['final_cta_title'] ?? "LET'S START YOUR TRAINING TODAY", [
                                    'class' => 'form-control form-control-solid'
                                ]) !!}
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-gray-800">{{ __('Final CTA Subtext') }}</label>
                                {!! Form::text('final_cta_text', $settings['final_cta_text'] ?? 'Get your custom program now and connect with us directly', [
                                    'class' => 'form-control form-control-solid'
                                ]) !!}
                            </div>

                            <div class="col-md-12">
                                <label class="form-label fw-bold text-gray-800">{{ __('Trainer Action Photo') }}</label>
                                <div class="d-flex align-items-center gap-5">
                                    <div class="image-input image-input-outline" data-kt-image-input="true">
                                        @php
                                            $trainerImg = !empty($settings['trainer_photo']) ? asset($settings['trainer_photo']) : asset('assets/media/svg/avatars/blank.svg');
                                        @endphp
                                        <div class="image-input-wrapper w-125px h-125px rounded shadow-sm border" id="trainer_preview" style="background-image: url('{{ $trainerImg }}'); background-size: cover; background-position: center;"></div>
                                    </div>
                                    <div class="flex-grow-1">
                                        {!! Form::file('trainer_photo', [
                                            'class' => 'form-control form-control-solid',
                                            'accept' => '.png, .jpg, .jpeg, .webp',
                                            'onchange' => 'previewImage(this, "#trainer_preview")'
                                        ]) !!}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary submit">
                                <span class="indicator-label"><i class="fa-solid fa-check-circle me-1"></i> {{ __('Save Motivation & CTA Settings') }}</span>
                                <span class="indicator-progress">{{ __('Please wait') }}... <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                            </button>
                        </div>
                        {!! Form::close() !!}
                    </div>

                    <!-- ========================================== -->
                    <!-- TAB 6: REVIEWS & SOCIALS                   -->
                    <!-- ========================================== -->
                    <div class="tab-pane fade" id="tab_reviews" role="tabpanel">
                        <!-- Customer Reviews List -->
                        <div class="d-flex justify-content-between align-items-center mb-6">
                            <div>
                                <h4 class="fw-bold text-gray-900 mb-1">{{ __('Customer Reviews & Testimonials') }}</h4>
                                <div class="text-muted fs-7">{{ __('Trainee reviews, ratings, and avatar photos') }}</div>
                            </div>
                            <button type="button" class="btn btn-primary" onclick="openPostModal('review')">
                                <i class="fa-solid fa-plus me-1"></i> {{ __('Add Review') }}
                            </button>
                        </div>

                        <div class="table-responsive mb-10">
                            <table class="table align-middle table-row-dashed fs-6 gy-4">
                                <thead>
                                    <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                                        <th>#</th>
                                        <th>{{ __('Avatar') }}</th>
                                        <th>{{ __('Reviewer Name') }}</th>
                                        <th>{{ __('Role / Status') }}</th>
                                        <th>{{ __('Review Text') }}</th>
                                        <th>{{ __('Rating') }}</th>
                                        <th>{{ __('Sort') }}</th>
                                        <th class="text-end">{{ __('Actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="text-gray-700 fw-semibold">
                                @if(!empty($reviews))
                                    @forelse($reviews as $item)
                                        <tr>
                                            <td>{{ $item->id }}</td>
                                            <td>
                                                @if($item->image_url)
                                                    <img src="{{ $item->image_url }}" class="w-40px h-40px rounded-circle object-fit-cover border" alt="Avatar">
                                                @else
                                                    <span class="badge badge-light-primary">{{ substr($item->title, 0, 1) }}</span>
                                                @endif
                                            </td>
                                            <td class="text-gray-900 fw-bold">{{ $item->title }}</td>
                                            <td>{{ $item->subtitle ?: '-' }}</td>
                                            <td class="fs-7 text-muted">{{ Str::limit($item->description, 60) }}</td>
                                            <td>
                                                <span class="badge badge-light-warning">
                                                    <i class="fa-solid fa-star text-warning me-1"></i> {{ $item->price ?: '5.0' }}
                                                </span>
                                            </td>
                                            <td>{{ $item->sort_order }}</td>
                                            <td class="text-end">
                                                <button type="button" class="btn btn-icon btn-light-primary btn-sm me-2" onclick="editPost({{ json_encode($item) }})">
                                                    <i class="fa-solid fa-pen-to-square"></i>
                                                </button>
                                                <button type="button" class="btn btn-icon btn-light-danger btn-sm" onclick="deletePost({{ $item->id }})">
                                                    <i class="fa-solid fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center text-muted py-6">{{ __('No reviews found.') }}</td>
                                        </tr>
                                    @endforelse
                                    @endif
                                </tbody>
                            </table>
                        </div>

                        <div class="separator separator-dashed mb-8"></div>

                        <!-- Social Media & Footer Links Form -->
                        <h4 class="fw-bold text-gray-900 mb-6"><i class="fa-solid fa-share-nodes text-primary me-2"></i> {{ __('Social Media & Contact URLs') }}</h4>

                        {!! Form::open([
                            'id' => 'socials-form',
                            'onsubmit' => 'FormSubmit("' . route('system.website.settings.update') . '", "socials-form"); return false;',
                            'method' => 'POST'
                        ]) !!}
                        <div id="socials-form-alert"></div>

                        <div class="row g-6 mb-8">
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-gray-800"><i class="fa-brands fa-whatsapp text-success me-1"></i> {{ __('WhatsApp Link') }}</label>
                                {!! Form::text('whatsapp_url', $settings['whatsapp_url'] ?? ($settings['whatsapp'] ?? ''), [
                                    'class' => 'form-control form-control-solid', 'placeholder' => 'https://wa.me/2010...'
                                ]) !!}
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-gray-800"><i class="fa-brands fa-instagram text-danger me-1"></i> {{ __('Instagram URL') }}</label>
                                {!! Form::text('instagram_url', $settings['instagram_url'] ?? '', [
                                    'class' => 'form-control form-control-solid', 'placeholder' => 'https://instagram.com/...'
                                ]) !!}
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-gray-800"><i class="fa-brands fa-tiktok text-dark me-1"></i> {{ __('TikTok URL') }}</label>
                                {!! Form::text('tiktok_url', $settings['tiktok_url'] ?? '', [
                                    'class' => 'form-control form-control-solid', 'placeholder' => 'https://tiktok.com/@...'
                                ]) !!}
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-gray-800"><i class="fa-brands fa-facebook text-primary me-1"></i> {{ __('Facebook URL') }}</label>
                                {!! Form::text('facebook_url', $settings['facebook_url'] ?? '', [
                                    'class' => 'form-control form-control-solid', 'placeholder' => 'https://facebook.com/...'
                                ]) !!}
                            </div>
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary submit">
                                <span class="indicator-label"><i class="fa-solid fa-check-circle me-1"></i> {{ __('Save Social Links') }}</span>
                                <span class="indicator-progress">{{ __('Please wait') }}... <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                            </button>
                        </div>
                        {!! Form::close() !!}
                    </div>

                </div>
                <!--end::Tab Content-->
            </div>
        </div>
    </div>
</div>

<!-- ========================================== -->
<!-- UNIFIED ITEM MODAL (ADD / EDIT)            -->
<!-- ========================================== -->
<div class="modal fade" id="modal-post" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content bg-body">
            <div class="modal-header border-0 pb-0">
                <h3 class="fw-bold text-gray-900" id="modal-post-title">{{ __('Add Item') }}</h3>
                <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal" aria-label="Close">
                    <i class="fa-solid fa-xmark fs-4"></i>
                </div>
            </div>

            {!! Form::open([
                'id' => 'form-post',
                'files' => true,
                'method' => 'POST'
            ]) !!}
            <div class="modal-body py-6 px-lg-10">
                <div id="form-post-alert"></div>
                {!! Form::hidden('type', 'transformation', ['id' => 'post_type']) !!}

                <div class="row g-5">
                    <!-- Title Field -->
                    <div class="col-12">
                        <label class="form-label required fw-bold" id="label-post-title">{{ __('Title') }}</label>
                        {!! Form::text('title', '', ['class' => 'form-control form-control-solid', 'id' => 'title-form-input', 'required' => true]) !!}
                        <div class="invalid-feedback" id="title-form-error"></div>
                    </div>

                    <!-- Subtitle Field -->
                    <div class="col-12" id="field-post-subtitle">
                        <label class="form-label fw-bold" id="label-post-subtitle">{{ __('Subtitle / Duration / Role') }}</label>
                        {!! Form::text('subtitle', '', ['class' => 'form-control form-control-solid', 'id' => 'subtitle-form-input']) !!}
                        <div class="invalid-feedback" id="subtitle-form-error"></div>
                    </div>

                    <!-- Price / Rating Field -->
                    <div class="col-md-6" id="field-post-price">
                        <label class="form-label fw-bold" id="label-post-price">{{ __('Price / Rating') }}</label>
                        {!! Form::text('price', '', ['class' => 'form-control form-control-solid', 'id' => 'price-form-input']) !!}
                        <div class="invalid-feedback" id="price-form-error"></div>
                    </div>

                    <!-- Icon / Link Field -->
                    <div class="col-md-6" id="field-post-link">
                        <label class="form-label fw-bold" id="label-post-link">{{ __('Icon Class / CTA Link') }}</label>
                        {!! Form::text('link', '', ['class' => 'form-control form-control-solid', 'id' => 'link-form-input', 'placeholder' => 'fa-solid fa-dumbbell']) !!}
                        <div class="invalid-feedback" id="link-form-error"></div>
                    </div>

                    <!-- Description / Text Field -->
                    <div class="col-12" id="field-post-description">
                        <label class="form-label fw-bold" id="label-post-description">{{ __('Description / Review Text') }}</label>
                        {!! Form::textarea('description', '', ['class' => 'form-control form-control-solid', 'rows' => 4, 'id' => 'description-form-input']) !!}
                        <div class="invalid-feedback" id="description-form-error"></div>
                    </div>

                    <!-- Before Image (Transformations) -->
                    <div class="col-md-6" id="field-post-extra-image">
                        <label class="form-label fw-bold">{{ __('Before Image') }}</label>
                        {!! Form::file('extra_image', [
                            'class' => 'form-control form-control-solid',
                            'id' => 'extra_image-form-input',
                            'accept' => '.png, .jpg, .jpeg, .webp'
                        ]) !!}
                        <div class="invalid-feedback" id="extra_image-form-error"></div>
                    </div>

                    <!-- Main Image / Avatar / After Image -->
                    <div class="col-md-6" id="field-post-image">
                        <label class="form-label fw-bold" id="label-post-image">{{ __('Image / Avatar / After Photo') }}</label>
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
                        {!! Form::number('sort', 0, ['class' => 'form-control form-control-solid', 'id' => 'sort-form-input']) !!}
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
                    <span class="indicator-label"><i class="fa-solid fa-check me-1"></i> {{ __('Save') }}</span>
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

    // Modal Visibility Configuration based on Type
    function configureModalForType(type) {
        $('#post_type').val(type);

        // Reset visibility
        $('#field-post-subtitle').show();
        $('#field-post-price').hide();
        $('#field-post-link').hide();
        $('#field-post-description').show();
        $('#field-post-extra-image').hide();
        $('#field-post-image').hide();

        if (type === 'service') {
            $('#modal-post-title').text('{{ __("Add Service Card") }}');
            $('#label-post-title').text('{{ __("Service Title (e.g. Online Training)") }}');
            $('#label-post-subtitle').text('{{ __("Subtitle / Category") }}');
            $('#label-post-description').text('{{ __("Service Description") }}');
            $('#field-post-link').show();
            $('#label-post-link').text('{{ __("FontAwesome Icon Class (e.g. fa-solid fa-dumbbell)") }}');
        } else if (type === 'transformation') {
            $('#modal-post-title').text('{{ __("Add Transformation") }}');
            $('#label-post-title').text('{{ __("Trainee Name") }}');
            $('#label-post-subtitle').text('{{ __("Duration (e.g. 12 Weeks)") }}');
            $('#label-post-description').text('{{ __("Transformation Story / Notes") }}');
            $('#field-post-extra-image').show();
            $('#field-post-image').show();
            $('#label-post-image').text('{{ __("After Image") }}');
        } else if (type === 'why_us') {
            $('#modal-post-title').text('{{ __("Add Why Us Item") }}');
            $('#label-post-title').text('{{ __("Feature Title") }}');
            $('#label-post-subtitle').text('{{ __("Short Subtext") }}');
            $('#field-post-link').show();
            $('#label-post-link').text('{{ __("Icon Class (e.g. fa-solid fa-trophy)") }}');
        } else if (type === 'review') {
            $('#modal-post-title').text('{{ __("Add Review") }}');
            $('#label-post-title').text('{{ __("Reviewer Name") }}');
            $('#label-post-subtitle').text('{{ __("Role / Client Status") }}');
            $('#label-post-description').text('{{ __("Review Content") }}');
            $('#field-post-price').show();
            $('#label-post-price').text('{{ __("Rating (e.g. 5.0)") }}');
            $('#field-post-image').show();
            $('#label-post-image').text('{{ __("Reviewer Avatar Photo") }}');
        }
    }

    // Open Modal for New Record
    function openPostModal(type) {
        console.log(type)
        configureModalForType(type);
        $('#form-post')[0].reset();
        $('#post_type').val(type);
        $('#form-post').attr('onsubmit', 'FormSubmit("{{ route("system.website.posts.store") }}", "form-post"); return false;');
        $('#modal-post').modal('show');
    }

    // Open Modal for Edit
    function editPost(item) {
        configureModalForType(item.type);
        $('#modal-post-title').text('{{ __("Edit Item") }} #' + item.id);

        $('#title-form-input').val(item.title || '');
        $('#subtitle-form-input').val(item.subtitle || '');
        $('#price-form-input').val(item.price || '');
        $('#link-form-input').val(item.link || '');
        $('#description-form-input').val(item.description || '');
        $('#sort_order-form-input').val(item.sort_order || 0);
        $('#status-form-input').val(item.status || 'active');

        var updateUrl = "{{ url('system/website/posts') }}/" + item.id;
        $('#form-post').attr('onsubmit', 'FormSubmit("' + updateUrl + '", "form-post"); return false;');
        $('#modal-post').modal('show');
    }

    // Delete Record via AJAX
    function deletePost(id) {
        if (confirm('{{ __("Are you sure you want to delete this item?") }}')) {
            $.ajax({
                url: "{{ url('system/landing/posts') }}/" + id,
                type: 'DELETE',
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
</script>
@endsection
