<?php

namespace Rooty\Contracts\Http;

interface Abort
{
    public function boot(): void;

    public function abort(?int $code, string $message = '', string $title = '', array $args = []);
}
