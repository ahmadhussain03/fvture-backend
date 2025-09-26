<?php

namespace App\Filament\Resources\Seatmaps\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;

class SeatmapForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Seatmap Details')
                    ->tabs([
                        Tabs\Tab::make('Details')
                            ->icon('heroicon-o-information-circle')
                            ->schema([
                                Section::make('Details')
                                    ->schema([
                                        Grid::make(2)
                                            ->schema([
                                                TextInput::make('name')
                                                    ->label('Seatmap Name')
                                                    ->required(),
                                                TextInput::make('map_width')
                                                    ->label('Map Width')
                                                    ->required()
                                                    ->numeric()
                                                    ->default(700),
                                                TextInput::make('map_height')
                                                    ->label('Map Height')
                                                    ->required()
                                                    ->numeric()
                                                    ->default(450),
                                            ]),
                                    ])
                                    ->columns(1)
                                    ->columnSpanFull(),
                            ]),
                        Tabs\Tab::make('Images')
                            ->icon('heroicon-o-photo')
                            ->schema([
                                Section::make('Images')
                                    ->schema([
                                        \Filament\Forms\Components\FileUpload::make('background_url')
                                            ->label('Background Image')
                                            ->image()
                                            ->disk('s3')
                                            ->directory('seatmaps/backgrounds')
                                            ->visibility('public')
                                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/gif', 'image/webp'])
                                            ->maxSize(5120) // 5MB max
                                            ->getUploadedFileNameForStorageUsing(
                                                fn (string $file): string => (string) str($file)->prepend(time() . '-')
                                            )
                                            ->moveFiles()
                                            ->helperText('Upload a background image for this seatmap (Max 5MB)')
                                            ->required()
                                            ->columnSpanFull(),
                                    ])
                                    ->columns(1)
                                    ->columnSpanFull(),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
