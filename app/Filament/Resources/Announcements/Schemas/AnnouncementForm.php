<?php

namespace App\Filament\Resources\Announcements\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class AnnouncementForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Announcement Information')
                    ->schema([
                        TextInput::make('title')
                            ->label('Title')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Enter announcement title'),
                        
                        RichEditor::make('description')
                            ->label('Description')
                            ->required()
                            ->placeholder('Enter announcement description')
                            ->toolbarButtons([
                                'bold',
                                'italic',
                                'underline',
                                'strike',
                                'link',
                                'bulletList',
                                'orderedList',
                            ]),
                        
                        FileUpload::make('image')
                            ->label('Image')
                            ->image()
                            ->disk('s3')
                            ->directory('announcements/images')
                            ->visibility('public')
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/gif', 'image/webp'])
                            ->maxSize(5120) // 5MB max
                            ->getUploadedFileNameForStorageUsing(
                                fn (string $file): string => (string) str($file)->prepend(time() . '-')
                            )
                            ->moveFiles()
                            ->helperText('Upload an image for the announcement (Max 5MB)')
                            ->columnSpanFull(),
                    ])
                    ->columns(1)
                    ->columnSpanFull(),
            ]);
    }
}
