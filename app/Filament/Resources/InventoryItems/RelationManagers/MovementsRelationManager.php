<?php

namespace App\Filament\Resources\InventoryItems\RelationManagers;

use Filament\Actions\CreateAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class MovementsRelationManager extends RelationManager
{
    protected static string $relationship = 'movements';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('type')
                    ->label('نوع الحركة')
                    ->options([
                        'purchase' => 'شراء',
                        'issue' => 'صرف',
                        'adjustment' => 'تعديل',
                    ])
                    ->required()
                    ->default('purchase'),
                TextInput::make('quantity')
                    ->label('الكمية')
                    ->numeric()
                    ->minValue(0.01)
                    ->required(),
                TextInput::make('unit_cost')
                    ->label('تكلفة الوحدة')
                    ->numeric()
                    ->minValue(0)
                    ->default(0),
                TextInput::make('amount_paid')
                    ->label('المبلغ المدفوع')
                    ->numeric()
                    ->minValue(0)
                    ->default(0),
                TextInput::make('balance_due')
                    ->label('المتبقي للمورد')
                    ->numeric()
                    ->minValue(0)
                    ->default(0),
                Select::make('supplier_id')
                    ->label('المورد')
                    ->relationship('supplier', 'company_name')
                    ->searchable()
                    ->preload()
                    ->nullable(),
                DateTimePicker::make('movement_at')
                    ->label('التاريخ')
                    ->default(now())
                    ->required(),
                Textarea::make('notes')
                    ->label('ملاحظات')
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('movement_at')->label('التاريخ')->dateTime(),
                TextColumn::make('type')->label('النوع')->badge(),
                TextColumn::make('quantity')->label('الكمية'),
                TextColumn::make('unit_cost')->label('تكلفة الوحدة')->numeric(decimalPlaces: 2),
                TextColumn::make('amount_paid')->label('المدفوع')->numeric(decimalPlaces: 2),
                TextColumn::make('supplier.company_name')->label('المورد'),
            ])
            ->headerActions([
                CreateAction::make(),
            ]);
    }
}
