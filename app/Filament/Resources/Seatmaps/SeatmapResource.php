<?php
namespace App\Filament\Resources\Seatmaps;

use App\Models\Seatmap;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Resources\Form;
use Filament\Tables;
use Filament\Forms;
use App\Filament\Resources\Seatmaps\Pages;
use App\Filament\Resources\Seatmaps\Tables\SeatmapTable;
use App\Filament\Resources\Seatmaps\Schemas\SeatmapFormSchema;

class SeatmapResource extends Resource
{
    protected static ?string $model = Seatmap::class;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static string|\UnitEnum|null $navigationGroup = 'Seatmaps';
    protected static ?string $slug = 'seatmaps';

    public static function form(\Filament\Schemas\Schema $schema): \Filament\Schemas\Schema
    {
        return $schema->schema(
            \App\Filament\Resources\Seatmaps\Schemas\SeatmapFormSchema::getSchema()
        );
    }

    public static function table(\Filament\Tables\Table $table): \Filament\Tables\Table
    {
        return $table->columns(
            \App\Filament\Resources\Seatmaps\Tables\SeatmapTable::getColumns()
        );
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSeatmaps::route('/'),
            'create' => Pages\CreateSeatmap::route('/create'),
            'edit' => Pages\EditSeatmap::route('/{record}/edit'),
        ];
    }
}
