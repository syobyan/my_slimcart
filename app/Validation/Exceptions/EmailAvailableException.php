<?php

namespace Cart\Validation\Exceptions;

use Respect\Validation\Exceptions\ValidationException;

/**
* 
*/
class EmailAvailableException extends ValidationException
{
	
	public static $defaultTemplates=[
		self::MODE_DEFAULT => [
			self::STANDARD => 'email is already taken',
		],
	];

}
 ?>