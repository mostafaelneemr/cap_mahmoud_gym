<?php

namespace App\Modules\Api;

use App\Http\Controllers\Controller;
use App\Services\LandingPageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WebsiteController extends Controller
{
    protected LandingPageService $landingPageService;

    public function __construct(LandingPageService $landingPageService)
    {
        $this->landingPageService = $landingPageService;
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
                'status' => false,
                'message' => 'Invalid section type requested.',
                'data' => null,
            ], 404);
        }

        return response()->json([
            'status' => true,
            'message' => $successMessage,
            'data' => $data,
        ], 200);
    }

    /**
     * Fetch single home section data strictly by section type.
     * Route: GET /api/v1/home/{type}
     *
     * @param Request $request
     * @param string $type
     * @return JsonResponse
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
     *
     * @param Request $request
     * @param string $type
     * @return JsonResponse
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
     *
     * @param Request $request
     * @param string $type
     * @return JsonResponse
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
     *
     * @param Request $request
     * @param string $type
     * @return JsonResponse
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
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getGlobalData(Request $request): JsonResponse
    {
        $lang = $this->getValidLanguage($request);

        $data = $this->landingPageService->getGlobalData($lang);

        return response()->json([
            'status' => true,
            'message' => 'Global website data retrieved successfully.',
            'data' => $data,
        ], 200);
    }
}
