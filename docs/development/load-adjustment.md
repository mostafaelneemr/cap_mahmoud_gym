# توثيق منظومة تعديل أحمال التمارين وموثوقية الإشعارات (Load Adjustment & Notification Reliability)

**المشروع:** نظام إدارة جيم شلتوت (Shaltout Gym Management System)  
**الفرع:** `dashboard-redesign`  
**بيئة التشغيل:** Laravel 13.35 / PHP 8.5.5 / MySQL 8.0.34  
**التاريخ:** نوفمبر 2026  

---

## 1. نظرة عامة والهدف المعماري (Architectural Overview)

تم تطوير منظومة تعديل أحمال التمارين (`Load Adjustment System`) لتوفير دورة تدريبية تفاعلية متكاملة وآمنة بين المتدرب والمدرب:
1. **الحفاظ الصارم على تجربة المتدرب القائمة:** يواصل المتدرب تقديم طلبات تعديل أوزان مجموعات التمارين مباشرة من داخل جدول التمارين اليومي أسفل كل تمرين دون أي تغيير في مسار إدخاله.
2. **مركز مراجعة موحد ومبسط للمدرب (`system.load-adjustment.index`):** تم إنشاء شاشة مراجعة مركزية سريعة تجيب المدرب فوراً على الأسئلة التشغيلية الثلاثة: من المتدربون الذين طلبوا تعديلاً؟ ما المجموعات التي تنتظر القرار؟ وما الإجراء المطلوب اتخاذه؟
3. **موثوقية واكتمال دورة حياة الإشعارات الداخلية:** سد الفجوة البرمجية الحرجة بإطلاق إشعارات فورية دقيقة للمتدرب عند قيام المدرب بمراجعة واعتماد أو رفض كل مجموعة تدريبية بشكل مستقل، مع دعم الأوزان البديلة وحل التعارضات وأيقونات KeenIcons المتوافقة.
4. **الامتثال الصارم لسياسة صلاحيات المتدرب في الإشعارات:** قصر وصول المتدرب حصرياً على جرس الترويسة، عداد التنبيهات، القائمة المنسدلة السريعة (Dropdown Preview)، وتحديد الإشعار كمقروء، مع **مَنع المتدرب خادمياً من دخول صفحة التنبيهات الكاملة بإرجاع كود `403 Forbidden`** وإخفاء الروابط من واجهته.

---

## 2. مسار تدفق طلب المتدرب المحفوظ (Trainee Submission Workflow)

> [!IMPORTANT]
> **التأكيد على ثبات تجربة المتدرب وعدم نقلها:**  
> لم يتم نقل مسار طلب المتدرب إلى أي صفحة خارجية، بل تم الإبقاء الكامل عليه أسفل كل تمرين داخل صفحة التمارين الخاصة به.

```
[صفحة التمارين: trainer-dashboard.blade.php]
                  │
                  ▼ (النقر على زر "تعديل الحمل / الأوزان")
[الواجهة الجزئية المضمنة: trainee-load-adjustment-inline-row.blade.php]
                  │
                  ├─► اختيار المجموعات عبر مربعات الاختيار (Set 1, Set 2, ...)
                  ├─► إدخال الوزن المقترح الجديد لكل مجموعة
                  └─► كتابة ملاحظة توضيحية اختيارية للمدرب
                  │
                  ▼ (إرسال AJAX POST إلى system.load-adjustment.store)
[LoadAdjustmentReportRequest: التحقق ومنع IDOR وصلاحية الخطة]
                  │
                  ▼
[LoadAdjustmentService::storeReport (معاملة ذرية DB::transaction)]
                  │
                  ├─► إنشاء سجل الأب TraineeLoadAdjustment
                  ├─► إنشاء سجلات المجموعات TraineeLoadAdjustmentItem مع حفظ الوزن الأساسي لحظياً
                  └─► إطلاق إشعار للمدربين المؤهلين (notifyCoachLoadAdjustmentSubmitted)
                  │
                  ▼
[تحديث لحظي عبر SweetAlert وظهور الحالة في الأكورديون: trainee-load-adjustment-day-history.blade.php]
```

### الملفات المعتمدة لمسار المتدرب:
- [`resources/views/system/trainer-dashboard.blade.php`](file:///e:/laragon/www/cap_mahmoud_gym/resources/views/system/trainer-dashboard.blade.php): الحاوية الرئيسية لجدول تمارين المتدرب.
- [`resources/views/system/workout/partials/trainee-load-adjustment-inline-row.blade.php`](file:///e:/laragon/www/cap_mahmoud_gym/resources/views/system/workout/partials/trainee-load-adjustment-inline-row.blade.php): الصف الموسع أسفل التمرين لاختيار المجموعات وإدخال الأوزان.
- [`resources/views/system/workout/partials/trainee-load-adjustment-day-history.blade.php`](file:///e:/laragon/www/cap_mahmoud_gym/resources/views/system/workout/partials/trainee-load-adjustment-day-history.blade.php): الأكورديون السفلي لعرض السجل الزمني للتعديلات والقرارات السابقة لليوم التدريبي.

---

## 3. مركز مراجعة أحمال التمارين للمدرب (Centralized Coach Review Center)

تم إنشاء صفحة مركزية مخصصة للمدرب بالمسار:
**`GET /system/load-adjustment` (`system.load-adjustment.index`)**

### أ. كروت المؤشرات التشغيلية العلوية (KPI Summary Cards):
| المؤشر | المعنى التشغيلي | مصدر البيانات |
| :--- | :--- | :--- |
| **مجموعات تنتظر القرار (Pending Sets)** | إجمالي المجموعات التدريبية الفردية المطلوب البت فيها | `TraineeLoadAdjustmentItem::where('status', 'pending')->count()` |
| **طلبات قيد المراجعة (Pending Requests)** | جلسات طلبات تعديل الأحمال المفتوحة كلياً أو جزئياً | `TraineeLoadAdjustment::whereIn('status', ['pending', 'partially_reviewed'])->count()` |
| **مجموعات بها تعارض (Conflicting Sets)** | المجموعات التي تغير وزن الخطة التدريبية لها بعد تقديم الطلب | `TraineeLoadAdjustmentItem::where('status', 'conflict')->count()` |

### ب. شريط التصفية والبحث (Filters Toolbar):
- **بحث المتدرب:** حقل نصي للبحث الفوري باسم أو بريد المتدرب.
- **تصفية الحالة:** خيارات تشمل (قيد الانتظار والمراجعة الجزئية، المتعارضة فقط، تمت المراجعة بالكامل، تم الإلغاء، كافة الطلبات).
- **تصفية التاريخ:** تقويم لتحديد تاريخ تقديم الطلب بدقة.
- **زر إعادة الضبط:** تفريغ الفلاتر والعودة للوضع الافتراضي بنقرة واحدة.

### ج. كروت الطلبات وقرارات المجموعات المستقلة:
تُعرض الطلبات في شكل كروت واضحة ومتباعدة، تحتوي كل منها على:
1. **معلومات الترويسة:** اسم المتدرب (مع رابط مباشر لملفه)، اليوم التدريبي، وتاريخ وتوقيت الطلب الدقيق ومؤشر الحالة الإجمالي.
2. **بيانات التمرين وملاحظة المتدرب:** اسم التمرين، الوزن المسجل في الخطة، وملاحظة المتدرب داخل صندوق اقتباس أنيق.
3. **صفوف المجموعات المستقلة:**
   - رقم المجموعة (المجموعة 1، 2، ...).
   - الوزن الأساسي وقت الطلب + الوزن الحالي في الخطة + الوزن المطلوب.
   - حقل إدخال اختياري لملاحظة المدرب التوجيهية لهذه المجموعة.
   - أزرار التحكم السريعة:
     - **اعتماد الوزن المقترح (`approve`):** يطبق الوزن المطلوب فوراً على الخطة ويحفظ القرار.
     - **اعتماد بوزن بديل (`approve_alternative`):** صندوق مخصص لإدخال وزن مختلف يحدده المدرب مع زر الاعتماد.
     - **رفض (`reject`):** رفض التعديل وتثبيت وزن الخطة دون أي تغيير.
     - **حل التعارض (`resolve_approve` / `resolve_reject`):** يظهر شريط تحذيري أحمر مع زر فرض الاعتماد أو رفض المتعارض في حال تغير وزن الخطة في هذه الأثناء.

---

## 4. دورة حياة قرارات المجموعات وحماية التعارض (Set Lifecycle & Conflict Protection)

### قواعد العمل الحتمية:
1. **استقلالية المجموعات (Set Independence):**
   - كل مجموعة تدريبية تُعامل ككيان مستقل تماماً.
   - اعتماد المجموعة رقم 1 لا يؤثر على المجموعة رقم 2 أو 3.
   - لا يوجد أي إلزام بالاعتماد الجماعي؛ يمكن للمدرب مراجعة مجموعة واحدة وتأجيل الباقي.
2. **منع الكتابة التلقائية عند التعارض (Conflict Protection):**
   - عند محاولة اعتماد مجموعة، يقارن النظام الوزن الأساسي وقت تقديم الطلب `$item->base_weight` مع الوزن المقيد حالياً في الخطة `$exercise->getWeightForSet()`.
   - إذا اختلف الوزنان، يتحول العنصر تلقائياً إلى حالة `conflict`، ويتم إيقاف التطبيق التلقائي لحماية الخطة.
   - يتطلب ذلك قراراً صريحاً من المدرب عبر تمرير `force: true` لاعتماد الوزن أو رفضه.
3. **قفل المعاملات والحماية من السباق المتزامن (Concurrency Locking):**
   - تعتمد الدالة `LoadAdjustmentService::reviewItem()` على المعاملات الذرية `DB::transaction()` والقفل التشاؤمي `lockForUpdate()` على جدول `trainee_load_adjustment_items` و `exercises` لمنع أي تضارب عند نقر المدرب المزدوج أو مراجعة متزامنة.

---

## 5. دورة حياة الإشعارات ومنطق تحديد المستلمين (Notification Lifecycle)

### أ. إشعار تقديم الطلب (من المتدرب إلى المدرب):
- **الحدث:** عند نجاح دالة `LoadAdjustmentController::store()`.
- **قواعد أهلية المستلمين (Eligible Coaches):**
  - استخراج المستخدمين النشطين (`status IN (1, '1', 'active')`).
  - غير المتدربين (`user_type == 1` أو أعضاء مجموعة المدراء `permission_group_id == 119`).
- **محتوى الإشعار:**
  - العنوان: `طلب تعديل أوزان جديد: {اسم المتدرب}`
  - الرسالة: `أرسل المتدرب {اسم المتدرب} طلباً لتعديل أحمال {عدد المجموعات} مجموعات لتمرين ({اسم التمرين}) في ({اليوم التدريبي}).`
  - الرابط المباشر (`action_url`): يقود المدرب مباشرة إلى مركز مراجعة الأحمال مع تحديد الطلب:  
    `route('system.load-adjustment.index', ['request_id' => $adjustment->id])`
  - الأيقونة: `ki-weight` (KeenIcons).

### ب. إشعار مراجعة المجموعة الفردية (من المدرب إلى المتدرب):
- **الحدث:** عند نجاح دالة `LoadAdjustmentController::reviewItem()`.
- **المستلم:** المتدرب صاحب الطلب حصراً (`$item->loadAdjustment->trainee->user`).
- **أنواع الإشعار حسب القرار:**
  1. **الاعتماد بالوزن المقترح:**
     - العنوان: `اعتماد تعديل الحمل: {اسم التمرين}`
     - الرسالة: `تم اعتماد الوزن المقترح ({الوزن المعتمد}) للمجموعة {رقم المجموعة}. ملاحظة المدرب: {الملاحظة إن وجدت}`
     - الأيقونة: `ki-check-circle`
  2. **الاعتماد بوزن بديل يحدده المدرب:**
     - العنوان: `اعتماد تعديل الحمل: {اسم التمرين}`
     - الرسالة: `وافق المدرب على تعديل المجموعة {رقم المجموعة} بوزن بديل ({الوزن البديل}) بدلاً من المقترح ({الوزن المقترح}). ملاحظة المدرب: {الملاحظة إن وجدت}`
     - الأيقونة: `ki-check-circle`
  3. **رفض الطلب:**
     - العنوان: `رفض تعديل الحمل: {اسم التمرين}`
     - الرسالة: `تم رفض طلب تعديل وزن المجموعة {رقم المجموعة} وتثبيت الوزن الحالي. ملاحظة المدرب: {الملاحظة إن وجدت}`
     - الأيقونة: `ki-cross-circle`
- **الرابط الموجه للمتدرب:** `route('system.dashboard.trainer')` ليفتح جدول التمارين المحدث فوراً.

### ج. حماية المعاملات والتكرار:
- لا يتم إرسال الإشعار إلا بعد إتمام المعاملة وحفظ البيانات في الخطة بنجاح.
- في حالة فشل التحقق أو وقوع خطأ، يتم التراجع عن المعاملة ولا يتم إرسال أي إشعار.
- تمنع دالة `InAppNotificationService::send()` تكرار الإشعارات المتطابقة غير المقروءة خلال ساعة واحدة.

---

## 6. تقييد صلاحيات المتدربين في الإشعارات (Notification Permissions & Security)

تم تطبيق السياسة الأمنية بدقة مزدوجة على مستوى وسيط الحماية (Middleware) ووحدة التحكم (Controller) وواجهات المستخدم (Views):

| المكون | آلية الحماية المطبقة | النتيجة |
| :--- | :--- | :--- |
| **`Permission.php`** | إضافة فحص صريح للمتدربين (`user_type == 2`)؛ إذا كان المسار `system.notifications.index` أو `system.load-adjustment.index` يتم إرجاع `abort(403)`. | منع أي محاولة وصول مباشرة عبر المتصفح برمز `403 Forbidden`. |
| **`NotificationController::index()`** | فحص هوية المستخدم؛ إذا كان متدرباً يُرجع `abort(403, 'Unauthorized access.')`. | حماية مزدوجة على مستوى المتحكم. |
| **`header.blade.php`** | تغليف زر "عرض كافة الإشعارات" بشرط `@if(auth()->user()->user_type != 2)`. | اختفاء الرابط تماماً من القائمة المنسدلة للمتدرب. |
| **`Global.helpers.php`** | خلو مصفوفة `$traineeRoutes` من المسار `system.notifications.index` أو `system.load-adjustment.index`. | إغلاق الصلاحية عبر دالة `userCan()`. |
| **`NotificationController::read()`** | الاستعلام عبر `where('id', $id)->where('user_id', $userId)->firstOrFail()`. | حماية تامة ضد ثغرات IDOR؛ لا يمكن للمستخدم قراءة أو تعديل إشعار مستخدم آخر. |

---

## 7. الروابط العميقة (Deep Links)

1. **إشعار المدرب:** يوجه إلى `route('system.load-adjustment.index', ['request_id' => $id])`.  
   - تقوم شيفرة JavaScript في مركز المراجعة بالتمرير التلقائي للكرت المطلوب وتمييز حوافه باللون الأزرق (`border-primary`).
2. **التبويب الثامن في صفحة المتدرب (`trainee.show`):**  
   - تمت إضافة شيفرة تنشيط تلقائي تكتشف وجود الهاش `#kt_trainee_load_updates_tab` أو المتغير `tab=load_updates` في الرابط وتقوم بفتح التبويب فورياً عبر كائن `bootstrap.Tab`.

---

## 8. حصر الملفات البرمجية والمسارات (Codebase Inventory)

### أ. الملفات التي تم إنشاؤها:
1. [`resources/views/system/load-adjustment/index.blade.php`](file:///e:/laragon/www/cap_mahmoud_gym/resources/views/system/load-adjustment/index.blade.php): الواجهة المركزية لمراجعة أحمال التمارين للمدرب.
2. [`tests/Feature/LoadAdjustmentNotificationAndCenterTest.php`](file:///e:/laragon/www/cap_mahmoud_gym/tests/Feature/LoadAdjustmentNotificationAndCenterTest.php): طاقم اختبارات شامل يضم 16 اختبار ميزة.
3. [`docs/development/load-adjustment.md`](file:///e:/laragon/www/cap_mahmoud_gym/docs/development/load-adjustment.md): هذا التوثيق الفني الشامل.

### ب. الملفات التي تم تعديلها:
1. [`app/Http/Middleware/Permission.php`](file:///e:/laragon/www/cap_mahmoud_gym/app/Http/Middleware/Permission.php): إحكام إرجاع 403 للمتدربين عند طلب شاشة التنبيهات أو مركز مراجعة الأحمال.
2. [`app/Modules/System/NotificationController.php`](file:///e:/laragon/www/cap_mahmoud_gym/app/Modules/System/NotificationController.php): تقييد `index()` للمتدربين وتطبيع الأيقونات إلى KeenIcons في القائمة المنسدلة.
3. [`app/Services/InAppNotificationService.php`](file:///e:/laragon/www/cap_mahmoud_gym/app/Services/InAppNotificationService.php): إضافة `notifyTraineeLoadAdjustmentItemReviewed()`، ودعم الأوزان البديلة، وضبط استعلام المدربين والأيقونات.
4. [`app/Services/LoadAdjustmentService.php`](file:///e:/laragon/www/cap_mahmoud_gym/app/Services/LoadAdjustmentService.php): إضافة دوال `getSummaryStats()` و `getPaginatedRequests()`.
5. [`app/Modules/System/LoadAdjustmentController.php`](file:///e:/laragon/www/cap_mahmoud_gym/app/Modules/System/LoadAdjustmentController.php): إضافة دالة العرض `index()` وإطلاق إشعار المتدرب داخل `reviewItem()`.
6. [`routes/system.php`](file:///e:/laragon/www/cap_mahmoud_gym/routes/system.php): تسجيل المسار `system.load-adjustment.index`.
7. [`app/Helpers/Global.helpers.php`](file:///e:/laragon/www/cap_mahmoud_gym/app/Helpers/Global.helpers.php): إضافة المسارات لقائمة `ignoredRoutes()`.
8. [`resources/views/system/partials/header.blade.php`](file:///e:/laragon/www/cap_mahmoud_gym/resources/views/system/partials/header.blade.php): إخفاء زر "عرض كافة الإشعارات" للمتدربين.
9. [`resources/views/system/menus.blade.php`](file:///e:/laragon/www/cap_mahmoud_gym/resources/views/system/menus.blade.php): إضافة رابط "مركز مراجعة الأحمال" في القائمة الجانبية للمدربين.
10. [`resources/views/system/trainee/show.blade.php`](file:///e:/laragon/www/cap_mahmoud_gym/resources/views/system/trainee/show.blade.php): دعم التنشيط التلقائي لتبويب تحديثات الأوزان.

---

## 9. الاختبارات والتحقق الآلي (Testing & Verification)

تم التحقق من كافة المتطلبات عبر تشغيل طواقم الاختبار الآلية:

### أ. اختبارات الميزة الجديدة (`LoadAdjustmentNotificationAndCenterTest`):
```bash
php artisan test --filter=LoadAdjustmentNotificationAndCenterTest
```
**النتيجة:**
`PASS  Tests\Feature\LoadAdjustmentNotificationAndCenterTest`
- `✓ new request creates correct coach notification`
- `✓ individual approved set creates trainee notification`
- `✓ individual rejected set creates trainee notification`
- `✓ alternative approved weight accurately represented`
- `✓ conflict resolution creates correct notification`
- `✓ failed transactions do not create success notifications`
- `✓ duplicate review submissions do not send duplicate notifications`
- `✓ notification unread count updates correctly`
- `✓ coach can access full notifications page`
- `✓ trainee receives http 403 when accessing full notifications page`
- `✓ trainee can access header dropdown`
- `✓ trainee can mark own notification as read`
- `✓ trainee cannot read or modify another users notification`
- `✓ coach can access centralized load adjustment page`
- `✓ trainee receives http 403 on centralized load adjustment page`
- `✓ existing trainee submission flow beneath exercise still functions`
**الإحصائية:** `16 passed (61 assertions)` في 3.33 ثانية.

### ب. اختبارات التوافقية السابقة (`TraineeExerciseLoadUpdateTest`):
```bash
php artisan test --filter=TraineeExerciseLoadUpdateTest
```
**النتيجة:** `24 passed (126 assertions)` في 14.93 ثانية (100% نجاح لكافة السيناريوهات السابقة).

### ج. اختبارات الإشعارات (`InAppNotificationTest`):
```bash
php artisan test --filter=InAppNotificationTest
```
**النتيجة:** `6 passed (29 assertions)` في 1.68 ثانية (100% نجاح).

---
**تم إعداد وتطبيق النظام بالكامل وفق المعايير المعمارية وأعلى ضوابط الأمان واستقرار الإنتاج.**

