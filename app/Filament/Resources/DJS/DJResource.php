<?php

namespace App\Filament\Resources\DJS;

use App\Filament\Resources\DJS\Pages\CreateDJ;
use App\Filament\Resources\DJS\Pages\EditDJ;
use App\Filament\Resources\DJS\Pages\ListDJS;
use App\Filament\Resources\DJS\Schemas\DJForm;
use App\Filament\Resources\DJS\Tables\DJSTable;
use App\Models\DJ;
use BackedEnum;
use Filament\Resources\Resource;
use UnitEnum;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class DJResource extends Resource
{
    protected static ?string $model = DJ::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedMicrophone;

    protected static ?string $navigationLabel = 'DJs';

    protected static ?string $modelLabel = 'DJ';

    protected static ?string $pluralModelLabel = 'DJs';

    protected static ?string $recordTitleAttribute = 'name';

    protected static string|UnitEnum|null $navigationGroup = 'General';

    protected static ?int $navigationSort = 2;

    public static function getNavigationGroup(): ?string
    {
        return 'General';
    }

    public static function getNavigationSort(): ?int
    {
        return 2;
    }

    public static function form(Schema $schema): Schema
    {
        return DJForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DJSTable::configure($table);
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
            'index' => ListDJS::route('/'),
            'create' => CreateDJ::route('/create'),
            'edit' => EditDJ::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('dj.view_any') ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->can('dj.create') ?? false;
    }

    public static function canEdit($record): bool
    {
        return auth()->user()?->can('dj.update') ?? false;
    }

    public static function canDelete($record): bool
    {
        return auth()->user()?->can('dj.delete') ?? false;
    }

    public static function canDeleteAny(): bool
    {
        return auth()->user()?->can('dj.delete') ?? false;
    }
}
