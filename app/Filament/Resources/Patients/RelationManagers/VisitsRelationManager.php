<?php

namespace App\Filament\Resources\Patients\RelationManagers;

use App\Filament\Resources\Visits\VisitResource;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class VisitsRelationManager extends RelationManager
{
    protected static string $relationship = 'visits';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
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

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('visit_at')
            ->columns([
                TextColumn::make('visit_at')->label('التاريخ')->dateTime()->sortable(),
                TextColumn::make('status')->label('الحالة')->badge(),
                TextColumn::make('user.name')->label('الطبيب'),
            ])
            ->headerActions([
                CreateAction::make()
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['patient_id'] = $this->getOwnerRecord()->getKey();
                        $data['user_id'] = auth()->id();

                        return $data;
                    }),
            ])
            ->recordActions([
                EditAction::make()
                    ->url(fn ($record): string => VisitResource::getUrl('edit', ['record' => $record])),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
