<?php

namespace App\Modules\Web;

use App\Http\Controllers\Controller;
use App\Models\Setting;

class WebController extends Controller
{
    protected $viewData = [];

    protected function view($file, array $data = [])
    {
        return view('web.' . $file, $data);
    }

    protected function response($status, $code = '200', $message = 'Done', $data = []): array
    {
        return [
            'status' => $status,
            'code' => $code,
            'message' => $message,
            'data' => $data
        ];
    }

    public function index()
    {
        $settings = [];
        try {
            $settings = Setting::pluck('value', 'name')->toArray();
        } catch (\Exception $e) {
            $settings = [];
        }
        $this->viewData['settings'] = $settings;

        return $this->view('index', $this->viewData);
    }
}
