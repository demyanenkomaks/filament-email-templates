<?php

namespace Maksde\FilamentEmailTemplates\Resources\EmailTemplateResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Contracts\View\View;
use Maksde\FilamentEmailTemplates\Models\EmailTemplate;
use Maksde\FilamentEmailTemplates\Resources\EmailTemplateResource;

class EditEmailTemplate extends EditRecord
{
    protected static string $resource = EmailTemplateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make('Preview')
                ->modalContent(fn (EmailTemplate $record): View => view(
                    'md-filament-email-templates::forms.components.iframe',
                    ['record' => $record],
                ))->form(null),
            Actions\DeleteAction::make(),
        ];
    }
}
