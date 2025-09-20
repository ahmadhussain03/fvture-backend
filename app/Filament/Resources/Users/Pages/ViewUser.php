<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewUser extends ViewRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Ensure user_type is always 'admin' for display
        $data['user_type'] = 'admin';
        
        // Set the role_display for the form
        $user = $this->record;
        $firstRole = $user->roles()->first();
        $data['role_display'] = $firstRole ? $firstRole->name : 'No role assigned';
        
        return $data;
    }

    public function getFormMaxWidth(): string
    {
        return 'full';
    }
}
