<?php

namespace App\Filament\Resources\PatientAuditLogs;

use App\Filament\Resources\PatientAuditLogs\Pages\ListPatientAuditLogs;
use App\Filament\Resources\PatientAuditLogs\Tables\PatientAuditLogsTable;
use App\Models\PatientAuditLog;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PatientAuditLogResource extends Resource
{
    protected static ?string $model = PatientAuditLog::class;

    protected static ?string $navigationLabel = 'تدقيق المرضى';

    protected static ?string $modelLabel = 'سجل تدقيق';

    protected static ?string $pluralModelLabel = 'سجلات تدقيق المرضى';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentList;

    protected static string|\UnitEnum|null $navigationGroup = 'العيادة';

    protected static ?int $navigationSort = 45;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function table(Table $table): Table
    {
        return PatientAuditLogsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPatientAuditLogs::route('/'),
        ];
    }
}
