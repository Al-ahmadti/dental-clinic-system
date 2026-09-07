<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

In addition, [Laracasts](https://laracasts.com) contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

You can also watch bite-sized lessons with real-world projects on [Laravel Learn](https://laravel.com/learn), where you will be guided through building a Laravel application from scratch while learning PHP fundamentals.

## Agentic Development

Laravel's predictable structure and conventions make it ideal for AI coding agents like Claude Code, Cursor, and GitHub Copilot. Install [Laravel Boost](https://laravel.com/docs/ai) to supercharge your AI workflow:

```bash
composer require laravel/boost --dev

php artisan boost:install
```

Boost provides your agent 15+ tools and skills that help agents build Laravel applications while following best practices.

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## نسخ احتياطي واسترجاع (سجلات المرضى والعيادة)

للإنتاج يُنصح بجدولة النسخ خارج التطبيق:

1. **قاعدة البيانات:** تصدير دوري بـ `mysqldump` (أو أداة مكافئة لقاعدة البيانات المستخدمة) مع الاحتفاظ بعدة نسخ زمنية.
2. **الملفات المرفوعة:** نسخ مجلد `storage/app` (ومنها `storage/app/public` إن وُجدت مرفقات/صور مرضى) إلى تخزين آمن.
3. **الاسترجاع:** استيراد نسخة قاعدة البيانات ثم استعادة الملفات إلى نفس المسارات، ثم `php artisan storage:link` إن لزم.

يمكن إضافة أمر في `routes/console.php` مع `Schedule::command(...)` لاستدعاء سكربت نسخ خارجي (لا يُنفَّذ تلقائياً هنا لأنه يعتمد على بيئة الخادم).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).



خطة: تدقيق أمان، حقول فارغة، وحقول إلزامية

ملخص تنفيذي

المشروع عيادة أحادية المستخدم (Filament + مستخدم واحد افتراضياً، بدون أدوار). لا توجد ثغرات SQL واضحة أو Mass Assignment خطيرة. أخطر المشاكل: تجاوز صلاحيات عبر مسار PDF ومكوّنات Livewire، وقبول مبالغ مالية غير منطقية (صفر/سالب/دفع زائد). كثير من الحقول اختيارية عمداً في قاعدة البيانات لكن ليست مُوحّدة بين Filament وLivewire.

flowchart TB
  subgraph risks [مخاطر أعلى أولوية]
    PDF[PDF route auth فقط]
    LW[Livewire بدون canView]
    Pay[دفعات بدون حد أقصى]
  end
  subgraph ok [سليم نسبياً]
    Filament[Filament forms + FK]
    TXN[معاملات DB في الزيارات]
  end
  User[مستخدم مسجّل] --> PDF
  User --> LW
  User --> Filament



1) أمان وصلاحيات (ثغرات / ثغرات محتملة)







الخطورة



المشكلة



الموقع



التفاصيل





عالية



IDOR على كشف PDF



[PatientAccountStatementPdfController.php](c:\laragon\www\dental-cli\app\Http\Controllers\PatientAccountStatementPdfController.php)



يتحقق من auth()->check() فقط — أي مستخدم مسجّل يمكنه GET /patients/{id}/account-statement/pdf لأي مريض. لا يستدعي PatientPolicy::view.





عالية



Livewire بدون تحقق صلاحيات



[PatientVisitComposer](c:\laragon\www\dental-cli\app\Livewire\PatientVisitComposer.php), [VisitFinancialPanel](c:\laragon\www\dental-cli\app\Livewire\VisitFinancialPanel.php), [OdontogramEditor](c:\laragon\www\dental-cli\app\Livewire\OdontogramEditor.php)



mount($patientId) / mount($visitId) بدون abort_unless(PatientResource::canView(...)). من يعرف المعرّف يمكنه إنشاء زيارة/دفعة/علاج سن عبر طلب Livewire.





متوسطة



ملفات مرضى عامة



[PatientMediaForm](c:\laragon\www\dental-cli\app\Filament\Resources\PatientMedia\Schemas\PatientMediaForm.php) — disk('public'), visibility('public')



أي شخص يعرف رابط storage/patient-media/... يمكنه فتح الصورة دون تسجيل دخول.





متوسطة



سياسات مفتوحة بالكامل



[PatientPolicy](c:\laragon\www\dental-cli\app\Policies\PatientPolicy.php), [PatientAuditLogPolicy](c:\laragon\www\dental-cli\app\Policies\PatientAuditLogPolicy.php)



كل الدوال ترجع true — لا فصل أدوار (مقبول لعيادة بطبيب واحد، غير مقبول عند إضافة موظف استقبال/محاسب).





متوسطة



لا توجد Policies لباقي الموارد



لا يوجد إلا Policy للمريض وسجل التدقيق



Filament يعتمد على وجود Policy؛ عند غيابها يُسمح افتراضياً لكل مستخدمي اللوحة بـ CRUD كامل (زيارات، مصاريف، مخزون، إلخ).





منخفضة



كلمة مرور افتراضية ضعيفة



[DentistUserSeeder](c:\laragon\www\dental-cli\database\seeders\DentistUserSeeder.php)



password ثابت — خطر إن بقي في الإنتاج.





منخفضة



رفع ملفات بأسماء أصلية



preserveFilenames() في PatientMedia



اعتماد على تعقيم Filament؛ يُفضّل تقييد الامتدادات وإعادة تسمية آمنة.

ما هو سليم: لا مسارات API عامة كثيرة؛ اللوحة محمية بـ Authenticate؛ علاقات FK في الهجرات؛ [PatientVisitComposer](c:\laragon\www\dental-cli\app\Livewire\PatientVisitComposer.php) يستخدم DB::transaction وتحقق exists:services,id وFdiTooth::isValid.



2) حقول يجب ألا تكون فارغة (فجوات DB ↔ نموذج)

مريض — [patients](c:\laragon\www\dental-cli\database\migrations\2026_05_03_200001_create_patients_table.php)







الحقل



DB



Filament



ملاحظة





name



NOT NULL



required()



متسق





phone



nullable



غير required



يمكن حفظ مريض بدون هاتف — يضعف تنبيه التكرار





gender



nullable



اختياري



مقبول طبياً لكن تقارير «الجنس» تظهر «—»





file_number



nullable unique



اختياري



متعدد NULL مسموح في MySQL — OK





birth_date, notes



nullable



اختياري



OK

زيارة ومالية







الحقل



DB



التحقق



فجوة





visits.visit_at, user_id, patient_id



مطلوبة



Filament + Livewire required



OK





visit_line_items.unit_price



NOT NULL



Filament: required() بدون min(0.01)



سعر 0 ممكن من لوحة الزيارة





visit_line_items.quantity



default 1



RelationManager: minValue(1) مخفي



OK في Filament؛ Composer يثبت quantity => 1





payments.amount



NOT NULL



Filament: required() بدون min؛ Livewire: min:0.01



تناقض — من Filament يمكن 0 أو سالب





payments.check_number



nullable



required عند check في Filament فقط



OK في النموذج





دفع يتجاوز المتبقي



—



لا يوجد



[VisitFinancialPanel::savePayment](c:\laragon\www\dental-cli\app\Livewire\VisitFinancialPanel.php) لا يقارن المبلغ بـ balanceDue()

مصاريف ومخزون







الحقل



فجوة





ExpenseForm amount



required + numeric بدون min(0.01)





[MovementsRelationManager](c:\laragon\www\dental-cli\app\Filament\Resources\InventoryItems\RelationManagers\MovementsRelationManager.php)



quantity, unit_cost بدون حد أدنى؛ balance_due يُدخل يدوياً دون ارتباط تلقائي

موردون — [suppliers](c:\laragon\www\dental-cli\database\migrations\2026_05_03_200011_create_suppliers_table.php)







الحقل



DB



Filament





company_name



NOT NULL



required() — OK





contact_name, phone



nullable



غير مطلوبة — OK

خدمات وكتالوج







الحقل



ملاحظة





services.category_id



nullable في DB والنموذج — مقصود (خدمات بدون تصنيف)





services.price



default 0 — يمكن خدمة بسعر صفر



3) حقول فارغة مقبولة (ليست ثغرة)





visits.diagnosis, visits.notes, payments.notes



patient_media.visit_id اختياري



inventory_movements.supplier_id اختياري



expenses.inventory_movement_id اختياري



clinic_settings حقول شعار/عنوان اختيارية في [ClinicSettingForm](c:\laragon\www\dental-cli\app\Filament\Resources\ClinicSettings\Schemas\ClinicSettingForm.php) — clinic_name فقط مطلوب



4) تناسق التحقق (Livewire أقوى من Filament)







السياق



Livewire



Filament RelationManager





مبلغ الدفعة



min:0.01



لا min





بنود زيارة



line_price min:0.01, service_id required



unit_price required فقط





إنشاء مريض



تنبيه تكرار + name required



[PatientForm](c:\laragon\www\dental-cli\app\Filament\Resources\Patients\Schemas\PatientForm.php) — لا تنبيه تكرار في Edit



5) خطة إصلاح مقترحة (مرتبة بالأولوية)

المرحلة A — أمان (ضروري قبل إنتاج متعدد المستخدمين)





PDF: في [PatientAccountStatementPdfController](c:\laragon\www\dental-cli\app\Http\Controllers\PatientAccountStatementPdfController.php) إضافة Gate::authorize('view', $patient) أو abort_unless(PatientResource::canView($patient), 403).



Livewire: Trait مشترك AuthorizesPatientAccess يُستدعى في mount لكل مكوّن يأخذ patientId/visitId (الزيارة → مريض → canView).



وسائط المريض: نقل التخزين إلى disk خاص + route محمي للتحميل، أو visibility('private') مع Storage::url موقّع.



Seeder: إزالة كلمة المرور الثابتة من الإنتاج أو استخدام env('ADMIN_PASSWORD') مع تحذير في README.

المرحلة B — تحقق مالي (منع بيانات فاسدة)





قاعدة FormRequest أو قواعد Filament موحّدة:





amount, unit_price, line_price, expense amount → numeric|min:0.01



[VisitFinancialPanel::savePayment](c:\laragon\www\dental-cli\app\Livewire\VisitFinancialPanel.php): رفض إذا payment_amount > balanceDue() (مع هامش 0.01).



[PaymentsRelationManager](c:\laragon\www\dental-cli\app\Filament\Resources\Visits\RelationManagers\PaymentsRelationManager.php) و[LineItemsRelationManager](c:\laragon\www\dental-cli\app\Filament\Resources\Visits\RelationManagers\LineItemsRelationManager.php): نفس قواعد الحد الأدنى.



(اختياري) هجرة DB: CHECK (amount > 0) على payments — حماية على مستوى قاعدة البيانات.

المرحلة C — حقول إلزامية تجارية (حسب قرار العيادة)







قرار مقترح



تغيير





هاتف المريض إلزامي



phone → required() في [PatientForm](c:\laragon\www\dental-cli\app\Filament\Resources\Patients\Schemas\PatientForm.php) و[CreatePatient](c:\laragon\www\dental-cli\app\Filament\Resources\Patients\Pages\CreatePatient.php)؛ هجرة اختيارية nullable(false) بعد backfill





الجنس إلزامي



gender → required() في النماذج





تنبيه تكرار عند التعديل



نفس منطق [CreatePatient](c:\laragon\www\dental-cli\app\Filament\Resources\Patients\Pages\CreatePatient.php) في EditPatient عند تغيير الهاتف/الاسم

المرحلة D — صلاحيات مستقبلية (إن وُجد أكثر من مستخدم)





Policies لـ Visit, Expense, Payment, PatientMedia أو Package أدوار (Spatie) + تقييد PatientAuditLogPolicy::viewAny للمدير فقط.



عدم الاعتماد على return true في [PatientPolicy](c:\laragon\www\dental-cli\app\Policies\PatientPolicy.php).

المرحلة E — اختبارات





Feature: PDF ممنوع لمريض بدون canView (بعد تشديد Policy).



Feature: دفعة بمبلغ 0 مرفوضة من Filament.



Feature: دفعة أكبر من المتبقي مرفوضة في Livewire.



6) ما ليس ضمن النطاق / مقبول حالياً





عدم وجود أدوار لعيادة بطبيب واحد.



services بدون تصنيف أو بسعر 0 (سياسة عمل).



أداء ClinicFinancialStats (chunk) — ليس ثغرة.



dompdf ودعم العربية — جودة عرض وليس أماناً.



توصية للتنفيذ التالي

ابدأ بـ المرحلة A + B (أقل تغيير، أعلى تأثير). المرحلة C تحتاج تأكيدك: هل هاتف المريض والجنس إلزاميان في عيادتك؟خطة: تدقيق أمان، حقول فارغة، وحقول إلزامية

ملخص تنفيذي

المشروع عيادة أحادية المستخدم (Filament + مستخدم واحد افتراضياً، بدون أدوار). لا توجد ثغرات SQL واضحة أو Mass Assignment خطيرة. أخطر المشاكل: تجاوز صلاحيات عبر مسار PDF ومكوّنات Livewire، وقبول مبالغ مالية غير منطقية (صفر/سالب/دفع زائد). كثير من الحقول اختيارية عمداً في قاعدة البيانات لكن ليست مُوحّدة بين Filament وLivewire.

flowchart TB
  subgraph risks [مخاطر أعلى أولوية]
    PDF[PDF route auth فقط]
    LW[Livewire بدون canView]
    Pay[دفعات بدون حد أقصى]
  end
  subgraph ok [سليم نسبياً]
    Filament[Filament forms + FK]
    TXN[معاملات DB في الزيارات]
  end
  User[مستخدم مسجّل] --> PDF
  User --> LW
  User --> Filament



1) أمان وصلاحيات (ثغرات / ثغرات محتملة)







الخطورة



المشكلة



الموقع



التفاصيل





عالية



IDOR على كشف PDF



[PatientAccountStatementPdfController.php](c:\laragon\www\dental-cli\app\Http\Controllers\PatientAccountStatementPdfController.php)



يتحقق من auth()->check() فقط — أي مستخدم مسجّل يمكنه GET /patients/{id}/account-statement/pdf لأي مريض. لا يستدعي PatientPolicy::view.





عالية



Livewire بدون تحقق صلاحيات



[PatientVisitComposer](c:\laragon\www\dental-cli\app\Livewire\PatientVisitComposer.php), [VisitFinancialPanel](c:\laragon\www\dental-cli\app\Livewire\VisitFinancialPanel.php), [OdontogramEditor](c:\laragon\www\dental-cli\app\Livewire\OdontogramEditor.php)



mount($patientId) / mount($visitId) بدون abort_unless(PatientResource::canView(...)). من يعرف المعرّف يمكنه إنشاء زيارة/دفعة/علاج سن عبر طلب Livewire.





متوسطة



ملفات مرضى عامة



[PatientMediaForm](c:\laragon\www\dental-cli\app\Filament\Resources\PatientMedia\Schemas\PatientMediaForm.php) — disk('public'), visibility('public')



أي شخص يعرف رابط storage/patient-media/... يمكنه فتح الصورة دون تسجيل دخول.





متوسطة



سياسات مفتوحة بالكامل



[PatientPolicy](c:\laragon\www\dental-cli\app\Policies\PatientPolicy.php), [PatientAuditLogPolicy](c:\laragon\www\dental-cli\app\Policies\PatientAuditLogPolicy.php)



كل الدوال ترجع true — لا فصل أدوار (مقبول لعيادة بطبيب واحد، غير مقبول عند إضافة موظف استقبال/محاسب).





متوسطة



لا توجد Policies لباقي الموارد



لا يوجد إلا Policy للمريض وسجل التدقيق



Filament يعتمد على وجود Policy؛ عند غيابها يُسمح افتراضياً لكل مستخدمي اللوحة بـ CRUD كامل (زيارات، مصاريف، مخزون، إلخ).





منخفضة



كلمة مرور افتراضية ضعيفة



[DentistUserSeeder](c:\laragon\www\dental-cli\database\seeders\DentistUserSeeder.php)



password ثابت — خطر إن بقي في الإنتاج.





منخفضة



رفع ملفات بأسماء أصلية



preserveFilenames() في PatientMedia



اعتماد على تعقيم Filament؛ يُفضّل تقييد الامتدادات وإعادة تسمية آمنة.

ما هو سليم: لا مسارات API عامة كثيرة؛ اللوحة محمية بـ Authenticate؛ علاقات FK في الهجرات؛ [PatientVisitComposer](c:\laragon\www\dental-cli\app\Livewire\PatientVisitComposer.php) يستخدم DB::transaction وتحقق exists:services,id وFdiTooth::isValid.



2) حقول يجب ألا تكون فارغة (فجوات DB ↔ نموذج)

مريض — [patients](c:\laragon\www\dental-cli\database\migrations\2026_05_03_200001_create_patients_table.php)







الحقل



DB



Filament



ملاحظة





name



NOT NULL



required()



متسق





phone



nullable



غير required



يمكن حفظ مريض بدون هاتف — يضعف تنبيه التكرار





gender



nullable



اختياري



مقبول طبياً لكن تقارير «الجنس» تظهر «—»





file_number



nullable unique



اختياري



متعدد NULL مسموح في MySQL — OK





birth_date, notes



nullable



اختياري



OK

زيارة ومالية







الحقل



DB



التحقق



فجوة





visits.visit_at, user_id, patient_id



مطلوبة



Filament + Livewire required



OK





visit_line_items.unit_price



NOT NULL



Filament: required() بدون min(0.01)



سعر 0 ممكن من لوحة الزيارة





visit_line_items.quantity



default 1



RelationManager: minValue(1) مخفي



OK في Filament؛ Composer يثبت quantity => 1





payments.amount



NOT NULL



Filament: required() بدون min؛ Livewire: min:0.01



تناقض — من Filament يمكن 0 أو سالب





payments.check_number



nullable



required عند check في Filament فقط



OK في النموذج





دفع يتجاوز المتبقي



—



لا يوجد



[VisitFinancialPanel::savePayment](c:\laragon\www\dental-cli\app\Livewire\VisitFinancialPanel.php) لا يقارن المبلغ بـ balanceDue()

مصاريف ومخزون







الحقل



فجوة





ExpenseForm amount



required + numeric بدون min(0.01)





[MovementsRelationManager](c:\laragon\www\dental-cli\app\Filament\Resources\InventoryItems\RelationManagers\MovementsRelationManager.php)



quantity, unit_cost بدون حد أدنى؛ balance_due يُدخل يدوياً دون ارتباط تلقائي

موردون — [suppliers](c:\laragon\www\dental-cli\database\migrations\2026_05_03_200011_create_suppliers_table.php)







الحقل



DB



Filament





company_name



NOT NULL



required() — OK





contact_name, phone



nullable



غير مطلوبة — OK

خدمات وكتالوج







الحقل



ملاحظة





services.category_id



nullable في DB والنموذج — مقصود (خدمات بدون تصنيف)





services.price



default 0 — يمكن خدمة بسعر صفر



3) حقول فارغة مقبولة (ليست ثغرة)





visits.diagnosis, visits.notes, payments.notes



patient_media.visit_id اختياري



inventory_movements.supplier_id اختياري



expenses.inventory_movement_id اختياري



clinic_settings حقول شعار/عنوان اختيارية في [ClinicSettingForm](c:\laragon\www\dental-cli\app\Filament\Resources\ClinicSettings\Schemas\ClinicSettingForm.php) — clinic_name فقط مطلوب



4) تناسق التحقق (Livewire أقوى من Filament)







السياق



Livewire



Filament RelationManager





مبلغ الدفعة



min:0.01



لا min





بنود زيارة



line_price min:0.01, service_id required



unit_price required فقط





إنشاء مريض



تنبيه تكرار + name required



[PatientForm](c:\laragon\www\dental-cli\app\Filament\Resources\Patients\Schemas\PatientForm.php) — لا تنبيه تكرار في Edit



5) خطة إصلاح مقترحة (مرتبة بالأولوية)

المرحلة A — أمان (ضروري قبل إنتاج متعدد المستخدمين)





PDF: في [PatientAccountStatementPdfController](c:\laragon\www\dental-cli\app\Http\Controllers\PatientAccountStatementPdfController.php) إضافة Gate::authorize('view', $patient) أو abort_unless(PatientResource::canView($patient), 403).



Livewire: Trait مشترك AuthorizesPatientAccess يُستدعى في mount لكل مكوّن يأخذ patientId/visitId (الزيارة → مريض → canView).



وسائط المريض: نقل التخزين إلى disk خاص + route محمي للتحميل، أو visibility('private') مع Storage::url موقّع.



Seeder: إزالة كلمة المرور الثابتة من الإنتاج أو استخدام env('ADMIN_PASSWORD') مع تحذير في README.

المرحلة B — تحقق مالي (منع بيانات فاسدة)





قاعدة FormRequest أو قواعد Filament موحّدة:





amount, unit_price, line_price, expense amount → numeric|min:0.01



[VisitFinancialPanel::savePayment](c:\laragon\www\dental-cli\app\Livewire\VisitFinancialPanel.php): رفض إذا payment_amount > balanceDue() (مع هامش 0.01).



[PaymentsRelationManager](c:\laragon\www\dental-cli\app\Filament\Resources\Visits\RelationManagers\PaymentsRelationManager.php) و[LineItemsRelationManager](c:\laragon\www\dental-cli\app\Filament\Resources\Visits\RelationManagers\LineItemsRelationManager.php): نفس قواعد الحد الأدنى.



(اختياري) هجرة DB: CHECK (amount > 0) على payments — حماية على مستوى قاعدة البيانات.

المرحلة C — حقول إلزامية تجارية (حسب قرار العيادة)







قرار مقترح



تغيير





هاتف المريض إلزامي



phone → required() في [PatientForm](c:\laragon\www\dental-cli\app\Filament\Resources\Patients\Schemas\PatientForm.php) و[CreatePatient](c:\laragon\www\dental-cli\app\Filament\Resources\Patients\Pages\CreatePatient.php)؛ هجرة اختيارية nullable(false) بعد backfill





الجنس إلزامي



gender → required() في النماذج





تنبيه تكرار عند التعديل



نفس منطق [CreatePatient](c:\laragon\www\dental-cli\app\Filament\Resources\Patients\Pages\CreatePatient.php) في EditPatient عند تغيير الهاتف/الاسم

المرحلة D — صلاحيات مستقبلية (إن وُجد أكثر من مستخدم)





Policies لـ Visit, Expense, Payment, PatientMedia أو Package أدوار (Spatie) + تقييد PatientAuditLogPolicy::viewAny للمدير فقط.



عدم الاعتماد على return true في [PatientPolicy](c:\laragon\www\dental-cli\app\Policies\PatientPolicy.php).

المرحلة E — اختبارات





Feature: PDF ممنوع لمريض بدون canView (بعد تشديد Policy).



Feature: دفعة بمبلغ 0 مرفوضة من Filament.



Feature: دفعة أكبر من المتبقي مرفوضة في Livewire.



6) ما ليس ضمن النطاق / مقبول حالياً





عدم وجود أدوار لعيادة بطبيب واحد.



services بدون تصنيف أو بسعر 0 (سياسة عمل).



أداء ClinicFinancialStats (chunk) — ليس ثغرة.



dompdf ودعم العربية — جودة عرض وليس أماناً.



توصية للتنفيذ التالي

ابدأ بـ المرحلة A + B (أقل تغيير، أعلى تأثير). المرحلة C تحتاج تأكيدك: هل هاتف المريض والجنس إلزاميان في عيادتك؟
