<?php
namespace App\Filament\Resources\Seatmaps\Pages;

use App\Filament\Resources\Seatmaps\SeatmapResource;
use Filament\Resources\Pages\CreateRecord;
use Livewire\Attributes\On;

class CreateSeatmap extends CreateRecord
{
    protected static string $resource = SeatmapResource::class;

    public $clubTables = [];

    public function mount(): void
    {
        parent::mount();
        $this->clubTables = \App\Models\ClubTable::all();
    }

    protected function getHeaderActions(): array
    {
        return [];
    }

    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
        // Extract and decode table data from hidden field
        $tablesJson = $data['seatmap_tables_json'] ?? '[]';
        $tables = json_decode($tablesJson, true) ?: [];
        
        unset($data['seatmap_tables_json']);

        return \DB::transaction(function () use ($data, $tables) {
            $seatmap = $this->getModel()::create($data);
            foreach ($tables as $table) {
                $seatmap->seatmapTables()->create([
                    'club_table_id' => $table['club_table_id'] ?? null,
                    'pos_x'         => $table['x'] ?? 0,
                    'pos_y'         => $table['y'] ?? 0,
                    'seat_width'    => $table['width'] ?? 0,
                    'seat_height'   => $table['height'] ?? 0,
                    'seat_number'   => $table['number'] ?? null,
                ]);
            }
            return $seatmap;
        });
    }
}
