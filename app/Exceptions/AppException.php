<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Response;

class AppException extends Exception
{
    protected $cause = null;

    // Redefine the exception so message isn't optional
    public function __construct($message, $code = 400, $cause = null, Exception $previous = null)
    {
        // some code
        $this->cause = $cause;

        // make sure everything is assigned properly
        parent::__construct($message, $code, $previous);
    }

    // custom string representation of object
    public function __toString()
    {
        return __CLASS__ . ": [{$this->code}]: {$this->message} {$this->cause}\n";
    }

    public function getCause()
    {
        return $this->cause;
    }

    public static function inst($message,
                                $code = 500,
                                $cause = null,
                                Exception $previous = null)
    {
        return new self($message, $code, $cause, $previous);
    }

    public static function bad($message,
                               $cause = null,
                               $code = 400,
                               Exception $previous = null)
    {
        return new self($message, $code, $cause, $previous);
    }

    public static function internal($message,
                                    $cause = null,
                                    $code = 500,
                                    Exception $previous = null)
    {
        return new self($message, $code, $cause, $previous);
    }

    public static function unprocessed($message,
                                       $cause = null,
                                       $code = Response::HTTP_UNPROCESSABLE_ENTITY,
                                       Exception $previous = null)
    {
        return new self($message, $code, $cause, $previous);
    }

    public static function flash($code = 500,
                                 $message,
                                 $cause = null,
                                 Exception $previous = null)
    {
        return new self($message, $code, $cause, $previous);
    }
}
