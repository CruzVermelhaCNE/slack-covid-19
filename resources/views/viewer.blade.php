<!doctype html>
<html lang="en" style="height: 100%">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css"
        integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">

    <title>SALOP - COVID-19 / SARS-COV-2</title>
</head>

<body style="height: 100%">
    <div id="infected_map" style="width:100%; height:100%"></div>
    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"
        integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous">
    </script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"
        integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous">
    </script>

    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
        var infected_countries = [["Country", "Infected"]];
        var infected_countries_list = [];
        var global =  [["Country", "Confirmed", "Recovered", "Deaths", "Infected"]];
        var global_list = [];
        function getData(callback) {
            $.getJSON( "/all", function( data ) {
                data.forEach(element => {        
                    let infected = element.confirmed - element.recovered - element.deaths;   
                    if(element.country == "Mainland China") {
                        element.country = "China";
                    }         
                    if(!(infected_countries_list.includes(element.country))) {
                        infected_countries_list.push(element.country);
                        infected_countries.push([element.country,infected]);
                    }
                    else {
                        let country_to_search = element.country;
                        let index = infected_countries.findIndex(function (element, index, array) {
                            console.log(element[0]);
                            return element[0] == country_to_search;
                        });
                        infected_countries[index][1] += infected;
                    }
                    if(!(global_list.includes(element.country))) {        
                        global.push([element.country,element.confirmed,element.recovered,element.deaths,element.infected]);
                    }       
                });
                callback();
            });
        }
        // Load the Visualization API and the corechart package.
      google.charts.load('current', {
          'packages':['geochart'],
          'mapsApiKey': 'AIzaSyAa2174AvOd201tdGVONGqbKD0Q1i2moJ8'
        });

      // Set a callback to run when the Google Visualization API is loaded.
      google.charts.setOnLoadCallback(drawChart);

      // Callback that creates and populates a data table,
      // instantiates the pie chart, passes in the data and
      // draws it.
      function drawChart() {
        getData(function() {
            var infected_map_data = google.visualization.arrayToDataTable(infected_countries);
            var infected_map_options = {
                showTooltip: true,
                showInfoWindow: true,
                colorAxis: {
                    colors: ['#4374e0','#00ff00','#ff6600','red','brown','black'],
                    values: [0,1,50,500,5000,100000]
                },
                datalessRegionColor: '#4374e0',
                backgroundColor: '#81d4fa',               
            };
            var infected_map = new google.visualization.GeoChart(document.getElementById('infected_map'));
            infected_map.draw(infected_map_data, infected_map_options);

            /*var global_map_data = google.visualization.arrayToDataTable(global);
            var global_map_options = {
                showTooltip: true,
                showInfoWindow: true
            };
            var global_map = new google.visualization.GeoChart(document.getElementById('global_map'));
            global_map.draw(global_map_data, global_map_options);*/
        });
    }
    </script>

</body>

</html>