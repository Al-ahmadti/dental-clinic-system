<?php

namespace App\Filament\Resources\InventoryItems\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class InventoryItemForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('اسم الصنف')
                    ->required()
                    ->maxLength(255),
                Select::make('type')
                    ->label('النوع')
                    ->options([
                        'medical' => 'مواد طبية',
                        'tools' => 'أدوات',
                    ])
                    ->default('medical')
                    ->required(),
                TextInput::make('unit')
                    ->label('الوحدة')
                    ->default('piece')
                    ->maxLength(16),
            ]);
    }
}
