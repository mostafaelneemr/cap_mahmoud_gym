<?php

namespace App\Modules\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\SubmitContactRequest;
use App\Http\Requests\Api\SubmitJoinUsRequest;
use App\Http\Requests\Api\SubmitReviewRequest;
use App\Models\Post;
use App\Services\JoinUsService;
use App\Services\LandingPageService;
use App\Services\MessageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WebsiteController extends Controller
{
    protected LandingPageService $landingPageService;
    protected MessageService $messageService;
    protected JoinUsService $joinUsService;

    public function __construct(
        LandingPageService $landingPageService,
        MessageService     $messageService,
        JoinUsService      $joinUsService
    ) {
        $this->landingPageService = $landingPageService;
        $this->messageService     = $messageService;
        $this->joinUsService      = $joinUsService;
    }

    private function getValidLanguage(Request $request): string
    {
        $lang = strtolower((string) $request->query('lang', 'en'));
        return in_array($lang, ['ar', 'en'], true) ? $lang : 'en';
    }

    private function buildSectionResponse(?array $data, string $successMessage): JsonResponse
    {
        if ($data === null) {
            return response()->json([
                'status'  => false,
                'message' => 'Invalid section type requested.',
                'data'    => null,
            ], 404);
        }

        return response()->json([
            'status'  => true,
            'message' => $successMessage,
            'data'    => $data,
        ], 200);
    }

    /**
     * Fetch single home section data strictly by section type.
     * Route: GET /api/v1/home/{type}
     */
    public function getHomePageData(Request $request, string $type): JsonResponse
    {
        $lang = $this->getValidLanguage($request);
        $data = $this->landingPageService->getSectionData($type, $lang);

        return $this->buildSectionResponse($data, 'Section data retrieved successfully.');
    }

    /**
     * Fetch single transformations page section data.
     * Route: GET /api/v1/transformations/{type}
     */
    public function getTransformationPageData(Request $request, string $type): JsonResponse
    {
        $lang = $this->getValidLanguage($request);
        $data = $this->landingPageService->getTransformationPageData($type, $lang);

        return $this->buildSectionResponse($data, 'Transformations section data retrieved successfully.');
    }

    /**
     * Fetch single Join-Us / Pricing page section data.
     * Route: GET /api/v1/Join-us/{type}
     */
    public function getJoinUsPageData(Request $request, string $type): JsonResponse
    {
        $lang = $this->getValidLanguage($request);
        $data = $this->landingPageService->getJoinUsPageData($type, $lang);

        return $this->buildSectionResponse($data, 'Join-Us section data retrieved successfully.');
    }

    /**
     * Fetch single Contact page section data.
     * Route: GET /api/v1/contact/{type}
     */
    public function getContactPageData(Request $request, string $type): JsonResponse
    {
        $lang = $this->getValidLanguage($request);
        $data = $this->landingPageService->getContactPageData($type, $lang);

        return $this->buildSectionResponse($data, 'Contact section data retrieved successfully.');
    }

    /**
     * Fetch global website data (Branding, Navigation, Social Links, Footer).
     * Route: GET /api/v1/global
     */
    public function getGlobalData(Request $request): JsonResponse
    {
        $lang = $this->getValidLanguage($request);
        $data = $this->landingPageService->getGlobalData($lang);

        return response()->json([
            'status'  => true,
            'message' => 'Global website data retrieved successfully.',
            'data'    => $data,
        ], 200);
    }

    // =========================================================================
    // FORM SUBMISSIONS
    // =========================================================================

    /**
     * Handle Contact Us form submission.
     * Route: POST /api/v1/contact
     * Throttle: 10 requests / minute (applied in routes/api.php)
     */
    public function submitContact(SubmitContactRequest $request): JsonResponse
    {
        $stored = $this->messageService->store($request);

        if (!$stored) {
            return response()->json([
                'status'  => false,
                'message' => 'Something went wrong. Please try again.',
            ], 500);
        }

        return response()->json([
            'status'  => true,
            'message' => 'Your message has been received. We will get back to you soon!',
        ], 201);
    }

    /**
     * Handle Join Us application form submission.
     * Route: POST /api/v1/join-us
     * Throttle: 10 requests / minute (applied in routes/api.php)
     */
    public function submitJoinUs(SubmitJoinUsRequest $request): JsonResponse
    {
        $stored = $this->joinUsService->store($request->validated());

        if (!$stored) {
            return response()->json([
                'status'  => false,
                'message' => 'Something went wrong. Please try again.',
            ], 500);
        }

        return response()->json([
            'status'  => true,
            'message' => 'Your application has been received! We will contact you shortly.',
        ], 201);
    }

    /**
     * Handle visitor review submission.
     * Reviews are stored as INACTIVE post_items awaiting admin approval.
     * Route: POST /api/v1/reviews
     * Throttle: 10 requests / minute (applied in routes/api.php)
     */
    public function submitReview(SubmitReviewRequest $request): JsonResponse
    {
        // Resolve the parent 'review' post record
        $post = Post::where('type', 'review')->first();

        if (!$post) {
            return response()->json([
                'status'  => false,
                'message' => 'Review section is not configured. Please contact the administrator.',
            ], 503);
        }

        try {
            $post->items()->create([
                'email'          => $request->email,
                'title_en'       => $request->name,
                'title_ar'       => $request->name,
                'description_en' => $request->message,
                'description_ar' => $request->message,
                'rating'         => $request->rating,
                'status'         => 'inactive', // pending admin approval
                'sort'           => 0,
            ]);

            return response()->json([
                'status'  => true,
                'message' => 'Thank you for your review! It will appear after moderation.',
            ], 201);
        } catch (\Exception $e) {
            errorLog($e->getMessage());

            return response()->json([
                'status'  => false,
                'message' => 'Something went wrong. Please try again.',
            ], 500);
        }
    }
}
