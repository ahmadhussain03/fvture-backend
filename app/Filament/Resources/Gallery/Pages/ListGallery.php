<?php

namespace App\Filament\Resources\Gallery\Pages;

use App\Filament\Resources\Gallery\GalleryResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListGallery extends ListRecords
{
    protected static string $resource = GalleryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
