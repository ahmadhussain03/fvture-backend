<?php

namespace App\Filament\Resources\DJS\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class DJForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('DJ Information')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('name')
                                    ->label('DJ Name')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('Enter DJ name'),
                                
                                FileUpload::make('image')
                                    ->label('DJ Image')
                                    ->image()
                                    ->disk('s3')
                                    ->directory('djs/images')
                                    ->visibility('public')
                                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/gif', 'image/webp'])
                                    ->maxSize(5120) // 5MB max
                                    ->getUploadedFileNameForStorageUsing(
                                        fn (string $file): string => (string) str($file)->prepend(time() . '-')
                                    )
                                    ->moveFiles()
                                    ->helperText('Upload a profile image for the DJ (Max 5MB)'),
                            ]),
                        
                        RichEditor::make('description')
                            ->label('Description')
                            ->placeholder('Enter a detailed description of the DJ...')
                            ->columnSpanFull(),
                    ])
                    ->columns(1)
                    ->columnSpanFull(),
            ]);
    }
}
