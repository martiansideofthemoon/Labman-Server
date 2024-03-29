<?php
/*
|--------------------------------------------------------------------------
| Application & Route Filters
|--------------------------------------------------------------------------
|
| Below you will find the "before" and "after" events for the application
| which may be used to do any work before or after a request into your
| application. Here you may also register your custom route filters.
|
*/
App::before(function($request)
{
	$data = json_encode(Input::all());
	$file = fopen(storage_path()."/logs/route_logs.txt",'a');
	fwrite($file,Request::path()."\n\n");
	fwrite($file,$data);
	if($data != ""){
        $t = date("Y-m-d G:i:s",time());
        $data ="\nFound matches at at ".$t."\n----------------------------------\n\n";
	}
	fwrite($file,$data);
});
App::after(function($request, $response)
{
	return $response;
});
/*
|--------------------------------------------------------------------------
| Authentication Filters
|--------------------------------------------------------------------------
|
| The following filters are used to verify that the user of the current
| session is logged into this application. The "basic" filter easily
| integrates HTTP Basic authentication for quick, simple checking.
|
*/
Route::filter('auth', function()
{
	if (Auth::guest())
	{
		if (Request::ajax())
		{
			return Response::make('Unauthorized', 401);
		}
		else
		{
			return Redirect::guest('login');
		}
	}
});
Route::filter('auth.basic', function()
{
	return Auth::basic();
});
/*
|--------------------------------------------------------------------------
| Guest Filter
|--------------------------------------------------------------------------
|
| The "guest" filter is the counterpart of the authentication filters as
| it simply checks that the current user is not logged in. A redirect
| response will be issued if they are, which you may freely change.
|
*/
Route::filter('guest', function()
{
	if (Auth::check()) return Redirect::to('/');
});
/*
|--------------------------------------------------------------------------
| CSRF Protection Filter
|--------------------------------------------------------------------------
|
| The CSRF filter is responsible for protecting your application against
| cross-site request forgery attacks. If this special token in a user
| session does not match the one given in this request, we'll bail.
|
*/
Route::filter('csrf', function()
{
	if (Session::token() != Input::get('_token'))
	{
		throw new Illuminate\Session\TokenMismatchException;
	}
});
App::missing(function($exception)
{
   return Error::make(404,404);
});
Route::filter('API',function() {
	if (Input::has('key')) {
		if (Input::get('key')!="teninchlong"){
			return Error::make(401,401);
		}
	}
	else{
			return Error::make(1,2);
	}
});
Route::filter('afterAPI', function($request, $response)
{
	$response->header('Content-Type', 'application/json');
	return $response;
});