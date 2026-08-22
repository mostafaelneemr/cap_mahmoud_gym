<?php

namespace App\Services;

use App\Models\setting;
use App\Repositories\Setting\SettingRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
use Spatie\Image\Image;

class SettingService extends BaseService
{
    protected $settingRepository;

    public function __construct(SettingRepository $settingRepository)
    {
        parent::__construct();
        $this->settingRepository = $settingRepository;
    }

    public function loadViewData()
    {
        $settingGroups = $this->settingRepository->getDataTableQuery();

        $setting = [];
        foreach ($settingGroups as $value) {
            $setting[] = $this->settingRepository->getSetting($value);
        }

        $this->otherData([
            'settingGroups' => $settingGroups,
            'setting' => $setting,
        ]);

        return $this->retunData;
    }

    public function update($request)
    {
        DB::beginTransaction();
        try {

            $data = $request->all();

            $settingTable = Setting::get(['name', 'input_type', 'value']);
            $uploadDir = public_path('upload/setting');

            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            foreach ($settingTable as $value) {

                switch ($value->input_type) {
                    case 'image':
                        $validator = Validator::make($request->all(), [
                            $value->name => 'nullable|image',
                        ]);

                        if (!$validator->fails() && $request->hasFile($value->name) && $request->file($value->name)->isValid()) {

                            // 🗑️ 1. جلب مسار الصورة القديمة ومسحها إن وجدت
                            $oldSetting = Setting::where('name', $value->name)->first();
                            if ($oldSetting && !empty($oldSetting->value)) {
                                $oldFilePath = public_path($oldSetting->value);
                                if (File::exists($oldFilePath)) {
                                    File::delete($oldFilePath); // مسح الصورة القديمة من السيرفر
                                }
                            }

                            // 📤 2. تجهيز ورفع الصورة الجديدة
                            $file = $request->file($value->name);
                            $name_gen = hexdec(uniqid()) . '.' . $file->getClientOriginalExtension();
                            $save_url = 'upload/setting/' . $name_gen;
                            $destinationPath = public_path($save_url);

                            // معالجة الصورة بـ Spatie Image
                            $image = Image::load($file->getRealPath());

                            if ($value->name === 'site_logo') {
                                $image->width(230)->height(70);
                            }

                            $image->save($destinationPath);

                            // 💾 3. تحديث قايمة البيانات بالمسار الجديد
                            Setting::where('name', $value->name)
                                ->where('is_visible', 'yes')
                                ->update(['value' => $save_url]);
                        }
                        break;

                    default:
                        if (isset($data[$value->name])) {
                            $valueToUpdate = $data[$value->name];
                            if (is_array($valueToUpdate)) {
                                $valueToUpdate = @serialize($valueToUpdate);
                            }
                            Setting::where(['name' => $value->name])->where('is_visible', 'yes')->update(['value' => $valueToUpdate]);
                        } else {
                            Setting::where(['name' => $value->name])->where('is_visible', 'yes')->update(['value' => '']);
                        }
                        break;
                }

            }

            DB::commit();
            return ['status' => true];
        } catch (\Exception $e) {
            DB::rollback();
            errorLog($e->getMessage());
            return false;
        }
    }
}
