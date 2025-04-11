<?php

use App\Models\{Active_section,Setting};
use App\Enums\{DefaultStatus, EventEnum, ReadMessageEnum, SliderTypeEnum, StatusEnum, TrainingLevelEnum};
use Illuminate\Support\Facades\Log;
use Bugsnag\BugsnagLaravel\Facades\Bugsnag;
use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumberUtil;

function img($path)
{
    return asset('storage/' . $path);
}

function exportXLS($title, $heads, $exData, $callback)
{
    return view('system.partials.divs.export_xls', compact('title', 'heads', 'exData', 'callback'));
}

function direction()
{
    if (lang() == 'ar') {
        return '.rtl';
    } else {
        return '';
    }
}

function lang()
{
    $language = 'en';
    if (app()->getLocale() == 'ar')
        $language = 'ar';
    return $language;
}

function opencart_lang()
{
    $language = 'en-gb';
    if (app()->getLocale() == 'ar')
        $language = 'ar';
    return $language;
}

function languageId()
{
    $language_id = 1;
    if (lang() == 'ar')
        $language_id = 2;
    return $language_id;
}

function whereBetween(&$eloquent, $columnName, $form, $to)
{
    if (!empty($form) && empty($to)) {
        $eloquent->whereRaw("$columnName >= ?", [$form]);
    } elseif (empty($form) && !empty($to)) {
        $eloquent->whereRaw("$columnName <= ?", [$to]);
    } elseif (!empty($form) && !empty($to)) {
        $eloquent->where(function ($query) use ($columnName, $form, $to) {
            $query->whereRaw("$columnName BETWEEN ? AND ?", [$form, $to]);
        });
    }
}

function imageResize($imagePath, $width, $height)
{
    $vImagePath = $imagePath;
    $imagePath = storage_path('app/public/' . $imagePath);

    if (File::exists($imagePath) && explode('/', File::mimeType($imagePath))[0] == 'image') {
        $resizedFileName = File::dirname($imagePath) . '/' . File::name($imagePath) . '_' . $width . 'X' . $height . '.' . File::extension($imagePath);

        if (!Storage::exists($resizedFileName)) {
            Image::make($imagePath)
                ->resize($width, $height)
                ->save($resizedFileName);
        }

        return File::dirname($vImagePath) . '/' . File::name($imagePath) . '_' . $width . 'X' . $height . '.' . File::extension($imagePath);

        //        return $resizedFileName;
    }


    return false;
}




function image($imagePath, $width, $height)
{
    return imageResize($imagePath, $width, $height);
}


function generateMenu(array $array)
{
    return view('system.partials.divs.generate_menu', compact('array'));
}


function MenuRoute($routename)
{
    $requestRoute = request()->route()->getName();
    if (is_array($routename)) {
        if (in_array($requestRoute, $routename)) {
            return true;
        }
        return false;
    }

    return ($requestRoute == $routename) ? true : false;
}

function flash_msg($type, $msg)
{
    \request()->session()->flash('msg', $msg);
    \request()->session()->flash('type', $type);
}

function ignoredRoutes()
{
    return [
        'log-viewer.index',
        'system.change',
        'system.change',
        'system.dashboard',
        'login',
        'logout',
        'system.misc.ajax',
        'system.user.change-password',
        'system.user.change-password-post',
        'system.notifications.url',
        'system.notifications.index',
        'system.user.user-sessions',
        'system.user.profile',
        'system.user.show-profile',
        'system.user.update-profile',
        'system.reset-password',
    ];
}

function userCan($routename, $userId = null)
{

    if ($userId && $userId == request()->user()->id) {
        $userId = null;
    }

    $userObj = $userId ? \App\Models\User::where('id', $userId)->first() : auth('user')->user();

    static $permissions;
    if (is_null($permissions)) {
        $permissions = \App\Models\User::UserPerms($userObj->id)->toArray();
    }
    $permissions = array_merge($permissions, ignoredRoutes());
    if (is_array($routename)) {
        $arr = array_diff($routename, $permissions);
        return (!$arr) ? true : ((count($arr) == count($routename)) ? false : true);
    } else {
        return (in_array($routename, $permissions)) ? true : false;
    }
}

function formError($error, $fieldName, $checkHasError = false)
{

    if ($checkHasError) {
        if ($error->has($fieldName)) {
            return ' has-danger';
        } else {
            return null;
        }
    }

    if ($error->has($fieldName)) {
        return view('system.partials.errors.form_error', compact('error', 'fieldName', 'checkHasError'));
    } else {
        return null;
    }
}


function label($text, $required = '')
{
    return view('system.partials.labels.label', compact('text', 'required'));
}

function status_select_data()
{
    return ['' => '', '1' => __('Active'), '0' => __('In-Active')];
}

function force_reset_password_select_data()
{
    return ['' => '', 1 => __('Active'), 0 => __('In-Active')];
}

function filter_btn()
{
    return view('system.partials.buttons.filter_btn');
}

function download_btn()
{
    return view('system.partials.buttons.download_btn');
}

function ajax_btn($action, $title, $params = [])
{
    return view('system.partials.buttons.ajax_btn', compact('action', 'title', 'params'));
}


function delete_links($route, $link, $params = [], $row_id = null)
{
    if (userCan($route))
        return view('system.partials.buttons.delete_links', compact('route', 'link','params','row_id'));
}

function add_links($link, $route, $title = '')
{
    if (userCan($route))
        return  view('system.partials.links.add_links', compact('title', 'link','route'));
}

function show_links($link, $route)
{
    if (userCan($link))
        return view('system.partials.links.show_links', compact('route', 'link','route'));
}

function edit_links($link, $route)
{
    if (userCan($link))
        return view('system.partials.links.edit_links', compact('route','link'));
}


function datatable_links($link, $route, $label, $attributes = [])
{
    if (userCan($link))
        return view('system.partials.links.datatable_links', compact('link', 'route', 'label', 'attributes'));
    return $label;
}
function view_delete($link, $route, $row_id = null)
{
    if (userCan($route))
        return view('system.partials.buttons.view_delete', compact('link', 'route', 'row_id'));
}

function datatable_menu_delete($link, $route, $row_id = null)
{
    if (userCan($route))
        return view('system.partials.buttons.datatable_menu_delete', compact('link', 'route', 'row_id'));
}

function datatable_site_link($ex_url)
{
    return view('system.partials.links.datatable_site_link', compact('ex_url'));
}

function datatable_menu_log($link)
{
    return view('system.partials.links.datatable_menu_log', compact('link'));
}

function datatable_menu_edit($link, $route)
{
    if (userCan($route))
        return view('system.partials.links.datatable_menu_edit', compact('link','route'));
}

function datatable_menu_button($link, $route, $icon = 'fa-check', $status = 'approve', $rowId = null )
{
    $btnClass = $icon == 'fa-check' ? 'btn-success' : 'btn-danger';
    if (userCan($route))
        return view('system.partials.buttons.datatable_menu_button', compact('link', 'route', 'icon', 'status', 'rowId', 'btnClass'));
}

function datatable_menu_link_to($link, $route, $icon = 'fa-check', $status = 'approve', $rowId = null)
{
    $btnClass = $icon == 'fa-check' ? 'btn-success' : 'btn-danger';
    if (userCan($route))
        return view('system.partials.links.datatable_menu_link_to', compact('link', 'route', 'icon', 'status', 'rowId', 'btnClass'));
}

function datatable_menu_show($link, $route, $target = null)
{
    if (userCan($route))
        return view('system.partials.links.datatable_menu_show', compact('link', 'route', 'target'));
}

function datatable_menu_link($link, $route,$label, $target = null)
{
    if (userCan($route))
        return view('system.partials.links.datatable_menu_link', compact('link', 'route','label', 'target'));
}

function datatable_menu_popup($linkId, $route, $type)
{
    if (userCan($route))
        return view('system.partials.buttons.datatable_menu_popup', compact('linkId', 'route', 'type'));
}

function link_modal($route, $title, $modalId, $icon)
{
    $color = ($modalId == 'subfolder-modal') ? 'info' : 'success';
    if (userCan($route))
        return view('system.partials.buttons.link_modal', compact('route', 'title', 'modalId', 'color', 'icon'));
}
function link_add_modal_icon($route, $modalId,$title =null)
{
    if (userCan($route))
        return view('system.partials.buttons.link_add_modal_icon', compact('route', 'modalId','title'));
}
function link_modal_edit($route, $link, $modalId, $rowId = null, $prevStatus = null, $type = null, $updateUrl = null, $class = 'btn-sm')
{
    if (userCan($route))
        return view('system.partials.buttons.link_modal_edit', compact('route', 'link', 'modalId', 'rowId', 'prevStatus', 'type', 'updateUrl', 'class'));
}

function link_modal_show($route, $link, $modalId, $rowId = null, $prevStatus = null, $type = null, $updateUrl = null, $class = 'btn-sm')
{
    if (userCan($route))
        return view('system.partials.buttons.link_modal_show', compact('route', 'link', 'modalId', 'rowId', 'prevStatus', 'type', 'updateUrl', 'class'));
}

function datatable_menu_show_onclick($link, $route, $id)
{
    if (userCan($route))
        return view('system.partials.buttons.datatable_menu_show_onclick', compact('route', 'link', 'id'));
}


function links($link, $route, $label, $attributes = [])
{
    if (userCan($link))
        return view('system.partials.links.links', compact('link', 'route', 'label', 'attributes'));
}
function datatable_text_modal($route, $link, $modalId, $rowId = null, $prevStatus = null, $type = null, $updateUrl = null, $title)
{
    return view('system.partials.links.datatable_text_modal', compact('link', 'route', 'modalId', 'rowId', 'prevStatus', 'type', 'updateUrl', 'title'));
}

function recursiveFind(array $array, $needle)
{
    $response = [];
    $iterator = new RecursiveArrayIterator($array);
    $recursive = new RecursiveIteratorIterator(
        $iterator,
        RecursiveIteratorIterator::SELF_FIRST
    );
    foreach ($recursive as $key => $value) {
        if ($key === $needle) {
            $response[] = $value;
        }
    }
    return ((count($response) == '1') ? $response : $response);
}

function getRealIP()
{
    return env('HTTP_CF_CONNECTING_IP') ?? env('REMOTE_ADDR');
}

function getUserAgent()
{
    return request()->server('HTTP_USER_AGENT');
}

function status_icon($status)
{
    return view('system.partials.icons.status_icon', compact('status'));
}

function status_style($status)
{
    return view('system.partials.divs.status_style', compact('status'));
}

function BooleanStyle($boolean)
{
    return view('system.partials.spans.boolean_style', compact('boolean'));
}
function errorLog($message)
{
    Bugsnag::notifyException(new RuntimeException($message));
    Log::error($message);
}

function fixMobileNumber($Mobile)
{
    $regionCode = "SA";
    $telephone = toEnglishNumrics($Mobile);
    if (strncmp($telephone, "9", 1) === 0) {
        $telephone = "+" . $telephone;
        $regionCode = null;
    }
    $phoneUtil = PhoneNumberUtil::getInstance();
    try {
        $parsed_telephone = $phoneUtil->parse($telephone, $regionCode);
        (string)$data['country_code'] = (string)$parsed_telephone->getCountryCode();
        (string)$data['national_number'] = (string)$parsed_telephone->getNationalNumber();
        $Mobile = $data['country_code'] . $data['national_number'];
    } catch (NumberParseException $e) {
        errorLog($e->getMessage());
    }
    return $Mobile;
}
function toEnglishNumrics($number)
{
    $number = str_replace('+', '', $number);
    $number = str_replace('٠', '0', $number);
    $number = str_replace('١', '1', $number);
    $number = str_replace('٢', '2', $number);
    $number = str_replace('٣', '3', $number);
    $number = str_replace('٤', '4', $number);
    $number = str_replace('٥', '5', $number);
    $number = str_replace('٦', '6', $number);
    $number = str_replace('٧', '7', $number);
    $number = str_replace('٨', '8', $number);
    $number = str_replace('٩', '9', $number);
    $number = str_replace(' ', '', $number);

    return $number;
}
function preparePhone($phone)
{
    $new_phone = preg_replace('/^(\+|0)+/', '', $phone);
    //    $is_matched = preg_match('/^[966|971|974|968|965|973]/',$new_phone);
    $is_matched = substr($new_phone, 0, 3);
    if ($is_matched != '966') {
        $new_phone = '966' . $new_phone;
    }
    return $new_phone;
}

function changeDateFormat($date)
{
    $date = new \DateTime($date);
    return $date->format('Y-m-d H:i');
}

function imageIconLink($path)
{
    return view('system.partials.links.image_icon_link', compact('path'));
}

function fixSeo($string)
{
    $string = str_replace(' -', '', trim($string));
    $string = str_replace('&', '', trim($string));
    $string = str_replace('amp;', '', trim($string));
    $string = str_replace('`', '', $string);
    try {
        $string = str_replace("'", '', $string);
    } catch (Exception $e) {
    }
    $string = str_replace(' ', '-', $string);
    $string = str_replace('%', 'percent', $string);
    $string = str_replace('+', 'plus', $string);

    $string = trim($string);

    return strtolower($string);
}



function selectAllCheckbox()
{
    return view('system.partials.divs.select_all_checkbox');
}



function amount($number, $decimal = 2)
{
    $ex = explode('.', $number);
    if (isset($ex[1]) && $ex[1] > 0  && !empty($decimal)) {
        return number_format($number, $decimal);
    } else {
        return  number_format($number, 0);
    }
}

function break_word($text)
{
    return view('system.partials.spans.break_word' , compact('text'));
}

function get_types_trans($text)
{
    $types = get_types();

    return isset($types[$text]) ? $types[$text] : '';
}


function language_data()
{
    return ['' => '', 'en-gb' => __('English'), 'ar' => __('عربي')];
}

function color_span($color, $status = null)
{
    $status = $status ? $status : $color;
    return view('system.partials.spans.color_span', compact('color', 'status'));
}

function phone_direction($phone)
{
    return view('system.partials.spans.phone_direction', compact('phone'));
}


function datatableImage($path)
{
    return view('system.partials.imgs.datatable_image', compact('path'));
}


function datatable_badge($value,$class){
    return view('system.partials.spans.badge', compact('value', 'class'));

}


function status_enum($key = null, $withLang = false)
{
    if ($withLang) {
        return StatusEnum::values_lang();
    }
    if (!empty($key))
        return StatusEnum::values()[$key];

    return StatusEnum::values();
}

function datatable_menu_show_user($link, $route, $target = null)
{
    if (userCan($route))
        return view('system.partials.links.datatable_menu_show_user', compact('link', 'route', 'target'));
}
function icon_user_id($user_id)
{
    return view('system.partials.icons.icon_user_id', compact('user_id'));
}

function default_status($key = null, $withLang = false)
{
    if ($withLang) {
        return DefaultStatus::values_lang();
    }
    if (!empty($key))
        return DefaultStatus::values()[$key];

    return DefaultStatus::values();
}

function slider_type_enum($key = null, $withLang = false)
{
    if ($withLang) {
        return SliderTypeEnum::values_lang();
    }
    if (!empty($key))
        return SliderTypeEnum::values()[$key];

    return SliderTypeEnum::values();
}

function Setting($name)
{
     return Setting::where('name',$name)->first();
}

function rate_values()
{
    return [1 => __('1'), 2 => __('2'), 3 => __('3'), 4 => __('4'), 5 => __('5')];
}

function event_enum($key = null, $withLang = false)
{
    if ($withLang) {
        return EventEnum::values_lang();
    }
    if (!empty($key))
        return EventEnum::values()[$key];

    return EventEnum::values();
}

function datatable_read_button_message($link, $route, $icon = 'fa-check', $status = 'approve', $rowId = null)
{
    $btnClass = $icon == 'fa-check' ? 'btn-success' : 'btn-danger';
//    if (userCan($route))
        return view('system.partials.buttons.datatable_read_button_message', compact('link', 'route', 'icon', 'status', 'rowId', 'btnClass'));
}

function message_read($key = null, $withLang = false, $replacePendingValue = false)
{
    if ($withLang) {
        return ReadMessageEnum::values_lang($replacePendingValue);
    }
    if (!empty($key))
        return ReadMessageEnum::values()[$key];

    return ReadMessageEnum::values();
}

if (!function_exists('notify')) {
    function notify($type, $message)
    {
        session()->flash('notify', [
            'type' => $type,
            'message' => $message,
        ]);
    }
}

function active_section($name)
{
    return Active_section::where('name',$name)->first()->value == DefaultStatus::Active->value;
}

function training_level($key = null, $withLang = false, $replacePendingValue = false)
{
    if ($withLang) {
        return TrainingLevelEnum::values_lang($replacePendingValue);
    }
    if (!empty($key))
        return TrainingLevelEnum::values()[$key];

    return TrainingLevelEnum::values();
}




















