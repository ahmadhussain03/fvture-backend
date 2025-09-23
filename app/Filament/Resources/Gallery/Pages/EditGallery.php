<?php

namespace App\Filament\Resources\Gallery\Pages;

use App\Filament\Resources\Gallery\GalleryResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditGallery extends EditRecord
{
    protected static string $resource = GalleryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    public function getFormMaxWidth(): string
    {
        return 'full';
    }
}
