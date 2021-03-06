<?php
/**
 * Created by PhpStorm.
 * User: elvinas
 * Date: 3/8/15
 * Time: 1:46 PM
 */

namespace App\Http\Controllers;

use App\Services\Api;
use App\User;
use Illuminate\Support\Facades\Input;

class ApiController extends Controller {

    /**
     * @var \App\User
     */
    protected $user;


    /**
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
        //$this->api = $api;
    }

    /**
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function insert(){
        $app_id = Input::get('app_id');
        $app_key = Input::get('app_key');

        $api = new Api($app_id, $app_key, $this->user);

        if($api->authenticate()){
            $api->insertStationData(Input::get('t'), Input::get('h'), Input::get('l'),
                Input::get('p'), Input::get('wd'), Input::get('ws'), Input::get('r'), Input::get('st'), Input::get('sh'));
            return response()->json(array('success'=> true, 'message' => 'Successfully authenticated and your data inserted'));
        }
        else {
            return response()->json(array('success'=> false, 'message' => 'Authenticate problem. Check your app_id and app_key.'));
        }
    }

    /**
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getAllData($id)
    {
        $api = new Api($id, null, $this->user);
        $weathers = $api->getAllData();
        return response()->json($weathers);
    }

    /**
     * @param $id
     * @param $startDate
     * @param $endDate
     * @param string $groupBy
     */
    public function getByDate($id, $startDate, $endDate, $groupBy = "all")
    {
        $api = new Api($id, null, $this->user);
        $q = $api->getDataByDate($startDate,$endDate, $groupBy);
        dd($q);
    }


    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getStations()
    {
        return response()->json($this->user->all(['id', 'station_name', 'location', 'description']));
    }

    //day, month, hour, week
    /**
     * @param $id
     * @param $format
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getStationTemperature($id, $format)
    {
        $api = new Api($id, null, $this->user);
        $temperatures = $api->getStationDataByFormat($format, 'temperature');
        return response()->json($temperatures);
    }

    /**
     * @param $id
     * @param $format
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getStationHumidity($id, $format)
    {
        $api = new Api($id, null, $this->user);
        $humidities = $api->getStationDataByFormat($format, 'humidity');
        return response()->json($humidities);
    }

    /**
     * @param $id
     * @param $format
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getStationWindSpeed($id, $format = "m"){
        $api = new Api($id, null, $this->user);
        $speeds = $api->getStationDataByFormat($format, 'wind_speed');
        return response()->json($speeds);
    }

    /**
     * @param $id
     * @param $format
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getStationPressure($id, $format){
        $api = new Api($id, null, $this->user);
        $pressures = $api->getStationDataByFormat($format, 'pressure');
        return response()->json($pressures);
    }

    /**
     * @param $id
     * @param $format
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getStationLightLevels($id, $format){
        $api = new Api($id, null, $this->user);
        $lightLevels = $api->getStationDataByFormat($format, 'light_level');
        return response()->json($lightLevels);
    }

    /**
     * @param $id
     * @param $format
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getStationRain($id, $format){
        $api = new Api($id, null, $this->user);
        $rain = $api->getStationDataByFormat($format, 'rain');
        return response()->json($rain);
    }

    /**
     * @param $id
     * @param $chart
     * @param $startDate
     * @param $endDate
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getStationDataByDate($id, $chart, $startDate, $endDate){
        $api = new Api($id, null, $this->user);
        if($chart != "wind_direction")
            $data = $api->getChartByDate($chart, $startDate, $endDate);
        else $data = $api->getWindDirectionCountsByDate($startDate, $endDate);

        return response()->json($data);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getFirstStation(){
        return response()->json($this->user->all(['id'])->last());
    }

    /**
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getLastStationInformation($id){
        $api = new Api($id, null, $this->user);
        $information = $api->getLastInformation();
        return response()->json($information);
    }

    /**
     * @param $dir
     */
    public function tryDir($dir){
        $api = new Api("3RkTSJ", null, $this->user);
        $api->getWindDirectionName($dir);
    }

    /**
     * @param $id
     * @param $format
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getStationWindDirection($id, $format = 'm'){
        $api = new Api($id, null, $this->user);
        $data = $api->getWindDirectionCounts($format);
        return response()->json($data);
    }

    /**
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function checkStationExist($id){
        $api = new Api($id, null, $this->user);
        $u = $this->user->find($id);
        if($u != null){
            return response()->json(array('exist'=> true));
        } else {
            return response()->json(array('exist'=> false));
        }
    }

    /**
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getStationUpdateTime($id){
        $api = new Api($id, null, $this->user);
        return response()->json($api->getTime());
    }
}