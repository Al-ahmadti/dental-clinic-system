<?php

namespace App\Filament\Resources\Patients;

use App\Filament\Resources\Patients\Pages\CreatePatient;
use App\Filament\Resources\Patients\Pages\CreatePatientVisit;
use App\Filament\Resources\Patients\Pages\EditPatient;
use App\Filament\Resources\Patients\Pages\ListPatients;
use App\Filament\Resources\Patients\Pages\ManagePatientDashboard;
use App\Filament\Resources\Patients\Pages\ManagePatientOdontogram;
use App\Filament\Resources\Patients\Pages\PatientAccountStatement;
use App\Filament\Resources\Patients\Pages\PatientPrintReport;
use App\Filament\Resources\Patients\Schemas\PatientForm;
use App\Filament\Resources\Patients\Tables\PatientsTable;
use App\Models\Patient;
use BackedEnum;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PatientResource extends Resource
{
    protected static ?string $model = Patient::class;

    protected static ?string $navigationLabel = 'المرضى';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserGroup;

    protected static string|\UnitEnum|null $navigationGroup = 'العيادة';

    protected static ?int $navigationSort = 1;

    public static function getModelLabel(): string
    {
        return 'مريض';
    }

    public static function getPluralModelLabel(): string
    {
        return 'المرضى';
    }

    public static function getRecordTitleAttribute(): ?string
    {
        return 'name';
    }

    public static function form(Schema $schema): Schema
    {
        return PatientForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name')->label('الاسم')->required(),
                TextEntry::make('file_number')->label('رقم الملف')->placeholder('—'),
                TextEntry::make('phone')->label('الهاتف'),
                TextEntry::make('gender')->label('الجنس')->required(),
                TextEntry::make('birth_date')->label('تاريخ الميلاد')->date()->required(),
                TextEntry::make('notes')->label('ملاحظات')->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return PatientsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPatients::route('/'),
            'create' => CreatePatient::route('/create'),
            'view' => ManagePatientDashboard::route('/{record}'),
            'statement' => PatientAccountStatement::route('/{record}/statement'),
            'report' => PatientPrintReport::route('/{record}/report'),
            'edit' => EditPatient::route('/{record}/edit'),
            'odontogram' => ManagePatientOdontogram::route('/{record}/odontogram'),
            'create-visit' => CreatePatientVisit::route('/{record}/visits/create'),
        ];
    }
}
