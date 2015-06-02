<?php

class Note extends \Eloquent {

	protected $table = 'note';
	
	public $timestamps = false;

	// Add your validation rules here
	public static $rules = [
		// 'title' => 'required'
	];

	// Don't forget to fill this array
	protected $fillable = [];

}