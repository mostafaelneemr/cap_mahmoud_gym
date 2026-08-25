<?php

namespace App\Modules\System;


use App\Services\WebsiteService;
use Illuminate\Http\Request;

class WebsiteController extends SystemController
{
    protected $websiteService;

    public function __construct(WebsiteService $websiteService)
    {
        parent::__construct();
        $this->websiteService = $websiteService;
    }

    public function index(Request $request)
    {

        return $this->view('website.index');
    }



}
