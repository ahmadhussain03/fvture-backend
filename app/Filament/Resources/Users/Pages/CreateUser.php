<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_type'] = 'admin';
        
        return $data;
    }

    protected function afterCreate(): void
    {
        $user = $this->record;
        $roleId = $this->data['role_id'] ?? null;
        
        if ($roleId) {
            $user->roles()->sync([$roleId]);
        }
    }

    protected function getFormActions(): array
    {
        return [
            $this->getCreateFormAction(),
            $this->getCancelFormAction(),
        ];
    }

    public function getFormActionsAlignment(): string
    {
        return 'left';
    }

    public function getFormMaxWidth(): string
    {
        return 'full';
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
