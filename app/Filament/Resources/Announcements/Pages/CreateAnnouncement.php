<?php

namespace App\Filament\Resources\Announcements\Pages;

use App\Filament\Resources\Announcements\AnnouncementResource;
use App\Models\User;
use App\Notifications\AnnouncementNotification;
use Filament\Resources\Pages\CreateRecord;

class CreateAnnouncement extends CreateRecord
{
    protected static string $resource = AnnouncementResource::class;

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

    protected function afterCreate(): void
    {
        $announcement = $this->record;
        
        // Get all app users
        $appUsers = User::where('user_type', 'app')->get();
        
        // Send notifications to all app users
        foreach ($appUsers as $user) {
            $user->notify(new AnnouncementNotification($announcement));
        }
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
