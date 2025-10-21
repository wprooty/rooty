<?php

namespace Rooty\Http;

use Symfony\Component\HttpFoundation\Request;
use Rooty\Http\Response;

class Dispatcher
{
    public function __invoke(Request $request)
    {
        return new Response();
    }
}
