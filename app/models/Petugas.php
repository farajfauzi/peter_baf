<?php

class Petugas extends \Eloquent {

	protected $table = 'petugas';
	
	public $timestamps = false;

	// Add your validation rules here
	public static $rules = [
		// 'title' => 'required'
	];

	// Don't forget to fill this array
	protected $fillable = [];

	protected $hidden = ['password'];
}