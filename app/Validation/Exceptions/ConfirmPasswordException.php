<?php

namespace Cart\Validation\Exceptions;

use Respect\Validation\Exceptions\ValidationException;

class ConfirmPasswordException extends ValidationException {

	public static $defaultTemplates = [
        self::MODE_DEFAULT => [
            self::STANDARD => 'password did not match' // or any message you want
        ]
    ];
    
}


?>