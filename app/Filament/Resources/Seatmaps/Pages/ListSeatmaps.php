<?php

namespace App\Filament\Resources\Seatmaps\Pages;

use App\Filament\Resources\Seatmaps\SeatmapResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSeatmaps extends ListRecords
{
    protected static string $resource = SeatmapResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
