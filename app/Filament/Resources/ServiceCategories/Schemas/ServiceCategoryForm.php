<?php

namespace App\Filament\Resources\ServiceCategories\Schemas;

use App\Models\ServiceCategory;
use App\Support\ServiceCatalogTree;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;

class ServiceCategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('parent_id')
                    ->label('التصنيف الأب')
                    ->nullable()
                    ->searchable()
                    ->preload()
                    ->options(function (?Model $record): array {
                        $paths = ServiceCatalogTree::flattenedAdminCategoryLabels();
                        $exclude = [];
                        if ($record instanceof ServiceCategory && $record->exists) {
                            $exclude = ServiceCatalogTree::descendantCategoryIdsIncluding((int) $record->getKey());
                        }
                        foreach ($exclude as $id) {
                            unset($paths[$id]);
                        }

                        return $paths;
                    }),
                TextInput::make('name')
                    ->label('الاسم')
                    ->required()
                    ->maxLength(255),
                TextInput::make('sort_order')
                    ->label('الترتيب')
                    ->numeric()
                    ->default(0),
                Toggle::make('is_active')
                    ->label('نشط')
                    ->default(true),
            ]);
    }
}
