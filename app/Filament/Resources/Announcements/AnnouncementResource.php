<?php

namespace App\Filament\Resources\Announcements;

use App\Filament\Resources\Announcements\Pages\CreateAnnouncement;
use App\Filament\Resources\Announcements\Pages\ListAnnouncements;
use App\Filament\Resources\Announcements\Pages\ViewAnnouncement;
use App\Filament\Resources\Announcements\Schemas\AnnouncementForm;
use App\Filament\Resources\Announcements\Tables\AnnouncementsTable;
use App\Models\Announcement;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class AnnouncementResource extends Resource
{
    protected static ?string $model = Announcement::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedSpeakerWave;

    protected static ?string $navigationLabel = 'Announcements';

    protected static ?string $modelLabel = 'Announcement';

    protected static ?string $pluralModelLabel = 'Announcements';

    protected static ?string $recordTitleAttribute = 'title';

    protected static string|UnitEnum|null $navigationGroup = 'General';

    protected static ?int $navigationSort = 3;

    public static function getNavigationGroup(): ?string
    {
        return 'General';
    }

    public static function getNavigationSort(): ?int
    {
        return 3;
    }

    public static function form(Schema $schema): Schema
    {
        return AnnouncementForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AnnouncementsTable::configure($table);
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
            'index' => ListAnnouncements::route('/'),
            'create' => CreateAnnouncement::route('/create'),
            'view' => ViewAnnouncement::route('/{record}'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('announcement.view_any') ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->can('announcement.create') ?? false;
    }

    public static function canEdit($record): bool
    {
        return false; // No edit functionality
    }

    public static function canDelete($record): bool
    {
        return auth()->user()?->can('announcement.delete') ?? false;
    }

    public static function canDeleteAny(): bool
    {
        return auth()->user()?->can('announcement.delete') ?? false;
    }
}
