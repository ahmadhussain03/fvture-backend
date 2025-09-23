<?php

namespace App\Filament\Resources\AppUsers;

use App\Filament\Resources\AppUsers\Pages\CreateAppUser;
use App\Filament\Resources\AppUsers\Pages\EditAppUser;
use App\Filament\Resources\AppUsers\Pages\ListAppUsers;
use App\Filament\Resources\AppUsers\Pages\ViewAppUser;
use App\Filament\Resources\AppUsers\Schemas\AppUserForm;
use App\Filament\Resources\AppUsers\Tables\AppUsersTable;
use App\Models\User;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class AppUserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUser;

    protected static ?string $navigationLabel = 'App Users';

    public static function form(Schema $schema): Schema
    {
        return AppUserForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AppUsersTable::configure($table);
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
            'index' => ListAppUsers::route('/'),
            'create' => CreateAppUser::route('/create'),
            'view' => ViewAppUser::route('/{record}'),
            'edit' => EditAppUser::route('/{record}/edit'),
        ];
    }

    public static function getNavigationGroup(): ?string
    {
        return 'User Management';
    }

    public static function getNavigationSort(): ?int
    {
        return 2;
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()
            ->where('user_type', 'app');
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('app_user.view_any') ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->can('app_user.create') ?? false;
    }

    public static function canEdit($record): bool
    {
        return auth()->user()?->can('app_user.update') ?? false;
    }

    public static function canDelete($record): bool
    {
        return auth()->user()?->can('app_user.delete') ?? false;
    }
}
