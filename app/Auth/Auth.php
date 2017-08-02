<?php
namespace Cart\Auth;

use Cart\Models\Customer;

class Auth
{

	public function user()
	{
		if(isset($_SESSION['user'])){
			return Customer::find($_SESSION['user']);
		}
		 
	}

	public function check()
	{
		return isset($_SESSION['user']);
	}

	

	public function attempt($email, $password)
	{
		$user = Customer::where('email', $email)->first();

		if(!$user){
			return false;
		}

		if(password_verify($password, $user->password)){
		//if($password ==  $user->password){
			$_SESSION['user'] = $user->id;
			return true;
		}

		return false;
	}

	public function logout()
	{
		unset($_SESSION['user']);

	}
}

 ?>