<?php

namespace App\Filament\Resources\Blogs\Schemas;

use App\Models\Blog;
use App\Models\Category;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
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
                            ->required(),
                    ])
                    ->columns(1)
                    ->columnSpanFull(),
                
                Section::make('Categories')
                    ->schema([
                        Select::make('categories')
                            ->label('Categories')
                            ->relationship('categories', 'name')
                            ->options(Category::active()->ordered()->pluck('name', 'id'))
                            ->multiple()
                            ->searchable()
                            ->preload()
                            ->createOptionForm([
                                TextInput::make('name')
                                    ->required()
                                    ->maxLength(255),
                                TextInput::make('slug')
                                    ->maxLength(255),
                            ])
                            ->createOptionUsing(function (array $data): int {
                                $category = Category::create([
                                    'name' => $data['name'],
                                    'slug' => $data['slug'] ?: Str::slug($data['name']),
                                ]);
                                
                                return $category->getKey();
                            }),
                    ])
                    ->columns(1)
                    ->columnSpanFull(),
                
                Section::make('Media & Settings')
                    ->schema([
                        FileUpload::make('banner_image')
                            ->label('Banner Image')
                            ->image()
                            ->disk('s3')
                            ->directory('blog-banners')
                            ->visibility('public')
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/gif', 'image/webp'])
                            ->maxSize(5120) // 5MB max
                            ->getUploadedFileNameForStorageUsing(
                                fn (string $file): string => (string) str($file)->prepend(time() . '-')
                            )
                            ->moveFiles()
                            ,
                        Grid::make(2)
                            ->schema([
                                Toggle::make('is_published')
                                    ->default(false),
                                DateTimePicker::make('published_at')
                                    ->visible(fn (callable $get) => $get('is_published'))
                                    ->required(fn (callable $get) => $get('is_published')),
                            ]),
                    ])
                    ->columns(1)
                    ->columnSpanFull(),
                
            ]);
    }
}
