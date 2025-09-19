<?php

namespace App\Filament\Resources\Blogs\Schemas;

use App\Models\Blog;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class BlogForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Blog Content')
                    ->schema([
                        TextInput::make('title')
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (string $operation, $state, callable $set) => 
                                $operation === 'create' ? $set('slug', Str::slug($state)) : null
                            ),
                        TextInput::make('slug')
                            ->required()
                            ->unique(Blog::class, 'slug', ignoreRecord: true)
                            ->rules(['alpha_dash']),
                        RichEditor::make('content')
                            ->required()
                            ->columnSpanFull(),
                        Textarea::make('excerpt')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
                
                Section::make('Media & Settings')
                    ->schema([
                        FileUpload::make('featured_image')
                            ->image()
                            ->directory('blog-images')
                            ->visibility('public'),
                        Grid::make(2)
                            ->schema([
                                Toggle::make('is_published')
                                    ->default(false),
                                DateTimePicker::make('published_at')
                                    ->visible(fn (callable $get) => $get('is_published'))
                                    ->required(fn (callable $get) => $get('is_published')),
                            ]),
                    ])
                    ->columns(2),
                
                Section::make('Author')
                    ->schema([
                        Select::make('user_id')
                            ->relationship('user', 'name')
                            ->required()
                            ->default(auth()->id())
                            ->searchable()
                            ->preload(),
                    ])
                    ->collapsible(),
            ]);
    }
}
