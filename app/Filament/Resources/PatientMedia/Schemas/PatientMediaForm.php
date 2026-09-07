<?php

namespace App\Filament\Resources\PatientMedia\Schemas;

use App\Models\PatientMedia;
use App\Models\Visit;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class PatientMediaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('patient_id')
                    ->label('المريض')
                    ->relationship('patient', 'name')
                    ->searchable()
                    ->preload()
                    ->live()
                    ->required(),
                Select::make('visit_id')
                    ->label('الزيارة (اختياري)')
                    ->options(function (Get $get): array {
                        $patientId = $get('patient_id');
                        if (! $patientId) {
                            return [];
                        }

                        return Visit::query()
                            ->where('patient_id', $patientId)
                            ->orderByDesc('visit_at')
                            ->get()
                            ->mapWithKeys(fn (Visit $visit): array => [
                                $visit->id => $visit->visit_at?->format('Y-m-d H:i') ?? ('#'.$visit->id),
                            ])
                            ->all();
                    })
                    ->searchable()
                    ->nullable(),
                Select::make('kind')
                    ->label('النوع')
                    ->options(PatientMedia::kindOptions())
                    ->required(),
                FileUpload::make('path')
                    ->label('الملف')
                    ->image()
                    ->directory('patient-media')
                    ->disk('local')
                    ->storeFileNamesIn('original_name')
                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                    ->required()
                    ->maxSize(10240),
            ]);
    }
}
