# توثيق محرري النصوص المنسقة للإحماء وما بعد التمرين (Warm-up & Post-workout Rich-Text Editors)

## 1. نظرة عامة وتطور المعمارية (Architecture & Evolution)

تم استبدال نظام "منشئ المحتوى متعدد العناصر (Multi-item Content Builder)" السابق بحل أكثر استقراراً وسهولة في الاستخدام:
**محرر نصوص منسقة (Rich-Text Editor) مستقل للإحماء ومحرر نصوص منسقة مستقل لما بعد التمرين**، بالاعتماد الكامل على مكتبة **Quill.js 2** المدمجة مسبقاً في النظام والمتوافقة مع نظام تصميم Metronic.

يضمن هذا التحول:
1. تجربة مستخدم سلسة وخفيفة للمدرب دون الحاجة لنوافذ منبثقة أو إضافة عناصر متعددة معقدة.
2. تنسيق مرن وغني يشمل العناوين، الخط العريض والمائل، القوائم النقطية والرقمية، الجداول، الروابط، وفيديوهات الشرح.
3. استقلالية تامة بين حقلي الإحماء (Warm-up) وما بعد التمرين (Post-workout) دون أن يطغى أحدهما على الآخر.

---

## 2. السبب الجذري لمشكلة تكرار 5 عناصر بنقرة واحدة (Root Cause of 5-Item Duplication Bug)

### التشخيص والسبب الجذري:
في النموذج السابق، كان سكريبت `ContentBuilder` يعتمد على دالة `ContentBuilder.init()` التي تستدعي بداخلها `this.bindEvents()`. وكانت هذه الدالة تقوم بربط مستمعي الأحداث على مستوى كائن المستند العام:
```javascript
$(document).on('click', '#cb-modal-save-btn', ...);
$(document).on('click', '.cb-add-type-btn', ...);
```
دون فك الربط السابق `$(document).off(...)` ودون تفعيل علم تحقق (Flag). وعند إنشاء خطة تدريبية تتكون من 5 أيام (أو فتح المودال عدة مرات)، كانت دالة `init()` تُستدعى 5 مرات (أو 10 مرات لحساب قسمي الإحماء والتهدئة). ونتيجة لذلك، تم تسجيل 5 مستمعات نقر متطابقة على زر الحفظ `#cb-modal-save-btn`. وعند ضغط المدرب نقرة واحدة على زر الحفظ، كانت المستمعات الخمس تنفذ بالتتابع مسببة إضافة العنصر 5 مرات متتالية في قائمة DOM.

### المعالجة الجذرية:
1. تم استبعاد بنية الـ Content Builder والمودال المشترك بالكامل من واجهات الإحماء وما بعد التمرين.
2. تم استبدالها بتهيئة مباشرة للمحررين المستقلين لكل يوم عبر `WarmupQuill.init('#warmup_editor_step_' + i, '#warmup_input_step_' + i)` و `WarmupQuill.init('#post_workout_editor_step_' + i, '#post_workout_input_step_' + i)`.
3. تم التخلص نهائياً من أي تكرار لمستمعي الأحداث أو النوافذ المنبثقة التراكمية.

---

## 3. نطاق العمل الصارم (Strict Scope)

- **قسمي الإحماء وما بعد التمرين فقط**: التعديلات محصورة حصراً بـ Warm-up و Post-workout.
- **عزل تام لقسم التمارين الأساسية (Main Exercises)**: لم يتم المساس إطلاقاً بأي جدول، حقل، قاعدة تحقق، مصفوفة أوزان، منطق أعمال أو علاقة قاعدة بيانات خاصة بالتمارين الأساسية.
- **بدون ترحيلات قاعدة بيانات (Zero Migration)**: استخدام حقلي `warmup` و `post_workout` الموجودين مسبقاً كـ `TEXT NULL` في جدول `workout_plans`.
- **الحفاظ الكامل على تعديلات شجرة العمل الحالية**: الحفاظ التام على ميزة تعديل أوزان المتدرب (`TraineeLoadAdjustment`) وكافة ملفاتها غير المثبتة.

---

## 4. بنية التخزين والتحقق (Storage & Validation)

### أ. هيئة التخزين (Storage Format)
تُخزن البيانات في عمودي `warmup` و `post_workout` كنص HTML منسق ونظيف ومعقم مباشرة (Sanitized Rich-Text HTML String) أو كقيمة `null` عند خلو الحقل.

### ب. التعقيم الأمني الصارم (XSS Sanitization)
تخضع كافة المدخلات للدالة الثابتة `WorkoutPlan::sanitizeRichText($html)`:
- الوسوم المسموحة: `<h1>, <h2>, <h3>, <h4>, <h5>, <h6>, <p>, <br>, <b>, <strong>, <i>, <em>, <u>, <s>, <ul>, <ol>, <li>, <a>, <blockquote>, <table>, <thead>, <tbody>, <tr>, <th>, <td>`.
- تجريد كافة سمات الأحداث الخبيثة (`onclick`, `onerror`, `onload`, `style`).
- فحص وسوم الروابط `<a>` والسماح فقط ببروتوكولات آمنة (`http`, `https`, `mailto`, `tel`) مع فرض `target="_blank" rel="noopener noreferrer"`.
- إفراغ المدخلات الخاوية: في حال احتواء المحرر على وسوم خالية من المحتوى النصي الفعلي مثل `<p><br></p>` أو `<p></p>`، يتم تفريغ الحقل وحفظه كـ `null` دون استعادة أي بيانات قديمة.

### ج. تدفق طلبات الحفظ والتعديل (Store & Edit Flow)
1. **المزامنة قبل الإرسال**: يقوم كود الجافاسكريبت `WarmupQuill.syncAll()` بنقل محتوى محررات Quill إلى الحقول المخفية المتطابقة (`name="warmup"`, `name="post_workout"`, أو `name="program[day_X][...]"`) قبل إرسال النموذج.
2. **فئات الطلب (Form Requests)**:
   - `StoreExercisFormRequest`: تقوم دالة `prepareForValidation` بتطبيق `sanitizeRichText()` على حقلي `warmup` و `post_workout`.
   - `WorkoutFormRequest`: تطبق الدالة `sanitizeRichText()` على كل يوم تدريبي في مصفوفة `program.*.warmup` و `program.*.post_workout`.
3. **الخدمات والمستودعات**:
   - `WorkoutService::store`: تخزين نصوص الـ HTML المعقمة في سجلات الأيام التدريبية.
   - `WorkoutRepository::syncDayExercises`: تحديث الحقلين في خطة اليوم بشكل مباشر ومستقل.

---

## 5. التوافقية الرجعية الكاملة (Backward Compatibility)

لضمان عدم فقدان أي بيانات سابقة تم إنشاؤها عبر الواجهات المختلفة:
1. **واجهة التعديل (Edit Mode)**:
   - توفر دالة `formatContentForEditor()` في نموذج `WorkoutPlan` التوافق التلقائي الذكي:
     - إذا كانت البيانات بصيغة JSON من مرحلة منشئ المحتوى السابقة: يتم تحويل عناصر النصوص والأنشطة والروابط إلى وسوم HTML منسقة وقابلة للتحرير فوراً داخل محرر Quill.
     - إذا كانت البيانات نصاً عادياً قديماً: يتم تحويل فواصل الأسطر والروابط إلى فقرات وروابط نشطة.
     - إذا كانت بيانات HTML منسقة: يتم تحميلها مباشرة بعد تعقيمها.
2. **واجهة العرض للمتدرب ولوحة التحكم (Display Mode)**:
   - تعتمد خاصيتا `display_warmup` و `display_post_workout` على تحويل أي بيانات سابقة إلى عرض HTML semantic متسق وأنيق.

---

## 6. واجهة المتدرب والتنسيق البصري (Trainee Details & Dashboard Styling)

تم تصحيح وتوحيد تنسيق المحتوى في:
- `resources/views/system/trainee/show.blade.php` (الأيام النشطة ونافذة الإضافة السريعة والأرشيف).
- `resources/views/system/trainer-dashboard.blade.php`.

### التحسينات المطبقة:
- طباعة نصوص منسقة واضحة مع هوامش مريحة (`warmup-rich-content`).
- منع انكسار الواجهة مع الروابط الطويلة عبر `word-break: break-word` و `overflow-x: auto`.
- دعم كامل للوضعين الليلي والفاتح (Dark / Light Metronic Themes).
- تجنب الصناديق المتداخلة، إزالة العناوين المكررة، واستخدام أيقونات هادئة وألوان معتمدة (البنفسجي للإحماء `#A78BFA` والسماوي لما بعد التمرين `#38BDF8`).

---

## 7. الاختبارات الآلية والتحقق (Test Coverage)

تم تنفيذ وتمرير اختبارات PHPUnit الشاملة بنجاح 100%:
- ملف الاختبار: `tests/Feature/WorkoutContentBuilderTest.php`
- عدد الاختبارات: 16 اختباراً متخصصاً (127 تأكيداً - Assertions).
  1. `test_create_workout_program_with_warmup_rich_text`: إنشاء خطة مع إحماء منسق.
  2. `test_create_workout_program_with_post_workout_rich_text`: إنشاء خطة مع ما بعد التمرين.
  3. `test_saving_both_fields_independently`: الحفظ المستقل التام للحقلين.
  4. `test_updating_existing_content_without_duplication`: تعديل المحتوى وتحديثه دون تكرار.
  5. `test_loading_saved_content_correctly_into_edit_interface`: تحميل البيانات في Quill للمحرر.
  6. `test_preserving_content_when_validation_fails`: الحفاظ على المدخلات عند أخطاء التحقق.
  7. `test_handling_empty_fields_and_intentionally_clearing_content`: تفريغ المحتوى إلى null.
  8. `test_preserving_legacy_workout_program_content`: الحفاظ على المحتوى القديم.
  9. `test_safely_sanitizing_malicious_html_and_dangerous_urls`: الحماية من XSS والروابط الخطرة.
  10. `test_rendering_formatted_content_correctly_for_trainee_display`: صحة التنسيق والعرض.
  11. `test_supporting_multiple_workout_days`: دعم أيام متعددة بمحررات مستقلة.
  12. `test_main_exercises_section_remains_unchanged`: استقرار وحماية قسم التمارين الأساسية.
  13. `test_table_created_in_editor_is_persisted_with_structure_intact`: حفظ هيكل الجداول بالكامل (صفوف، أعمدة، دمج).
  14. `test_stored_table_content_is_restored_correctly_for_editor`: استعادة الجداول المحفوظة للمحرر عند التعديل.
  15. `test_trainee_view_renders_table_structure_and_responsive_wrapper`: عرض الجداول بغلاف متجاوب مانع للـ overflow.
  16. `test_dangerous_attributes_and_scripts_are_stripped_from_tables`: تنظيف الجداول من أي أكواد ضارة أو أحداث خبيثة.

بالإضافة إلى التحقق من اختبارات تعديل الأوزان `tests/Feature/TraineeExerciseLoadUpdateTest.php` (10/10 نجاح، المجموع الكلي 26/26).

---

## 8. معالجة الجداول المنسقة وألوان الثيم (Rich Text Tables & Theme-Aware Colors)

### أ. السبب الجذري لمشكلة الجداول ومعالجتها:
1. **تجريد الخصائص في التعقيم**: سابقاً، كانت دالة `WorkoutPlan::sanitizeRichText` تقوم بإلغاء كافة خصائص وسوم الجدول (`colspan`, `rowspan`, `data-row`, `data-cell`, `class`, `style`). تم تحديث الدالة للسماح بالخصائص الهيكلية الآمنة للجدول وتثبيت نسب العرض ودمج الخلايا، مع تجريد أي أكواد JavaScript أو أحداث مضمنة.
2. **عناصر QuillTableBetter المؤقتة**: كان وسم `<temporary>` يُفرغ من وسومه ويترك `<br>` كابن مباشر لـ `<table>`، مما يفسد بنية الـ DOM للجدول في المتصفحات. تمت معالجة ذلك بحذف وسم `<temporary>` ومحتواه قبل التعقيم.
3. **تحميل الجداول في المحرر عند التعديل**: تم اعتماد `quill.clipboard.convert({ html: ... })` مع `quill.setContents(delta)` بدلاً من الإسناد المباشر لـ `innerHTML`، مما يمكن محرك Quill من بناء شجرة الـ Blots والجداول بشكل متوافق وموثوق.
4. **التجاوب وعدم كسر الشاشة**: تم تغليف أي جدول يُعرض للمتدرب أو المدرب بغلاف `.table-responsive` مع توفير تمرير أفقي محلي على شاشات الجوال دون التسبب في أي overflow على مستوى الصفحة العامة.

### ب. ألوان العناوين والأوصاف المتوافقة مع الثيم (Theme-Aware Colors):
- **المشكلة السابقة**: كانت العناوين محددة بفئة `text-white` الصارمة، مما جعلها بيضاء وغير مقروءة على الخلفيات الفاتحة في الوضع النهاري (Light Mode).
- **المعالجة**: تم استبدال `text-white` بفئات مخصصة `.wb-rich-title` و `.wb-rich-desc`، وضبط قواعد CSS في `warmup-editor-assets.blade.php`:
  - **الوضع الفاتح (Light Mode)**: نصوص سوداء بالكامل (`#000000 !important`) للعناوين والأوصاف لقراءة واضحة ومتباينة.
  - **الوضع الليلي (Dark Mode)**: نصوص بيضاء بالكامل (`#ffffff !important`).
  - مع الحفاظ التام على ألوان التنسيق المخصصة التي قد يختارها المدرب داخل محتوى المحرر نفسه.
