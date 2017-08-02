<?php 

namespace Cart\Validation\Rules;


use Cart\Models\Customer;
use Respect\Validation\Rules\AbstractRule;



class EmailAvailable extends AbstractRule
{
	
	public function validate($input)
	{
		return Customer::where('email', $input)->count() === 0;
	}
}



 ?>