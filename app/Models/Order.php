<?php

namespace Cart\Models;

use Cart\Models\Address;
use Cart\Models\Customer;
use Cart\Models\Product;
use Cart\Models\Payment;
use Illuminate\Database\Eloquent\Model;

class Order extends Model{

	protected $fillable = [
		'hash',
		'number',
		'total',
		'paid',
		'status',
		'address_id',
		'customer_id', //new add
	];

	public function address(){
		return $this->belongsTo(Address::class);
	}


	public function customer(){
		return $this->belongsTo(Customer::class);
	}

	public function products(){
		return $this->belongsToMany(Product::class, 'orders_products')->withPivot('quantity');
	}

	public function payment(){
		return $this->hasOne(Payment::class);
	}
}
