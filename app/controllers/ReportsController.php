<?php

class ReportsController extends \BaseController {

	/**
	 * Display a listing of reports
	 *
	 * @return Response
	 */
	public function index()
	{
		try {
	        if ($user = \JWTAuth::parseToken()->authenticate()) {
	        	
	        	$response = Report::with('kandang', 'petugas_pj', 'laporan_sop.nilai', 'report_details')
	        				->where('hapus', 1)
	        				->where('status', 0)
	        				->where('id_petugas_pj', $user->id)
	        				->get();

				return $this->response->array($response->toArray());
	        }
	    } catch (\Exception $e) {
	    	throw new \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException();
	    	
	    }
	}

	/**
	 * Store a newly created report in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		try {
	        if ($user = \JWTAuth::parseToken()->authenticate()) {
	        	
	        	$bobot 			= Input::get('bobot');
				$id 			= Input::get('id_header_laporan');
				$jumlah_pakan 	= Input::get('jumlah_pakan');
				$morbilitas 	= Input::get('morbilitas');
				$mortalitas 	= Input::get('mortalitas');
				$pakan 			= Input::get('pakan');
				$tanggal 		= \Carbon\Carbon::parse(\Input::get('tanggal'))->addDay()->format('Y/m/d');
				$selectedSop 	= Input::get('selectedSop');

	        	$response = Report::findOrFail($id);
				if ($response->id_petugas_pj != $user->id) {
					throw new \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException();
				}

				if ($response->status != 0) {
					throw new \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException();
				}

				// cek apa sudah ada detail laporan di hari itu
				$is_exists = ReportDetail::where('id_header_laporan', $id)->where('tanggal', $tanggal)->count();
				if ($is_exists > 0) {
					throw new \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException();
				}

				$detail_terakhir = ReportDetail::where('id_header_laporan', $id)->where('tanggal', '<', $tanggal)->orderBy('tanggal', 'desc')->first();

				$reportDetail = new ReportDetail();
				$reportDetail->id_header_laporan = $id;
				$reportDetail->tanggal = $tanggal;
				$reportDetail->mortalitas = $mortalitas;
				$reportDetail->morbilitas = $morbilitas;
				$reportDetail->populasi_akhir = $detail_terakhir->populasi_akhir - $mortalitas;
				$reportDetail->bobot = $bobot;
				$reportDetail->save();

				// detail laporan terdampak
				$detail_laporan_terdampak = ReportDetail::where('id_header_laporan', $id)->where('tanggal', '>', $tanggal)->orderBy('tanggal', 'desc')->get();
				foreach ($detail_laporan_terdampak as $dlt) {
					$temp = ReportDetail::find($dlt['id']);
					$temp->populasi_akhir = $temp->populasi_akhir - $mortalitas;
					$temp->save();
				};

				$logPakan = new PakanLog();
				$logPakan->id_pakan = $pakan;
				$logPakan->id_header_laporan = $id;
				$logPakan->tanggal = $tanggal;
				$logPakan->status = 'Keluar';
				$logPakan->jumlah = $jumlah_pakan;
				$logPakan->save();

				SopLaporan::whereIn('id', $selectedSop)->update(['status' => 1]);

				$response = Report::findOrFail($id);
				return $this->response->array($response->toArray());
	        }
	    } catch (\Exception $e) {
	    	throw new \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException();
	    }
	}

	/**
	 * Display the specified report.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		try {
	        if ($user = \JWTAuth::parseToken()->authenticate()) {
	        	
	        	$response = Report::with(
					array(
						'kandang',
						'petugas_pj',
						'strain.perusahaan',
						'petugas_laporan.petugas',
						'report_details' => function ($query)
						{
							$query->orderBy('tanggal', 'asc');
						},
						'log_pakan' => function ($query)
						{
							$query->with('pakan')->orderBy('tanggal', 'asc');
						}
					)
				)->findOrFail($id);
				if ($response->id_petugas_pj != $user->id) {
					throw new \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException();
				}

				if ($response->status != 0) {
					throw new \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException();
				}
				
				$response['jumlah_sop_aktif'] = SopLaporan::where('id_header_laporan', $id)->where('status', 1)->count();
				$response['sop_non_aktif'] = SopLaporan::with('nilai')->where('id_header_laporan', $id)->where('status', 0)->get();
				$response['laporan_sop'] = SopLaporan::with('nilai')->where('id_header_laporan', $id)->where('status', 1)->get();
				$response['jumlah_pakan_sekarang'] = Report::jumlahPakanSekarang($id);
					$created = new Carbon\Carbon($response->tgl_masuk);
					$now = \Carbon\Carbon::now();
					$usia_dalam_hari = ($created->diff($now)->days < 1) ? 1 : $created->diffInDays($now) + 1;
					$usia_dalam_minggu = ($created->diff($now)->days < 7) ? 1 : $created->diffInWeeks($now) + 1;
				$response['usia_ternak'] = $usia_dalam_hari;
				
				$detail_terakhir = ReportDetail::where('id_header_laporan', $id)->orderBy('tanggal', 'desc')->first();
				$bobot_akhir = $detail_terakhir->bobot;
				$populasi_akhir = $detail_terakhir->populasi_akhir;
				$jumlah_konsumsi_pakan = Report::jumlah_pakan_pakai($id);
				$bobot_awal_fcr = $response->bobot_doc * $response->populasi_awal;
				$bobot_akhir_fcr = $bobot_akhir * $populasi_akhir;
				$bobot_hasil_fcr = $bobot_akhir_fcr - $bobot_awal_fcr;
				if ($bobot_hasil_fcr > 0) {
					$fcr = ($jumlah_konsumsi_pakan / ($bobot_hasil_fcr));
					$fcr = round($fcr, 2);
				} else {
					$fcr = 0;
				}

				$deplesi = (($response->populasi_awal - $populasi_akhir) * 100);

				$response['deplesi'] = $deplesi;

				$response['sisa_populasi'] = $populasi_akhir;
				$response['fcr'] = $fcr;

				$bobot_per_minggu = array();
				$mortalitas_per_minggu = array();
				$morbilitas_per_minggu = array();
				for ($i=0; $i < $usia_dalam_minggu; $i++) {
					$awal = $created->toDateString();
					$akhir = $created->addWeek()->subDay()->toDateString();

					$bobot = Report::getBobotPerMinggu($id, $awal, $akhir);
					$mortalitas = Report::getMortalitasPerMinggu($id, $awal, $akhir);
					$morbilitas = Report::getMorbilitasPerMinggu($id, $awal, $akhir);

					if (!is_null($bobot)) {
						$bobot_per_minggu[] = ($bobot->bobot) * 1000;
						$mortalitas_per_minggu[] = $mortalitas;
						$morbilitas_per_minggu[] = $morbilitas;
					} else {
						$bobot_per_minggu[] = 0;
						$mortalitas_per_minggu[] = 0;
						$morbilitas_per_minggu[] = 0;
					}

					$created->addDay();
				}
				$response['bobot_per_minggu'] =  $bobot_per_minggu;
				$response['mortalitas_per_minggu'] =  $mortalitas_per_minggu;
				$response['morbilitas_per_minggu'] =  $morbilitas_per_minggu;

				return $this->response->array($response->toArray());
	        }
	    } catch (\Exception $e) {
	    	throw new \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException();
	    }
	}
}
