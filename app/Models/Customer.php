<?php

namespace Cart\Models;

use Cart\Models\Order;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model{
	
	protected $table = 'customers';

	protected $fillable = [
		'email',
		'name',
		'phone',
		'password',
	];
	
	public function orders(){
		return $this->hasMany(Order::class);
	}

	public function setPassword($password)
	{
		$this->update([
			'password' => password_hash($password, PASSWORD_DEFAULT)
		]);
	}
}
