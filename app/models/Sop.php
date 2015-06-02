<?php

class Sop extends \Eloquent {

	protected $table = 'sop';
	
	public $timestamps = false;

	// Add your validation rules here
	public static $rules = [
		// 'title' => 'required'
	];

	protected $hidden = ['hapus'];
	// Don't forget to fill this array
	protected $fillable = [];

}