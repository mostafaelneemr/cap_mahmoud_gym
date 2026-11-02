# توثيق محرك النسخ الاحتياطي المحلي الآلي لقاعدة البيانات MySQL
**مشروع:** نظام إدارة جيم شلتوت (Shaltout Gym Management System)  
**البيئة المستهدفة:** Laravel 13 / PHP 8.5 / MySQL 8.0  
**التاريخ:** نوفمبر 2026

---

## 1. المعمارية الفنية للمحرك (Backup Architecture)

تم تصميم محرك النسخ الاحتياطي `DatabaseBackupService` كخدمة مستقلة وعالية الأمان تعتمد بالكامل على بروتوكول PHP PDO الأصلي دون الحاجة إلى دوال تنفيذ الأوامر الخارجية المحظورة في كثير من البيئات الاستضافية (مثل `proc_open` أو `exec`).

### دورة حياة النسخة الاحتياطية (Lifecycle):
1. **قراءة إعدادات الاتصال النشط:** عبر واجهة إعدادات لارافيل `config('database.connections.*')` وليس قراءة مباشرة من `env()`، مما يضمن التوافق التام مع التخزين المؤقت للإعدادات (`config:cache`).
2. **اتساق البيانات (Consistency Snapshot):** تفعيل معاملة معزولة متسقة لجداول InnoDB (`START TRANSACTION /*!40108 WITH CONSISTENT SNAPSHOT */`).
3. **التدفق المباشر للضغط (Streaming Gzip Compression):** كتابة مخرجات SQL مباشرة إلى ملف مضغوط `.sql.gz` عبر مؤشر مجرى `gzwrite` مجزأة على دفعات (`chunk_size: 1000`) دون تجميع الجداول الضخمة في ذاكرة الـ RAM.
4. **التحقق من سلامة الضغط (GZIP Integrity Verification):** فحص دفق البيانات المضغوطة والتأكد من صحة الترويسة وكتل البيانات قبل اعتماد الملف.
5. **التشفير المصادق (Authenticated Encryption - AES-256-GCM):** تشفير النسخة المضغوطة باستخدام خوارزمية AES-256-GCM لإنتاج ملف بحاوية خاصة مشفرة تنتهي بالامتداد `.sql.gz.enc`.
6. **الاعتماد الذري للملف (Atomic Finalization):** إنشاء الملفات أولاً بأسماء مؤقتة مخفية `.tmp_*.sql.gz.enc`، ثم التحقق منها وإعادة تسميتها ذرياً (`rename`) إلى الاسم النهائي، مما يمنع نهائياً وجود نسخ احتياطية ناقصة أو تالفة.
7. **تطبيق سياسة التدوير (Retention Policy):** حذف النسخ الأقدم تلقائياً بعد اكتمال النسخة الجديدة وتأكيد سلامتها.

---

## 2. أمر Artisan وخيارات التشغيل (Artisan Command)

الأمر الأساسي المخصص للنسخ الاحتياطي هو:

```bash
php artisan app:backup-database-weekly
```

### الخيارات المتاحة (Options):
| الخيار | الوظيفة | مثال |
| :--- | :--- | :--- |
| `--connection=` | تحديد وصلة قاعدة بيانات مخصصة من `config/database.php` (الافتراضي: الوصلة الافتراضية). | `--connection=mysql` |
| `--no-encrypt` | تعطيل التشفير وحفظ النسخة بصيغة GZIP فقط (`.sql.gz`). | `--no-encrypt` |
| `--key=` | تمرير مفتاح تشفير مخصص (مفتاح 256 بت بصيغة Base64 أو نصي). | `--key="base64:..."` |

### مثال لمخرجات التشغيل الناجح:
```text
============================================================
 Shaltout Gym System - Automated Local Database Backup Engine
============================================================
Starting database backup process...

✔ Backup completed successfully!

+---------------------+----------------------------------------------------+
| Property            | Value                                              |
+---------------------+----------------------------------------------------+
| Database            | gym                                                |
| Connection          | mysql                                              |
| Driver              | mysql                                              |
| Backup Filename     | backup-gym-2026-11-02_150000.sql.gz.enc            |
| Backup Size         | 4.25 MB                                            |
| Execution Duration  | 3.12 seconds                                       |
| Encrypted           | Yes (AES-256-GCM)                                  |
| Tables Processed    | 48                                                 |
| Total Rows Dumped   | 14520                                              |
| Pruned Old Backups  | 1                                                  |
| Storage Location    | /var/www/cap_mahmoud_gym/storage/app/backups/database |
+---------------------+----------------------------------------------------+

Backup stored securely in private local storage.
```

---

## 3. موقع التخزين والعزل الأمني (Storage Location & Public Isolation)

- **المسار المعتمد:**
  ```text
  storage/app/backups/database/
  ```
- **إجراءات الأمان الإلزامية:**
  - يتم فحص مسار التخزين برمجياً قبل أي عملية؛ إذا تبين أن المسار يقع داخل مجلد الويب العام `public/` (أو `public/storage`)، يتم إيقاف العملية فوراً وإطلاق استثناء أمني (`Security violation`).
  - يتم إنشاء ملف `.gitignore` تلقائياً لمنع رفع ملفات النسخ إلى مستودعات Git.
  - يتم وضع ملف `index.html` فارغ بحالة `403 Forbidden` لمنع استعراض المجلد (Directory Listing).

---

## 4. التشفير وإدارة المفاتيح (Encryption & Key Management)

### آلية التشفير (AES-256-GCM):
تستخدم خوارزمية **AES-256-GCM** لأنها توفر تشفيراً مصادقاً (Authenticated Encryption with Associated Data - AEAD). هذا يعني أنه لا يمكن فك تشفير الملف فقط، بل يتم أيضاً التحقق التلقائي من أن الملف لم يتعرض لأي تعديل أو تلف أو تلاعب ولو في بت واحد (Bit-flip).

### هيكل الحاوية المشفرة (Container Binary Layout):
1. **Magic Header (15 bytes):** `SHALTOUT_GCM_V1`
2. **IV Length (1 byte) + IV (12 bytes):** متجه تهيئة عشوائي مولّد بواسطة `random_bytes(12)`.
3. **Tag Length (1 byte) + Authentication Tag (16 bytes):** وسام المصادقة للتحقق من سلامة البيانات.
4. **Ciphertext (Payload):** البيانات المضغوطة المشفرة.

### توليد وتخزين مفتاح التشفير:
1. توليد مفتاح 256 بت آمن عبر Artisan Tinker أو PHP:
   ```bash
   php -r "echo 'base64:' . base64_encode(random_bytes(32)) . PHP_EOL;"
   ```
2. إضافة المفتاح في ملف `.env`:
   ```env
   BACKUP_ENCRYPTION_KEY="base64:your-generated-key-here="
   ```
> [!IMPORTANT]
> **قاعدة أمان صارمة:**
> - بدون هذا المفتاح، **تعتبر النسخ المشفرة غير قابلة للاستعادة نهائياً**.
> - يجب حفظ هذا المفتاح في مدير كلمات مرور آمن (Password Manager) خارج الخادم.
> - يُمنع منعاً باتاً طباعة المفتاح أو تسجيله في ملفات السجلات (`Log`).

---

## 5. فحص سلامة النسخ الاحتياطية (Integrity Inspection)

يمكن فحص سلامة النسخة المشفرة أو المضغوطة برمجياً عبر دالة الفحص في الخدمة:

```php
use App\Services\DatabaseBackupService;

$service = app(DatabaseBackupService::class);

// فحص سلامة حاوية التشفير وصحة وسام المصادقة (Authentication Tag)
$isValid = $service->verifyEncryptedFile($backupPath, $binaryKey);

// فحص سلامة دفق GZIP
$isGzValid = $service->verifyGzipIntegrity($gzFilePath);
```

---

## 6. كيفية استعادة النسخة الاحتياطية في البيئة المحلية (Safe Local Restoration)

> [!WARNING]
> لا تقم أبداً بتشغيل استعادة النسخ ضد قاعدة البيانات الإنتاجية. استخدم قاعدة بيانات اختبارية أو محلية معزولة.

### الخطوات:
1. **فك تشفير النسخة الاحتياطية (إذا كانت مشفرة):**
   ```php
   use App\Services\DatabaseBackupService;

   $service = app(DatabaseBackupService::class);
   $binaryKey = $service->normalizeKey(env('BACKUP_ENCRYPTION_KEY'));

   $service->decryptBackupFile(
       storage_path('app/backups/database/backup-gym-2026-11-02_150000.sql.gz.enc'),
       storage_path('app/backups/database/restored.sql.gz'),
       $binaryKey
   );
   ```

2. **الاستعادة البرمجية المباشرة:**
   ```php
   $service->restoreBackup(
       storage_path('app/backups/database/backup-gym-2026-11-02_150000.sql.gz.enc'),
       'mysql', // أو وصلة اختبارية مثل 'sqlite' أو 'test_mysql'
       env('BACKUP_ENCRYPTION_KEY')
   );
   ```

3. **الاستعادة اليدوية عبر موجه أوامر MySQL (بعد فك التشفير وفك الضغط):**
   ```bash
   # فك الضغط في بيئة لينكس / ماك:
   gzip -d restored.sql.gz
   mysql -u root -p gym_local_test < restored.sql
   ```

---

## 7. سياسة الاحتفاظ بالنسخ (Retention Policy)

- **الحد الافتراضي:** يتم الاحتفاظ بآخر **4 نسخ أسبوعية ناجحة**.
- **ضمانات الحماية من الحذف العرضي:**
  - لا يتم حذف أي نسخة إلا بعد اكتمال النسخة الجديدة والتأكد من سلامتها 100%.
  - لا يتم حذف أي ملفات غير تابعة للنسخ الاحتياطي (فقط الملفات المطابقة لـ `backup-*.sql.gz*`).
  - إذا كان عدد النسخ أقل من أو يساوي 4، لا يتم حذف أي ملف إطلاقاً.
  - لا يتم حذف النسخة الوحيدة الصالحة تحت أي ظرف.

---

## 8. التوافق مع الاستضافات المشتركة (Shared Hosting Considerations)

1. **دوال التنفيذ المحظورة:** الخدمة لا تستخدم `exec` أو `shell_exec` أو `proc_open` كاعتمادية إجبارية.
2. **حدود الذاكرة:** استخدام أسلوب التجزئة `chunk_size` والكتابة اللحظية في المجرى المضغوط يحافظ على استهلاك الذاكرة في حدود دنيا (أقل من 30 ميجابايت حتى مع الجداول الكبيرة).
3. **أوقات التنفيذ:** في حال الاستضافات المشتركة ذات المهلة القصيرة، يُنصح بتشغيل الأمر عبر الـ CLI Cron وليس عبر طلبات المتصفح HTTP.

