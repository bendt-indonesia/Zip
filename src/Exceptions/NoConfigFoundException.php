<?php

namespace Bendt\Option\Exceptions;

class NoConfigFoundException extends \Exception
{
    /**
     * All of the guards that were checked.
     *
     * @var array
     */
    protected $guards;

    /**
     * Create a new authentication exception.
     *
     * @param  string  $key
     * @return void
     */
    public function __construct($key = null)
    {
        if($key) {
            $message = "No CMSConfig is found using key \"{$key}\".";
        }
        else {
            $message = "Trying to get CMSConfig with wrong key.";
        }

        parent::__construct($message);
    }

    /**
     * Get the guards that were checked.
     *
     * @return array
     */
    public function guards()
    {
        return $this->guards;
    }
}
