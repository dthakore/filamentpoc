<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Archilex\AdvancedTables\Components\PresetView;
use Archilex\AdvancedTables\AdvancedTables;
class ListUsers extends ListRecords
{
    use AdvancedTables;

    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
    public function getPresetViews(): array
    {
        return [
            'Eazybot Email Users' => PresetView::make()
                ->modifyQueryUsing(fn ($query) => $query->where('email','like', '%@eazybot.com%')),
            'Active Users' => PresetView::make()
                ->modifyQueryUsing(fn ($query) => $query->where('is_active', true)),
        ];
    }
}
