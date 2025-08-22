<?php

namespace App\Modules\Web;

use App\Enums\DefaultStatus;
use App\Enums\SliderTypeEnum;
use App\Http\Controllers\Controller;
use App\Models\ChooseItem;
use App\Models\Site;
use App\Models\Slider;
use App\Models\Testimonial;


class WebController extends Controller{

    protected $viewData = [];


    protected function view($file,array $data = []){
        return view('web.'.$file,$data);
    }

    protected function response($status,$code = '200',$message = 'Done',$data = []): array {
        return [
            'status'=> $status,
            'code'=> $code,
            'message'=> $message,
            'data'=> $data
        ];
    }

    public function index(){

        $this->viewData['sliders'] = Slider::where('status',DefaultStatus::Active->value)->where('type',SliderTypeEnum::Home->value)->get();
        $this->viewData['items'] = ChooseItem::where('status',DefaultStatus::Active->value)->get();
        $this->viewData['testimonials'] = Testimonial::where('status',DefaultStatus::Active->value)->get();
//dd(Testimonial::where('status',DefaultStatus::Active->value)->get());
        return $this->view('home', $this->viewData);
    }

    public function home()
    {
        $this->viewData['links'] = Site::where('status',DefaultStatus::Active->value)->get();
        return view('web.home1',$this->viewData);
    }

    public function contact()
    {
        return $this->view('contact');
    }

}
