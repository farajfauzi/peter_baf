<?php

class Kandang extends \Eloquent {

	protected $table = 'kandang';
	
	public $timestamps = false;

	// Add your validation rules here
	public static $rules = [
		// 'title' => 'required'
	];

	// Don't forget to fill this array
	protected $fillable = [];

}