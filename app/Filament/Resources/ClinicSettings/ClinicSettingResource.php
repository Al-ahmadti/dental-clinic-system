<?php

namespace App\Filament\Resources\ClinicSettings;

use App\Filament\Resources\ClinicSettings\Pages\EditClinicSetting;
use App\Filament\Resources\ClinicSettings\Pages\ListClinicSettings;
use App\Filament\Resources\ClinicSettings\Schemas\ClinicSettingForm;
use App\Filament\Resources\ClinicSettings\Tables\ClinicSettingsTable;
use App\Models\ClinicSetting;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ClinicSettingResource extends Resource
{
    protected static ?string $model = ClinicSetting::class;

    protected static ?string $navigationLabel = 'إعدادات العيادة';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static string|\UnitEnum|null $navigationGroup = 'الإعدادات';

    protected static ?int $navigationSort = 100;

    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return ClinicSettingForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ClinicSettingsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListClinicSettings::route('/'),
            'edit' => EditClinicSetting::route('/{record}/edit'),
        ];
    }
}
