<?php

namespace Tests\Feature;

use App\Filament\Resources\Patients\Pages\PatientPrintReport;
use App\Filament\Resources\Patients\PatientResource;
use App\Models\ClinicSetting;
use App\Models\Patient;
use App\Models\User;
use App\Services\Patients\PatientReportBuilder;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class PatientPrintReportTest extends TestCase
{
    use RefreshDatabase;

    public function test_report_builder_aggregates_clinic_and_patient_data(): void
    {
        ClinicSetting::query()->create([
            'clinic_name' => 'عيادة الاختبار',
            'primary_color' => '#2563EB',
            'secondary_color' => '#14B8A6',
        ]);

        $patient = Patient::query()->create([
            'name' => 'مريض التقرير',
            'phone' => '0501112222',
            'gender' => 'male',
        ]);

        $report = app(PatientReportBuilder::class)->build($patient);

        $this->assertSame('مريض التقرير', $report->patient->name);
        $this->assertSame('عيادة الاختبار', $report->clinic?->clinic_name);
        $this->assertSame('#2563EB', $report->primaryColor);
    }

    public function test_authenticated_user_can_view_patient_report_page(): void
    {
        $user = User::factory()->create();

        ClinicSetting::query()->create([
            'clinic_name' => 'عيادة الاختبار',
            'primary_color' => '#2563EB',
            'secondary_color' => '#14B8A6',
        ]);

        $patient = Patient::query()->create([
            'name' => 'مريض التقرير',
            'phone' => '0501112222',
            'gender' => 'male',
        ]);

        Filament::setCurrentPanel(Filament::getPanel('admin'));
        $this->actingAs($user);

        Livewire::test(PatientPrintReport::class, ['record' => $patient->getKey()])
            ->assertOk()
            ->assertSee('مريض التقرير')
            ->assertSee('عيادة الاختبار')
            ->assertSee('تقرير المريض');
    }

    public function test_report_page_requires_authentication(): void
    {
        $patient = Patient::query()->create([
            'name' => 'مريض',
            'phone' => '050',
            'gender' => 'male',
        ]);

        $url = PatientResource::getUrl('report', ['record' => $patient], panel: 'admin');

        $this->get($url)->assertRedirect();
    }
}
