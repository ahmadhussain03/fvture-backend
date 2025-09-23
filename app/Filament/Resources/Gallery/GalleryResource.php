<?php

namespace App\Filament\Resources\Gallery;

use App\Filament\Resources\Gallery\Pages\CreateGallery;
use App\Filament\Resources\Gallery\Pages\EditGallery;
use App\Filament\Resources\Gallery\Pages\ListGallery;
use App\Filament\Resources\Gallery\Pages\ViewGallery;
use App\Filament\Resources\Gallery\Schemas\GalleryForm;
use App\Filament\Resources\Gallery\Tables\GalleryTable;
use App\Models\Gallery;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class GalleryResource extends Resource
{
    protected static ?string $model = Gallery::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPhoto;

    protected static ?string $navigationLabel = 'Gallery';

    protected static ?string $modelLabel = 'Gallery Item';

    protected static ?string $pluralModelLabel = 'Gallery';

    protected static ?string $recordTitleAttribute = 'title';

    protected static string|UnitEnum|null $navigationGroup = 'General';

    protected static ?int $navigationSort = 4;

    public static function getNavigationGroup(): ?string
    {
        return 'General';
    }

    public static function getNavigationSort(): ?int
    {
        return 4;
    }

    public static function form(Schema $schema): Schema
    {
        return GalleryForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return GalleryTable::configure($table);
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
            'index' => ListGallery::route('/'),
            'create' => CreateGallery::route('/create'),
            'view' => ViewGallery::route('/{record}'),
            'edit' => EditGallery::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('gallery.view_any') ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->can('gallery.create') ?? false;
    }

    public static function canEdit($record): bool
    {
        return auth()->user()?->can('gallery.update') ?? false;
    }

    public static function canDelete($record): bool
    {
        return auth()->user()?->can('gallery.delete') ?? false;
    }

    public static function canDeleteAny(): bool
    {
        return auth()->user()?->can('gallery.delete') ?? false;
    }
}
