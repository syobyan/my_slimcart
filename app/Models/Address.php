<?php

namespace Cart\Models;

use Illuminate\Database\Eloquent\Model;

class Address extends Model{

	protected $fillable = [
		'address_one',
		'address_two',
		'city',
		'postal_code',
	];

}
