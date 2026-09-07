<?php

namespace App\Filament\Resources\PatientMedia\Tables;

use App\Models\PatientMedia;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PatientMediaTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('thumbnail')
                    ->label('الصورة')
                    ->height(56)
                    ->square()
                    ->getStateUsing(fn (PatientMedia $record): ?string => $record->isImage() ? $record->url() : null)
                    ->url(fn (PatientMedia $record): string => $record->url())
                    ->openUrlInNewTab(),
                TextColumn::make('patient.name')
                    ->label('المريض')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('visit.visit_at')
                    ->label('الزيارة')
                    ->dateTime('Y-m-d H:i')
                    ->placeholder('بدون زيارة')
                    ->sortable(),
                TextColumn::make('kind')
                    ->label('النوع')
                    ->formatStateUsing(fn (string $state, PatientMedia $record): string => $record->kindLabel())
                    ->badge()
                    ->color(fn (PatientMedia $record): string => match ($record->kindColor()) {
                        'primary' => 'primary',
                        'secondary' => 'success',
                        default => 'gray',
                    }),
                TextColumn::make('original_name')
                    ->label('اسم الملف')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('created_at')
                    ->label('تاريخ الإضافة')
                    ->dateTime('Y-m-d H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
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
