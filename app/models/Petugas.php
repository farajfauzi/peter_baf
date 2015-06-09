<?php

use Illuminate\Database\Eloquent\ModelNotFoundException;

class Petugas extends \Eloquent {

	protected $table = 'petugas';
	
	public $timestamps = false;

	// Add your validation rules here
	public static $rules = [
		// 'title' => 'required'
	];

	// Don't forget to fill this array
	protected $fillable = [];

	protected $hidden = ['password'];

	public static function findPenanggungJawab($username, $password)
	{
		if ( ! is_null($user = static::whereUsername($username)->wherePassword($password)->whereStatus('PJ')->whereAktif('1')->whereHapus('1')->first())) {
            return $user;
        }

        throw (new ModelNotFoundException)->setModel(get_called_class());
	}
}