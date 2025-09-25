<?php

namespace App\Filament\Resources\Seatmaps\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SeatmapForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Seatmap Details')
                    ->tabs([
                        Tabs\Tab::make('Details')
                            ->schema([
                                TextInput::make('name')
                                    ->required(),
                                TextInput::make('map_width')
                                    ->required()
                                    ->numeric(),
                                TextInput::make('map_height')
                                    ->required()
                                    ->numeric(),
                            ]),
                        Tabs\Tab::make('Background Image')
                            ->schema([
                                FileUpload::make('background_url')
                                    ->label('Background Image')
                                    ->image()
                                    ->disk('s3')
                                    ->directory('seatmaps/backgrounds')
                                    ->visibility('public')
                                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/gif', 'image/webp'])
                                    ->maxSize(5120)
                                    ->getUploadedFileNameForStorageUsing(fn (string $file) => (string) str($file)->prepend(time() . '-'))
                                    ->moveFiles()
                                    ->required(),
                            ]),
                    ]),
            ]);
    }
}
