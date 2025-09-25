<?php

namespace App\Filament\Resources\Events\Schemas;

use App\Models\Artist;
use App\Rules\NoOverlappingEvents;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class EventForm
{
    public static function configure(Schema $schema, $excludeEventId = null): Schema
    {
        return $schema
            ->components([
                Tabs::make('Event Details')
                    ->tabs([
                        Tabs\Tab::make('Basic Information')
                            ->icon('heroicon-o-information-circle')
                            ->schema([
                                Section::make('Event Details')
                                    ->schema([
                                        Grid::make(2)
                                            ->schema([
                                                TextInput::make('name')
                                                    ->label('Event Name')
                                                    ->required()
                                                    ->maxLength(255)
                                                    ->live(onBlur: true)
                                                    ->afterStateUpdated(fn (string $operation, $state, callable $set) => 
                                                        $operation === 'create' ? $set('slug', Str::slug($state)) : null
                                                    ),
                                                
                                                DateTimePicker::make('from_date')
                                                    ->label('Event Start Date & Time')
                                                    ->required()
                                                    ->native(false)
                                                    ->displayFormat('d/m/Y H:i')
                                                    ->seconds(false)
                                                    ->minutesStep(15)
                                                    ->rules([
                                                        'after_or_equal:now',
                                                        function ($get) use ($excludeEventId) {
                                                            return function (string $attribute, $value, \Closure $fail) use ($get, $excludeEventId) {
                                                                $toDate = $get('to_date');
                                                                if ($value && $toDate) {
                                                                    $rule = new NoOverlappingEvents($value, $toDate, $excludeEventId);
                                                                    $rule->validate($attribute, $value, $fail);
                                                                }
                                                            };
                                                        },
                                                    ]),
                                                
                                                DateTimePicker::make('to_date')
                                                    ->label('Event End Date & Time')
                                                    ->required()
                                                    ->native(false)
                                                    ->displayFormat('d/m/Y H:i')
                                                    ->seconds(false)
                                                    ->minutesStep(15)
                                                    ->after('from_date')
                                                    ->rules([
                                                        'after:from_date',
                                                        function ($get) use ($excludeEventId) {
                                                            return function (string $attribute, $value, \Closure $fail) use ($get, $excludeEventId) {
                                                                $fromDate = $get('from_date');
                                                                if ($value && $fromDate) {
                                                                    $rule = new NoOverlappingEvents($fromDate, $value, $excludeEventId);
                                                                    $rule->validate($attribute, $value, $fail);
                                                                }
                                                            };
                                                        },
                                                    ]),
                                            ]),
                                        
                                        Textarea::make('description')
                                            ->label('Description')
                                            ->rows(4)
                                            ->placeholder('Enter a detailed description of the event...')
                                            ->columnSpanFull(),
                                    ])
                                    ->columns(1)
                                    ->columnSpanFull(),
                            ]),
                        
                        Tabs\Tab::make('Artist Lineup')
                            ->icon('heroicon-o-microphone')
                            ->schema([
                                Section::make('Artist Lineup')
                                    ->description('Select artists performing at this event or create new ones')
                                    ->schema([
                                        Select::make('artists')
                                            ->label('Select Artists')
                                            ->relationship('artists', 'name')
                                            ->options(Artist::orderBy('name')->pluck('name', 'id'))
                                            ->multiple()
                                            ->searchable()
                                            ->preload()
                                            ->createOptionForm([
                                                TextInput::make('name')
                                                    ->label('Artist Name')
                                                    ->required()
                                                    ->maxLength(255),
                                                
                                                FileUpload::make('image')
                                                    ->label('Artist Image')
                                                    ->image()
                                                    ->disk('s3')
                                                    ->directory('artists/images')
                                                    ->visibility('public')
                                                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/gif', 'image/webp'])
                                                    ->maxSize(5120)
                                                    ->getUploadedFileNameForStorageUsing(
                                                        fn (string $file): string => (string) str($file)->prepend(time() . '-')
                                                    )
                                                    ->moveFiles(),
                                                
                                                Textarea::make('description')
                                                    ->label('Description')
                                                    ->rows(3),
                                            ])
                                            ->createOptionUsing(function (array $data): int {
                                                $artist = Artist::create([
                                                    'name' => $data['name'],
                                                    'image' => $data['image'] ?? null,
                                                    'description' => $data['description'] ?? null,
                                                ]);
                                                
                                                return $artist->getKey();
                                            })
                                            ->columnSpanFull(),
                                    ])
                                    ->columns(1)
                                    ->columnSpanFull(),
                            ]),
                        
                        Tabs\Tab::make('Media & Additional Info')
                            ->icon('heroicon-o-photo')
                            ->schema([
                                Section::make('Media')
                                    ->schema([
                                        FileUpload::make('video')
                                            ->label('Event Video')
                                            ->disk('s3')
                                            ->directory('events/videos')
                                            ->visibility('public')
                                            ->acceptedFileTypes(['video/mp4', 'video/avi', 'video/mov', 'video/wmv', 'video/webm'])
                                            ->getUploadedFileNameForStorageUsing(
                                                fn (string $file): string => (string) str($file)->prepend(time() . '-')
                                            )
                                            ->moveFiles()
                                            ->helperText('Upload a promotional video or highlight reel for this event (Max 200MB)'),
                                        
                                        FileUpload::make('banner_image')
                                            ->label('Event Banner Image')
                                            ->image()
                                            ->disk('s3')
                                            ->directory('events/banners')
                                            ->visibility('public')
                                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/gif', 'image/webp'])
                                            ->maxSize(5120) // 5MB max
                                            ->getUploadedFileNameForStorageUsing(
                                                fn (string $file): string => (string) str($file)->prepend(time() . '-')
                                            )
                                            ->moveFiles()
                                            ->helperText('Upload a banner image for this event (Max 5MB)'),
                                    ])
                                    ->columns(1)
                                    ->columnSpanFull(),
                                
                                Section::make('Additional Information')
                                    ->schema([
                                        Textarea::make('other_information')
                                            ->label('Additional Information')
                                            ->rows(4)
                                            ->placeholder('Enter any additional information about the event (ticket prices, special requirements, etc.)...')
                                            ->columnSpanFull(),
                                    ])
                                    ->columns(1)
                                    ->columnSpanFull(),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
