<?php

namespace App\Filament\Resources\Gallery\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class GalleryTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('file_path')
                    ->label('Preview')
                    ->disk('s3')
                    ->visibility('public')
                    ->square()
                    ->size(60)
                    ->defaultImageUrl('/images/placeholder-video.png')
                    ->getStateUsing(function ($record) {
                        return $record->isImage() ? $record->file_path : null;
                    }),
                
                TextColumn::make('title')
                    ->label('Title')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->limit(30),
                
                TextColumn::make('type')
                    ->label('Type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'image' => 'success',
                        'video' => 'info',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => ucfirst($state)),
                
                TextColumn::make('event.name')
                    ->label('Event')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('warning')
                    ->placeholder('No event attached'),
                
                TextColumn::make('formatted_file_size')
                    ->label('File Size')
                    ->sortable()
                    ->toggleable(),
                
                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->label('File Type')
                    ->options([
                        'image' => 'Image',
                        'video' => 'Video',
                    ]),
                
                SelectFilter::make('event_id')
                    ->label('Event')
                    ->relationship('event', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
