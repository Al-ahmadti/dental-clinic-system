<?php

namespace App\Filament\Resources\Services\Tables;

use App\Support\ServiceCatalogTree;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ServicesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('category_path')
                    ->label('مسار التصنيف')
                    ->state(function ($record): string {
                        if (! $record->category_id) {
                            return '—';
                        }
                        $paths = ServiceCatalogTree::adminCategoryPathsById();

                        return $paths[(int) $record->category_id] ?? '—';
                    })
                    ->wrap()
                    ->toggleable(),
                TextColumn::make('code')
                    ->label('رمز')
                    ->searchable()
                    ->placeholder('—'),
                TextColumn::make('name')
                    ->label('الاسم')
                    ->searchable(),
                TextColumn::make('price')
                    ->label('السعر')
                    ->money()
                    ->sortable(),
                TextColumn::make('sort_order')
                    ->label('الترتيب')
                    ->sortable(),
                IconColumn::make('is_active')
                    ->label('نشط')
                    ->boolean(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
