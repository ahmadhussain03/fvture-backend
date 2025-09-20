<?php

namespace App\Filament\Resources\Events\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class EventsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Event Name')
                    ->searchable()
                    ->sortable(),
                
                TextColumn::make('event_date_time')
                    ->label('Date & Time')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                
                TextColumn::make('djs.name')
                    ->label('DJ Lineup')
                    ->formatStateUsing(function ($record): string {
                        return $record->djs->pluck('name')->join(', ');
                    })
                    ->limit(50)
                    ->badge()
                    ->color('info'),
                
                TextColumn::make('description')
                    ->label('Description')
                    ->limit(50)
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();
                        return strlen($state) > 50 ? $state : null;
                    }),
                
                TextColumn::make('media')
                    ->label('Media')
                    ->formatStateUsing(function ($record): string {
                        $hasVideo = !empty($record->video);
                        $hasBanner = !empty($record->banner_image);
                        
                        if ($hasVideo && $hasBanner) {
                            return 'Video + Banner';
                        } elseif ($hasVideo) {
                            return 'Video';
                        } elseif ($hasBanner) {
                            return 'Banner';
                        }
                        
                        return 'No Media';
                    })
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Video + Banner' => 'success',
                        'Video', 'Banner' => 'warning',
                        'No Media' => 'gray',
                    }),
                
                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Filter::make('upcoming')
                    ->label('Upcoming Events')
                    ->query(fn (Builder $query): Builder => $query->where('event_date_time', '>=', now())),
                
                Filter::make('past')
                    ->label('Past Events')
                    ->query(fn (Builder $query): Builder => $query->where('event_date_time', '<', now())),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('event_date_time', 'desc');
    }
}
