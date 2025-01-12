<?php

namespace App\Services;


use App\Enums\StatusEnum;
use App\Repositories\Blog\BlogRepository;
use App\Repositories\Language\LanguageRepository;
use Illuminate\Support\Facades\DB;
use Intervention\Image\Facades\Image;
use Datatables;


class BlogService extends BaseService
{
    protected $blogRepository,$languageRepository;

    public function __construct(BlogRepository $blogRepository, LanguageRepository $languageRepository)
    {
        parent::__construct();
        $this->blogRepository = $blogRepository;
        $this->languageRepository = $languageRepository;
    }

    public function loadViewData(): array
    {
        $this->pageTitle(__('Blogs'));
        $this->tableColumns([
            __('ID'),
            __('thumb'),
            __('title'),
            __('name'),
            __('status'),
            __('Action'),
        ]);

        $this->jsColumns([
            'id' => 'post.id',
            'thumb' => 'post.thumb',
            'title' => 'post.title',
            'name' => 'post.name',
            'status' => 'post.status',
            'action' => '',
        ]);

        $this->filterIgnoreColumns(['action']);
        $this->addButton('system.blog.create','Add Blog');
        return $this->retunData;
    }

    public function loadDataTableData()
    {
        return Datatables::eloquent($this->blogRepository->getDataTableQuery())
            ->addColumn('id', '{{$id}}')
            ->addColumn('image', function ($data) {
                return datatableImage($data->image);
            })
            ->addColumn('status', function($data) {
                return status_icon($data->status);
            })
            ->addColumn('action', function($data) {
                $this->actionButtons(datatable_menu_edit(route('system.blog.edit', $data->id), 'system.blog.edit'));
                return $this->actionButtonsRender($this->blogRepository->modelPath(), $data->id);
            })
            ->escapeColumns([])
            ->make(true);
    }

    public function create(): array
    {
        $this->pageTitle('Create Blog');
        $this->breadcrumb('Home');
        $this->breadcrumb('Blogs', 'system.blog.index');
        $this->otherData([
            'languages' => $this->languageRepository->getWhere(['status' => StatusEnum::Enable->value]),
        ]);
        return $this->retunData;
    }

    public function store($request)
    {
        try {
            DB::beginTransaction();

            $save_image = null;
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

            $store = $this->blogRepository->store($data);
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
        $this->pageTitle('Update Blog');
        $this->breadcrumb('Blogs', 'system.blog.index');

        $this->otherData([
                'result' => $this->blogRepository->find($id),
                'languages' => $this->languageRepository->getWhere(['status' => StatusEnum::Enable->value])
            ]);
        return $this->retunData;
    }

    public function update($id,$request)
    {
        try {

            DB::beginTransaction();
            $testimonial = $this->blogRepository->find($id);

            if (!$testimonial) {
                throw new \Exception("No testimonial found for ID: " . $id);
            }

            if ($request->file('image')) {
                if (file_exists($testimonial->image)) {
                    @unlink($testimonial->image);
                }
                $image = $request->file('image');
                $name_gen = hexdec(uniqid()) . '.' . $image->getClientOriginalExtension();
                Image::make($image)->save('upload/home/' . $name_gen);
                $filePath = 'upload/home/' . $name_gen;
                $testimonial->image = $filePath;
            }

            $testimonial->title_ar = $request['input']['lang'][2]['title'] ?? '';
            $testimonial->title_en = $request['input']['lang'][1]['title'] ?? '';
            $testimonial->name_ar = $request['input']['lang'][2]['name'] ?? '';
            $testimonial->name_en = $request['input']['lang'][1]['name'] ?? '';
            $testimonial->text_ar = $request['input']['lang'][2]['text'] ?? '';
            $testimonial->text_en = $request['input']['lang'][1]['text'] ?? '';
            $testimonial->status = $request['input']['status'];
            $testimonial->rating = $request['input']['rating'];

            $testimonial->save();
            DB::commit();
            return $testimonial;
        } catch (\Exception $e) {
            DB::rollback();
            errorLog($e->getMessage());
            return false;
        }
    }

}
