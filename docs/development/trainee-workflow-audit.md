# توثيق التدقيق التكاملي وانحدار سير عمل إدارة المتدربين
**(Trainee Workflow Integration & Regression Audit)**

**المشروع:** نظام إدارة التدريب الرياضي كابتن محمود (Captain Mahmoud Fitness Management System)  
**المسار:** `docs/development/trainee-workflow-audit.md`  
**تاريخ التوثيق:** أكتوبر 2026  
**نطاق العمل:** المهمة 5 — التدقيق التكاملي وسير العمل واختبارات الانحدار (Task 5 — Trainee Workflow Integration & Regression Audit)

---

## 1. نظرة عامة والأهداف

تهدف هذه الوثيقة إلى تقديم تقرير فني وتدقيق تكاملي شامل (`Integration Audit`) لكافة تدفقات وسير عمل إدارة المتدربين بعد اكتمال المهام من 1 إلى 4. 

تم التحقق من تكامل العمليات بين:
1. إنشاء المتدربين وحسابات المستخدمين المقابلة (`Trainee Creation & Account Mapping`).
2. عزل المعرفات المتبادلة وعدم الخلط بين `trainees.id` و `user.id`.
3. تحديث الملفات الشخصية، حماية كلمات المرور وتجزئتها، وقواعد فرادة البريد الإلكتروني.
4. آليات الحذف الآمن (`Soft Deletes`) وسلوك تسلسل السجلات المرتبطة (`Cascades vs Retention`).
5. أمن تسجيلات المتابعة والصور ومنع ثغرات الوصول المباشر غير المصرح به (`IDOR`).
6. مصفوفة الصلاحيات ووسيط التحقق (`Permission Middleware`) والتوجيه السليم لكافة أنواع المستخدمين (`user_type` 1 و 2 و 3).
7. اتساق ترحيل المخطط الحديث وتأثيره على قواعد البيانات النظيفة والقائمة.

---

## 2. سير العمل وقواعد الصلاحيات المعتمدة (Workflows & Authorization Rules)

### أ. إنشاء المتدرب وربط الحساب (`Store Workflow`)
* يتم إنشاء المتدرب عبر طلب `POST` إلى المسار `system.trainee.store` عبر [TraineeController@store](file:///e:/laragon/www/cap_mahmoud_gym/app/Modules/System/TraineeController.php#L36).
* تتولى الخدمة [TraineeService@store](file:///e:/laragon/www/cap_mahmoud_gym/app/Services/TraineeService.php#L229) تنفيذ العملية بالكامل داخل معاملة قاعدة بيانات (`DB::transaction`).
* **ربط الكيانين:**
  1. إنشاء حساب المستخدم في جدول `user` بقيمة `user_type = 2` ومجموعة الصلاحيات `permission_group_id = 125` وكلمة مرور افتراضية مشفرة `Hash::make('123456')`.
  2. إنشاء السجل الرياضي في جدول `trainees` وربطه بالمستخدم عبر `trainees.user_id = user.id`.
  3. مزامنة حقل الاسم `name` في كلا الجدولين لضمان عدم وجود قيم فارغة.
* **سلامة المعاملة:** في حال فشل أي خطوة أو إلقاء أي استثناء، يتم التراجع الفوري (`DB::rollback`) لمنع وجود مستخدم يتيم بدون سجل متدرب.

### ب. تحديث الملف الشخصي وإدارة كلمات المرور (`Update Workflow`)
* مسار التحديث: `PUT /system/trainee/{trainee}` باسم المسار `system.trainee.update`.
* **الحماية المزدوجة في التحديث:**
  1. **الطبقة الأولى (الوسيط):** وسيط [Permission.php](file:///e:/laragon/www/cap_mahmoud_gym/app/Http/Middleware/Permission.php#L29) يوجه المتدربين (`user_type` 2 أو 3) إلى لوحة تحكمهم فقط عند محاولة الوصول لمسار التحديث الإداري.
  2. **الطبقة الثانية (المتحكم):** يتحقق المتحكم [TraineeController@update](file:///e:/laragon/www/cap_mahmoud_gym/app/Modules/System/TraineeController.php#L80) من هوية المستخدم المصادق، ويرفض أي محاولة من متدرب لتعديل حساب رياضي آخر برمي استثناء `403 Forbidden`.
* **معالجة كلمة المرور:**
  - عند ترك حقل `password` فارغاً، يتم استبعاده بالكامل من مصفوفة التحديث لحفظ التجزئة الحالية (`Password Hash`).
  - عند تزويد كلمة مرور جديدة، يتم تشفيرها تلقائياً عبر `Hash::make`.
* **فرادة البريد الإلكتروني:** يستخرج [TraineeFormRequest](file:///e:/laragon/www/cap_mahmoud_gym/app/Http/Requests/TraineeFormRequest.php#L45) المعرف الحقيقي للمستخدم ويتجاهله في قاعدة `Rule::unique('user', 'email')->ignore($userId)` لمنع خطأ البريد المكرر عند تحديث بيانات نفس المتدرب.

### ج. حذف المتدرب وسلوك السجلات التابعة (`Deletion & Cascade Policy`)
* مسار الحذف: `DELETE /system/trainee/{trainee}` باسم المسار `system.trainee.destroy`.
* **الصلاحية:** محصورة بالمدربين والمسؤولين الذين يحملون إذن `delete-trainee` وممنوعة تماماً على المتدربين والضيوف.
* **سياسة الحذف المعتمدة:**
  1. **الحذف الآمن (Soft Delete) للمتدرب والمستخدم:** يتم تعيين `deleted_at` لكل من `trainees` و `user`.
  2. **خطط التمرين والتمارين:** تُحذف ناعماً بالتتابع (`Soft Deleted`) عبر أحداث النموذج `deleting` في `Trainee` و `WorkoutPlan`.
  3. **صور الأجهزة الرياضية:** تظل محفوظة في التخزين طالما كان الحذف ناعماً، وتُحذف فيزيائياً فقط عند الحذف النهائي (`forceDeleted`).
  4. **إلغاء جلسات الدخول (`Revoking Sessions`):** يتم حذف كافة السجلات التابعة في `auth_session` للمستخدم لمنع استمرار الجلسات النشطة.
  5. **السجلات التاريخية المستبقاة (`Data Retention`):** يتم الإبقاء على سجلات المتابعة والأوزان (`trainee_progress_checkins`) وصورها وسجلات أداء التمارين (`workout_logs`) للأغراض التوثيقية والتدقيقية والرجوع إليها عند استعادة الحساب.

### د. متابعة التقدم والصور التحويلية (`Progress & Photo Tracking`)
* المسارات: `system.trainee-progress.*`.
* تم تأكيد إغلاق كافة ثغرات الوصول المباشر للكائنات (`IDOR`):
  - لا يمكن لأي متدرب استعراض أو مقارنة أو تعديل أو حذف قياسات أو صور متدرب آخر حتى مع تمرير المعرفات المشفرة أو الرقمية في الطلب.
  - لا يمكن للمتدرب تعديل ملاحظات المدرب (`coach_notes`).

---

## 3. العيوب المكتشفة والإصلاحات المنفذة (Defects & Implemented Fixes)

خلال عملية التدقيق التكاملي، تم التحقق من العيوب التالية وتنفيذ الإصلاحات المباشرة:

### 1. عدم اتساق توجيه المتدربين من النوع الثالث (`user_type == 3`) في الوسيط:
* **الخلل:** في [app/Http/Middleware/Permission.php](file:///e:/laragon/www/cap_mahmoud_gym/app/Http/Middleware/Permission.php#L29)، كان فحص القائمة البيضاء للمتدربين مقتصراً على `if ($authUser->user_type == 2)`. وكان المتدربون المعينون كـ `user_type == 3` يمرون إلى فحص الصلاحيات الإدارية ويفشلون بـ 401 بدلاً من توجيههم للقائمة البيضاء المخصصة للمتدربين.
* **الإصلاح:** تم تعديل الشرط ليشمل كلا النوعين:
  ```php
  // توجيه المتدربين (user_type: 2 أو 3) إلى المسارات المسموحة فقط
  if ($authUser->user_type == 2 || $authUser->user_type == 3) {
      $whitelist = [ ... ];
  ```

### 2. إهمال مزامنة حقل الاسم في جدول المتدربين (`Trainee Name Desynchronization`):
* **الخلل:** في [app/Services/TraineeService.php](file:///e:/laragon/www/cap_mahmoud_gym/app/Services/TraineeService.php#L254)، عند إنشاء متدرب جديد في دالة `store()`، كان حقل `name` يُحفظ في جدول `user` فقط ويُترك فارغاً `null` في جدول `trainees`. كذلك في دالة `update()`، لم تكن تحديثات الاسم تنعكس على جدول `trainees`.
* **الإصلاح:** تمت إضافة `'name' => $request->name` إلى مصفوفة `$traineeData` في دالة `store()`، ومزامنتها في دالة `update()` عند ملئها، مما يضمن اتساق البيانات عبر الجدولين.

---

## 4. تدقيق ترحيل المخطط الحديث (Migration Audit & Risk Assessment)

تم تدقيق الترحيل [database/migrations/2026_10_20_000001_sync_missing_trainee_progress_and_workout_schema.php](file:///e:/laragon/www/cap_mahmoud_gym/database/migrations/2026_10_20_000001_sync_missing_trainee_progress_and_workout_schema.php):

### أ. التوافقية مع البيئات النظيفة والقائمة:
* **البيئة النظيفة (Fresh Installation):** يتم تشغيل الترحيل التأسيسي ثم ترحيل المزامنة؛ يتم إنشاء جدول `trainee_progress_checkins` مع المفتاح الأجنبي `fk_checkins_trainee`، وجدول `trainee_progress_photos` مع `fk_progress_photos_checkin`، وتُضاف حقول `warmup_links` و `type` و `notes_en` و `machine_image` بنجاح كامل.
* **البيئة القائمة (Existing Database):** تحمي شروط `Schema::hasTable` و `Schema::hasColumn` الترحيل من إعادة إنشاء الجداول أو الأعمدة المتواجدة بالفعل، مما يمنع حدوث أخطاء `Table already exists` أو تعارض المفاتيح.

### ب. ملاحظات ومخاطر دالة التراجع (`Rollback Concerns`):
* **تحذير أمني:** تتضمن دالة `down()` إسقاطاً لجدولي `trainee_progress_photos` و `trainee_progress_checkins`. في بيئة إنتاجية تحتوي على بيانات تاريخية سابقة لهذه الترحيلة، يُحظر تماماً تشغيل `migrate:rollback` تجنباً لفقدان الجداول. يجب الالتزام دائماً بالترحيلات الأمامية (`Forward-only Migrations`).

---

## 5. نتائج اختبارات الانحدار والتكامل (Regression Testing Results)

تم بناء فئة اختبارات تكامل مخصصة في:
[tests/Feature/TraineeWorkflowIntegrationTest.php](file:///e:/laragon/www/cap_mahmoud_gym/tests/Feature/TraineeWorkflowIntegrationTest.php)

تختبر الفئة 12 سيناريو تكاملي شامل:

| # | سيناريو الاختبار | دالة الاختبار | النتيجة |
|---|---|---|---|
| 1 | نجاح إنشاء المتدرب وربط حساب المستخدم المقابل بدقة | `test_trainee_creation_creates_and_maps_user_account_correctly` | **PASSED** |
| 2 | فشل التحقق في الإنشاء يمنع إنشاء أي سجلات جزئية | `test_trainee_creation_validation_prevents_partial_record_creation` | **PASSED** |
| 3 | تراجع المعاملة (Rollback) عند حدوث خطأ غير متوقع | `test_transaction_rollback_cleans_up_on_service_failure` | **PASSED** |
| 4 | الحفاظ على كلمة المرور الحالية عند التحديث بدون كلمة مرور | `test_trainee_update_preserves_existing_password_when_omitted` | **PASSED** |
| 5 | تشفير كلمة المرور الجديدة عند تزويدها في التحديث | `test_trainee_update_hashes_new_password_correctly` | **PASSED** |
| 6 | فرادة البريد الإلكتروني (السماح بالتحديث الذاتي ورفض التكرار) | `test_trainee_email_uniqueness_allows_self_update_but_rejects_collision` | **PASSED** |
| 7 | منع المتدربين من تعديل حسابات أخرى (لكلا النوعين 2 و 3) | `test_unauthorized_cross_trainee_update_rejected_for_both_trainee_types` | **PASSED** |
| 8 | رفض محاولات حذف المتدربين من المتدربين والضيوف | `test_unauthorized_trainee_deletion_rejected_for_trainees_and_guests` | **PASSED** |
| 9 | الحذف المصرح به: حذف ناعم وإلغاء الجلسات النشطة | `test_authorized_deletion_soft_deletes_trainee_and_user_and_revokes_sessions` | **PASSED** |
| 10 | الاحتفاظ بسجلات المتابعة التاريخية عند الحذف الناعم | `test_soft_delete_retains_historical_workout_logs_and_checkins` | **PASSED** |
| 11 | منع الوصول غير المصرح به لتسجيلات التقدم والصور للمتدربين | `test_progress_tracking_authorization_prevents_cross_trainee_access` | **PASSED** |
| 12 | سلوك وسيط الصلاحيات (Middleware) ومطابقة الصلاحيات بدقة | `test_permission_middleware_and_route_mapping_integrity` | **PASSED** |

### مخرجات التشغيل الفعلية:
```bash
# 1. اختبارات التكامل وسير العمل للمتدربين
php vendor/phpunit/phpunit/phpunit tests/Feature/TraineeWorkflowIntegrationTest.php
# النتيجة: Tests: 12, Assertions: 70, Failures: 0 (100% Success)

# 2. كامل طاقم اختبارات النظام لجميع المهام (1 إلى 5)
php vendor/phpunit/phpunit/phpunit
# النتيجة: Tests: 53, Assertions: 319, Failures: 0 (100% Success)
```

---

## 6. سلامة البيئة التشغيلية (Production Safety Declaration)

- لم يتم الاتصال أو الاستعلام أو التعديل على أي قاعدة بيانات أو خدمة إنتاج.
- لم يُنفذ أي أمر تدميري لقواعد البيانات (`migrate:fresh`, `db:wipe`, `DROP`, `TRUNCATE`).
- تم الحفاظ على كافة التعديلات والتجهيزات التي تمت في المهام 1 و 2 و 3 و 4.
- نُفذت جميع الاختبارات داخل بيئة SQLite In-Memory المعزولة بنسبة 100%.
