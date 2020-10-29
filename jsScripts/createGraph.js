  var errorPrefix = "createGraphs Says: ";
  var amountUnits = {};
  var timeUnits = {};
  var valid = true;

  //COLOURS

  var accentColor = "#4CAAC4";

  function formatToUSDate(date){
    if (date.length != 10) {
      return false;
    }
    date = date.split("-");
    date = new Date(date[2],date[1]-1,date[0]); //create a date with a new order of the numbers (UK->US)
    if (date == "Invalid Date"){ //JS Returns "Invalid Date" if a parse fails
      return false;
    }
    else{
      return date; //return the new Date object
    }
  }

  //Used to convert date object to string thats yyyy-mm-dd

  function dateToString(date) { // FROM https://stackoverflow.com/questions/23593052/format-javascript-date-to-yyyy-mm-dd
    var d = new Date(date),
        month = '' + (d.getMonth() + 1),
        day = '' + d.getDate(),
        year = d.getFullYear();

    if (month.length < 2) month = '0' + month;
    if (day.length < 2) day = '0' + day;

    return [day, month, year].join('-');
}




  function getUnits(unitType){ //gets the possible units from the database
    $.ajax({
      type: "POST",
      async: false,
      dataType: "JSON",
      data:{type: unitType},
      url: "ajaxPages/getUnits.php",

      success: function(data){ //data is the echo'd returned by the url
        switch (unitType){
          case "amount":
            $.each(data,function() {
              amountUnits[this[0]] = this[1]; //assigns the unit name as a key to the dictionary and adds the conversion value
            });
            break;
          case "time":
            $.each(data,function() {
              timeUnits[this[0]] = this[1];
            });
            break;
          default:
            console.log(errorPrefix+"Unknown unit type: "+unitType);
          }
      }
    });
  }

  getUnits("amount"); //creates the unit selects
  getUnits("time");

  function convertAmount(value, type, unit){
    type = type.toLowerCase();
    if (type == "amount"){
      return (value / amountUnits[unit]); //divides the value in kg to whatever the desired unit is
    }
    else if (type =="time"){
      return (value / timeUnits[unit]); //divides the value in seconds to whatever the desired unit is
    }
    else{
      cosole.log("Invalid Unit Type"); //outputs if an unexpected unit is entered as the value parameter
    }
  }


  function createSingleLineGraph(id,unit,line,searchType,earliest,latest){ //earliest latest and nedID are not required
      if (id === undefined || unit === undefined || line === undefined || searchType === undefined){ //location unit or line name has not been specified
        console.log(errorPrefix+"Paremeters not correctly specified");
        return; //exits the function due to not correctly entering paremeters
      }

      google.charts.load('current', {'packages': ['corechart']});
      google.charts.setOnLoadCallback(drawChart);

      function drawChart() {
            var data = new google.visualization.DataTable(); //Creates a datatable allowing the data to be added

            //CREATE COLUMNS
            data.addColumn("date","Date");
            data.addColumn('number', line); //add a column with the name of the exercise that will be added


            //AJAX TO GET DATA
           $.ajax({
              type: "POST",
              dataType: "JSON",
              async: false,
              data:({value: line, limit: 30, searchType: searchType, exact: true, earliest: dateToString(earliest), latest: dateToString(latest)}), //i+2 is the index of the exercise because the first 2 are not exercises
              url: "ajaxPages/getSessions.php",

              success: function(data){ //data is the echo'd returned by the url
                valid = false;
                if (data == "Invalid Unique"){
                  console.log(errorPrefix+"Invalid Unique")
                }

                else if(data == "NED"){ //will run if not enough data is found
                  valid = false;
                  $(".ned").fadeIn("fast");
                  $(".ned").text("Not enough data in that time period");
                }

                else if (Array.isArray(data)){
                  valid = true;
                  for (var i = 0; i < data.length; i++) {
                    addRowToGraph(data[i],unit);
                  }
                }

                else{
                  console.log(errorPrefix+data);
                }

              },
              error: function(req) {
                alert('Error: ' + req.status);
              }
           });

            function addRowToGraph(d,u){
              if (searchType == "maxAmount"){
                data.addRow([new Date(d[2]),convertAmount(d[4],d[1],u)]);
              }
              else if (searchType == "maxReps") {
                data.addRow([new Date(d[2]),d[3]]);
              }
            }

            var options = {
              title: "",
              titleTextStyle: {
                color: '#FFF'
              },
              colors: [accentColor],
              hAxis: {
                title: 'Date',
                slantedText: true,
                titleTextStyle: {
                  color: '#FFF'
                },
                minorGridlines:{count:0}, //remove the smaller grid lines from the grid
                gridlines: {color: "white"},
                format: 'd/MM/YY',
                textStyle:{
                  color: '#FFF'
                }
                /*
                minValue: earliest, // DEBUG: this is currently not supported by google charts
                maxValue: latest,
                */
              },
              vAxis: {
                title: undefined,
                titleTextStyle: {
                  color: '#FFF'
                },
                minorGridlines:{count:0}, //remove the smaller grid lines from the grid
                gridlines: {color: "white"},
                viewWindow: {
                  min: 0
                },
                textStyle:{
                  color: '#FFF'
                }
              },
              legendTextStyle: {
                color: "#FFF"
              },
              pointSize: 5,
              backgroundColor: {fill:'transparent'}
            };

            if (searchType == "maxAmount"){
              options.vAxis.title = "Max Amount in a Set ("+unit+")";
            }
            else if (searchType == "maxReps"){
              options.vAxis.title = "Max Reps in a Set";
            }
          else{
            console.log("Unknown Search Type: "+searchType);
            valid = false;
          }



            if (valid){
              var chart = new google.visualization.LineChart(document.getElementById(id)); //replaces element with id chart_div
              chart.draw(data, options); //draws the graph with the options, 'options'
              return true; //this will return true meaning that the query has finished
            }
        }
  }
