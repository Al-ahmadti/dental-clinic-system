<?php

namespace App\Filament\Resources\ServiceCategories\Tables;

use App\Support\ServiceCatalogTree;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class ServiceCategoriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('full_path')
                    ->label('المسار الكامل')
                    ->state(function ($record): string {
                        $paths = ServiceCatalogTree::adminCategoryPathsById();

                        return $paths[(int) $record->getKey()] ?? $record->name;
                    })
                    ->searchable(query: function ($query, string $search): void {
                        $query->where('name', 'like', '%'.$search.'%');
                    })
                    ->wrap(),
                TextColumn::make('parent.name')
                    ->label('الأب المباشر')
                    ->placeholder('—')
                    ->toggleable(),
                TextColumn::make('name')
                    ->label('اسم العقدة')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('sort_order')
                    ->label('الترتيب')
                    ->numeric()
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
                TernaryFilter::make('roots_only')
                    ->label('المستوى')
                    ->nullable()
                    ->trueLabel('جذور فقط')
                    ->falseLabel('لها أب')
                    ->queries(
                        true: fn ($q) => $q->whereNull('parent_id'),
                        false: fn ($q) => $q->whereNotNull('parent_id'),
                    ),
                SelectFilter::make('is_active')
                    ->label('الحالة')
                    ->options([
                        '1' => 'نشط',
                        '0' => 'غير نشط',
                    ]),
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
