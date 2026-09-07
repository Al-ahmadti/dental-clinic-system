# نظام إدارة عيادة الأسنان — تطبيق سطح المكتب (NativePHP)

## التشغيل للتطوير

```bash
# إن ظهرت شاشة سوداء على Windows:
set ELECTRON_DISABLE_GPU=1

php artisan native:run
```

أو:

```bash
composer native:dev
```

لا تشغّل اختصار سطح المكتب مع `native:run` في نفس الوقت.

## بناء ملف Windows للطبيب

```bash
# مهم على Windows بدون وضع المطوّر / صلاحيات symlink:
set CSC_IDENTITY_AUTO_DISCOVERY=false

npm run build
php artisan native:build win
```

ملف التثبيت يظهر في:

`nativephp/electron/dist/Dental Clinic-1.0.0-setup.exe`

ومجلد التشغيل غير المعبأ:

`nativephp/electron/dist/win-unpacked/`

> ملاحظة: إن فشل البناء بسبب `Cannot create symbolic link`، تأكد أن `signAndEditExecutable: false` موجود في  
> `vendor/nativephp/desktop/resources/electron/electron-builder.mjs` تحت قسم `win`  
> (قد يُعاد الكتابة بعد تحديث الحزمة — أعد إضافة السطر إن لزم).

## بيانات الدخول الافتراضية (أول تشغيل)

- البريد: `doctor@dental.local`
- كلمة المرور: قيمة `ADMIN_PASSWORD` في `.env` (الافتراضي: `password`)

غيّر كلمة المرور بعد أول دخول.

> إصلاح 403: نموذج المستخدم يطبّق `FilamentUser` حتى يعمل الدخول في نسخة الإنتاج المبنية.

## النسخ الاحتياطي

قاعدة البيانات وملفات التطبيق على جهاز الطبيب:

- تطوير: `%APPDATA%\dental-clinic-dev\`
- إنتاج (بعد البناء): `%APPDATA%\com.dental.clinic\` (أو حسب `NATIVEPHP_APP_ID`)

انسخ مجلد `database` (ومجلد `storage` إن وُجدت صور مرضى) احتياطياً بشكل دوري.

## ملاحظات

- كل جهاز طبيب مستقل (بيانات منفصلة) حتى لو كان نفس ملف التثبيت.
- Windows Defender قد يحذّر من ملفات غير موقّعة — طبيعي في التسليم الأولي بدون شهادة توقيع.
- زد `NATIVEPHP_APP_VERSION` مع كل تحديث ترسله للطبيب.
