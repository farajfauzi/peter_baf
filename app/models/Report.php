<?php

class Report extends \Eloquent {

	protected $table = 'header_laporan';
	
	public $timestamps = false;
	public static $rules = [
		// 'title' => 'required'
	];

	// Don't forget to fill this array
	protected $fillable = [];

	public function kandang()
    {
        return $this->hasOne('Kandang', 'id', 'id_kandang');
    }

    public function petugas_pj()
    {
    	return $this->hasOne('Petugas', 'id', 'id_petugas_pj');
    }

    public function strain()
    {
    	return $this->hasOne('Strain', 'id', 'id_perusahaan_strain');
    }
    public function laporan_sop()
    {
        return $this->hasMany('SopLaporan', 'id_header_laporan' , 'id');
    }
     public function petugas_laporan()
    {
        return $this->hasMany('PetugasLaporan', 'id_header_laporan' , 'id');
    }
    public function report_details()
    {
        return $this->hasMany('ReportDetail', 'id_header_laporan', 'id');
    }

    public static function jumlahPakanSekarang($id_header_laporan)
    {
        return DB::table('log_pakan')
                    ->join('pakan', 'log_pakan.id_pakan', '=', 'pakan.id')
                    ->join('header_laporan', 'log_pakan.id_header_laporan', '=', 'header_laporan.id')
                    ->select('log_pakan.id_pakan', 'pakan.nama', 'pakan.jenis', DB::raw("(
                        COALESCE((SELECT sum(jumlah) as keluar FROM `log_pakan` as b where status = 'Masuk'  and id_pakan = log_pakan.id_pakan and id_header_laporan = log_pakan.id_header_laporan group by id_pakan), 0)
                        -
                        COALESCE((SELECT sum(jumlah) as keluar FROM `log_pakan` as b where status = 'keluar' and id_pakan = log_pakan.id_pakan and id_header_laporan = log_pakan.id_header_laporan group by id_pakan), 0)
                     ) as jumlah"))
                    ->where('header_laporan.id', $id_header_laporan)
                    ->groupBy('log_pakan.id_pakan')
                    ->get();
    }

    public function log_pakan()
    {
        return $this->hasMany('PakanLog', 'id_header_laporan', 'id');
    }

    public static function getBobotPerMinggu($id_header_laporan, $tanggal_awal, $tanggal_akhir)
    {
        // SELECT bobot FROM detail_laporan WHERE id_header_laporan = 1 AND tanggal BETWEEN tanggal AND tanggal + INTERVAL 6 DAY ORDER BY `tanggal` DESC LIMIT 1
        return DB::table('detail_laporan')
                    ->select('*')
                    ->where('id_header_laporan', $id_header_laporan)
                    ->whereBetween('tanggal', array($tanggal_awal, $tanggal_akhir))
                    ->orderBy('tanggal', 'desc')
                    ->first();
    }

    public static function getMortalitasPerMinggu($id_header_laporan, $tanggal_awal, $tanggal_akhir)
    {
        // SELECT bobot FROM detail_laporan WHERE id_header_laporan = 1 AND tanggal BETWEEN tanggal AND tanggal + INTERVAL 6 DAY ORDER BY `tanggal` DESC LIMIT 1
        return DB::table('detail_laporan')
                    ->where('id_header_laporan', $id_header_laporan)
                    ->whereBetween('tanggal', array($tanggal_awal, $tanggal_akhir))
                    ->orderBy('tanggal', 'desc')
                    ->sum('mortalitas');
    }

    public static function getMorbilitasPerMinggu($id_header_laporan, $tanggal_awal, $tanggal_akhir)
    {
        // SELECT bobot FROM detail_laporan WHERE id_header_laporan = 1 AND tanggal BETWEEN tanggal AND tanggal + INTERVAL 6 DAY ORDER BY `tanggal` DESC LIMIT 1
        return DB::table('detail_laporan')
                    ->where('id_header_laporan', $id_header_laporan)
                    ->whereBetween('tanggal', array($tanggal_awal, $tanggal_akhir))
                    ->orderBy('tanggal', 'desc')
                    ->sum('morbilitas');
    }
}