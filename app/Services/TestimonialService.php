<?php

namespace App\Services;


use App\Enums\StatusEnum;
use App\Repositories\Language\LanguageRepository;
use App\Repositories\Testimonial\TestimonialRepository;
use Illuminate\Support\Facades\DB;
use Intervention\Image\Facades\Image;
use App\Enums\SliderTypeEnum;
use Datatables;


class TestimonialService extends BaseService
{
    protected $testimonialRepository,$languageRepository;

    public function __construct(TestimonialRepository $testimonialRepository,LanguageRepository $languageRepository)
    {
        parent::__construct();
        $this->testimonialRepository = $testimonialRepository;
        $this->languageRepository = $languageRepository;
    }

    public function loadViewData(): array
    {
        $this->pageTitle(__('Sliders'));
        $this->tableColumns([
            __('ID'),
            __('Image'),
            __('Name'),
            __('Title'),
            __('Text'),
            __('Status'),
            __('Action'),
        ]);

        $this->jsColumns([
            'id' => 'testimonial.id',
            'image' => 'testimonial.image',
            'name' => 'testimonial.name_'.lang(),
            'title' => 'testimonial.title_'.lang(),
            'text' => 'testimonial.text_'.lang(),
            'status' => 'testimonial.status',
            'action' => '',
        ]);

        // $this->breadcrumb('');
        $this->filterIgnoreColumns(['action']);
        $this->addButton('system.testimonial.create','Add Testimonial');
        return $this->retunData;
    }

    public function loadDataTableData()
    {
        return Datatables::eloquent($this->testimonialRepository->getDataTableQuery())
            ->addColumn('id', '{{$id}}')
            ->addColumn('image', function ($data) {
                return datatableImage($data->image);
            })
            ->addColumn('name', function ($data) {
                return $data->{ 'name_'.lang() };
            })
            ->addColumn('title', function ($data) {
                return $data->{ 'title_'.lang() };
            })
            ->addColumn('text', function ($data) {
                return $data->{'text_'.lang()} ?? '';
            })
            ->addColumn('status', function($data) {
                return status_icon($data->status);
            })
            ->addColumn('action', function($data) {
                $this->actionButtons(datatable_menu_edit(route('system.testimonial.edit', $data->id), 'system.testimonial.edit'));
                return $this->actionButtonsRender($this->testimonialRepository->modelPath(), $data->id);
            })
            ->escapeColumns([])
            ->make(true);
    }

    public function create(): array
    {
        $this->pageTitle('Create Testimonial');
        $this->breadcrumb('Home');
        $this->breadcrumb('Testimonials', 'system.testimonial.index');
        $this->otherData([
            'languages' => $this->languageRepository->getWhere(['status' => StatusEnum::Enable->value]),
        ]);
        return $this->retunData;
    }

    public function store($request)
    {
        try {
            DB::beginTransaction();

            if ($request->has('image')) {
                $image = $request->file('image');
                $name_gen = hexdec(uniqid()). '.' .$image->getClientOriginalExtension();
                Image::make($image)->save('upload/home/' .$name_gen);
                $save_image = 'upload/home/'. $name_gen;
            }

            $data = [
                'title_ar' => $request['input']['lang'][2]['title'] ?? '',
                'title_en' => $request['input']['lang'][1]['title'] ?? '',
                'name_ar' => $request['input']['lang'][2]['name'] ?? '',
                'name_en' => $request['input']['lang'][1]['name'] ?? '',
                'text_ar' => $request['input']['lang'][2]['text'] ?? '',
                'text_en' => $request['input']['lang'][1]['text'] ?? '',
                'status' => $request['input']['status'],
                'image' => $save_image,
            ];

            $store = $this->testimonialRepository->store($data);
            DB::commit();
            return $store;
        } catch (\Exception $e) {
            DB::rollback();
            errorLog($e->getMessage());
            return false;
        }
    }

    public function edit($id): array
    {
        $this->pageTitle('Update Testimonial');
        $this->breadcrumb('Testimonials', 'system.testimonial.index');

        $this->otherData([
                'testimonial' => $this->testimonialRepository->find($id),
                'languages' => $this->languageRepository->getWhere(['status' => StatusEnum::Enable->value])
            ]);
        return $this->retunData;
    }

    public function update($id,$request)
    {
        try {
            DB::beginTransaction();
            $old_image = $request->old_image;

            if($request->file('image')) {
                @unlink($old_image);
                $image = $request->file('image');
                $name_gen = hexdec(uniqid()). '.' .$image->getClientOriginalExtension();
                Image::make($image)->save('upload/home/'.$name_gen);
                $filePath = 'upload/home/'.$name_gen;
                $this->testimonialRepository->update($id,['image' => $filePath]);
            }

            $data = [
                'title_ar' => $request['input']['lang'][2]['title'] ?? '',
                'title_en' => $request['input']['lang'][1]['title'] ?? '',
                'name_ar' => $request['input']['lang'][2]['name'] ?? '',
                'name_en' => $request['input']['lang'][1]['name'] ?? '',
                'text_ar' => $request['input']['lang'][2]['text'] ?? '',
                'text_en' => $request['input']['lang'][1]['text'] ?? '',
                'status' => $request['input']['status'],
            ];

            $update = $this->testimonialRepository->update($id,$data);
            DB::commit();
            return $update;
        } catch (\Exception $e) {
            DB::rollback();
            errorLog($e->getMessage());
            return false;
        }
    }

}
