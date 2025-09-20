<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;
use Spatie\Permission\Models\Role;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('User Information')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('name')
                                    ->label('Full Name')
                                    ->required()
                                    ->maxLength(255)
                                    ->disabled(fn ($record) => $record && $record->name === 'Super Admin'),
                                
                                TextInput::make('email')
                                    ->label('Email Address')
                                    ->email()
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->maxLength(255),
                            ]),
                        
                        Grid::make(2)
                            ->schema([
                                TextInput::make('password')
                                    ->label('Password')
                                    ->password()
                                    ->required(fn (string $context): bool => $context === 'create')
                                    ->minLength(8)
                                    ->dehydrated(fn ($state) => filled($state))
                                    ->dehydrateStateUsing(fn ($state) => bcrypt($state)),
                                
                                TextInput::make('password_confirmation')
                                    ->label('Confirm Password')
                                    ->password()
                                    ->required(fn (string $context): bool => $context === 'create')
                                    ->minLength(8)
                                    ->same('password')
                                    ->dehydrated(false),
                            ]),
                        
                        Select::make('role_id')
                            ->label('Assign Role')
                            ->options(Role::all()->pluck('name', 'id'))
                            ->searchable()
                            ->preload()
                            ->required()
                            ->helperText('Select a single role for this user')
                            ->dehydrated(false)
                            ->disabled(fn ($record) => $record && $record->name === 'Super Admin')
                            ->afterStateUpdated(function ($state, $set, $get, $livewire) {
                                if ($state) {
                                    $livewire->data['roles'] = [$state];
                                }
                            }),
                    ])
                    ->columns(1)
                    ->columnSpanFull(),
            ]);
    }

}