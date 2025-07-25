<?php

namespace Maksde\FilamentEmailTemplates\Contracts;

interface CreateMailableInterface
{
    public function createMailable($record);
}
