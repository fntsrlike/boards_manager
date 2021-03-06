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
	//
});


App::after(function($request, $response)
{
	//
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

Route::filter('perm_boards_manage', function()
{
	if ( !Auth::user()->can('boards_management') ){
		return Response::json(['success' => false, 'messages' => 'Permission Deny']);
	}
});

Route::filter('perm_user_manage', function()
{
	if ( !Auth::user()->can('users_management') ){
		return Response::json(['success' => false, 'messages' => 'Permission Deny']);
	}
});


Route::filter('perm_apply', function()
{
	if ( !( Auth::user()->ability([], ['apply_records_management', 'apply_post']) ) ){
		return Response::json(['success' => false, 'messages' => 'Permission Deny']);
	}
});

Route::filter('perm_apply_owner', function()
{
	$record_id = Request::segment(3);
	$record    = ApplyRecord::find($record_id);
	$user_id   = $record->user_id;

	if ( !Auth::user()->can('apply_records_management') ) {
		if ( $user_id !== Auth::id() ) {
			return Response::json(['success' => false, 'messages' => 'Permission Deny']);
		}
	}
});

Route::filter('input_date', function()
{
	$validator = Validator::make( Input::all(), [
		'from' => 'date_format:Y-m-d',
		'end'  => 'date_format:Y-m-d',
	]);

	if ($validator->fails()) {
		return Response::json(['success' => false, 'messages' => $validator->errors()]);
	}
});
