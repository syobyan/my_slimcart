<?php

namespace Cart\Models;

use Illuminate\Database\Eloquent\Model;

class Admin extends Model{

	protected $table = 'admin';
	protected $fillable = [
		'name',
		'password',
	];

	public function setPassword($password)
	{
		$this->update([
			'password' => password_hash($password, PASSWORD_DEFAULT)
		]);
	}

}
