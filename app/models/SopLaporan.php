<?php

class SopLaporan extends \Eloquent {

	protected $table = 'laporan_sop';
	
	public $timestamps = false;

	// Add your validation rules here
	public static $rules = [
		// 'title' => 'required'
	];

	protected $hidden = ['hapus'];
	// Don't forget to fill this array
	protected $fillable = [];

	public function nilai()
	{
		return $this->hasOne('Sop', 'id', 'id_sop');
	}
}