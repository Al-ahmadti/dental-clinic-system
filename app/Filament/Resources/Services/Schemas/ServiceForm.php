<?php

namespace App\Filament\Resources\Services\Schemas;

use App\Support\ServiceCatalogTree;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ServiceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('category_id')
                    ->label('التصنيف')
                    ->nullable()
                    ->searchable()
                    ->preload()
                    ->options(fn (): array => ServiceCatalogTree::flattenedAdminCategoryLabelsForServiceForm()),
                TextInput::make('code')
                    ->label('رمز مختصر')
                    ->maxLength(32)
                    ->helperText('اختياري — يظهر في قوائم الاختيار لتمييز العلاجات المتشابهة.'),
                TextInput::make('name')
                    ->label('اسم العلاج')
                    ->required()
                    ->maxLength(255),
                TextInput::make('sort_order')
                    ->label('الترتيب داخل التصنيف')
                    ->numeric()
                    ->default(0),
                TextInput::make('price')
                    ->label('السعر')
                    ->numeric()
                    ->default(0)
                    ->required(),
                Toggle::make('is_active')
                    ->label('نشط')
                    ->default(true),
            ]);
    }
}
