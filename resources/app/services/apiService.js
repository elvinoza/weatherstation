stationsApp.service('apiService', function($http){
    var baseURL = '/api/v1/';

    this.getStationTemperature = function(id, format){
        return $http.get(baseURL + 'get/temperatures/' + id + '/' + format);
    };

    this.getStationHumidity = function(id, format){
        return $http.get(baseURL + 'get/humidities/' + id + '/' + format);
    };

    this.getStationWindSpeed = function(id, format){
        return $http.get(baseURL + 'get/wind_speeds/' + id + '/' + format);
    };

    this.getFirstStation = function(){
        return $http.get(baseURL + 'get/firstStation');
    };

    this.getStationList = function(){
        return $http.get(baseURL + 'get/stations-list');
    };

    this.getLastStationInformation = function(id){
        return $http.get(baseURL + 'get/lastStationInformation/' + id);
    };
});