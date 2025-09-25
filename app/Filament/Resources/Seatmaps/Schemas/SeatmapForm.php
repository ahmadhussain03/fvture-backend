<?php

namespace App\Filament\Resources\Seatmaps\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class SeatmapForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                Textarea::make('background_url')
                    ->required()
                    ->columnSpanFull(),
                TextInput::make('map_width')
                    ->required()
                    ->numeric(),
                TextInput::make('map_height')
                    ->required()
                    ->numeric(),
            ]);
    }
}
