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

	/**
	 * Show the form for creating a new report
	 *
	 * @return Response
	 */
	public function create()
	{
		return View::make('reports.create');
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
			$response = Report::with('nilai')->findOrFail($id);

			return $this->response->array($response->toArray());
		} catch (Exception $e) {
			throw new Exception("Error Processing Request", 1);
		}
	}

	/**
	 * Show the form for editing the specified report.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$report = Report::find($id);

		return View::make('reports.edit', compact('report'));
	}

	/**
	 * Update the specified report in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		$report = Report::findOrFail($id);

		$validator = Validator::make($data = Input::all(), Report::$rules);

		if ($validator->fails())
		{
			return Redirect::back()->withErrors($validator)->withInput();
		}

		$report->update($data);

		return Redirect::route('reports.index');
	}

	/**
	 * Remove the specified report from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		Report::destroy($id);

		return Redirect::route('reports.index');
	}

}
