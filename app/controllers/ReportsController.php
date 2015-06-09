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
		$validator = Validator::make($data = Input::all(), Report::$rules);

		if ($validator->fails())
		{
			return Redirect::back()->withErrors($validator)->withInput();
		}

		Report::create($data);

		return Redirect::route('reports.index');
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
				$response['jumlah_sop_aktif'] = SopLaporan::where('id_header_laporan', $id)->where('status', 1)->count();
				$response['laporan_sop'] = SopLaporan::with('nilai')->where('id_header_laporan', $id)->where('status', 1)->get();
				$response['sisa_populasi'] = ReportDetail::where('id_header_laporan', $id)->orderBy('id', 'desc')->first()->populasi_akhir;
				$response['jumlah_pakan_sekarang'] = Report::jumlahPakanSekarang($id);
					$created = new Carbon\Carbon($response->tgl_masuk);
					$now = \Carbon\Carbon::now();
					$usia_dalam_hari = ($created->diff($now)->days < 1) ? 1 : $created->diffInDays($now) + 1;
					$usia_dalam_minggu = ($created->diff($now)->days < 7) ? 1 : $created->diffInWeeks($now) + 1;
				$response['usia_ternak'] = $usia_dalam_hari;

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
