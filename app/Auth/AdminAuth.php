<?php
namespace Cart\Auth;

use Cart\Models\Admin;

class AdminAuth
{

	public function user()
	{
		if(isset($_SESSION['personnel'])){
			return Admin::find($_SESSION['personnel']);
		}
		 
	}

	public function check()
	{
		return isset($_SESSION['personnel']);
	}

	

	public function attempt($name, $password)
	{
		$personnel = Admin::where('name', $name)->first();

		if(!$personnel){
			return false;
		}

		if(password_verify($password, $personnel->password)){
		//if($password ==  $user->password){
			$_SESSION['personnel'] = $personnel->id;
			return true;
		}

		return false;
	}

	public function logout()
	{
		unset($_SESSION['personnel']);

	}
}

 ?>