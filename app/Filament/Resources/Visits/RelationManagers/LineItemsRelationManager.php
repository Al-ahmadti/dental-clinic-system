<?php

namespace App\Filament\Resources\Visits\RelationManagers;

use App\Models\Service;
use App\Support\ServiceCatalogTree;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class LineItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'lineItems';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('service_id')
                    ->label('الخدمة')
                    ->options(ServiceCatalogTree::flattenedServiceLabels())
                    ->searchable()
                    ->required()
                    ->live()
                    ->afterStateUpdated(function ($state, callable $set): void {
                        if ($state) {
                            $price = Service::query()->whereKey($state)->value('price');
                            $set('unit_price', $price);
                        }
                    }),
                TextInput::make('fdi_number')
                    ->label('رقم السن (FDI)')
                    ->numeric()
                    ->minValue(11)
                    ->maxValue(48)
                    ->nullable(),
                TextInput::make('quantity')
                    ->label('الكمية')
                    ->numeric()
                    ->default(1)
                    ->required()
                    ->minValue(1)
                    ->hidden()
                    ->dehydrated(),
                TextInput::make('unit_price')
                    ->label('المبلغ')
                    ->numeric()
                    ->minValue(0.01)
                    ->required(),
                TextInput::make('notes')
                    ->label('ملاحظات'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                TextColumn::make('service.name')->label('الخدمة'),
                TextColumn::make('fdi_number')->label('FDI')->placeholder('—'),
                TextColumn::make('unit_price')->label('المبلغ')->numeric(decimalPlaces: 2),
                TextColumn::make('line_total')
                    ->label('الإجمالي')
                    ->state(fn ($record) => number_format($record->lineTotal(), 2)),
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
