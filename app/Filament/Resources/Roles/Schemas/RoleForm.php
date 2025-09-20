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
                
                Section::make('Permissions')
                    ->schema([
                        CheckboxList::make('permissions')
                            ->label('Select Permissions')
                            ->relationship('permissions', 'name')
                            ->options(self::getFormattedPermissions())
                            ->columns(2)
                            ->searchable()
                            ->bulkToggleable(),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    private static function getFormattedPermissions(): array
    {
        $permissions = Permission::all()->pluck('name', 'id');
        $formatted = [];

        foreach ($permissions as $id => $name) {
            $parts = explode('.', $name);
            $group = $parts[0] ?? 'Other';
            $action = $parts[1] ?? $name;
            
            $formatted[$id] = ucfirst($action) . ' ' . ucfirst($group);
        }

        return $formatted;
    }
}