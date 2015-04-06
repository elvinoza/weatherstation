<?php
/**
 * Created by PhpStorm.
 * User: elvinas
 * Date: 3/9/15
 * Time: 9:27 PM
 */
namespace App\Services;

use App\User;
use App\Weather;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class Api {

    /** @var string */
    protected $app_id;
    /** @var string */
    protected $app_key;
    /** @var User */
    protected $user;

    /**
     * @param $app_id
     * @param $app_key
     * @param User $user
     */
    public function __construct($app_id, $app_key, User $user)
    {
        $this->app_id = $app_id;
        $this->app_key = $app_key;
        $this->user = $user;
    }

    /**
     * @return bool
     */
    public function authenticate(){
        $this->user = $this->user->find($this->app_id);
        if($this->user != null)
            if(($this->user->id == $this->app_id) && ($this->user->app_key == $this->app_key)){
                return true;
            }
            else return false;
        else return false;
    }

    /**
     * @param $temperature
     * @param $humidity
     * @param $light_level
     * @param $pressure
     * @param $wind_direction
     * @param $wind_speed
     * @param $rain
     */
    public function insertStationData($temperature, $humidity, $light_level, $pressure, $wind_direction, $wind_speed, $rain){
        $weather = new Weather(array(
            'temperature'     => $temperature,
            'humidity'        => $humidity,
            'light_level'     => $light_level,
            'pressure'         => $pressure,
            'wind_direction'  => $wind_direction,
            'wind_speed'      => $wind_speed,
            'rain'            => $rain
        ));
        $this->user->weathers()->save($weather);
    }

    /**
     * @return array|mixed
     */
    public function getAllData(){
        $this->user = $this->user->find($this->app_id);
        if($this->user != null){
            return $weathers = $this->user->weathers;
        }
        else {
            return array('success' => false, 'error' => 'Station not found');
        }
    }

    /**
     * @param $start_date
     * @param $end_date
     * @param $group
     * @return array
     */
    public function getDataByDate($start_date, $end_date, $group)
    {
        $this->user = $this->user->find($this->app_id);
        if($this->user != null){
            $w = $this->user->weathers()->where('created_at', '>=', $start_date )->Where('created_at', '<=', $end_date)->get();
//            $weathers = $this->user->whereHas('weathers', function($q){
//                $q->where('temperature', '=', 1.10);
//            })->get();
//            return $weathers;
            return $w;
        } else {
            return array('success' => 'false', 'error' => 'Station not found');
        }
    }

    /**
     * @param $format
     * @param $option
     * @return array
     */
    public function getStationDataByFormat($format, $option)
    {
        $this->user = $this->user->find($this->app_id);
        if($this->user != null)
        {
            $data = [];
            if($format == "h"){
                $data = $this->user->weathers()
                                ->where('created_at', '>=', Carbon::now()->subHour())
                                ->select([$option, DB::raw("DATE_FORMAT(created_at, '%H:%i') as date")])->get();
            }
            else if($format == "m"){
                $data = $this->user->weathers()
                                ->where('created_at', '>=', Carbon::now()->subMonth())
                                ->select([DB::raw("AVG($option) as $option"), DB::raw("DATE_FORMAT(created_at, '%m-%d') AS date")])

                                ->groupBy('date')
                                ->get();
            }
            else if($format == "d"){
                $data = $this->user->weathers()
                                ->where('created_at', '>=', Carbon::now()->subDay())
                                ->select([DB::raw("AVG($option) as $option"), DB::raw("DATE_FORMAT(created_at, '%m-%d %Hh') AS date")])
                                ->groupBy('date')
                                ->get();
            }
            else if($format == "w"){
                $data = $this->user->weathers()
                                ->where('created_at', '>=', Carbon::now()->subWeek())
                                ->select([DB::raw("AVG($option) as $option"), DB::raw("DATE_FORMAT(created_at, '%m-%d') AS date")])
                                ->groupBy('date')
                                ->get();
            }
            return array('success' => true, 'data' => $data);
        } else {
            return array('success' => false, 'error' => 'Station not found');
        }
    }


    /**
     * @return array
     */
    public function getWindDirectionCounts(){
        $this->user = $this->user->find($this->app_id);
        if($this->user != null)
        {
            $dir = $this->user->weathers()
                            ->where('created_at', '>=', Carbon::now()->subMonth())
                            ->select([DB::raw("COUNT(*) as c_direction"), "wind_direction"])
                            ->groupBy("wind_direction")
                            ->get();
            foreach($dir as $key => $item){
                $dir[$key]['wind_direction'] = $this->getWindDirectionName($item['wind_direction']);
            }

            $result = $this->getDirectionsArray($dir);

            return array('success' => true, 'data' => $result);
        } else {
            return array('success' => false, 'error' => 'Station not found');
        }
    }

    /**
     * @return array
     */
    public function getLastInformation(){
        $this->user = $this->user->find($this->app_id);
        if($this->user != null){
            if(count($this->user->weathers)){
                $information = $this->user->weathers->last();
                $information->wind_direction = $this->getWindDirectionName($information->wind_direction);
                return array('success' => true, 'information' => $information);
            }
            else{
                return array('success' => false, 'error' => "This station haven't information.");
            }
        }
        else {
            return array('success' => false, 'error' => 'Station not found.');
        }
    }

    /**
     * @param $length
     * @return string
     */
    public function regenerateKey($length){
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    /**
     * @param $direction
     * @return string
     */
    public function getWindDirectionName($direction){
        $direction_name = "";
        switch($direction){
            case $direction <= 22.5 || $direction >= 337.5:
                $direction_name = "N";
                break;
            case $direction > 22.5 && $direction <=  67.5:
                $direction_name = "NE";
                break;
            case $direction > 67.5 && $direction <= 112.5:
                $direction_name = "E";
                break;
            case $direction > 112.5 && $direction <= 157.5:
                $direction_name = "SE";
                break;
            case $direction > 157.5 && $direction <= 202.5:
                $direction_name = "S";
                break;
            case $direction > 202.5 && $direction <= 247.5:
                $direction_name = "SW";
                break;
            case $direction > 247.5 && $direction <= 292.5:
                $direction_name = "W";
                break;
            case $direction > 292.5 && $direction <= 337.5:
                $direction_name = "NW";
                break;
        }
        return $direction_name;
    }

    /**
     * @param $directions
     * @return array
     */
    public function getDirectionsArray($directions){
        $grouped = [
            "N" => 0,
            "NE" => 0,
            "E" => 0,
            "SE" => 0,
            "S" => 0,
            "SW" => 0,
            "W" => 0,
            "NW" => 0
        ];
        foreach($directions as $direction){
            $grouped[$direction['wind_direction']] += $direction['c_direction'];
        }
        return $grouped;
    }
}