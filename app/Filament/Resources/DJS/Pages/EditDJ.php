<?php

namespace App\Filament\Resources\DJS\Pages;

use App\Filament\Resources\DJS\DJResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditDJ extends EditRecord
{
    protected static string $resource = DJResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
