# توثيق تدقيق مخطط قاعدة البيانات والترحيلات (Database Schema & Migration Consistency Audit)

**المشروع:** نظام إدارة التدريب الرياضي كابتن محمود (Captain Mahmoud Fitness Management System)  
**المسار:** `docs/development/database-schema-audit.md`  
**تاريخ التوثيق:** أكتوبر 2026  
**نطاق العمل:** المهمة 4 — تدقيق اتساق مخطط قاعدة البيانات والترحيلات (Task 4 — Database Schema & Migration Consistency Audit)

---

## 1. نظرة عامة والأهداف

تهدف هذه الوثيقة إلى تقديم تقرير فني شامل ومفصل لعملية تدقيق ومراجعة مخطط قاعدة البيانات (`Database Schema`)، وملفات الترحيل (`Migrations`)، ونماذج Eloquent (`Models`)، والعلاقات والمفاتيح الأجنبية وفهارس البحث، وسلوك الحذف الآمن (`Soft Deletes`)، والتوافقية مع بيئة الاختبار المعزولة في تطبيق Laravel الخاص بنظام كابتن محمود.

ركزت عملية التدقيق على تحقيق الأهداف التالية:
1. مطابقة المخطط الفعلي لقاعدة البيانات (`MySQL`) مع ملفات الترحيل ونماذج التطبيق.
2. الكشف عن أي جداول أو أعمدة مستخدمة برمجياً ولكنها مفقودة من الترحيلات.
3. معالجة النواقص عبر ترحيل أمامي آمن (`Forward Migration`) يحافظ على سلامة بيانات الإنتاج ولا يقوم بأي عمليات تدميرية.
4. تصحيح أي أخطاء في تعريف علاقات النماذج البرمجية (`Eloquent Relationships`).
5. التحقق الصارم من تشغيل الترحيلات وسلوك النماذج في بيئة اختبار معزولة بالكامل دون المساس ببيئة الإنتاج.

---

## 2. جرد الترحيلات وجداول قاعدة البيانات (Migration & Table Inventory)

### أ. ملفات الترحيل في المشروع
* **الترحيل التأسيسي:** `database/migrations/2026_09_29_000000_create_baseline_schema.php`  
  كُتب لتأسيس البنية الأولية للتطبيق مع حماية `if (!Schema::hasTable(...))`.
* **الترحيل الأمامي المضاف حديثاً (المهمة 4):** `database/migrations/2026_10_20_000001_sync_missing_trainee_progress_and_workout_schema.php`  
  أُضيف لمزامنة الجداول والأعمدة الناقصة بأمان تام ودون كسر لقواعد البيانات القائمة.
* **الترحيلات القديمة المعزولة:** مجلد `database/migrations_old/` يحتوي على ترحيلات افتراضية قديمة تخص قوالب سابقة غير مستخدمة.

### ب. جرد الجداول الفعلية في التطبيق (23 جدولاً أساسياً)

| # | اسم الجدول (`Table`) | النموذج المقابل (`Model`) | الوصف والمسؤولية |
|---|---|---|---|
| 1 | `user` | `App\Models\User` | حسابات المستخدمين والمصادقة (إدارة، مدربين، متدربين). |
| 2 | `trainees` | `App\Models\Trainee` | السجلات الرياضية للمتدربين والاشتراكات والقياسات الأساسية. |
| 3 | `workout_plans` | `App\Models\WorkoutPlan` | خطط التمرين اليومية والأيام التدريبية والروابط. |
| 4 | `exercises` | `App\Models\Exercise` | التمارين الرياضية التفصيلية المرتبطة بالأيام وأنواعها وصور الأجهزة. |
| 5 | `workout_logs` | *(سجل الأداء الرياضي)* | تسجيلات التكرارات والأوزان المرفوعة أثناء أداء التمارين. |
| 6 | `nutrition_plans` | `App\Models\NutritionPlan` | الخطط والأنظمة الغذائية للمتدربين. |
| 7 | `trainee_progress_checkins` | `App\Models\TraineeProgressCheckin` | تسجيلات المتابعة الدورية للأوزان والملاحظات والمدرب. |
| 8 | `trainee_progress_photos` | `App\Models\TraineeProgressPhoto` | صور التقدم والمقارنة المرفقة بتسجيلات المتابعة. |
| 9 | `permission_groups` | `App\Models\PermissionGroup` | مجموعات الصلاحيات وأدوار النظام. |
| 10 | `permissions` | `App\Models\Permission` | مسارات الصلاحيات المخصصة لكل مجموعة. |
| 11 | `auth_session` | `App\Models\AuthSession` | جلسات الدخول النشطة والتوكنات. |
| 12 | `activity_log` | `App\Models\Activity` | سجل تدقيق النشاطات والتعديلات. |
| 13 | `posts` | `App\Models\Post` | أقسام واجهة الموقع (الهيرو، الخدمات، التحولات، الآراء). |
| 14 | `post_items` | `App\Models\PostItem` | عناصر وبطاقات أقسام الموقع. |
| 15 | `join_us_submissions` | `App\Models\JoinUsSubmission` | طلبات الانضمام والاستفسارات من واجهة الموقع. |
| 16 | `message` | `App\Models\Message` | رسائل التواصل الواردة من الموقع. |
| 17 | `settings` | `App\Models\Setting` | إعدادات النظام العامة. |
| 18 | `activate_section` | `App\Models\Active_section` | تفعيل وتعطيل أقسام الموقع الديناميكية. |
| 19 | `language` | `App\Models\Language` | اللغات المدعومة والترجمات. |
| 20 | `social_links` | *(الروابط الاجتماعية)* | روابط وسائل التواصل الاجتماعي. |
| 21 | `password_resets` | *(استعادة كلمات المرور)* | توكنات إعادة تعيين كلمات المرور. |
| 22 | `failed_jobs` | *(الوظائف الفاشلة)* | سجل طابور العمليات الفاشلة. |
| 23 | `personal_access_tokens` | *(توكنات Sanctum)* | توكنات الوصول لواجهات البرمجة API. |

---

## 3. الفروقات المؤكدة بين الـ Schema والـ Models (Verified Discrepancies)

كشفت المراجعة الميدانية الدقيقة لبيانات MySQL مقارنة بملف التأسيس `2026_09_29_000000_create_baseline_schema.php` عن الفروقات التالية:

### أ. جداول كاملة مفقودة من الترحيل التأسيسي (Critical Missing Tables)
* **`trainee_progress_checkins`:** الجدول موجود في MySQL ومستخدم في نماذج وخدمات التطبيق (المهام 2 و 3)، ولكنه كان مفقوداً تماماً من الترحيل التأسيسي.
* **`trainee_progress_photos`:** الجدول موجود في MySQL ويرتبط بـ `trainee_progress_checkins` عبر قيد حذف متسلسل (`ON DELETE CASCADE`)، وكان مفقوداً من الترحيل.
* **الأثر:** أي عملية تثبيت جديدة للمشروع من خلال `php artisan migrate` كانت ستفشل فوراً عند محاولة تشغيل أو فحص وظائف متابعة المتدربين وصورهم لعدم وجود الجدولين.

### ب. أعمدة مفقودة من الترحيل التأسيسي (Missing Columns)
* **جدول خطط التمرين (`workout_plans`):**
  - الحقل المفقود: `warmup_links` (نوع `json` أو `text` قابل للقيمة الفارغة).
  - الاستخدام الفعلي: يُستخدم في [WorkoutPlan.php](file:///e:/laragon/www/cap_mahmoud_gym/app/Models/WorkoutPlan.php#L21) عبر مصفوفة الـ casts وقواعد التحقق ودوال تنظيف وعرض روابط الفيديو في لوحة التحكم وتفاصيل المتدرب.
* **جدول التمارين (`exercises`):**
  - الحقول المفقودة:
    1. `type` (`enum('warmup', 'main', 'post_workout') DEFAULT 'main'`): عمود حيوي تعتمد عليه نطاقات التمارين (`scopeWarmup`, `scopeMain`, `scopePostWorkout`) وعلاقات خطة التمرين (`warmupExercises`, `mainExercises`, `postWorkoutExercises`).
    2. `notes_en` (`text NULL`): الملاحظات الفنية باللغة الإنجليزية للتمارين.
    3. `machine_image` (`varchar(255) NULL`): مسار صورة الجهاز الرياضي المستخدم في خدمة [ExerciseMachineImageService](file:///e:/laragon/www/cap_mahmoud_gym/app/Services/ExerciseMachineImageService.php).

### ج. أخطاء في تعريف العلاقات البرمجية (Model Relationship Bugs)
* **نموذج الصلاحيات [Permission.php](file:///e:/laragon/www/cap_mahmoud_gym/app/Models/Permission.php):**
  - كانت العلاقة معرفة كـ:
    ```php
    public function permission_group(){
        return $this->hasOne('App\Models\PermissionGroup');
    }
    ```
  - **الخلل:** جدول `permissions` يحمل المفتاح الأجنبي `permission_group_id` الذي يشير إلى `permission_groups.id`. استخدام `hasOne` يفترض خطأً وجود `permission_id` في جدول المجموعات، مما يؤدي إلى فشل استعلام العلاقة.
  - **التصحيح:** تم تصحيحها إلى علاقة انتمائية سليمة [belongsTo](file:///e:/laragon/www/cap_mahmoud_gym/app/Models/Permission.php#L24):
    ```php
    public function permission_group()
    {
        return $this->belongsTo(PermissionGroup::class, 'permission_group_id', 'id');
    }
    ```

### د. حقول موروثة غير مستخدمة (Legacy Artifacts - Not Defects)
* **`User::$fillable['department_id']`:** حقل موروث من نظام سابق قديم، لا يوجد عمود `department_id` في جدول `user` بقاعدة التطبيق، ويتم تجاهله تلقائياً في [UserService](file:///e:/laragon/www/cap_mahmoud_gym/app/Services/UserService.php#L93).
* **`PermissionGroup::$fillable['whitelist_ip']`:** حقل موروث من قوالب قديمة، لا يوجد عمود مطابق له في قاعدة البيانات الحالية ولا يعتمد عليه أي مسار أمني.

---

## 4. المعالجة البرمجية واستراتيجية الترحيلات (Remediation Strategy)

وفقاً لقواعد السلامة الصارمة للتطبيق وتفادياً للمساس بسجل الترحيلات التاريخي أو تعديل بيئة الإنتاج:

1. **إنشاء ترحيل أمامي آمن (Forward Migration):**
   تم إنشاء الترحيل:
   `database/migrations/2026_10_20_000001_sync_missing_trainee_progress_and_workout_schema.php`
   - يتميز الترحيل بالحماية المزدوجة باستخدام `if (!Schema::hasTable(...))` و `if (!Schema::hasColumn(...))` قبل كل عملية إضافة.
   - إذا تم تشغيل الترحيل في بيئة تحوي الجداول مسبقاً (مثل بيئة التطوير الحالية أو الإنتاج)، يتجاوز الترحيل الإضافات بأمان دون إلقاء أخطاء.
   - إذا تم تشغيله في بيئة جديدة تماماً (أو في بيئة اختبارات نظيفة)، يقوم بإنشاء الجداول والأعمدة الناقصة بالتتابع الصحيح.
   - يتضمن الترحيل دالة تراجع (`down`) دقيقة تراعي إسقاط الجداول بالترتيب العكسي للمفاتيح الأجنبية.

2. **تصحيح علاقة النموذج:**
   تم تحديث [Permission.php](file:///e:/laragon/www/cap_mahmoud_gym/app/Models/Permission.php#L24) ليرتبط بصورة صحيحة بمجموعة الصلاحيات عبر `belongsTo`.

---

## 5. مصفوفة النماذج والعلاقات الأساسية (Relationships Matrix)

| النموذج الأساسي | العلاقة | النموذج المرتبط | المفتاح الأجنبي والمحلي | السلوك عند الحذف |
|---|---|---|---|---|
| `User` | `hasOne` | `Trainee` | `trainees.user_id -> user.id` | حذف ناعم متسلسل عبر حدث `deleting` في `User` |
| `Trainee` | `belongsTo` | `User` | `trainees.user_id -> user.id` | قيد قاعدة البيانات `RESTRICT` لحماية البيانات |
| `Trainee` | `hasMany` | `WorkoutPlan` | `workout_plans.trainee_id -> trainees.id` | حذف ناعم متسلسل عبر حدث `deleting` في `Trainee` |
| `WorkoutPlan` | `belongsTo` | `Trainee` | `workout_plans.trainee_id -> trainees.id` | قيد قاعدة البيانات `CASCADE` |
| `WorkoutPlan` | `hasMany` | `Exercise` | `exercises.workout_plan_id -> workout_plans.id` | حذف ناعم متسلسل عبر حدث `deleting` في `WorkoutPlan` |
| `Exercise` | `belongsTo` | `WorkoutPlan` | `exercises.workout_plan_id -> workout_plans.id` | قيد قاعدة البيانات `CASCADE` |
| `Trainee` | `hasMany` | `NutritionPlan` | `nutrition_plans.trainee_id -> trainees.id` | حذف متسلسل عبر حدث `deleting` في `Trainee` |
| `Trainee` | `hasMany` | `TraineeProgressCheckin` | `trainee_progress_checkins.trainee_id -> trainees.id` | قيد قاعدة البيانات `CASCADE` |
| `TraineeProgressCheckin` | `hasMany` | `TraineeProgressPhoto` | `trainee_progress_photos.checkin_id -> trainee_progress_checkins.id` | قيد قاعدة البيانات وحذف فيزيائي للملفات |
| `PermissionGroup` | `hasMany` | `Permission` | `permissions.permission_group_id -> permission_groups.id` | حذف الصلاحيات المرتبطة |
| `Permission` | `belongsTo` | `PermissionGroup` | `permissions.permission_group_id -> permission_groups.id` | قيد الربط بالمجموعة |

---

## 6. تغطية اختبارات الانحدار والتحقق (Regression Testing Suite)

تم إنشاء ملف اختبارات مخصص ومتكامل في:
[tests/Feature/DatabaseSchemaConsistencyTest.php](file:///e:/laragon/www/cap_mahmoud_gym/tests/Feature/DatabaseSchemaConsistencyTest.php)

يعتمد الاختبار على تشغيل دورة الترحيل الكاملة (`Artisan::call('migrate')`) في بيئة SQLite In-Memory نظيفة ومستقلة، ويغطي 7 سيناريوهات أساسية:

1. **إنشاء كافة الجداول المتوقعة:** التحقق من وجود الـ 23 جدولاً بنجاح عند تشغيل الترحيلات التأسيسية والأمامية.
2. **التحقق من الأعمدة الحيوية:** فحص وجود الأعمدة التي كانت مفقودة (`warmup_links`, `type`, `notes_en`, `machine_image`, أعمدة تسجيلات التقدم والصور).
3. **صحة علاقات Eloquent المتبادلة:** إنشاء بيانات تجريبية واختبار استرجاع كافة العلاقات في الاتجاهين بما في ذلك نطاقات التمارين (`warmup`, `main`, `post_workout`).
4. **عزل وتمييز المعرفات:** التحقق من عدم التباس معرف المتدرب `trainees.id` مع معرف المستخدم `user.id`.
5. **تسلسل الحذف الآمن (`Soft Deletes`):** التحقق من أن حذف المتدرب ناعماً يؤدي إلى حذف خططه التدريبية وتمارينه ناعماً دون حذف نهائي.
6. **سلوك حذف تسجيلات وصور المتابعة:** التأكد من ارتباط الصور بسجل المتابعة وحذفها عند حذف السجل.
7. **عزل بيئة الاختبار الصارم:** التأكد من أن الاتصال محصور في `:memory:` ولا يتصل بأي قاعدة بيانات حقيقية.

### نتائج تشغيل الاختبارات الفعلية:
```bash
# 1. اختبارات تدقيق المخطط والعلاقات الموجهة
php vendor/phpunit/phpunit/phpunit tests/Feature/DatabaseSchemaConsistencyTest.php
# النتيجة: Tests: 7, Assertions: 85, Failures: 0 (100% Success)

# 2. كامل طاقم اختبارات التطبيق (المهام 1 و 2 و 3 و 4)
php vendor/phpunit/phpunit/phpunit
# النتيجة: Tests: 41, Assertions: 249, Failures: 0 (100% Success)
```

---

## 7. سلامة البيئة التشغيلية وإرشادات النشر (Production Safety & Deployment Guide)

1. **سلامة بيانات الإنتاج:**
   - لم يتم تنفيذ أي أمر تدميري (`migrate:fresh`, `db:wipe`, `DROP`, `TRUNCATE`) على الإطلاق.
   - لم يتم الاتصال أو التعديل على أي خادم أو قاعدة بيانات إنتاج.
   - كافة الفحوصات تمت محلياً على فرع العمل `refactor-project`.

2. **تعليمات النشر لبيئة الإنتاج مستقبلاً:**
   - يتم رفع الترحيل الجديد `2026_10_20_000001_sync_missing_trainee_progress_and_workout_schema.php`.
   - عند تشغيل الأمر المعتاد `php artisan migrate --force`، سيقوم الترحيل بالتحقق من الجداول والأعمدة؛ فإن كانت موجودة مسبقاً في الإنتاج يتم تخطيها تلقائياً وبأمان تام دون أي تعطل للخدمة أو فقدان لأي سجل.
