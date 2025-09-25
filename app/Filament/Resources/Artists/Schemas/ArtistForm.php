<?php

namespace App\Filament\Resources\Artists\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ArtistForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Artist Information')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('name')
                                    ->label('Artist Name')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('Enter artist name'),
                                
                                FileUpload::make('image')
                                    ->label('Artist Image')
                                    ->image()
                                    ->disk('s3')
                                    ->directory('artists/images')
                                    ->visibility('public')
                                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/gif', 'image/webp'])
                                    ->maxSize(5120) // 5MB max
                                    ->getUploadedFileNameForStorageUsing(
                                        fn (string $file): string => (string) str($file)->prepend(time() . '-')
                                    )
                                    ->moveFiles()
                                    ->helperText('Upload a profile image for the artist (Max 5MB)'),
                            ]),
                        
                        RichEditor::make('description')
                            ->label('Description')
                            ->placeholder('Enter a detailed description of the artist...')
                            ->columnSpanFull(),
                    ])
                    ->columns(1)
                    ->columnSpanFull(),
            ]);
    }
}
