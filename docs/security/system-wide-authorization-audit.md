# تقرير التدقيق الأمني الشامل ونظام الصلاحيات (Task 6 — System-Wide Authorization & Security Audit)

**تاريخ التدقيق:** 20 أكتوبر 2026  
**الفرع:** `refactor-project`  
**الحالة:** تم بنجاح والتحقق بالاختبارات المؤتمتة (ACCEPT)

---

## 1. ملخص تنفيذي (Executive Summary)

تم تنفيذ تدقيق أمني شامل لنظام إدارة النادي الرياضي (*Captain Mahmoud Gym Management System*) بهدف اكتشاف الثغرات الأمنية وفجوات الصلاحيات عبر جميع وحدات ومسارات النظام، بعد استكمال المهام من 1 إلى 5 التي ركزت على عزل بيئة الاختبارات، وتأمين قياسات المتدربين، واستقرار إدارة الحسابات، وتناسق قاعدة البيانات.

تم حصر سطح الهجوم بالكامل، واختبار المسارات الإدارية، ونقاط النهاية البرمجية (APIs)، وعمليات إدارة التغذية، والتمارين، والمستخدمين، والإعدادات، والصفحات التعريفية. وأسفر التدقيق عن اكتشاف وعلاج عيوب أمنية حرجة ومؤكدة بأقل التغييرات المستهدفة ودون زعزعة استقرار النظام القائم.

---

## 2. نموذج الصلاحيات والأدوار المكتشف في الشيفرة (Role & Permission Model)

يعتمد التطبيق على بنية هجينة لإدارة الصلاحيات تعتمد على الحقول التالية في جدول `user`:
1. **المدراء والمشرفون (`user_type == 1` أو `null`):**
   - ترتبط حساباتهم بجدول مجموعات الصلاحيات (`permission_groups`) عبر `permission_group_id`.
   - يتم تخزين الصلاحيات الخاصة بكل مجموعة في جدول `permissions` كربط بين `permission_group_id` واسم المسار (`route_name`).
   - يقوم وسيط التحقق (`App\Http\Middleware\Permission`) بالتحقق من وجود اسم المسار الحالي ضمن الصلاحيات الممنوحة للمستخدم أو ضمن المسارات المستثناة (`ignoredRoutes()`).
   - تمكين خاصية التخزين المؤقت عبر `Cache::remember("user_perms_{$id}")`.

2. **المتدربون (`user_type == 2` أو `user_type == 3`):**
   - `user_type = 2`: متدرب مسجل بالنظام (حساب قياسي).
   - `user_type = 3`: متدرب تدريب أونلاين/مخصص.
   - لا ترتبط حسابات المتدربين بمجموعات صلاحيات موظفين، بل تخضع لقائمة بيضاء صارمة ومحدودة داخل وسيط `Permission.php`:
     - لوحة تحكم المتدرب: `system.dashboard.trainer`
     - الخطة الغذائية الشخصية: `system.nutrition.my-plan`
     - متابعة القياسات والصور: مسارات `system.trainee-progress.*`
     - إنهاء المحاكاة ومسارات الجلسات: `system.impersonate.leave`, `logout`, `auth.google.*`
   - أي محاولة من متدرب للوصول إلى مسار إداري خارج القائمة يتم تحويله تلقائياً وبأمان إلى `system.dashboard.trainer`.

---

## 3. سطح الهجوم والوحدات والمسارات التي خضعت للتدقيق (Attack Surface & Audited Modules)

تم فحص ومراجعة كافة المسارات المسجلة في `routes/system.php` و `routes/web.php` و `routes/api.php`:

| الوحدة / الموديول | المسارات المفحوصة | وسيط المصادقة والتحقق | ملاحظات التحقق الأمني |
| :--- | :--- | :--- | :--- |
| **إدارة المستخدمين (`UserController`)** | `system.user.*`, `system.user.profile`, `system.user.update-profile`, `system.get-user-activity-log`, `system.get-auth-session` | `auth:user`, `Permission` | مسار تحديث الملف الشخصي مستثنى من جدول الصلاحيات، وخضع لتدقيق Mass Assignment. |
| **إدارة المتدربين (`TraineeController`)** | `system.trainee.*`, `system.trainee.reset-plan`, `system.trainee.restore-plan`, `system.trainee.impersonate` | `auth:user`, `Permission` | فحص حماية محاكاة الحسابات، ومنع الحذف وتصفير الخطط من قبل المتدربين. |
| **متابعة القياسات والصور (`TraineeProgressController`)** | `system.trainee-progress.*` (index, store, update-notes, destroy, destroy-photo, compare) | `auth:user`, `Permission` | تم التأكد من استمرار سريان حمايات Task 2 ومنع الـ IDOR. |
| **إدارة التغذية (`NutritionController`)** | `system.nutrition.*` (index, create, store, show, edit, update, destroy, my-plan) | `auth:user`, `Permission` | تم اكتشاف ثغرة IDOR في `my-plan` وتصحيحها فوراً. |
| **إدارة التمارين والبرامج (`WorkoutController`)** | `system.workout.*`, `updateDay`, `toggleDayStatus`, `storeInlineDay` | `auth:user`, `Permission` | تم إلغاء استثناء مسار `create` من الصلاحيات وتأمين التعديل. |
| **مجموعات الصلاحيات (`PermissionGroupsController`)** | `system.permission-group.*` | `auth:user`, `Permission` | مقصورة بالكامل على المشرفين المعتمدين برمجياً. |
| **إعدادات النظام والموقع (`SettingController`, `WebsiteController`)** | `system.setting.*`, `system.website.*`, `system.website.settings.update` | `auth:user`, `Permission` | مسارات إدارية محمية بوسيط الصلاحيات. |
| **رسائل التواصل وطلبات الانضمام (`MessageController`, `JoinUsController`)** | `system.message.*`, `system.join-us.*` | `auth:user`, `Permission` | عمليات التحديث محمية، وعمليات الحذف معطلة على مستوى المسارات. |
| **واجهة برمجة التطبيقات والموقع العام (`routes/api.php`, `routes/web.php`)** | `api/v1/*`, `web.home`, `web.contact`, `web.join-us` | `throttle:10,1`, `throttle:60` | نقاط النهاية العامة تخضع لـ Rate Limiting مشدد، ولا تعرض أي بيانات حساسة بدون تصريح. |

---

## 4. الثغرات المكتشفة وتصنيف خطورتها والحلول المنفذة (Vulnerabilities & Fixes)

### 1) ثغرة تجاوز الصلاحيات المباشر (IDOR) في الخطة الغذائية للمتدرب
- **الملف:** `app/Modules/System/NutritionController.php`
- **الدالة:** `myPlan(Request $request)`
- **التصنيف والخطورة:** **حرجة جداً (High / Critical)**
- **طبيعة الخلل:** مسار `system.nutrition.my-plan` متاح للمتدربين بحكم طبيعته. كان يستقبل المعاملات `trainee_id` أو `trainee` (المشفر) دون التحقق مما إذا كان السجل المستهدف يخص المتدرب المسجل دخوله حالياً. كان بإمكان أي متدرب تغيير المعرف واستعراض الخطة الغذائية الكاملة والملاحظات الخاصة بأي متدرب آخر في النظام.
- **الحل المنفذ:** تم تطبيق تحقق برمجي صارم: إذا كان المستخدم متدرباً (`in_array((int) $user->user_type, [2, 3], true)`)، يتم إلزامه بسجله الشخصي فقط. في حال محاولة تمرير معرف مشفر أو رقمي لمتدرب آخر، يتم رفض الطلب فوراً برمز `403 Forbidden`.

### 2) ثغرة تصعيد الصلاحيات عبر الإسناد الجماعي (Privilege Escalation via Mass Assignment)
- **الملف:** `app/Modules/System/UserController.php`
- **الدالة:** `updateProfile(ProfileFormRequest $request)`
- **التصنيف والخطورة:** **عالية (High)**
- **طبيعة الخلل:** دالة تحديث الملف الشخصي كانت تمرر كافة مدخلات الطلب `$request->all()` إلى `UserService::update` ومستودع المستخدمين. وبما أن مصفوفة `$fillable` في نموذج `User` تتضمن `permission_group_id` و `user_type` و `status`، كان يمكن لأي مستخدم إرسال معاملات خبيثة وترقية حسابه إلى مشرف عام أو تعديل نوع حسابه.
- **الحل المنفذ:** تم عزل وتطهير المدخلات داخل `UserController::updateProfile` بحيث يقتصر التحديث على الحقول الآمنة للملف الشخصي فقط (`email`, `default_language`, `password`, `telephone`, `image`)، واستبعاد أي حقول إدارية تماماً.

### 3) تجاوز التحقق من الصلاحيات لمسار إنشاء التمارين في `ignoredRoutes()`
- **الملف:** `app/Helpers/Global.helpers.php`
- **الدالة:** `ignoredRoutes()`
- **التصنيف والخطورة:** **متوسطة (Medium)**
- **طبيعة الخلل:** كان مسار `system.workout.create` مضافاً بالخطأ ضمن مصفوفة `ignoredRoutes()`. وكان هذا الاستثناء يلغي فحص الصلاحية في وسيط `Permission.php`، مما يتيح لأي موظف بدون صلاحية `create-workout` فتح شاشة إنشاء التمارين.
- **الحل المنفذ:** تم حذف `'system.workout.create'` من `ignoredRoutes()`، وضمان خضوعه لصلاحية `'create-workout'` المعتمدة في `Permissions.php`.

### 4) عدم اتساق فحص المتدربين وتسرب الذاكرة في دالة المساعدة `userCan()`
- **الملف:** `app/Helpers/Global.helpers.php`
- **الدالة:** `userCan($routename, $userId = null)`
- **التصنيف والخطورة:** **متوسطة (Medium)**
- **طبيعة الخلل:**
  - كانت الدالة تتحقق فقط من `$userObj->user_type == 2` متجاهلة المتدربين الأونلاين (`user_type == 3`).
  - كانت تفتقر إلى مسار `'system.impersonate.leave'` ضمن قائمة مسارات المتدربين.
  - كان المتغير الثابت `static $permissions` يحفظ صلاحيات أول مستخدم تم فحصه للعملية ككل، مما يسبب تداخلاً وتسرباً للصلاحيات بين المستخدمين في البيئات المستمرة والاختبارات.
- **الحل المنفذ:**
  - دعم كلا نوعي المتدربين `[2, 3]`.
  - إضافة مسار الخروج من المحاكاة.
  - تحويل التخزين الثابت ليصبح مفهرساً بمعرف المستخدم `$permissions[$userIdKey]`.

### 5) خطأ استثناء (Fatal Error) عند عرض ترويسة النظام للمتدربين
- **الملف:** `resources/views/system/partials/header.blade.php`
- **التصنيف والخطورة:** **متوسطة (Medium / Stability)**
- **طبيعة الخلل:** كانت الترويسة تحاول قراءة `auth()->user()->permission_group->name` بشكل مباشر. وبما أن حسابات المتدربين لا تملك `permission_group_id`، كان عرض أي صفحة تحتوي الترويسة الرئيسية للمتدربين ينهار بخطأ 500 (`Attempt to read property "name" on null`).
- **الحل المنفذ:** استخدام عامل الاندماج الآمن: `{{ auth()->user()->permission_group->name ?? __('Trainee') }}`.

---

## 5. الاختبارات الأمنية التوكيدية والانحدارية (Regression Security Tests)

تم إنشاء مجموعة اختبارات مميكنة متخصصة وشاملة في الملف:
[`tests/Feature/SystemAuthorizationSecurityTest.php`](file:///e:/laragon/www/cap_mahmoud_gym/tests/Feature/SystemAuthorizationSecurityTest.php)

تغطي السيناريوهات الأمنية الـ 12 التالية:
1. `test_unauthenticated_requests_to_system_routes_are_redirected_to_login`: التحقق من حظر كافة طلبات الزوار غير المسجلين للمسارات المحمية وتحويلهم لصفحة الدخول.
2. `test_unauthorized_staff_user_cannot_access_workout_creation_page`: منع الموظف غير المصرح له من الوصول لصفحة إنشاء التمارين برمز `401 Unauthorized`.
3. `test_unauthorized_user_cannot_access_administrative_modules_without_permissions`: منع الموظف بدون صلاحيات من الوصول للمستخدمين والإعدادات.
4. `test_trainee_is_redirected_away_from_administrative_routes`: إعادة توجيه المتدربين (النوعين 2 و 3) تلقائياً عند محاولة الوصول لأي مسار إداري.
5. `test_trainee_can_view_own_nutrition_plan`: تمكين المتدرب من استعراض خطته الغذائية الخاصة بنجاح (كود 200).
6. `test_trainee_cannot_view_another_trainees_nutrition_plan_via_raw_id_idor`: التحقق الحرج من إحباط محاولة IDOR بالمعرف الرقمي ورفضها برمز `403 Forbidden`.
7. `test_trainee_cannot_view_another_trainees_nutrition_plan_via_encrypted_id_idor`: التحقق الحرج من إحباط محاولة IDOR بالمعرف المشفر ورفضها برمز `403 Forbidden`.
8. `test_coach_can_view_any_trainees_nutrition_plan`: التأكد من صلاحية المدرب والمشرف في استعراض خطط المتدربين للعمليات التشغيلية.
9. `test_profile_update_prevents_privilege_escalation_via_mass_assignment`: إثبات فشل أي محاولة لتصعيد الصلاحيات بحقن `permission_group_id` أو `user_type`.
10. `test_trainee_cannot_delete_trainees_or_users`: منع المتدربين من حذف أي حسابات.
11. `test_trainee_cannot_reset_or_restore_workout_plans`: منع المتدربين من تصفير أو استعادة البرامج التدريبية.
12. `test_user_can_helper_respects_trainee_roles_and_cache_isolation`: التحقق من دقة دالة `userCan` للمتدربين وعزل صلاحيات كل مستخدم.

---

## 6. نتائج تشغيل الاختبارات المؤتمتة (Test Execution Results)

تم تشغيل الاختبارات المؤتمتة باستخدام بيئة SQLite المعزولة في الذاكرة بالكامل دون المساس بأي قاعدة بيانات حقيقية:

```bash
# 1. تشغيل اختبارات الأمان الجديدة المستهدفة:
php vendor/phpunit/phpunit/phpunit tests/Feature/SystemAuthorizationSecurityTest.php
# النتيجة: Tests: 12, Assertions: 75, Failures: 0, Errors: 0. Time: 00:01.801 (100% نجاح)

# 2. تشغيل حزمة الاختبارات الكاملة للنظام (Tasks 1 to 6):
php vendor/phpunit/phpunit/phpunit
# النتيجة: Tests: 65, Assertions: 394, Failures: 0, Errors: 0. Time: 00:08.494 (100% نجاح)
```

جميع الاختبارات التراكمية السابقة لـ Task 1 و Task 2 و Task 3 و Task 4 و Task 5 تعمل بكفاءة وبدون أي انحدار.

---

## 7. الملفات العامة ومخاطر تتطلب قراراً منفصلاً (Public Files & Out-of-Scope Risks)

- **الملفات التخزينية وصور التقدم (Static Public Media Exposure):**
  - يتم حالياً تخزين صور القياسات وصور التمارين تحت قرص التخزين العام `public` في مجلد `storage/app/public/`.
  - الروابط العامة للملفات تعتمد على مسارات مباشرة ومفتوحة للقراءة العامة إذا عُرف الرابط.
  - **التوصية:** عملاً بقواعد السلامة، لم يتم تغيير بنية التخزين أو تهجير الملفات في هذا التدقيق. يوصى بجدولة مهمة مستقلة لنقل وسائط القياسات الحساسة إلى قرص تخزين خاص (`private`) مع تقديم الصور عبر مسار محمي وموثق (Streaming Controller) يتحقق من الهوية والصلاحية قبل التقديم.

- **مسار `system.activate.update` المتبقي:**
  - في `SettingController::updateActivateSection`، الشيفرة تحتوي على `dd($id, $request)`. المسار محمي بصلاحية المشرف ولا يمكن للمتدربين استغلاله، لكن ينبغي استكمال تنفيذه الوظيفي لاحقاً.

---

## 8. الخاتمة والتأكيدات الصارمة (Conclusion & Assurances)

- تم إنجاز التدقيق الأمني بنجاح تام وفق ضوابط السلامة الصارمة.
- تم الحفاظ على كافة التغييرات والتحسينات السابقة من المهام 1 إلى 5 دون أي حذف أو إلغاء.
- لم يتم الاتصال بأي بيئة إنتاج أو قواعد بيانات حية.
- لم يتم تشغيل أي أوامر مدمرة لقواعد البيانات (`migrate:fresh`, `db:wipe` وغيرها).
- تم الاعتماد التام على قاعدة بيانات SQLite المعزولة في الذاكرة لجميع الاختبارات المؤتمتة.
