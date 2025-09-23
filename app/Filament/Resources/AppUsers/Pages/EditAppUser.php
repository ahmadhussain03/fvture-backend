<?php

namespace App\Filament\Resources\AppUsers\Pages;

use App\Filament\Resources\AppUsers\AppUserResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditAppUser extends EditRecord
{
    protected static string $resource = AppUserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Ensure user_type is always 'app' for display
        $data['user_type'] = 'app';
        
        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Ensure user_type is always 'app' when saving
        $data['user_type'] = 'app';
        
        return $data;
    }

    public function getFormMaxWidth(): string
    {
        return 'full';
    }
}
