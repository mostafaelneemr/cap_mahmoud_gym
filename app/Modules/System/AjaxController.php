<?php

namespace App\Modules\System;


use Illuminate\Http\Request;
use App;

class AjaxController extends SystemController{

    public function __construct()
    {
        parent::__construct();
    }

    public function index(Request $request)
    {
        switch ($request->type) {
            case 'user':
                $word = $request->word;

                $data = App\Models\User::where('status', 1)
                    ->where(function ($query) use ($word) {
                        $query->Where('name', 'LIKE', '%' . $word . '%')
                            ->OrWhere('mobile', 'LIKE', '%' . $word . '%')
                            ->OrWhere('id', $word);
                    });
                if ($request->module && $request->recordId) {
                    $data = $data->doesntHave('filesShared', 'and', function ($query) use ($request) {
                        $query->where($request->module . '_id', $request->recordId);
                    });
                }
                $data = $data->get(['id as id',
                    \DB::raw('name as value')
                ]);

                if (!$data) return [];
                return $data;
        }
        return [];
    }
}
