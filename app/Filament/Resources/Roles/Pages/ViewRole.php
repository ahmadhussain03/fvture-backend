<?php

namespace App\Filament\Resources\Roles\Pages;

use App\Filament\Resources\Roles\RoleResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewRole extends ViewRecord
{
    protected static string $resource = RoleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make()
                ->visible(fn () => $this->record->name !== 'Super Admin'),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Ensure guard_name is always 'web' for display
        $data['guard_name'] = 'web';
        
        return $data;
    }

    public function getFormMaxWidth(): string
    {
        return 'full';
    }
}
