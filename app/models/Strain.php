<?php

class Strain extends \Eloquent {

	protected $table = 'strain';
	
	public $timestamps = false;

	// Add your validation rules here
	public static $rules = [
		// 'title' => 'required'
	];

	// Don't forget to fill this array
	protected $fillable = [];

	public function perusahaan()
	{
    	return $this->hasOne('Perusahaan', 'id', 'id_perusahaan');
    }
}