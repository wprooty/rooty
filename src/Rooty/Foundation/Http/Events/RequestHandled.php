<?php

namespace Rooty\Foundation\Http\Events;

class RequestHandled
{
    /**
     * The request instance.
     *
     * @var \Symfony\Component\HttpFoundation\Request
     */
    public $request;

    /**
     * The response instance.
     *
     * @var \Illuminate\Http\Response
     */
    public $response;

    /**
     * Create a new event instance.
     *
     * @param  \Symfony\Component\HttpFoundation\Request  $request
     * @param  \Symfony\Component\HttpFoundation\Response  $response
     */
    public function __construct($request, $response)
    {
        $this->request = $request;
        $this->response = $response;
    }
}
