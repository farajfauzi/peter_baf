<?php

class PetugasController extends \BaseController {

	public function login()
	{
		$credentials = Input::only(array('username', 'password'));

	    try {
	    	// $petugas = Petugas::findPenanggungJawab($username, $password);

	        // attempt to verify the credentials and create a token for the user
	        if (! $token = JWTAuth::attempt($credentials)) {
	        	throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException("Username atau Password anda salah");
	        }
	    } catch (Tymon\JWTAuth\Exceptions\JWTException $e) {
	        // something went wrong whilst attempting to encode the token
	        throw new Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException("Could not create token");
	    }

	    $petugas = \JWTAuth::authenticate($token);
	    $petugas = Petugas::findOrFail($petugas->id);
	    $response = array(
	    	'petugas' 	=> $petugas,
	    	'token'		=> $token
	    );

	    return $this->response->array($response);
	}
}
