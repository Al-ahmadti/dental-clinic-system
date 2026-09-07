<?php

namespace App\Filament\Resources\Expenses\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ExpenseForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('amount')
                    ->label('المبلغ')
                    ->numeric()
                    ->minValue(0.01)
                    ->required(),
                DatePicker::make('expense_date')
                    ->label('تاريخ المصروف')
                    ->required()
                    ->default(now()),
                Select::make('category')
                    ->label('التصنيف')
                    ->options([
                        'rent' => 'إيجار',
                        'payroll' => 'رواتب',
                        'inventory_purchase' => 'مشتريات مخزن',
                        'tools' => 'أدوات',
                        'other' => 'أخرى',
                    ])
                    ->required(),
                Textarea::make('description')
                    ->label('الوصف')
                    ->columnSpanFull(),
                Select::make('user_id')
                    ->label('المستخدم')
                    ->relationship('user', 'name')
                    ->default(fn () => auth()->id())
                    ->disabled()
                    ->dehydrated()
                    ->required(),
                Select::make('inventory_movement_id')
                    ->label('ربط بحركة مخزن (اختياري)')
                    ->relationship('inventoryMovement', 'id', fn ($query) => $query->orderByDesc('movement_at'))
                    ->nullable()
                    ->searchable(),
            ]);
    }
}
