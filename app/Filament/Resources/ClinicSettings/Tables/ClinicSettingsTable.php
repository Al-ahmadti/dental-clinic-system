<?php

namespace App\Filament\Resources\ClinicSettings\Tables;

use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ClinicSettingsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('clinic_name')->label('الاسم'),
                TextColumn::make('phone')->label('الهاتف'),
            ])
            ->recordActions([
                EditAction::make(),
            ]);
    }
}
