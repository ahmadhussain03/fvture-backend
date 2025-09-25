<?php
namespace App\Filament\Resources\Seatmaps\Tables;

use Filament\Tables;

class SeatmapTable
{
    public static function getColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('id')->sortable(),
            Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
            Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable(),
            Tables\Columns\TextColumn::make('updated_at')->dateTime()->sortable(),
        ];
    }
}
