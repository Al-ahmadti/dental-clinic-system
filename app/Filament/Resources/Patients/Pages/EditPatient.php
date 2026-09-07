<?php

namespace App\Filament\Resources\Patients\Pages;

use App\Filament\Resources\Patients\PatientResource;
use App\Models\Patient;
use App\Services\Patients\PatientDuplicateChecker;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\View;
use Filament\Schemas\Schema;
use Illuminate\Support\Collection;

class EditPatient extends EditRecord
{
    protected static string $resource = PatientResource::class;

    /** @var Collection<int, Patient> */
    public Collection $duplicatePatients;

    public bool $allowDuplicate = false;

    public function mount(int|string $record): void
    {
        parent::mount($record);
        $this->duplicatePatients = collect();
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('file_number')
                    ->label('رقم الملف')
                    ->maxLength(64)
                    ->nullable()
                    ->unique(table: 'patients', column: 'file_number', ignoreRecord: true),
                TextInput::make('name')
                    ->label('الاسم')
                    ->required()
                    ->maxLength(255)
                    ->live(debounce: 500)
                    ->afterStateUpdated(fn () => $this->refreshDuplicateMatches()),
                TextInput::make('phone')
                    ->label('الهاتف')
                    ->tel()
                    ->required()
                    ->maxLength(32)
                    ->live(debounce: 500)
                    ->afterStateUpdated(fn () => $this->refreshDuplicateMatches()),
                Select::make('gender')
                    ->label('الجنس')
                    ->options([
                        'male' => 'ذكر',
                        'female' => 'أنثى',
                    ])
                    ->required()
                    ->native(false),
                DatePicker::make('birth_date')
                    ->label('تاريخ الميلاد')
                    ->native(false),
                Textarea::make('notes')
                    ->label('ملاحظات')
                    ->rows(3)
                    ->columnSpanFull(),
                Section::make('تنبيه تكرار محتمل')
                    ->schema([
                        View::make('filament.forms.patient-duplicate-alert'),
                        Checkbox::make('allowDuplicate')
                            ->label('متابعة الحفظ على أي حال (أؤكد أنه مريض مختلف)')
                            ->live()
                            ->visible(fn (): bool => $this->duplicatePatients->isNotEmpty()),
                    ])
                    ->visible(fn (): bool => $this->duplicatePatients->isNotEmpty())
                    ->columnSpanFull(),
            ]);
    }

    public function refreshDuplicateMatches(): void
    {
        $state = $this->form->getRawState();
        $this->duplicatePatients = app(PatientDuplicateChecker::class)->findSimilar(
            name: $state['name'] ?? null,
            phone: $state['phone'] ?? null,
            fileNumber: $state['file_number'] ?? null,
            excludePatientId: (int) $this->getRecord()->getKey(),
        );

        if ($this->duplicatePatients->isEmpty()) {
            $this->allowDuplicate = false;
            unset($this->data['allowDuplicate']);
        }
    }

    protected function beforeSave(): void
    {
        $this->refreshDuplicateMatches();

        $allow = (bool) ($this->form->getState()['allowDuplicate'] ?? $this->allowDuplicate);

        if ($this->duplicatePatients->isNotEmpty() && ! $allow) {
            Notification::make()
                ->title('يوجد مريض قد يكون نفس الشخص')
                ->body('راجع القائمة أدناه أو فعّل «متابعة الحفظ على أي حال».')
                ->warning()
                ->persistent()
                ->send();

            $this->halt();
        }
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        unset($data['allowDuplicate']);

        return $data;
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
