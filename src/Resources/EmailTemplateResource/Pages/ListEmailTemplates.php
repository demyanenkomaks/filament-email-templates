<?php

namespace Maksde\FilamentEmailTemplates\Resources\EmailTemplateResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Maksde\FilamentEmailTemplates\Resources\EmailTemplateResource;

class ListEmailTemplates extends ListRecords
{
    protected static string $resource = EmailTemplateResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
