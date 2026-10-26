<?php

namespace App\Filament\Resources\MasterShifts\Pages;

use App\Filament\Resources\MasterShifts\MasterShiftResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;
use App\Models\CabangKantor;
use App\Models\MasterShift;

class ListMasterShifts extends ListRecords
{
    protected static string $resource = MasterShiftResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        $tabs = [
            'semua' => Tab::make('Semua Shift')
                ->badge(MasterShift::count()),
        ];

        $cabangs = CabangKantor::orderBy('nama_cabang')->get();
        foreach ($cabangs as $cabang) {
            $tabs['cabang_' . $cabang->id] = Tab::make($cabang->nama_cabang)
                ->badge(MasterShift::where('cabang_id', $cabang->id)->count())
                ->modifyQueryUsing(fn (Builder $query) => $query->where('cabang_id', $cabang->id));
        }

        return $tabs;
    }
}
