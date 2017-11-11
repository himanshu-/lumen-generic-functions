<?php

namespace App;

use Laravel\Lumen\Application;

class Lumen extends Application
{
    /**
     * Register container bindings for the application.
     *
     * @return void
     */
    protected function registerHashBindings()
    {
        $this->singleton('hash', function () {
            return new TransitionalHasher(); // here's your custom hasher
        });
    }
}