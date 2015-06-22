<?php

class ReportDetail extends \Eloquent {

	protected $table = 'detail_laporan';
	
	public $timestamps = false;
	public static $rules = [
		// 'title' => 'required'
	];

	// Don't forget to fill this array
	protected $fillable = [
		'id_header_laporan',
		'tanggal',
		'mortalitas',
		'morbilitas',
		'populasi_akhir',
		'bobot'
	];

}