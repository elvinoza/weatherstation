stationsApp.controller('HomeController', function ($scope, $rootScope, apiService, $interval, ngProgress) {
    ngProgress.height('3px');
    $scope.stations = [];
    //$scope.stationId = $rootScope.gstationId;
    $scope.getStationId = function(){
        if(!$rootScope.selected){
            console.log("selected");
            apiService.getStationList().success(function(data){
                $scope.stations = data;
                $scope.station = data[0];
            });
        }
        else {
            console.log("not selected");
            apiService.getStationList().success(function(data){
                $scope.stations = data;
                $scope.station = $rootScope.station;
            });
        }
    };

    $scope.setStationId = function(station){
        $rootScope.selected = true;
        $rootScope.selectedStationId = station.id;
        $rootScope.selectedStationName = station.station_name;
        $rootScope.station = station;
        $scope.getStationInformation();
    };

    var count = 0;
    var int = $interval(function(){
        ngProgress.set(count);
        count = count + 1;
        if(count == 100){
            ngProgress.start();
            count = 0;
        }
    }, 700);

    $scope.$on('$destroy', function () {
        $interval.cancel(int);
        ngProgress.set(0);
    });

    $interval($scope.getStationInformation = function(){
        apiService.getLastStationInformation($rootScope.selectedStationId).success(function(data){
            if(data.success){
                $scope.has = true;
                $scope.temperature = data.information.temperature;
                $scope.humidity = data.information.humidity;
                $scope.light_level = data.information.light_level;
                $scope.pressure = data.information.pressure;
                $scope.wind_direction = data.information.wind_direction;
                $scope.wind_speed = data.information.wind_speed;
                $scope.rain = data.information.rain;
                $scope.time = data.information.created_at;
            } else{
                $scope.has = false;
            }
        });
    }, 70000);

    $scope.getStationId();
    $scope.getStationInformation();
});

stationsApp.controller("PanelController", function(){
    this.tab = 1;

    this.selectedTab = function(setTab){
        this.tab = setTab;
    };

    this.isSelected = function(checkTab){
        return this.tab === checkTab;
    }
});

stationsApp.controller("NavBarController", function($scope, $location){

    $scope.isActive = function(viewLocation){
        return viewLocation === $location.path();
    }
});

stationsApp.controller("ChartsController", function($scope, $routeParams, $rootScope, apiService){
    var initialChartType = "m";
    $scope.stationId = $routeParams.selectedStationId;
    $scope.tempType = initialChartType;
    $scope.humType = initialChartType;
    $scope.windSpeedType = initialChartType;
    $scope.pressureType = initialChartType;
    $scope.lightType = initialChartType;

    $scope.getTemperatureChart = function(tempType){
        $scope.tempType = tempType;
        apiService.getStationTemperature($scope.stationId, tempType).success(function(data){
            $scope.tempLabels = [];
            $scope.tempData = [];
            $scope.tempSeries = [$rootScope.selectedStationName + ' temperature'];
            $scope.tempLabels = data.data.map(function(item){ return item.date;});
            $scope.tempData.push(data.data.map(function(item){ return item.temperature;}));
            if(data.success){
                $scope.hasTemperature = true;

            } else {
                $scope.hasTemperature = false;
            }
        });
    };

    $scope.getHumidityChart = function(humType){
        $scope.humType = humType;
        apiService.getStationHumidity($scope.stationId, humType).success(function(data){
            $scope.humLabels = [];
            $scope.humData = [];
            $scope.humLabels = data.data.map(function(item){ return item.date;});
            $scope.humSeries = ['Humidity'];
            $scope.humData.push(data.data.map(function(item){ return item.humidity;}));
        });
    };

    $scope.getWindSpeedChart = function(windSpeedType){
        $scope.windSpeedType = windSpeedType;
        apiService.getStationWindSpeed($scope.stationId, windSpeedType).success(function(data){
            $scope.windSpeedLabels = [];
            $scope.windSpeedData = [];
            $scope.windSpeedLabels = data.data.map(function(item){ return item.date;});
            $scope.windSpeedSeries = ['Wind speed'];
            $scope.windSpeedData.push(data.data.map(function(item){ return item.wind_speed;}));
        });
    };

    $scope.getPressureChart = function(pressureType){
        $scope.pressureType = pressureType;
        apiService.getStationPressure($scope.stationId, pressureType).success(function(data){
            $scope.pressureLabels = [];
            $scope.pressureData = [];
            $scope.pressureLabels = data.data.map(function(item){ return item.date;});
            $scope.pressureSeries = ['Pressure'];
            $scope.pressureData.push(data.data.map(function(item){ return item.pressure;}));
        });
    };


    $scope.getLightChart = function(lightType){
        $scope.lightType = lightType;
        apiService.getStationlightLevels($scope.stationId, lightType).success(function(data){
            $scope.lightLabels = [];
            $scope.lightData = [];
            $scope.lightLabels = data.data.map(function(item){ return item.date;});
            $scope.lightSeries = ['Light level'];
            $scope.lightData.push(data.data.map(function(item){ return item.light_level;}));
        });
    };
    //initial charts
    $scope.getTemperatureChart($scope.tempType);
    $scope.getHumidityChart($scope.humType);
    $scope.getWindSpeedChart($scope.windSpeedType);
    $scope.getPressureChart($scope.pressureType);
    $scope.getLightChart($scope.lightType);
});

stationsApp.controller("LiveController", function($scope, apiService){

    $scope.getLiveData = function(){

    };
});