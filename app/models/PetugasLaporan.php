<?php

class PetugasLaporan extends \Eloquent {

	protected $table = 'petugas_laporan';
	
	public $timestamps = false;

	// Add your validation rules here
	public static $rules = [
		// 'title' => 'required'
	];

	// Don't forget to fill this array
	public function petugas()
	{
		return $this->hasOne('Petugas', 'id', 'id_petugas');
	}
}