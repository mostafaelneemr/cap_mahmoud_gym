<?php

namespace App\Modules\Web\Controllers;

use App\Http\Controllers\Controller;
use App\Repositories\SocialLink\SocialLinkRepository;

class SocialLinkWebController extends Controller
{
    protected $socialLinkRepository;

    public function __construct(SocialLinkRepository $socialLinkRepository)
    {
        $this->socialLinkRepository = $socialLinkRepository;
    }

    public function index()
    {
        $links = $this->socialLinkRepository->getActiveLinksOrdered();
        return view('web.social.links', compact('links'));
    }
}
