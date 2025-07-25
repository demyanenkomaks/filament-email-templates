<?php

namespace Maksde\FilamentEmailTemplates\Traits;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Log;
use Maksde\FilamentEmailTemplates\Models\EmailTemplate;

trait BuildGenericEmail
{
    public $emailtemplate;

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $this->emailTemplate = EmailTemplate::findEmailByKey($this->template, App::currentLocale());

        if (! $this->emailTemplate) {
            Log::warning(sprintf('Email template %s was not found.', $this->emailtemplate));

            return $this;
        }

        if ($this->attachment ?? false) {
            $this->attach(
                $this->attachment->getPath(),
                [
                    'as' => $this->attachment->filename,
                    'mime' => $this->attachment->mime_type,
                ]
            );
        }

        if (is_array($this->emailTemplate->cc) && count($this->emailTemplate->cc)) {
            $this->cc($this->emailTemplate->cc);
        }

        if (is_array($this->emailTemplate->bcc) && count($this->emailTemplate->bcc)) {
            $this->bcc($this->emailTemplate->bcc);
        }

        return $this->from($this->emailTemplate->from['email'], $this->emailTemplate->from['name'])
            ->html(Blade::render($this->emailTemplate->content, $this->data))
            ->subject(Blade::render($this->emailTemplate->subject, $this->data));
    }
}
