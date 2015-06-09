<?php

class SopController extends \BaseController {

	/**
	 * Display a listing of reports
	 *
	 * @return Response
	 */
	public function index()
	{
		try {
			$response = SopLaporan::with('nilai')->where('status', 0)->get();

			return $this->response->array($response->toArray());
		} catch (Exception $e) {
			return $e;
			throw new Exception("Error Processing Request", 1);
		}
	}
}
