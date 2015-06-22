<?php

class PakanLog extends \Eloquent {

	protected $table = 'log_pakan';
	
	public $timestamps = false;

	// Add your validation rules here
	public static $rules = [
		// 'title' => 'required'
	];

	// Don't forget to fill this array
	protected $fillable = [
		'id_pakan',
		'id_header_laporan',
		'status',
		'jumlah'
	];

	public function pakan()
	{
		return $this->hasOne('Pakan', 'id', 'id_pakan');
	}

}