# التقرير الفني الشامل لتدقيق وتحليل ميزة تعديل أحمال التمارين (Load Adjustment Technical Audit & Redesign Analysis)

**المشروع:** نظام إدارة جيم شلتوت (Shaltout Gym Management System)  
**الفرع الحالي:** `dashboard-redesign`  
**بيئة التشغيل:** Laravel 13.35 / PHP 8.5.5 / MySQL 8.0.34  
**نوع المهمة:** تدقيق تقني وتحليلي شامل وقراءة فقط (STRICTLY READ-ONLY AUDIT)  
**التاريخ:** نوفمبر 2026  

---

> [!IMPORTANT]
> **التزام صارم بقواعد السلامة وقراءة النظام فقط:**  
> تم إعداد هذا التقرير بالكامل دون إجراء أي تعديل على الشيفرة المصدرية للتطبيق، أو مساراته، أو واجهاته، أو قواعد البيانات الحية. كما تم الحفاظ الكامل على كافة التعديلات البرمجية غير المثبتة (Uncommitted Changes) عبر جميع الفروع السابقة، استناداً إلى مصفوفة الصلاحيات وقاعدة البيانات الحقيقية `DB-structure.sql` وسجلات النظام.

---

## الفهرس العام للتقرير
1. [البنية المعمارية الحالية لمنظومة تعديل الأحمال](#1-البنية-المعمارية-الحالية-منظومة-تعديل-الأحمال-current-architecture)
2. [مسار تدفق طلب المتدرب وتجربة الاستخدام المحفوظة](#2-مسار-تدفق-طلب-المتدرب-وتجربة-الاستخدام-المحفوظة-trainee-submission-flow--preserved-ux)
3. [مسار تدفق مراجعة المدرب وتجربة الاستخدام الحالية](#3-مسار-تدفق-مراجعة-المدرب-وتجربة-الاستخدام-الحالية-coach-review-flow--current-ux)
4. [هيكل قاعدة البيانات والعلاقات الفعلية](#4-هيكل-قاعدة-البيانات-والعلاقات-الفعلية-database-structure--actual-relations)
5. [دورة حياة الإشعارات والخلل المرصود](#5-دورة-حياة-الإشعارات-والخلل-المرصود-notification-lifecycle--detected-issues)
6. [نتائج وتدقيق الصلاحيات والقيود الخاصة بالمتدربين](#6-نتائج-وتدقيق-الصلاحيات-والقيود-الخاصة-بالمتدربين-permissions--trainee-access-restrictions)
7. [مشاكل واجهة وتجربة المستخدم للمدرب ومواضع الاحتكاك](#7-مشاكل-واجهة-وتجربة-المستخدم-للمدرب-ومواضع-الاحتكاك-coach-uiux-issues--friction-points)
8. [مقترح التصميم والتنظيم المبسط للوحة المدرب](#8-مقترح-التصميم-والتنظيم-المبسط-للوحة-المدرب-proposed-simplified-coach-review-interface)
9. [حصر دقيق وشامل للملفات المطلوبة للتعديل مستقبلاً](#9-حصر-دقيق-وشامل-للملفات-المطلوبة-للتعديل-مستقبلاً-file-modification-inventory)
10. [التعديلات المطلوبة على قاعدة البيانات](#10-التعديلات-المطلوبة-على-قاعدة-البيانات-database-changes-evaluation)
11. [الاختبارات المفقودة أو غير الكافية](#11-الاختبارات-المفقودة-أو-غير-الكافية-missing--inadequate-tests)
12. [خطة تنفيذية مرحلية مقترحة](#12-خطة-تنفيذية-مرحلية-مقترحة-phased-implementation-plan)

---

## 1. البنية المعمارية الحالية لمنظومة تعديل الأحمال (Current Architecture)

تتبع ميزة تعديل الأحمال نمط التصميم المعتمد في المشروع:
**Controller → Form Request → Service Layer → Model / DB Transaction**.

```
[واجهة المتدرب: لوحة التمارين] ──(AJAX POST)──> [LoadAdjustmentController::store]
                                                        │
                                                        ▼
                                          [LoadAdjustmentReportRequest] (IDOR / Plan Check)
                                                        │
                                                        ▼
                                          [LoadAdjustmentService::storeReport]
                                                        │
                                                        ▼ (DB Transaction)
                                   ┌────────────────────┴────────────────────┐
                                   ▼                                         ▼
                     [TraineeLoadAdjustment]                    [TraineeLoadAdjustmentItem]
                          (السجل الرئيسي)                             (مجموعات التمرين الفردية)
                                   │
                                   ▼
                    [InAppNotificationService::notifyCoachLoadAdjustmentSubmitted]
```

### المكونات البرمجية الرئيسية ومسؤولياتها:
1. **وحدة التحكم [`app/Modules/System/LoadAdjustmentController.php`](file:///e:/laragon/www/cap_mahmoud_gym/app/Modules/System/LoadAdjustmentController.php):**
   - تستقبل طلبات AJAX القادمة من المتدربين والمدربين.
   - تضم ثلاث دوال أساسية:
     - `store()`: استلام طلب تعديل الحمل وتخزينه وإرسال إشعار للمدرب.
     - `reviewItem()`: مراجعة واعتماد/رفض مجموعة تدريبية محددة من قبل المدرب.
     - `review()`: دالة المراجعة الإجمالية للتقرير الرئيسي (مسار التوافق القديم).
2. **طبقة الخدمة المعزولة [`app/Services/LoadAdjustmentService.php`](file:///e:/laragon/www/cap_mahmoud_gym/app/Services/LoadAdjustmentService.php):**
   - تحتوي على كافة العمليات الحسابية والمنطق التشغيلي، بما في ذلك:
     - إدارة المعاملات المالية وقفل السجلات المتزامن (`DB::transaction` و `lockForUpdate()`).
     - فحص التعارض الزمني التلقائي (Automatic Conflict Detection) عند تغير أوزان الخطة بعد تقديم الطلب.
     - تعديل وزن التمرين في الخطة التدريبية مباشرة عبر دوال الكائن `Exercise`.
     - إعادة احتساب الحالة الإجمالية للتقرير الأب (`recalculateAggregateStatus`).
3. **طلبات التحقق والصلاحيات (Form Requests):**
   - [`app/Http/Requests/LoadAdjustmentReportRequest.php`](file:///e:/laragon/www/cap_mahmoud_gym/app/Http/Requests/LoadAdjustmentReportRequest.php): حماية المتدرب من التلاعب برقم التمرين، والتحقق من نشاط الخطة، وتفحص أرقام المجموعات وعدم تكرارها.
   - [`app/Http/Requests/LoadAdjustmentItemReviewRequest.php`](file:///e:/laragon/www/cap_mahmoud_gym/app/Http/Requests/LoadAdjustmentItemReviewRequest.php): مراجعة المدرب لكل مجموعة بشكل مستقل مع التحقق من صلاحيات المدرب ومنع المتدربين.
   - [`app/Http/Requests/LoadAdjustmentReviewRequest.php`](file:///e:/laragon/www/cap_mahmoud_gym/app/Http/Requests/LoadAdjustmentReviewRequest.php): مراجعة التقرير الإجمالي.
4. **نماذج البيانات (Eloquent Models):**
   - [`app/Models/TraineeLoadAdjustment.php`](file:///e:/laragon/www/cap_mahmoud_gym/app/Models/TraineeLoadAdjustment.php): السجل الأب الذي يمثل جلسة طلب التعديل.
   - [`app/Models/TraineeLoadAdjustmentItem.php`](file:///e:/laragon/www/cap_mahmoud_gym/app/Models/TraineeLoadAdjustmentItem.php): السجل الابن لكل مجموعة على حدة (Set 1, Set 2, ...).
   - [`app/Models/Exercise.php`](file:///e:/laragon/www/cap_mahmoud_gym/app/Models/Exercise.php): تمرين الخطة التدريبية الذي يحفظ الأوزان المقررة.
5. **خدمة الإشعارات الداخلية [`app/Services/InAppNotificationService.php`](file:///e:/laragon/www/cap_mahmoud_gym/app/Services/InAppNotificationService.php):**
   - مسؤولة عن إرسال إشعارات النظام لقاعدة البيانات وعرضها في الجرس العلوي.

---

## 2. مسار تدفق طلب المتدرب وتجربة الاستخدام المحفوظة (Trainee Submission Flow & Preserved UX)

> [!IMPORTANT]
> **التأكيد على حتمية الحفاظ على تجربة المتدرب الحالية:**  
> تم تصميم وبناء تجربة المتدرب بعناية فائقة لتكون مدمجة وسلسة مباشرة داخل جدول التمارين اليومي للمتدرب. **يجب الحفاظ الكامل على نقطة الدخول هذه وعدم تغيير مكانها أو نقلها إلى صفحات خارجية.**

### تفاصيل المسار التشغيلي للمتدرب:
1. **نقطة الدخول (Entry Point):**
   - تقع داخل صفحة التمارين الخاصة بالمتدرب [`resources/views/system/trainer-dashboard.blade.php`](file:///e:/laragon/www/cap_mahmoud_gym/resources/views/system/trainer-dashboard.blade.php).
   - أسفل كل تمرين، يوجد زر أنيق يحمل الأيقونة `fa-weight-hanging` بعنوان **"تعديل الحمل / الأوزان"** (`.btn-toggle-inline-load`).
2. **واجهة الإدخال المضمنة (Inline Expansion Row):**
   - يتم تحميل الواجهة الجزئية [`resources/views/system/workout/partials/trainee-load-adjustment-inline-row.blade.php`](file:///e:/laragon/www/cap_mahmoud_gym/resources/views/system/workout/partials/trainee-load-adjustment-inline-row.blade.php).
   - تعرض مجموعات التمرين الفعلية (من 1 إلى 4 مجموعات) مع:
     - الوزن الحالي المقيد في الخطة التدريبية لكل مجموعة.
     - مربع اختيار (Checkbox) لاختيار المجموعات المراد تعديلها حصراً.
     - حقل إدخال نصي للوزن المقترح (مثل: `22.5kg`).
     - حقل نصي لملاحظات المتدرب للمدرب (سبب الزيادة/النقصان).
3. **الإرسال غير المتزامن (AJAX Submission):**
   - عند النقر على "إرسال طلب التعديل"، يتم تجميع البيانات وإرسالها بطلب `POST` إلى `route('system.load-adjustment.store')`.
   - يتم تفعيل حالة التحميل وتعطيل الزر لمنع الإرسال المزدوج.
4. **حماية الأمان والتحقق الخادمي:**
   - يمنع `LoadAdjustmentReportRequest` استغلال ثغرات الـ IDOR حيث يستنتج النظام هوية المتدرب تلقائياً من جلسة المستخدم المسجل (`$user->trainee`).
   - يتحقق من أن الخطة التدريبية تابعة له وأن حالتها `active`.
   - يتحقق من أن رقم المجموعة لا يتجاوز إجمالي مجموعات التمرين الفعلي المسجل في جدول `exercises`.
5. **عرض السجل والتأكيد اللحظي للمتدرب:**
   - يظهر تنبيه SweetAlert بنجاح العملية.
   - تنعكس الحالة فوراً في قسم **"سجل تحديثات الأوزان لليوم التدريبي"** المدار بالملف [`resources/views/system/workout/partials/trainee-load-adjustment-day-history.blade.php`](file:///e:/laragon/www/cap_mahmoud_gym/resources/views/system/workout/partials/trainee-load-adjustment-day-history.blade.php).
   - تظهر كل مجموعة بحالة **"قيد المراجعة"** بلون أصفر حتى يبت المدرب في أمرها.

---

## 3. مسار تدفق مراجعة المدرب وتجربة الاستخدام الحالية (Coach Review Flow & Current UX)

### آلية المعالجة الحالية:
1. **نقطة مراجعة المدرب الحالية:**
   - تقع حالياً داخل صفحة تفاصيل المتدرب [`resources/views/system/trainee/show.blade.php`](file:///e:/laragon/www/cap_mahmoud_gym/resources/views/system/trainee/show.blade.php) تحت التبويب الثامن `#kt_trainee_load_updates_tab` (السطور 1440–1600).
   - يتم تضمين بطاقة مراجعة المجموعات [`resources/views/system/workout/partials/coach-load-adjustment-sets-card.blade.php`](file:///e:/laragon/www/cap_mahmoud_gym/resources/views/system/workout/partials/coach-load-adjustment-sets-card.blade.php).
2. **الخيارات المتاحة للمدرب لكل مجموعة مستقلة:**
   - **اعتماد الوزن المقترح (`approve`):** يطبق الوزن المقترح على الخطة فوراً ويحول حالة المجموعة إلى `approved`.
   - **اعتماد بوزن بديل (`approve_alternative`):** يحدد المدرب وزناً آخر مناسباً في حقل مخصص، ويتم تطبيقه على الخطة بدلاً من المقترح.
   - **رفض الطلب (`reject`):** يحول حالة المجموعة إلى `rejected` ويبقى وزن الخطة كما هو دون أدنى تغيير.
   - **إضافة ملاحظة توجيهية:** إدخال نصي اختياري يوضح للمتدرب سبب التعديل أو توجيهات أداء الحركة.
3. **منظومة حماية الخطة ومنع الكتابة فوق التعديلات المتعارضة (Conflict Protection):**
   - أثناء استدعاء `reviewItem()`، يفحص النظام الوزن الأساسي وقت تقديم الطلب `$item->base_weight` ويقارنه بالوزن الحالي في الخطة `$exercise->getWeightForSet()`.
   - إذا تم تعديل الخطة يدوياً في هذه الأثناء، يعتبر الطلب في حالة تعارض (`conflict`)، ويتم قفل التطبيق التلقائي مع إظهار شريط تحذيري برتقالي للمدرب.
   - يمنح المدرب حينها خيارين لحل التعارض: "فرض الاعتماد" (`resolve_approve`) أو "رفض الطلب المتعارض" (`resolve_reject`).

---

## 4. هيكل قاعدة البيانات والعلاقات الفعلية (Database Structure & Actual Relations)

وفقاً لفحص قاعدة البيانات الحية ومقارنتها بـ `DB-structure.sql`:

```
┌─────────────────────────────────┐
│     trainees (المتدربون)       │
└────────────────┬────────────────┘
                 │ 1
                 │ N
┌────────────────▼────────────────────────────────┐
│   trainee_load_adjustments (التقرير الرئيسي)   │
├─────────────────────────────────────────────────┤
│ id (PK, bigint unsigned)                        │
│ trainee_id (FK -> trainees.id)                  │
│ workout_plan_id (FK -> workout_plans.id)        │
│ exercise_id (FK -> exercises.id)                │
│ workout_day_name (varchar 191)                  │
│ exercise_name (varchar 191)                     │
│ prescribed_load (varchar 191, nullable)         │
│ reported_load (varchar 191, nullable)           │
│ notes (text, nullable)                          │
│ coach_notes (text, nullable)                    │
│ status ('pending','reviewed','partially_reviewed'│
│        ,'dismissed','conflict')                 │
│ reviewed_by (FK -> user.id, nullable)           │
│ reviewed_at (timestamp, nullable)               │
│ created_at / updated_at                         │
└────────────────┬────────────────────────────────┘
                 │ 1
                 │ N
┌────────────────▼────────────────────────────────┐
│ trainee_load_adjustment_items (مجموعات التمرين) │
├─────────────────────────────────────────────────┤
│ id (PK, bigint unsigned)                        │
│ load_adjustment_id (FK -> parent.id cascade)    │
│ set_number (tinyint unsigned: 1 to 4)           │
│ base_weight (varchar 50, nullable)              │
│ suggested_weight (varchar 50)                   │
│ approved_weight (varchar 50, nullable)          │
│ status ('pending','approved','rejected'         │
│        ,'conflict')                             │
│ conflict_current_weight (varchar 50, nullable)  │
│ conflict_detected_at (timestamp, nullable)      │
│ coach_notes (text, nullable)                    │
│ reviewed_by (FK -> user.id, nullable)           │
│ reviewed_at (timestamp, nullable)               │
│ applied_at (timestamp, nullable)                │
│ created_at / updated_at                         │
└─────────────────────────────────────────────────┘
```

### العلاقات المعرفة في النماذج (Eloquent Relationships):
- `TraineeLoadAdjustment` ينتمي إلى `Trainee` (`belongsTo`)، و`WorkoutPlan` (`belongsTo`)، و`Exercise` (`belongsTo`)، و`User` (المراجع عبر `reviewed_by`).
- `TraineeLoadAdjustment` يمتلك العديد من المجموعات `TraineeLoadAdjustmentItem` عبر العلاقة `hasMany('items')`.
- `TraineeLoadAdjustmentItem` ينتمي إلى الأب `TraineeLoadAdjustment` عبر `belongsTo('loadAdjustment')`، وإلى المراجع `User` عبر `belongsTo('reviewer')`.

---

## 5. دورة حياة الإشعارات والخلل المرصود (Notification Lifecycle & Detected Issues)

خلال التدقيق المعمق لخدمة الإشعارات الداخلية [`app/Services/InAppNotificationService.php`](file:///e:/laragon/www/cap_mahmoud_gym/app/Services/InAppNotificationService.php) ووحدة التحكم، تم رصد عدة ثغرات معمارية وتشغيلية بالغة الأهمية:

### أ. إشعار إرسال الطلب (من المتدرب إلى المدرب):
- **نقطة الإطلاق:** يتم استدعاؤه في `LoadAdjustmentController::store()` عبر الدالة `notifyCoachLoadAdjustmentSubmitted($report)`.
- **العيوب والمشاكل المرصودة:**
  1. **خلل استعلام المدربين:** تستخدم الخدمة:
     ```php
     User::where('user_type', 1)->where('status', '1')->get()
     ```
     في جدول المستخدمين `user`، يتم تمثيل حالة النشاط أحياناً بـ `'active'` أو `'1'`، ما قد يؤدي إلى استثناء بعض المدربين الفعليين. بالإضافة إلى إرسال الإشعار لجميع مدراء النظام بدلاً من توجيهه للمدرب المخصص للمتدرب إن وجد.
  2. **رابط التوجيه المفقود (Misleading Deep Link):**
     الرابط المحفوظ في الإشعار هو:
     ```php
     'action_url' => route('system.trainee.show', $report->trainee_id)
     ```
     عند نقر المدرب على الإشعار من القائمة العلوية، ينتقل إلى صفحة المتدرب ويفتح التبويب الأول (الملف الشخصي العام)، بينما تقع طلبات تعديل الأوزان في التبويب الثامن العميق `#kt_trainee_load_updates_tab`. يضطر المدرب للبحث اليدوي داخل التبويبات!
  3. **أيقونة غير متوافقة:** تستخدم الشيفرة `'icon' => 'fa-scale-unbalanced-flip'` من FontAwesome القديمة، بينما يعتمد قالب Metronic v8 الحديث في الترويسة العلوية على أيقونات KeenIcons (`ki-outline ki-...`)، مما يتسبب في ظهور فراغ أو أيقونة مشوهة في شريط التنبيهات.

### ب. إشعار مراجعة واعتماد الطلب (من المدرب إلى المتدرب) — **الخلل الأكبر (Critical Gap)**:
- **الانقطاع التام في المراجعات الفردية للمجموعات:**
  عندما يقوم المدرب بمراجعة مجموعة معينة في الواجهة الحالية (وهو الإجراء الأكثر شيوعاً)، يستدعي النظام المسار:
  `POST /system/load-adjustment/item/{id}/review` الذي ينفذ `LoadAdjustmentController::reviewItem()`.
  > [!CAUTION]
  > **لا يتم استدعاء أي إشعار على الإطلاق داخل دالة `reviewItem()`!**  
  > يظل المتدرب جاهلاً بقرار المدرب (سواء تم الاعتماد، أو الرفض، أو وضع وزن بديل) إلا إذا فتح صفحة التمارين يدوياً وفحص السجل بنفسه.
- **قصور دالة التقرير الكلي القديمة:**
  دالة `notifyTraineeLoadAdjustmentReviewed()` تستقبل التقرير الأب فقط `$report`، وتطلق رسالة عامة "قام المدرب بمراجعة طلب تعديل الحمل..." دون توضيح المجموعات، أو الأوزان المعتمدة، أو أسباب الرفض.
  كما تستخدم أيقونة FontAwesome (`fa-dumbbell`) غير المتناسقة مع واجهة التنبيهات.

---

## 6. نتائج وتدقيق الصلاحيات والقيود الخاصة بالمتدربين (Permissions & Trainee Access Restrictions)

### التحليل الأمني لصلاحيات المتدرب في التنبيهات:
تتطلب المعايير الصارمة للنظام ما يلي:
> **يجب أن يقتصر وصول المتدرب في نظام الإشعارات على: أيقونة الجرس في الترويسة، عداد التنبيهات غير المقروءة، القائمة المنسدلة السريعة (Dropdown Preview)، وتحديد الإشعار كمقروء والانتقال للهدف. ويُمنع المتدرب منعاً باتاً من فتح الصفحة الكاملة لجميع التنبيهات (`system.notifications.index`).**

### الوضع الفعلي الحالي في الشيفرة (المخالف للسياسة):
1. **في ملف وسيط الصلاحيات [`app/Http/Middleware/Permission.php`](file:///e:/laragon/www/cap_mahmoud_gym/app/Http/Middleware/Permission.php):**
   المسار `system.notifications.index` مدرج ضمن المصفوفة البيضاء `$whitelist` المتاحة لكافة المستخدمين المسجلين دون فحص نوع المستخدم (`user_type`).
2. **في المساعد العام [`app/Helpers/Global.helpers.php`](file:///e:/laragon/www/cap_mahmoud_gym/app/Helpers/Global.helpers.php):**
   المسار `system.notifications.index` مسجل صراحة ضمن مصفوفة `$traineeRoutes` المسموح للمتدرب طلبها.
3. **في القائمة الجانبية [`resources/views/system/menus.blade.php`](file:///e:/laragon/www/cap_mahmoud_gym/resources/views/system/menus.blade.php):**
   يظهر رابط صفحة التنبيهات في القائمة.
4. **في ترويسة الصفحة وقائمة الجرس [`resources/views/system/partials/header.blade.php`](file:///e:/laragon/www/cap_mahmoud_gym/resources/views/system/partials/header.blade.php):**
   يحتوي ذيل القائمة المنسدلة للجرس على زر:
   ```html
   <a href="{{ route('system.notifications.index') }}">عرض كافة الإشعارات</a>
   ```
   وهو متاح للنقر لجميع المستخدمين بمن فيهم المتدربون.
5. **في وحدة تحكم الإشعارات [`app/Modules/System/NotificationController.php`](file:///e:/laragon/www/cap_mahmoud_gym/app/Modules/System/NotificationController.php):**
   دالة `index()` لا تحتوي على أي فحص يمنع المتدرب (`user_type == 2`)، مما يسمح للمتدرب بفتح شاشة الإدارة الكاملة للإشعارات.

### خطة تصحيح الصلاحيات المطلوبة مستقبلاً:
| الموضع | التعديل المستهدف | النتيجة المتوقعة |
| :--- | :--- | :--- |
| `NotificationController::index()` | إضافة فحص نوع المستخدم؛ إذا كان `user_type == 2` يتم إرجاع `abort(403)` أو إعادة توجيهه للوحة المتدرب. | منع الوصول المباشر عبر الروابط لصفحة الإدارة. |
| `Global.helpers.php` | حذف `system.notifications.index` من مصفوفة `$traineeRoutes`. | إغلاق الباب أمام أي تجاوز في مسارات المتدربين. |
| `header.blade.php` | إخفاء زر "عرض كافة الإشعارات" (View All) إذا كان المستخدم الحالي متدرباً (`Auth::user()->user_type == 2`). | بقاء المتدرب داخل نطاق القائمة المنسدلة فقط. |
| المسارات المسموحة للمتدرب | الإبقاء الكامل على: `system.notifications.dropdown`, `system.notifications.read`, `system.notifications.read-all`. | تمكين المتدرب من قراءة تنبيهاته من القائمة المنسدلة بسلاسة. |

---

## 7. مشاكل واجهة وتجربة المستخدم للمدرب ومواضع الاحتكاك (Coach UI/UX Issues & Friction Points)

خلال تدقيق واجهات المدرب، تم تحديد 4 نقاط احتكاك رئيسية تعيق كفاءة المتابعة اليومية:

1. **الدفن العميق لطلبات التعديل في صفحة المتدرب (Deep Nesting Friction):**
   - صفحة تفاصيل المتدرب [`resources/views/system/trainee/show.blade.php`](file:///e:/laragon/www/cap_mahmoud_gym/resources/views/system/trainee/show.blade.php) ضخمة جداً (أكثر من 2600 سطر) وتحتوي على 8 تبويبات فرعية.
   - تبويب "تحديثات الأوزان" هو التبويب الأخير (رقم 8). لا يمكن للمدرب معرفة وجود طلب جديد إلا إذا فتح صفحة كل متدرب على حدة ونقر على التبويب الثامن.
2. **غياب شاشة مراجعة مركزية موحدة لكافة المتدربين (Lack of Centralized Queue):**
   - إذا قدم 10 متدربين طلبات لتعديل الأوزان في أوقات متفرقة، لا يمتلك المدرب شاشة مركزية (Inbox / Queue) تجمع الطلبات المعلقة لجميع المتدربين في مكان واحد، مما يؤدي إلى تأخر الردود وتشتت المدرب.
3. **خلل التوجيه من الإشعارات (Broken Context Navigation):**
   - عندما ينقر المدرب على الإشعار الوارد إليه بطلب تعديل، يفتحه النظام على الصفحة العامة للمتدرب، ويحتاج المدرب لتذكر مسار التبويب وتغييره يدوياً.
4. **تداخل البطاقات في الشاشات المتوسطة والصغيرة:**
   - جدول المراجعة الحالي داخل التبويب الثامن يحتوي على عناصر فرعية مضمنة تتطلب تمرير أفقي غير مريح عند تصفح المدرب من أجهزة لوحية أو هواتف ذكية داخل صالة التدريب.

---

## 8. مقترح التصميم والتنظيم المبسط للوحة المدرب (Proposed Simplified Coach Review Interface)

لحل مشاكل الاحتكاك وجعل تجربة المدرب ترتقي إلى مستوى تطبيقات الـ SaaS الرياضية العالمية، نقترح هيكلية واجهة ترتكز على محورين:

### أ. إنشاء "مركز مراجعة أحمال التمارين" (Centralized Load Adjustment Center)
صفحة مركزية مستقلة وخفيفة للمدرب (`system.load-adjustment.index`) أو بطاقة تشغيلية عليا في لوحة تحكم المدرب، توفر:
- **شريط المؤشرات السريعة (Counters Bar):**
  - عدد الطلبات المعلقة (Pending Requests).
  - عدد المجموعات المعتمدة هذا الأسبوع (Approved This Week).
  - التنبيهات المعلقة التي بها تعارض وتنتظر قراراً (Conflicts Requiring Action).
- **قائمة مراجعة سريعة وموحدة (Unified Review Queue):**
  - جدول مدمج يعرض: اسم المتدرب + صورة حسابه، اليوم التدريبي والتمرين، المجموعات المطلوبة وأوزانها الحالية والمقترحة، وملاحظة المتدرب.
  - أزرار قرار سريعة لكل مجموعة أو للطلب كاملاً:
    - زر اعتماد مباشر بنقرة واحدة (One-Click Approve).
    - حقل سريع لإدخال وزن بديل واعتماده (Approve with Custom Weight).
    - زر رفض مسبب.
- **تصفية ذكية (Smart Filters):** تصفية حسب اسم المتدرب، أو حالة الطلب (معلق / متعارض / تمت مراجعته)، أو التاريخ.

### ب. تحسين التبويب المضمن في صفحة تفاصيل المتدرب (`trainee.show`):
- دعم تنشيط التبويب تلقائياً عبر الرابط (Auto-tab activation via `#kt_trainee_load_updates_tab`).
- تعديل رابط الإشعار المرسل للمدرب ليصبح:
  ```php
  route('system.trainee.show', ['id' => $report->trainee_id, 'tab' => 'load_updates']) . '#kt_trainee_load_updates_tab'
  ```
  أو التوجيه مباشرة إلى طلب المراجعة في المركز الموحد.

---

## 9. حصر دقيق وشامل للملفات المطلوبة للتعديل مستقبلاً (File Modification Inventory)

عند الموافقة على خطة التنفيذ، ستشمل التعديلات الملفات التالية بدقة ودون أي مساس بباقي أجزاء النظام:

```
├── app/
│   ├── Helpers/
│   │   └── Global.helpers.php                       [تعديل: حذف system.notifications.index من مسارات المتدربين]
│   ├── Http/
│   │   ├── Middleware/
│   │   │   └── Permission.php                       [تدقيق: فحص نوع المستخدم عند طلب شاشة التنبيهات الكاملة]
│   │   └── Requests/
│   │       ├── LoadAdjustmentReportRequest.php      [سليم تماماً - الحفاظ الكامل عليه]
│   │       └── LoadAdjustmentItemReviewRequest.php  [سليم تماماً - الحفاظ الكامل عليه]
│   ├── Modules/
│   │   └── System/
│   │       ├── LoadAdjustmentController.php         [تعديل: إطلاق إشعار المتدرب داخل reviewItem، وإضافة index للمدرب]
│   │       └── NotificationController.php           [تعديل: تقييد دالة index() ومنع المتدربين مع إرجاع 403]
│   └── Services/
│       └── InAppNotificationService.php             [تعديل: صياغة إشعار المجموعات الفردية، استبدال الأيقونات بـ KeenIcons، تصحيح الروابط العميقة]
├── resources/
│   └── views/
│       └── system/
│           ├── menus.blade.php                      [تعديل: إخفاء رابط صفحة التنبيهات للمتدربين]
│           ├── partials/
│           │   └── header.blade.php                 [تعديل: إخفاء زر View All للمتدربين وقصرهم على القائمة المنسدلة]
│           ├── trainee/
│           │   └── show.blade.php                   [تعديل: دعم التنشيط التلقائي لتبويب تحديثات الأوزان من الروابط]
│           └── workout/
│               └── partials/
│                   ├── trainee-load-adjustment-inline-row.blade.php  [سليم ومحفوظ بالكامل دون أي مساس]
│                   └── coach-load-adjustment-sets-card.blade.php     [تحسينات خفيفة على المظهر والاستجابة]
└── tests/
    └── Feature/
        └── TraineeExerciseLoadUpdateTest.php        [إضافة: اختبارات الإشعارات، تقييد صلاحيات المتدربين، وروابط التوجيه]
```

---

## 10. التعديلات المطلوبة على قاعدة البيانات (Database Changes Evaluation)

> [!TIP]
> **نتيجة الفحص الهيكلي: لا توجد أي حاجة لإنشاء جداول جديدة أو تعديل الجداول الحالية.**

### تقييم بنية البيانات:
1. جدول `trainee_load_adjustments` وجدول `trainee_load_adjustment_items` تم بناؤهما وفق معايير عليا تدعم التصميم الشجري (Parent-Child).
2. جدول المجموعات يشتمل بالفعل على:
   - كافة الحالات المحتملة (`pending`, `approved`, `rejected`, `conflict`).
   - حقول التعارض الزمني وتاريخه والوزن المتعارض.
   - حقول تتبع المراجع وتاريخ المراجعة وتاريخ التطبيق.
   - حقل ملاحظات المدرب المستقلة لكل مجموعة.
3. الفهارس الحالية (`indexes`) كافية ومحسنة للاستعلامات السريعة.
4. جدول الإشعارات `in_app_notifications` يشتمل على `action_url` و`icon` و`type` و`metadata` ويدعم كافة أنواع التنبيهات الحالية والمستقبلية.

---

## 11. الاختبارات المفقودة أو غير الكافية (Missing & Inadequate Tests)

يحتوي ملف الاختبارات الحالي [`tests/Feature/TraineeExerciseLoadUpdateTest.php`](file:///e:/laragon/www/cap_mahmoud_gym/tests/Feature/TraineeExerciseLoadUpdateTest.php) على 24 اختباراً تغطي منطق الأعمال الأساسي (Business Logic) والتعارض وقفل المعاملات وصلاحيات المتدرب على تمارينه.  
**ومع ذلك، توجد فجوات اختبارية جوهرية يجب تغطيتها في المرحلة القادمة:**

1. **اختبارات دورة حياة الإشعارات (Missing Notification Lifecycle Tests):**
   - عدم وجود اختبار يؤكد إنشاء سجل إشعار للمدرب في جدول `in_app_notifications` عند تقديم المتدرب لطلب جديد.
   - عدم وجود اختبار يؤكد إنشاء سجل إشعار للمتدرب عند قيام المدرب بمراجعة مجموعة معينة عبر `reviewItem()`.
   - عدم فحص صحة نص الإشعار، واحتوائه على رقم المجموعة والوزن المعتمد وملاحظة المدرب.
2. **اختبارات تقييد وصول المتدرب للإشعارات (Missing Trainee Access Restriction Tests):**
   - عدم وجود اختبار يتحقق من أن المتدرب يتلقى كود `403 Forbidden` عند محاولة طلب صفحة `system.notifications.index`.
   - عدم وجود اختبار يتحقق من قدرة المتدرب على جلب تنبيهاته عبر القائمة المنسدلة `system.notifications.dropdown` وتحديد الإشعار كمقروء عبر `system.notifications.read`.
3. **اختبارات روابط التوجيه المباشرة (Deep Linking Tests):**
   - التحقق من أن رابط الإشعار الموجه للمتدرب يقوده دائماً إلى لوحة التمارين الخاصة به `system.dashboard.trainer`.
   - التحقق من أن رابط الإشعار الموجه للمدرب يقوده إلى التبويب المباشر لتحديثات الأوزان أو المركز الموحد.

---

## 12. خطة تنفيذية مرحلية مقترحة (Phased Implementation Plan)

نوصي بتنفيذ التحسينات المطلوبة عبر أربع مراحل متتابعة ومضبوطة بأعلى معايير السلامة:

### المرحلة الأولى: إحكام الصلاحيات وتقييد وصول المتدربين (Permissions Hardening)
- تحديث دالة `NotificationController::index()` لمنع المتدربين بإرجاع `403` صريح.
- إزالة المسار من مصفوفة مسارات المتدربين في `Global.helpers.php`.
- إخفاء زر "عرض كافة الإشعارات" في الترويسة `header.blade.php` والقائمة الجانبية للمتدربين.
- كتابة اختبارات تأكيدية في ملف مخصص للتحقق من صرامة المنع.

### المرحلة الثانية: سد فجوة الإشعارات وتحسين جودتها (Notification Engine Completion)
- إضافة استدعاء إشعار المتدرب داخل `LoadAdjustmentController::reviewItem()` عند اعتماد أو رفض كل مجموعة.
- ترقية خدمة `InAppNotificationService`:
  - صياغة دالة مخصصة `notifyTraineeLoadAdjustmentItemReviewed($item)` تتضمن رقم المجموعة، والوزن المعتمد، وملاحظات المدرب.
  - استبدال أيقونات FontAwesome بأيقونات KeenIcons المتوافقة مع القالب (مثل `ki-outline ki-weight` و `ki-outline ki-notification-status`).
  - ضبط استعلام المدربين ليدعم قيم الحالة المختلفة وحسابات المدربين النشطة.
- كتابة اختبارات متكاملة تؤكد وصول الإشعار للمتدرب والمدرب فور كل عملية.

### المرحلة الثالثة: تحسين تجربة المدرب وروابط التوجيه (Coach Experience & Deep Linking)
- تعديل رابط إشعار المدرب ليوجهه مباشرة إلى تبويب تحديثات الأوزان مع تمرير المتغير الخاص بفتح التبويب فورياً.
- إضافة ميزة التنشيط التلقائي للتبويب عبر JavaScript في `trainee.show` إذا تضمن الرابط الهاش أو المعامل المحدد.
- (اختياري / مستقبلي): تجهيز صفحة مراجعة مركزية موحدة للمدرب تجمع كافة الطلبات المعلقة لجميع المتدربين.

### المرحلة الرابعة: الاختبارات الشاملة والتحقق النهائي (Testing & Verification)
- تشغيل طاقم الاختبارات كاملاً `php artisan test --filter=TraineeExerciseLoadUpdateTest`.
- التحقق الميداني من تجربة المتدرب في صفحة التمارين والتأكد بنسبة 100% من عدم تأثر مسار تقديم الطلب القائم تحت كل تمرين.
- توثيق كافة التغييرات المنجزة وتحديث ملفات التوثيق الفني للمشروع.

---
**نهاية تقرير التدقيق الفني.**  
*تم إعداده بواسطة كبير مهندسي ومصممي النظم المعمارية، وجاهز للمراجعة والمصادقة للبدء في مراحل التنفيذ.*

