<?php

namespace App\Filament\Resources\PatientAuditLogs\Tables;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PatientAuditLogsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')
                    ->label('الوقت')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('patient.name')
                    ->label('المريض')
                    ->searchable(),
                TextColumn::make('user.name')
                    ->label('المستخدم')
                    ->placeholder('—'),
                TextColumn::make('action')
                    ->label('الإجراء')
                    ->badge(),
                TextColumn::make('ip_address')
                    ->label('IP')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
