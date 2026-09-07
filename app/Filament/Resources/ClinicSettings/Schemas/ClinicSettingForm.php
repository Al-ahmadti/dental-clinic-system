<?php

namespace App\Filament\Resources\ClinicSettings\Schemas;

use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ClinicSettingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('clinic_name')
                    ->label('اسم العيادة')
                    ->required()
                    ->maxLength(255),
                TextInput::make('address')
                    ->label('العنوان')
                    ->maxLength(500),
                TextInput::make('phone')
                    ->label('هاتف العيادة')
                    ->maxLength(32),
                FileUpload::make('logo_path')
                    ->label('شعار العيادة')
                    ->image()
                    ->directory('clinic-logos')
                    ->disk('public')
                    ->visibility('public')
                    ->nullable(),
                ColorPicker::make('primary_color')
                    ->label('اللون الأساسي'),
                ColorPicker::make('secondary_color')
                    ->label('اللون الثانوي'),
                ColorPicker::make('accent_color')
                    ->label('لون التمييز'),
            ]);
    }
}
