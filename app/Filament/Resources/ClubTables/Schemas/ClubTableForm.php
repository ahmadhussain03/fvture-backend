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
                                    ->disk('s3')
                                    ->directory('club_tables/images')
                                    ->visibility('public')
                                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/gif', 'image/webp'])
                                    ->maxSize(5120)
                                    ->getUploadedFileNameForStorageUsing(fn (string $file) => (string) str($file)->prepend(time() . '-'))
                                    ->moveFiles()
                                    ->required(),
                                FileUpload::make('active_shape_url')
                                    ->label('Active Shape Image')
                                    ->image()
                                    ->disk('s3')
                                    ->directory('club_tables/active_shapes')
                                    ->visibility('public')
                                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/gif', 'image/webp'])
                                    ->maxSize(5120)
                                    ->getUploadedFileNameForStorageUsing(fn (string $file) => (string) str($file)->prepend(time() . '-'))
                                    ->moveFiles()
                                    ->required(),
                                FileUpload::make('shape_url')
                                    ->label('Shape Image')
                                    ->image()
                                    ->disk('s3')
                                    ->directory('club_tables/shapes')
                                    ->visibility('public')
                                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/gif', 'image/webp'])
                                    ->maxSize(5120)
                                    ->getUploadedFileNameForStorageUsing(fn (string $file) => (string) str($file)->prepend(time() . '-'))
                                    ->moveFiles()
                                    ->required(),
                            ]),
                    ]),
            ]);
    }
}
