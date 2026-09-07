<?php

namespace App\Filament\Resources\Visits\Tables;

use App\Models\Visit;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class VisitsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('patient.name')
                    ->label('المريض')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('visit_at')
                    ->label('التاريخ')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('status')
                    ->label('الحالة')
                    ->badge(),
                TextColumn::make('total')
                    ->label('إجمالي البنود')
                    ->state(fn (Visit $record): string => number_format($record->totalLineAmount(), 2)),
                TextColumn::make('paid')
                    ->label('المدفوع')
                    ->state(fn (Visit $record): string => number_format($record->totalPayments(), 2)),
                TextColumn::make('balance')
                    ->label('المتبقي')
                    ->state(fn (Visit $record): string => number_format($record->balanceDue(), 2)),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
