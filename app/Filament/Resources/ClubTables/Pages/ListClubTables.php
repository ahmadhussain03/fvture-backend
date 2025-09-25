<?php

namespace App\Filament\Resources\ClubTables\Pages;

use App\Filament\Resources\ClubTables\ClubTableResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListClubTables extends ListRecords
{
    protected static string $resource = ClubTableResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
