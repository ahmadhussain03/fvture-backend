<?php

namespace App\Filament\Resources\Seatmaps;

use App\Filament\Resources\Seatmaps\Pages\CreateSeatmap;
use App\Filament\Resources\Seatmaps\Pages\EditSeatmap;
use App\Filament\Resources\Seatmaps\Pages\ListSeatmaps;
use App\Filament\Resources\Seatmaps\Schemas\SeatmapForm;
use App\Filament\Resources\Seatmaps\Tables\SeatmapsTable;
use App\Models\Seatmap;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class SeatmapResource extends Resource
{
    protected static ?string $model = Seatmap::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return SeatmapForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SeatmapsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSeatmaps::route('/'),
            'create' => CreateSeatmap::route('/create'),
            'edit' => EditSeatmap::route('/{record}/edit'),
        ];
    }
}
