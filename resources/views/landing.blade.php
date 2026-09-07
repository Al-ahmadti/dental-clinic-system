<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $clinicName }} — نظام إدارة العيادة</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=cairo:400,600,700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root {
            --lp-primary: {{ $primary }};
            --lp-secondary: {{ $secondary }};
        }
    </style>
</head>
<body class="antialiased">
    <header class="landing-hero text-white">
        <nav class="relative z-10 flex items-center justify-between px-6 py-5 md:px-12 lg:px-16">
            <div class="flex items-center gap-3">
                @if ($logoUrl)
                    <img src="{{ $logoUrl }}" alt="{{ $clinicName }}" class="h-10 w-auto rounded-lg bg-white/10 p-1 backdrop-blur-sm">
                @else
                    <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-white/15 text-lg font-bold backdrop-blur-sm">أ</span>
                @endif
                <span class="text-sm font-semibold tracking-wide text-white/90 md:text-base">{{ $clinicName }}</span>
            </div>
            <a
                href="{{ url('/admin/login') }}"
                class="rounded-xl border border-white/25 bg-white/10 px-4 py-2 text-sm font-semibold text-white backdrop-blur-sm transition hover:bg-white/20"
            >
                دخول النظام
            </a>
        </nav>

        <div class="relative z-10 flex flex-1 flex-col items-center justify-center px-6 pb-24 pt-10 text-center md:px-12">
            <p class="landing-fade-up mb-4 text-sm font-semibold tracking-[0.2em] md:text-base" style="color: #99f6e4;">
                رعاية أسنان بثقة واحتراف
            </p>
            <h1 class="landing-fade-up landing-fade-up-delay max-w-4xl text-4xl font-bold leading-tight md:text-6xl lg:text-7xl">
                {{ $clinicName }}
            </h1>
            <p class="landing-fade-up landing-fade-up-delay-2 mt-5 max-w-xl text-base text-white/80 md:text-lg">
                نظام إدارة عيادة حديث لإدارة المرضى والحجوزات والملفات المالية في واجهة هادئة وواضحة.
            </p>
            <div class="landing-fade-up landing-fade-up-delay-2 mt-10">
                <a
                    href="{{ url('/admin/login') }}"
                    class="landing-cta inline-flex items-center gap-2 rounded-2xl px-8 py-3.5 text-base font-bold text-white transition hover:brightness-110"
                    style="background: linear-gradient(135deg, var(--lp-primary), #1d4ed8);"
                >
                    دخول النظام
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-5 w-5 rotate-180">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                    </svg>
                </a>
            </div>
        </div>
    </header>

    <section class="bg-[var(--color-medical-bg)] px-6 py-20 md:px-12 lg:px-16">
        <div class="mx-auto max-w-5xl">
            <h2 class="text-center text-2xl font-bold text-[var(--color-medical-text)] md:text-3xl" data-aos="fade-up">
                كل ما تحتاجه لإدارة العيادة
            </h2>
            <p class="mx-auto mt-3 max-w-2xl text-center text-[var(--color-medical-muted)]" data-aos="fade-up" data-aos-delay="80">
                أدوات واضحة للمرضى والحجوزات والمالية — بتصميم طبي نظيف ومريح للعين.
            </p>

            <div class="mt-14 grid gap-10 md:grid-cols-3">
                <div class="text-center" data-aos="fade-up" data-aos-delay="100">
                    <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-2xl" style="background: #dbeafe; color: var(--lp-primary);">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-7 w-7">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-[var(--color-medical-text)]">ملفات المرضى</h3>
                    <p class="mt-2 text-sm leading-relaxed text-[var(--color-medical-muted)]">سجل طبي منظم مع زيارات وأودونتوجرام ووثائق في مكان واحد.</p>
                </div>

                <div class="text-center" data-aos="fade-up" data-aos-delay="180">
                    <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-2xl" style="background: #ccfbf1; color: var(--lp-secondary);">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-7 w-7">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-[var(--color-medical-text)]">الحجوزات والزيارات</h3>
                    <p class="mt-2 text-sm leading-relaxed text-[var(--color-medical-muted)]">متابعة مواعيد اليوم والزيارات بسهولة مع مؤشرات واضحة للفريق.</p>
                </div>

                <div class="text-center" data-aos="fade-up" data-aos-delay="260">
                    <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-2xl" style="background: #dbeafe; color: var(--lp-primary);">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-7 w-7">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-[var(--color-medical-text)]">المالية والتقارير</h3>
                    <p class="mt-2 text-sm leading-relaxed text-[var(--color-medical-muted)]">تحصيل ومدفوعات وكشوف حساب دقيقة بلمسة احترافية.</p>
                </div>
            </div>
        </div>
    </section>

    <footer class="border-t border-[var(--color-medical-border)] bg-white px-6 py-10 text-center md:px-12">
        <p class="text-base font-semibold text-[var(--color-medical-text)]">{{ $clinicName }}</p>
        @if ($phone || $address)
            <p class="mt-2 text-sm text-[var(--color-medical-muted)]">
                @if ($phone)
                    <span>{{ $phone }}</span>
                @endif
                @if ($phone && $address)
                    <span class="mx-2 text-[var(--color-medical-border)]">|</span>
                @endif
                @if ($address)
                    <span>{{ $address }}</span>
                @endif
            </p>
        @endif
        <p class="mt-4 text-xs text-[var(--color-medical-muted)]">&copy; {{ date('Y') }} — جميع الحقوق محفوظة</p>
    </footer>
</body>
</html>
