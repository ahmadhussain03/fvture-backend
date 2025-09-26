<?php

namespace App\Filament\Resources\Seatmaps\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\ViewField;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;

class SeatmapForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Seatmap Details')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                    \Filament\Forms\Components\Hidden::make('club_tables_json')
                                        ->default(fn() => \App\Models\ClubTable::all()->toJson()),
                                TextInput::make('name')
                                    ->label('Seatmap Name')
                                    ->required(),
                                TextInput::make('map_width')
                                    ->label('Map Width')
                                    ->required()
                                    ->numeric(),
                                TextInput::make('map_height')
                                    ->label('Map Height')
                                    ->required()
                                    ->numeric()
                                    ->default(450),
                                \Filament\Forms\Components\FileUpload::make('background_url')
                                    ->label('Background Image')
                                    ->image()
                                    ->disk('s3')
                                    ->directory('seatmaps/backgrounds')
                                    ->visibility('public')
                                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/gif', 'image/webp'])
                                    ->maxSize(5120) // 5MB max
                                    ->getUploadedFileNameForStorageUsing(
                                        fn (string $file): string => (string) str($file)->prepend(time() . '-')
                                    )
                                    ->moveFiles()
                                    ->helperText('Upload a background image for this seatmap (Max 5MB)')
                                    ->required()
                                    ->columnSpanFull(),
                                ViewField::make('seatmap_preview')
                                    ->view('livewire.seatmap-form-container')
                                    ->label('Seatmap Preview')
                                    ->columnSpanFull(),
                                Grid::make(2)
                                    ->schema([
                                        \Filament\Forms\Components\Select::make('club_table_id')
                                            ->label('Select Club Table')
                                            ->options(fn() => \App\Models\ClubTable::all()->pluck('name', 'id'))
                                            ->searchable(),
                                        \Filament\Forms\Components\TextInput::make('number_of_tables')
                                            ->label('Number of Tables')
                                            ->numeric()
                                            ->minValue(1)
                                            ->default(1),
                                    ])
                                    ->columnSpanFull(),
                                ViewField::make('place_table_button')
                                    ->view('livewire.place-table-button')
                                    ->label('')
                                    ->columnSpanFull(),
                            ]),
                    ])
                    ->columns(1)
                    ->columnSpanFull(),
            ]);
    }
}
