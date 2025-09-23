<?php

namespace App\Filament\Resources\Gallery\Pages;

use App\Filament\Resources\Gallery\GalleryResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewGallery extends ViewRecord
{
    protected static string $resource = GalleryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }

    public function getFormMaxWidth(): string
    {
        return 'full';
    }
}
