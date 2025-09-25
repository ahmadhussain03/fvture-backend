<?php
namespace App\Filament\Resources\Seatmaps\Schemas;

use Filament\Forms;

class SeatmapFormSchema
{
    public static function getSchema(): array
    {
        return [
            Forms\Components\TextInput::make('name')
                ->required()
                ->maxLength(255),
        ];
    }
}
