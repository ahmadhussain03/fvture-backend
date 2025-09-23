<?php

namespace App\Filament\Resources\Gallery\Schemas;

use App\Models\Event;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class GalleryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Gallery Item Information')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('title')
                                    ->label('Title')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('Enter gallery item title'),
                                
                                Select::make('event_id')
                                    ->label('Attach to Event')
                                    ->relationship('event', 'name')
                                    ->options(Event::orderBy('name')->pluck('name', 'id'))
                                    ->searchable()
                                    ->preload()
                                    ->placeholder('Select an event (optional)'),
                            ]),
                        
                        Textarea::make('description')
                            ->label('Description')
                            ->maxLength(1000)
                            ->placeholder('Enter description for the gallery item')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->columns(1)
                    ->columnSpanFull(),
                
                Section::make('File Upload')
                    ->schema([
                        FileUpload::make('file_path')
                            ->label('Upload File')
                            ->disk('s3')
                            ->directory('gallery')
                            ->visibility('public')
                            ->acceptedFileTypes([
                                'image/jpeg',
                                'image/png',
                                'image/gif',
                                'image/webp',
                                'video/mp4',
                                'video/avi',
                                'video/mov',
                                'video/wmv',
                                'video/webm',
                            ])
                            ->maxSize(102400) // 100MB max
                            ->getUploadedFileNameForStorageUsing(
                                fn (string $file): string => (string) str($file)->prepend(time() . '-')
                            )
                            ->moveFiles()
                            ->helperText('Upload an image or video file (Max 100MB)')
                            ->required()
                            ->live()
                            ->afterStateUpdated(function ($state, $set, $get) {
                                if ($state) {
                                    // Determine file type based on MIME type
                                    $mimeType = mime_content_type($state);
                                    if (str_starts_with($mimeType, 'image/')) {
                                        $set('type', 'image');
                                    } elseif (str_starts_with($mimeType, 'video/')) {
                                        $set('type', 'video');
                                    }
                                    
                                    // Set file size
                                    $fileSize = filesize($state);
                                    $set('file_size', $fileSize);
                                    $set('mime_type', $mimeType);
                                }
                            })
                            ->columnSpanFull(),
                        
                        // Hidden field for type (auto-detected)
                        TextInput::make('type')
                            ->hidden()
                            ->dehydrated(),
                    ])
                    ->columns(1)
                    ->columnSpanFull(),
            ]);
    }
}
