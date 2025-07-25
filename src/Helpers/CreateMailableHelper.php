<?php

namespace Maksde\FilamentEmailTemplates\Helpers;

use Exception;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Maksde\FilamentEmailTemplates\Contracts\CreateMailableInterface;

class CreateMailableHelper implements CreateMailableInterface
{
    public const STUB_PATH = __DIR__.'/../Stubs/MailableTemplate.stub';

    public function createMailable($record)
    {
        try {
            $className = Str::studly($record->key);

            $this->prepareDirectory(config('filament-email-templates.mailable_directory'));

            $filePath = app_path(config('filament-email-templates.mailable_directory').sprintf('/%s.php', $className));

            if (file_exists($filePath)) {
                return $this->response('Class already exists', 'heroicon-o-exclamation-circle', 'danger', $filePath);
            }

            $classContent = str_replace(['{{className}}', '{{template-key}}'], [$className, $record->key], File::get(self::STUB_PATH));

            File::put($filePath, $classContent);

            return $this->response('Class generated successfully', 'heroicon-o-check-circle', 'success', $filePath);

        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->response('Error: '.$exception->getMessage(), 'heroicon-o-exclamation-circle', 'danger', '');
        }
    }

    private function prepareDirectory($folder)
    {
        $path = app_path($folder);
        File::ensureDirectoryExists($path, 0755);
    }

    private function response($title, $icon, $icon_color, $body)
    {
        return (object) [
            'title' => $title,
            'icon' => $icon,
            'icon_color' => $icon_color,
            'body' => $body,
        ];
    }
}
