<?php

namespace App\Filament\Resources\DJS\Pages;

use App\Filament\Resources\DJS\DJResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListDJS extends ListRecords
{
    protected static string $resource = DJResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
