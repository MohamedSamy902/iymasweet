<?php

namespace App\Filament\Resources\UiTexts\Pages;

use App\Filament\Resources\UiTexts\UiTextResource;
use App\Services\UiTextService;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ManageRecords;
use Filament\Support\Icons\Heroicon;

class ManageUiTexts extends ManageRecords
{
    protected static string $resource = UiTextResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
            Action::make('clearCache')
                ->label('مسح كاش النصوص')
                ->icon(Heroicon::OutlinedArrowPath)
                ->action(function () {
                    app(UiTextService::class)->forgetCache();
                    Notification::make()->title('تم مسح الكاش')->success()->send();
                }),
        ];
    }
}
