<?php

namespace App\Filament\Resources\ClubTables\Pages;

use App\Filament\Resources\ClubTables\ClubTableResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditClubTable extends EditRecord
{
    protected static string $resource = ClubTableResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
