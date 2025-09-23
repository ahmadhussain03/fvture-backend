<?php

namespace App\Filament\Resources\AppUsers\Schemas;

use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;

class AppUserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('App User Information')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('name')
                                    ->label('Full Name')
                                    ->required()
                                    ->maxLength(255),
                                
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
                    ])
                    ->columns(1)
                    ->columnSpanFull(),
            ]);
    }
}
