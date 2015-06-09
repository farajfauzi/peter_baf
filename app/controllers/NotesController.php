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
	        if ($user = \JWTAuth::parseToken()->authenticate()) {
	        	
	        	$response = Note::where('hapus', 1)->where('id_pegawai', $user->id)->get();

				return $this->response->array($response->toArray());
	        }
	    } catch (\Exception $e) {
	    	throw new \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException();
	    	
	    }
	}

	/**
	 * Store a newly created note in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		try {
	        if ($user = \JWTAuth::parseToken()->authenticate()) {
	        	
	        	$response = new Note;

				$response->id_pegawai = $user->id;
				$response->judul = Input::get('title');
				$response->isi   = Input::get('note');
				$response->tanggal = date("Y-m-d");

				$response->save();

				return $this->response->array($response->toArray());
	        }
	    } catch (\Exception $e) {
	    	throw new \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException();
	    	
	    }
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
	        if ($user = \JWTAuth::parseToken()->authenticate()) {
	        	
	        	$response = Note::findOrFail($id);

	        	if ($response->id_pegawai != $user->id) {
					throw new Dingo\Api\Exception\ResourceException("Bukan Notes Anda", 1);
				}

				if ($response->hapus === 0 ) {
					throw new Dingo\Api\Exception\ResourceException("Notes Telah Dihapus", 1);
				}

				return $this->response->array($response->toArray());
	        }
	    } catch(\Dingo\Api\Exception\ResourceException $e) {
	    	throw new Dingo\Api\Exception\ResourceException("Error Processing Request", $e->getMessage());
	    }catch (\Exception $e) {
	    	throw new \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException();
	    	
	    }
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
	        if ($user = \JWTAuth::parseToken()->authenticate()) {
	        	
	        	$response = Note::findOrFail($id);

	        	if ($response->id_pegawai != $user->id) {
					throw new Dingo\Api\Exception\ResourceException("Bukan Notes Anda", 1);
				}

				if ($response->hapus === 0 ) {
					throw new Dingo\Api\Exception\ResourceException("Notes Telah Dihapus", 1);
				}
				$response->hapus = 0;
				$response->save();
				return $this->response->array($response->toArray());
	        }
	    } catch(\Dingo\Api\Exception\ResourceException $e) {
	    	throw new Dingo\Api\Exception\ResourceException("Error Processing Request", $e->getMessage());
	    }catch (\Exception $e) {
	    	throw new \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException();
	    	
	    }
	}
}
