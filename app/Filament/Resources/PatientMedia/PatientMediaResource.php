<?php

namespace App\Filament\Resources\PatientMedia;

use App\Filament\Resources\PatientMedia\Pages\CreatePatientMedia;
use App\Filament\Resources\PatientMedia\Pages\EditPatientMedia;
use App\Filament\Resources\PatientMedia\Pages\ListPatientMedia;
use App\Filament\Resources\PatientMedia\Schemas\PatientMediaForm;
use App\Filament\Resources\PatientMedia\Tables\PatientMediaTable;
use App\Models\PatientMedia;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PatientMediaResource extends Resource
{
    protected static ?string $model = PatientMedia::class;

    protected static ?string $navigationLabel = 'صور المرضى';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPhoto;

    protected static string|\UnitEnum|null $navigationGroup = 'العيادة';

    protected static ?int $navigationSort = 20;

    public static function getPluralModelLabel(): string
    {
        return 'صور وأشعة';
    }

    public static function form(Schema $schema): Schema
    {
        return PatientMediaForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PatientMediaTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPatientMedia::route('/'),
            'create' => CreatePatientMedia::route('/create'),
            'edit' => EditPatientMedia::route('/{record}/edit'),
        ];
    }
}
