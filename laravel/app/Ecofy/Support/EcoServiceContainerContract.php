<?php
namespace App\Ecofy\Service;

interface AuthServiceContract
{
    /**
     * Registers a service instance
     */
    public function register($name, $instance);

    /**
     * Gets service instance by name
     */
    public function get($name);
}
