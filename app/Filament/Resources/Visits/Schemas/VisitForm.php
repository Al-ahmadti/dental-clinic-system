<?php

namespace App\Filament\Resources\Visits\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class VisitForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('patient_id')
                    ->label('المريض')
                    ->relationship('patient', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('user_id')
                    ->label('الطبيب')
                    ->relationship('user', 'name')
                    ->default(fn () => auth()->id())
                    ->disabled()
                    ->dehydrated()
                    ->required(),
                DateTimePicker::make('visit_at')
                    ->label('تاريخ ووقت الزيارة')
                    ->required()
                    ->default(now()),
                Select::make('status')
                    ->label('الحالة')
                    ->options([
                        'scheduled' => 'مجدول',
                        'completed' => 'مكتمل',
                        'cancelled' => 'ملغى',
                    ])
                    ->default('completed')
                    ->required(),
                Textarea::make('diagnosis')
                    ->label('التشخيص')
                    ->columnSpanFull(),
                Textarea::make('notes')
                    ->label('ملاحظات')
                    ->columnSpanFull(),
            ]);
    }
}
