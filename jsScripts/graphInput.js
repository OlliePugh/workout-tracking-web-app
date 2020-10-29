$( document ).ready(function() {

  var graphDrawn = false;

  function displayUnitInput(type){
    $("#"+type+"UnitInput").fadeIn("fast");
  }

  function NED(){
    $("#invalidDateText").text("Not enough data in that time period");
    $("#invalidDateText").fadeIn("fast");
  }

  function callGraphCreation(){
    if(validateDates()){
      $("#chart_div").empty();
      $("#noGraphText").fadeOut("fast");
      graphDrawn = true;
      let start = new Date(formatToUSDate($("#earliest").val()));
      let end = new Date(formatToUSDate($("#latest").val()));
      createSingleLineGraph("chart_div",$("#"+$("#exerciseName").attr("data-type")+"UnitInput").val(),$("#exerciseName").text(),$("#graphDataTypeSelect").val(),start,end);
    }
  }

  function turnOnSpotlight(object){
    $(".option").fadeIn("fast").css("display","block"); //this is needed to center everything
    $("#exerciseName").text($(object).attr("name"));
    $("#exerciseName").attr("data-type",$(object).attr("data-type"));
    $("#exerciseName").fadeIn("fast");
    $("#graphDataTypeSelect").fadeIn("fast");
    $("#exerciseSearch").fadeOut("fast");
    $("#exerciseNames").fadeOut("fast");
    $("#changeExercise").fadeIn("fast").css("display","block");
    $("#timeConstraintInput").fadeIn("fast");
    $("#timeConstraintInput .time-labels").fadeIn("fast").css("display","inline");
    displayUnitInput($(object).attr("data-type"));
    $("#unitSelect").fadeIn("fast");
    $("#exerciseNameContainer").css("overflow-y","auto");
    callGraphCreation();
  }

  function turnOffSpotlight(){
    let exerciseContainer = document.getElementById("exerciseNameContainer");
    exerciseContainer.scrollTop = 0; //reset the scroll bar back to the top before making overflow hidden again
    $("#exerciseNameContainer").css("overflow-y","hidden");
    $(".unitInputs").fadeOut("fast");
    $("#graphDataTypeSelect").fadeOut("fast");
    $("#unitSelect").fadeOut("fast");
    $("#exerciseSearch").val("");
    $("#exerciseSearch").fadeIn("fast");
    $("#exerciseNames").fadeIn("fast");
    $("#graphDataTypeSelect").fadeOut("fast");
    $("#changeExercise").fadeOut("fast");
    $("#timeConstraintInput").fadeOut("fast");
    $("#timeConstraintInput input").val("");
  }

  function getUnits(unitType){ //gets the possible units from the database
    $.ajax({
      type: "POST",
      dataType: "JSON",
      data:{type: unitType},
      url: "ajaxPages/getUnits.php",

      success: function(data){ //data is the echo'd returned by the url
      $.each(data,function(){
        if (this[0].toLowerCase() == "kilograms" || this[0].toLowerCase() == "seconds"){
          $("#"+unitType.toLowerCase()+"UnitInput").append("<option selected='selected' name='"+this[0].toLowerCase()+"'>"+this[0]+"</option>"); //adds a option to the amount and timed unit drop downs
        }
        else {
          $("#"+unitType.toLowerCase()+"UnitInput").append("<option name='"+this[0].toLowerCase()+"'>"+this[0]+"</option>"); //adds a option to the amount and timed unit drop downs
        }
      });
      }
    });
  }

  function validateDates(){
    let s = $("#earliest").val(); //has to be split up because Date uses mm-dd-yyyy normally
    s = formatToUSDate(s);
    let f = $("#latest").val();
    f = formatToUSDate(f);
    let e = formatToUSDate(earliest); //need to be date object to be able to compare them
    let l = formatToUSDate(latest);
    if ((s == false) || (f == false)){//if the date is invalid
      $("#invalidDateText").text("Please enter valid dates");
      $("#invalidDateText").fadeIn("fast");
      return false;
    }
    else if(s > f){ //if the start is after the finish
      $("#invalidDateText").text("End Date can not be before the start date");
      $("#invalidDateText").fadeIn("fast");
      return false;
    }
    else if (f < e || s > l){ //if the end date is before the start of the data
      NED();
    }
    else{
      $("#invalidDateText").fadeOut("fast");
      return true;
    }
  }

  getUnits("amount");
  getUnits("time");

  // DEBUG: SELECT COUNT(DISTINCT SessionDate) FROM sessions INNER JOIN exercises ON (sessions.ExerciseID = exercises.ExerciseID) WHERE sessions.UserID = 20 AND exercises.Name = "Jumping Jacks" STILL NEEDS DATES ADDING

  function getExercises(){ //gets the exercises that hte user has from the database
    $.ajax({
      type: "POST",
      dataType: "JSON",
      data:({value: $("#exerciseSearch").val()}),
      url: "ajaxPages/getExercisesWithEntries.php",

      success: function(data){ //data is the echo'd returned by the url
        $("#exerciseNames").empty();
        if (data.length !== 0){ //if the array that is returned contains things then display them
          $.each(data,function(){
              $("#exerciseNames").append("<li id='"+ this["Name"].replace(/\s/g, "").toLowerCase()+"' data-type= '" +this["Type"]+"'data-exerciseid='"+this["ExerciseID"]+"' name='" + this["Name"] +"'>"+ this["Name"] + "</li>"); //STILL NEEDS TO BE CLICKABLE BOXES
          });
          exerciseFound = true;
        }
        else if (data.length === 0 || empty(data)){ //if the array is empty it means that they matched no machines in the db
          $("#exerciseNames").append("<li id='no-exercise-found'>No exercises found with entries called "+$("#exerciseSearch").val()+"</li>");
          exerciseFound = false;
        }
      },
      error: function(xhr,status,error) {
        console.log(xhr);
        console.log(error);
      }
    });
  }

  getExercises(); //run it to display exercises with results

  var earliest;
  var latest;

  //Run when an exercise is clicked

  $("#exerciseNames").on('click', 'li', function () {
    if ($(this).attr("name") != undefined){
      //get dates for the exercise
      $.ajax({
        type: "POST",
        dataType: "JSON",
        async: false, //this needs to be changed in the long run
        data: {name: $(this).attr("name")},
        url: "ajaxPages/getSessionDates.php",

        success: function(data){ //data is the echo'd returned by the url
          earliest = data[0];
          latest = data[1];
          $("#earliest").val(earliest);
          $("#latest").val(latest);
        }
      });
      turnOnSpotlight(this);
    }
  });

  //EVENT LISTENERS

  $("#graphDataTypeSelect").change(function(){
    //values that can have the unit changed
    if($("#graphDataTypeSelect").val()=="maxAmount"){
      displayUnitInput($("#exerciseName").attr("data-type"));
    }
    else{
      $(".unitInputs").fadeOut("fast");
    }
    callGraphCreation();
  });

  $("#amountUnitInput").change(function(){
    callGraphCreation();
  });

  $("#timeUnitInput").change(function(){
    callGraphCreation();
  });

  $("#earliest").change(function(){
    callGraphCreation();
  });

  $("#latest").change(function(){
    callGraphCreation();
  });

  $("#changeExercise").click(function() {
    turnOffSpotlight();
  });

  $("#exerciseSearch").on('keyup', function() {
    getExercises();
  });

  $(".datepicker").keypress(function(e) {
      e.preventDefault();
  });

  $(window).resize(function() { //when the pages size changes
      if(this.resizeTO) clearTimeout(this.resizeTO); //if it is changing stop duplicate triggers being created
      this.resizeTO = setTimeout(function() { //inserts a break every 500ms of attempting to create a trigger
          $(this).trigger('resized'); // creates a trigger called resized when
      }, 500);
  });

  //redraw graph when window resize is completed
  $(window).on('resized', function() { //this still needs to be changed as querying db every refresh
    if (graphDrawn){ //this is so that it does not try to draw a graph before the user has selected one, thus causing an error
      callGraphCreation() //redraw the graph to fill the new div's size
    }
  });

});
