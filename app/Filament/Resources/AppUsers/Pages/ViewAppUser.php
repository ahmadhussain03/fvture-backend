<?php

namespace App\Filament\Resources\AppUsers\Pages;

use App\Filament\Resources\AppUsers\AppUserResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewAppUser extends ViewRecord
{
    protected static string $resource = AppUserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Ensure user_type is always 'app' for display
        $data['user_type'] = 'app';
        
        return $data;
    }

    public function getFormMaxWidth(): string
    {
        return 'full';
    }
}
