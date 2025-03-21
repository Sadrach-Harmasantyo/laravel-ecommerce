<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewOrder extends ViewRecord
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),

            Actions\Action::make('checkTracking')
                ->label('Check Tracking Status')
                ->icon('heroicon-o-map')
                ->color('success')
                ->url(fn () => $this->record->tracking_number ? "https://cekresi.com/?noresi={$this->record->tracking_number}" : null, true)
                ->visible(fn () => !empty($this->record->tracking_number) && $this->record->status === 'shipped'),
        ];
    }  
}
