<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->visible(fn () => $this->record->name !== 'Super Admin'),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Ensure user_type is always 'admin' for display
        $data['user_type'] = 'admin';
        
        // Set the role_id for the form
        $user = $this->record;
        $firstRole = $user->roles()->first();
        $data['role_id'] = $firstRole ? $firstRole->id : null;
        
        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Ensure user_type is always 'admin' when saving
        $data['user_type'] = 'admin';
        
        return $data;
    }

    protected function afterSave(): void
    {
        $user = $this->record;
        $roleId = $this->data['role_id'] ?? null;
        
        if ($roleId) {
            $user->roles()->sync([$roleId]);
        }
    }

    public function getFormMaxWidth(): string
    {
        return 'full';
    }
}
