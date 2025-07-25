<?php

namespace Maksde\FilamentEmailTemplates\Contracts;

interface TokenReplacementInterface
{
    public function replaceTokens(string $content, $models);
}
