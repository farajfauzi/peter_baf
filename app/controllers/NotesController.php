<?php

class NotesController extends \BaseController {

	/**
	 * Display a listing of notes
	 *
	 * @return Response
	 */
	public function index()
	{
		try {
			$response = Note::where('hapus', 1)->get();

			return $this->response->array($response->toArray());
		} catch (Exception $e) {
			throw new Exception("Error Processing Request", 1);
		}
	}

	/**
	 * Show the form for creating a new note
	 *
	 * @return Response
	 */
	public function create()
	{
		return View::make('notes.create');
	}

	/**
	 * Store a newly created note in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		try {
			$response = new Note;

			$response->id_pegawai = 1;
			$response->judul = Input::get('title');
			$response->isi   = Input::get('note');
			$response->tanggal = date("YYYY-mm-dd");

			$response->save();

			return $this->response->array($response->toArray());
		} catch (Exception $e) {
			return $e;// Exception("Error Processing Request", $e->getMessage());
		}
		// $validator = Validator::make($data = Input::all(), Note::$rules);

		// if ($validator->fails())
		// {
		// 	return Redirect::back()->withErrors($validator)->withInput();
		// }

		// Note::create($data);

		// return Redirect::route('notes.index');
	}

	/**
	 * Display the specified note.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		try {
			$response = Note::findOrFail($id);

			return $this->response->array($response->toArray());
		} catch (Exception $e) {
			throw new Exception("Error Processing Request", 1);
		}
	}

	/**
	 * Show the form for editing the specified note.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$note = Note::find($id);

		return View::make('notes.edit', compact('note'));
	}

	/**
	 * Update the specified note in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		$note = Note::findOrFail($id);

		$validator = Validator::make($data = Input::all(), Note::$rules);

		if ($validator->fails())
		{
			return Redirect::back()->withErrors($validator)->withInput();
		}

		$note->update($data);

		return Redirect::route('notes.index');
	}

	/**
	 * Remove the specified note from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		try {
			$response = Note::findOrFail($id);
			$response->hapus = 0;
			$response->save();

			return $this->response->array($response->toArray());
		} catch (Exception $e) {
			return $e;
			// throw new Exception("Error Processing Request", 1);
		}
		// Note::destroy($id);

		// return Redirect::route('notes.index');
	}

}
