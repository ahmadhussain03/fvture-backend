<?php

namespace App\Filament\Resources\Seatmaps\Pages;

use App\Filament\Resources\Seatmaps\SeatmapResource;
use Filament\Resources\Pages\CreateRecord;
use Livewire\Attributes\On;

class CreateSeatmap extends CreateRecord
{
    protected static string $resource = SeatmapResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }

    // Removed custom view from footer widgets; now only inside the form
}
