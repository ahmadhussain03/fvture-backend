<?php

namespace App\Filament\Resources\ClubTables\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ClubTableForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Club Table Details')
                    ->tabs([
                        Tabs\Tab::make('Details')
                            ->schema([
                                TextInput::make('name')
                                    ->required(),
                                TextInput::make('base_price')
                                    ->required()
                                    ->numeric(),
                                TextInput::make('capacity')
                                    ->required()
                                    ->numeric(),
                            ]),
                        Tabs\Tab::make('Images')
                            ->schema([
                                FileUpload::make('image_url')
                                    ->label('Image')
                                    ->image()
                                    ->required(),
                                FileUpload::make('active_shape_url')
                                    ->label('Active Shape Image')
                                    ->image()
                                    ->required(),
                                FileUpload::make('shape_url')
                                    ->label('Shape Image')
                                    ->image()
                                    ->required(),
                            ]),
                    ]),
            ]);
    }
}
