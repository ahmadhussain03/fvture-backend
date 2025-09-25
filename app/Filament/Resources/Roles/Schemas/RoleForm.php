<?php

namespace App\Filament\Resources\Roles\Schemas;

use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\CheckboxList;
use Filament\Schemas\Schema;
use Spatie\Permission\Models\Permission;

class RoleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Role Information')
                    ->schema([
                        TextInput::make('name')
                            ->label('Role Name')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->disabled(fn ($record) => $record && $record->name === 'Super Admin'),
                    ])
                    ->columnSpanFull(),
                
                ...self::getPermissionSections(),
            ]);
    }

    private static function getPermissionSections(): array
    {
        $permissions = Permission::all()->groupBy(function ($permission) {
            $parts = explode('.', $permission->name);
            return $parts[0] ?? 'other';
        });

        $sections = [];
        $sectionConfig = [
            'admin_user' => 'Admin Users',
            'blog' => 'Blogs',
            'artist' => 'Artists',
            'event' => 'Events',
            'app_user' => 'App Users',
            'announcement' => 'Announcements',
            'gallery' => 'Gallery',
            'role' => 'Roles',
        ];

        foreach ($sectionConfig as $key => $label) {
            if ($permissions->has($key)) {
                $sections[] = Section::make($label)
                    ->schema([
                        CheckboxList::make('permissions')
                            ->label("Select {$label} Permissions")
                            ->relationship('permissions', 'name')
                            ->options(self::getFormattedPermissionsForGroup($permissions[$key]))
                            ->columns(2)
                            ->searchable()
                            ->bulkToggleable(),
                    ])
                    ->columnSpanFull()
                    ->collapsible();
            }
        }

        // Add any other permissions that don't fit the main groups
        $otherPermissions = $permissions->filter(function ($group, $key) use ($sectionConfig) {
            return !array_key_exists($key, $sectionConfig);
        });

        if ($otherPermissions->isNotEmpty()) {
            $allOtherPermissions = $otherPermissions->flatten();
            $sections[] = Section::make('Other Permissions')
                ->schema([
                    CheckboxList::make('permissions')
                        ->label('Select Other Permissions')
                        ->relationship('permissions', 'name')
                        ->options(self::getFormattedPermissionsForGroup($allOtherPermissions))
                        ->columns(2)
                        ->searchable()
                        ->bulkToggleable(),
                ])
                ->columnSpanFull()
                ->collapsible();
        }

        return $sections;
    }

    private static function getFormattedPermissionsForGroup($permissions): array
    {
        $formatted = [];

        foreach ($permissions as $permission) {
            $parts = explode('.', $permission->name);
            $action = $parts[1] ?? $permission->name;
            
            $formatted[$permission->id] = ucfirst(str_replace('_', ' ', $action));
        }

        return $formatted;
    }
}