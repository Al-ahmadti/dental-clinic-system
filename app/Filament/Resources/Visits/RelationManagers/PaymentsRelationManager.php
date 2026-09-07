<?php

namespace App\Filament\Resources\Visits\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PaymentsRelationManager extends RelationManager
{
    protected static string $relationship = 'payments';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('amount')
                    ->label('المبلغ')
                    ->numeric()
                    ->minValue(0.01)
                    ->required(),
                Select::make('payment_method')
                    ->label('طريقة الدفع')
                    ->options([
                        'cash' => 'نقد',
                        'card' => 'بطاقة',
                        'transfer' => 'تحويل',
                        'check' => 'شيك',
                    ])
                    ->default('cash')
                    ->required()
                    ->live(),
                TextInput::make('check_number')
                    ->label('رقم الشيك')
                    ->maxLength(64)
                    ->visible(fn ($get) => $get('payment_method') === 'check')
                    ->required(fn ($get) => $get('payment_method') === 'check'),
                DateTimePicker::make('paid_at')
                    ->label('تاريخ الدفع')
                    ->default(now())
                    ->required(),
                TextInput::make('notes')
                    ->label('ملاحظات'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('amount')->label('المبلغ')->numeric(decimalPlaces: 2),
                TextColumn::make('payment_method')->label('الطريقة')->badge(),
                TextColumn::make('check_number')->label('رقم الشيك')->placeholder('—'),
                TextColumn::make('paid_at')->label('التاريخ')->dateTime(),
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
