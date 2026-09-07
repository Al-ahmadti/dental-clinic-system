<?php

namespace App\Filament\Resources\Visits\Pages;

use App\Filament\Resources\Visits\VisitResource;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditVisit extends EditRecord
{
    protected static string $resource = VisitResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function afterSave(): void
    {
        if ($this->getRecord()->balanceDue() > 0.009) {
            Notification::make()
                ->title('تنبيه')
                ->body('لا يزال هناك مبلغ متبقٍ على هذه الزيارة.')
                ->warning()
                ->send();
        }
    }
}
