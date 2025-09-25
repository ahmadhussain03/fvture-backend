<?php

namespace App\Filament\Resources\ClubTables;

use App\Filament\Resources\ClubTables\Pages\CreateClubTable;
use App\Filament\Resources\ClubTables\Pages\EditClubTable;
use App\Filament\Resources\ClubTables\Pages\ListClubTables;
use App\Filament\Resources\ClubTables\Schemas\ClubTableForm;
use App\Filament\Resources\ClubTables\Tables\ClubTablesTable;
use App\Models\ClubTable;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ClubTableResource extends Resource
{
    protected static ?string $model = ClubTable::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return ClubTableForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ClubTablesTable::configure($table);
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
            'index' => ListClubTables::route('/'),
            'create' => CreateClubTable::route('/create'),
            'edit' => EditClubTable::route('/{record}/edit'),
        ];
    }
}
