<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', array(
    'as' => 'home-page',
    'uses' => 'WelcomeController@index'
));

Route::get('home', 'HomeController@index');

Route::controllers([
	'auth' => 'Auth\AuthController',
	'password' => 'Auth\PasswordController',
]);
//Developer
Route::group(array('prefix' => 'developer'), function(){

    //login page
    Route::get('/', array(
       'as' => 'developer.index',
       'uses' => 'DeveloperController@index'
    ));

    Route::get('/sign-up', array(
        'as' => 'developer.sign-up',
        'uses' => 'Auth\AuthController@getRegister'
    ));

    Route::post('/register', array(
        'as' => 'developer.register',
        'uses' => 'Auth\AuthController@postRegister'
    ));

    Route::get('/sign-in', array(
        'as' => 'developer.sign-in',
        'uses' => 'Auth\AuthController@getLogin'
    ));

    Route::post('/login', array(
        'as' => 'developer.login',
        'uses' => 'Auth\AuthController@postLogin'
    ));

    Route::get('/dashboard', array(
        'as' => 'developer.dashboard',
        'uses' => 'Auth\AuthController@dashboard'
    ));

    Route::get('/logout', array(
        'as' => 'developer.logout',
        'uses' => 'Auth\AuthController@getLogout'
    ));

    Route::get('/auto-logout', array(
        'as' => 'developer.auto_logout',
        'uses' => 'Auth\AuthController@autoLogout'
    ));

    Route::get('/station', array(
        'as' => 'developer.station',
        'uses' => 'DeveloperController@updateForm'
    ));

    Route::post('/update', array(
        'as' => 'developer.update',
        'uses' => 'DeveloperController@updateInfo'
    ));

    Route::post('/password/change', array(
        'as' => 'developer.change-pass',
        'uses' => 'DeveloperController@changePassword'
    ));

    Route::get('/documentation', array(
        'as' => 'developer.documentation',
        'uses' => 'DeveloperController@showDocumentation'
    ));

    Route::get('/getNewKey', array(
        'as' => 'developer.generateNewKey',
        'uses' => 'DeveloperController@regenerateAppKey'
    ));

    Route::get('/data', 'DeveloperController@getStationData');

    Route::get('/delete', array(
        'as' => 'developer.delete',
        'uses' => 'DeveloperController@deleteStation'
    ));
});

//api
Route::group(array('prefix' => 'api/v1'), function(){
    //api/vi/get?app_id=3RkTSJ&app_key=KjdTEANlw6YPxKIPORINgmMKzQBTJtDt

    Route::get('/set', 'ApiController@insert');

    Route::get('get/stations-list', 'ApiController@getStations');
    //api/v1/get?id=123456&type=all
    Route::get('/get/allStationData/{id}', 'ApiController@getAllData');
    //groupby : all, day, month, hour
    Route::get('get/{id}/byDate/{startDate}/{endDate}/{groupBy?}', 'ApiController@getByDate');

    Route::get('get/temperatures/{id}/{format}', 'ApiController@getStationTemperature');

    Route::get('get/humidities/{id}/{format}', 'ApiController@getStationHumidity');

    Route::get('get/wind_speeds/{id}/{format}', 'ApiController@getStationWindSpeed');

    Route::get('get/pressures/{id}/{format}', 'ApiController@getStationPressure');

    Route::get('get/light_levels/{id}/{format}', 'ApiController@getStationLightLevels');

    Route::get('get/rain/{id}/{format}', 'ApiController@getStationRain');

    Route::get('get/chartByDate/{id}/{chart}/{startDate}/{endDate}', 'ApiController@getStationDataByDate');

    Route::get('get/firstStation', 'ApiController@getFirstStation');

    Route::get('get/direction/{dir}', 'ApiController@tryDir');

    Route::get('get/wind_direction/{id}/{format?}', 'ApiController@getStationWindDirection');

    Route::get('get/lastStationInformation/{id}', 'ApiController@getLastStationInformation');

    Route::get('app/checkStation/{id}', 'ApiController@checkStationExist');

    Route::get('get/update_time/{id}', 'ApiController@getStationUpdateTime');
});