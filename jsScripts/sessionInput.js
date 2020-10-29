$(document).ready(function(){ //runs this function when the page has fully loaded
  var maxSets = 25;
  var sessionArray = [];
  var currentDate = function(){ //Function by Samuel Meadows: https://stackoverflow.com/questions/1531093/how-do-i-get-the-current-date-in-javascript
    let today = new Date();
    let dd = today.getDate();
    let mm = today.getMonth() + 1; //January is 0!
    let yyyy = today.getFullYear();

    if (dd < 10) {
      dd = '0' + dd;
    }

    if (mm < 10) {
      mm = '0' + mm;
    }

    return (dd + '-' + mm + '-' + yyyy);
  }

  $("#datepicker").val(currentDate);

  class Exercise {
    constructor(exerciseName){
      this.localID = undefined; //called local id because it is not referncing any value in the database
      this.name = exerciseName;
    }
  }

  class AmountExercise extends Exercise{
    constructor(exerciseName, repAmount, weightAmount, exerciseUnit){
      super(exerciseName); //this calls the parents constrcutor class
      this.type="amount";
      this.reps = repAmount;
      this.weight = weightAmount;
      this.unit = exerciseUnit;
    }
  }

  class TimeExercise extends Exercise{
    constructor(exerciseName, timeDone, exerciseUnit){
      super(exerciseName);
      this.type="time";
      this.time = timeDone;
      this.unit= exerciseUnit;
    }
  }

  class Input{
    constructor(input, error){
      this.inputBox = input; //jQuery Object of the input box
      this.errorText = error; //jQuery Object of the error text

      this.inputBox.change($.proxy(this, 'displayError'));
      this.liveErrors();
    }
    displayError(){
      if (!this.isValid()){
        this.errorText.slideDown("fast"); //shows the error text
      }
      else{ //runs if the value is valid
        this.errorText.slideUp("fast"); //hides the error text
      }
    }
    liveErrors(){
      this.inputBox.change($.proxy(this, 'displayError'));
      if(this.confirmBox != undefined){
        this.confirmBox.change($.proxy(this, 'displayError')); //this is used so when a new password object is used the confirmed box is checked too
        }
    }
  }

  class NumberInput extends Input{
    constructor(input,error,min,max){
      super(input,error);
      this.minimum = min; //minimum value the number can be
      this.maximum = max;
    }
    isValid(){
      if((this.inputBox.val().replace(/^\s+|\s+$/g, '')!="") && !isNaN(this.inputBox.val()) && (this.inputBox.val() >= this.minimum) && (this.inputBox.val() <= this.maximum)){ //makes sure the value is valid
        return true;
      }
      else{
        return false;
      }
    }
  }

  class IntegerInput extends Input{
    constructor(input,error,min,max){
      super(input,error);
      this.minimum = min; //minimum value the number can be
      this.maximum = max;
    }
    isValid(){
      if ((this.inputBox.val().replace(/^\s+|\s+$/g, '')!="") && !isNaN(this.inputBox.val()) && (this.inputBox.val() >= this.minimum) && (this.inputBox.val() <= this.maximum) && (this.inputBox.val() == parseInt(this.inputBox.val(), 10))) { //makes sure the value is valid
        return true;
      }
      else{
        return false;
      }

    }
  }

  class dateInput extends Input{
    constructor(input,error){
      super(input,error)
    }
    isValid(){
      if ((this.inputBox.val() != undefined && this.inputBox.val() != "" && this.inputBox.val().length == 10)){
        return true;
      }
      else{
        return false;
      }
    }
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

  //creating the input objects

  dateInput = new dateInput($("#datepicker"),$("#dateInvalid"));
  //Amount Inputs
  repInput = new IntegerInput($("#repInput"),$("#repInvalid"),1,100000000000);
  weightInput = new NumberInput($("#weightInput"),$("#weightInvalid"),0,2000000);
  amountSetsInput = new IntegerInput($("#amountSetsInput"),$("#amountSetsInvalid"),1,maxSets);

  //Time Inputs
  timeDoneInput = new NumberInput($("#timeDoneInput"),$("#timeInvalid"),1,20000000);

  getUnits("Amount");
  getUnits("Time");

  $("#nameInput").on('keyup', function() {
    getExercises();
  });

  $("#exerciseTable tbody").on('click', 'tr', function () {
    if ($(this).attr("name") != undefined){
      turnOnSpot(this);
    }
  });

  $("#changeExercise").click(function(){ //triggered when the 'Change Exercise' button is pressed
    turnOffSpot();
  });

  var previousExerciseID = 0; //sets the default value of previous exercise id

  function arrangeSessionList(entryType){ //this will check that the list is in the correct order and all sets are grouped correctly
    switch(entryType){
      case undefined: //this will run if you are just appending a new exercise to the table
      exerciseBeingAdded = sessionArray[sessionArray.length-1];
      break;

      default:
      exerciseBeingAdded = entryType;
    }
    exerciseBeingAdded.localID = previousExerciseID;
    previousExerciseID++;

    let headerID = (exerciseBeingAdded.name).toLowerCase().replace(/\s/g, '')+"ListHeader"; //creates the template for the exercist list headers id's

    function addExerciseHeaderToList(){
      $("#sessionList").append("<div class='exercise-wrapper'><li id='"+headerID+"' class='exercise-header'>"+exerciseBeingAdded.name+"</li><ul></ul></div>") //adds header to the table with the name of the exercise
    }

    function addSetToList(){
        if(exerciseBeingAdded instanceof AmountExercise){
          $("#"+headerID).next().append(amountAppend); //Appends a list item to the bottom of the list
        }
        else if (exerciseBeingAdded instanceof TimeExercise){
          $("#"+headerID).next().append(timeAppend); //Appends a list item to the bottom of the list
        }
      }

    //declaring the html data that needs to be appended
    let amountAppend = ("<li class='session-set' data-localid='"+exerciseBeingAdded.localID+"'>"+exerciseBeingAdded.reps+" x "+exerciseBeingAdded.weight+" "+exerciseBeingAdded.unit+"<img src='css/images/delete.png' title='Delete' alt='Delete Exercise' class='deleteButton' data-exercise-id='"+exerciseBeingAdded.localID+"'></li>");
    let timeAppend = ("<li class='session-set' data-localid='"+exerciseBeingAdded.localID+"'> 1 x "+exerciseBeingAdded.time+" "+exerciseBeingAdded.unit+"<img src='css/images/delete.png' alt='Delete Exercise' title='Delete' class='deleteButton' data-exercise-id='"+exerciseBeingAdded.localID+"'></li>");

    if($("#"+headerID).length == 0){ //if no list header is found with the name of the exercise
      addExerciseHeaderToList(); //creates a header
      addSetToList(); //appends to the header that was created in the line above
    }
    else{
      addSetToList(); //appends to the table header of the exercise
    }
  }

  function recreateSessionList(){ //this will run if the entire table needs recreating
    previousExerciseID = 0; //restarts the previous exercise id counter as the id's of the list will
    $("#sessionList").empty();
    $.each(sessionArray, function(){ //will run for the length of sessionArray
      arrangeSessionList(this)
    });
  }

  function deleteSessionSet(id){
    sessionArray.splice(id,1); //removes the object at the index of the value the buttons attribute: 'data-localid' that was clicked
    recreateSessionList(); //recreates the session list with the changes made
  }

  $(document).on("click",".deleteButton",function(event) { //this will run everytime a delete button is clicked
    event.stopPropagation(); //clears all current handlers for the buttons and recreates them because it will add an event to all of the buttons everytime one is added
    event.stopImmediatePropagation(); //doesnt call listeners from the same event being called, just the one that was clicked
    deleteSessionSet($(this).attr("data-exercise-id"));
  });

  function validateSessionInput(type){
    if (type == "amount"){ //this will validate the amount type exercise inputs
      if (dateInput.isValid() && repInput.isValid() && weightInput.isValid() && amountSetsInput.isValid()){ return true; }
      else{
        repInput.displayError();
        weightInput.displayError();
        amountSetsInput.displayError();
        return false;
      }
    }
    else if(type =="time"){ //this will validate the time type exercise inputs
      if (dateInput.isValid() && timeDoneInput.isValid()){return true;}
      else {
        timeDoneInput.displayError();
        return false;}
    }
    else{ //this will run if it is not a time or amount type exercise (An unknown type)
      console.log("Unknown exercise type: "+type);
      return false;
    }
  }

  $("#addToSession").click(function(){
    if ($("#addToSession").hasClass("time") && $("#addToSession").hasClass("amount")){
      console.log("TOO MANY EXERCISE TYPES");
    }
    else if ($("#addToSession").hasClass("amount")){ //if the add to session button has class "amount" it runs
      if (validateSessionInput("amount")){
        for (i = 0; i < $("#amountSetsInput").val(); i++) {
        sessionArray.push(new AmountExercise($("#exerciseName").text(), $("#repInput").val(),$("#weightInput").val(),$("#amountUnitInput").val())); //I use the length of the array because arrays start at zero, therefore the length is the next slot that will be used
        arrangeSessionList();
        }
      }
    }
    else if ($("#addToSession").hasClass("time")){//if the add to session button has class "time" it runs
      if (validateSessionInput("time")){ //makes a new timed exercise for the amount of sets that were performed with the same data (time performing exercise)
        sessionArray.push(new TimeExercise($("#exerciseName").text(), $("#timeDoneInput").val(),$("#timeUnitInput").val()));
        arrangeSessionList();
      }
    }
    else{
      console.log("NO EXERCISE TYPE SPECIFIED IN DB"); //unknown exercise type is stored on the button
    }
  });

  function turnOnSpot(object){ //shows the spotlight div
    $("#changeExercise").fadeIn("fast").css('display', 'block');
    $("#exerciseTable").fadeOut("fast");
    $("#nameInput").fadeOut("fast");
    $("#exerciseName").text($(object).attr("name"));
    $("#nameInput").val("");
    $("#exerciseName").fadeIn("fast");
    $("#addToSession").fadeIn("fast").css('display', 'block');
    $("#table-wrapper").fadeOut("fast");
    if ($(object).attr("data-type") == "amount"){ //shows the amount spotlight type
      $("#amountTypeInput").fadeIn("fast");
    }
    else if ($(object).attr("data-type") == "time"){ //shows the time spotlight type
      $("#timeTypeInput").fadeIn("fast");
    }
    else{
      turnOffSpot();
      console.log("INVALID EXERCISE TYPE: "+$(object).attr("data-type"));
    }
    $("#addToSession").addClass($(object).attr("data-type"));
    }

  function turnOffSpot(){ //shows the spotlight div
    $(".invalidInput").fadeOut("fast");
    $("#changeExercise").fadeOut("fast");
    $("#table-wrapper").fadeIn("fast");
    $("#exerciseTable").fadeIn("fast");
    $("#nameInput").fadeIn("fast");
    $("#addToSession").fadeOut("fast");
    $("#exerciseName").fadeOut("fast");
    $(".spotLightInputs").fadeOut("fast");
    $(".spotLightInputs input").val("");
    $('#addToSession').removeClass("time");
    $('#addToSession').removeClass("amount");
    $(".setsInput").val(1);
  }

  function getExercises(){ //gets the exercises that hte user has from the database
    $.ajax({
      type: "POST",
      dataType: "JSON",
      data:({value: $("#nameInput").val()}),
      url: "ajaxPages/getExercises.php",

      success: function(data){ //data is the echo'd returned by the url
        $("#exerciseTable tbody").empty();
        if (data.length !== 0){ //if the array that is returned contains things then display them
          $.each(data,function(){
              $("#exerciseTable tbody").append("<tr id='"+ this["Name"].replace(/\s/g, "").toLowerCase()+"' data-type= '" +this["Type"]+"'data-exerciseid='"+this["ExerciseID"]+"' name='" + this["Name"] +"'><td>"+ this["Name"] + "</td></tr>"); //STILL NEEDS TO BE CLICKABLE BOXES
          });
          exerciseFound = true;
        }
        else if (data.length === 0 || empty(data)){ //if the array is empty it means that they matched no machines in the db
          $("#exerciseTable tbody").append("<tr id='no-exercise-found'><td>No exercises found called "+$("#nameInput").val()+"</td></tr>");
          $("#exerciseTable tbody").append("<tr><td><a href=exerciseMaker.php?exercise="+$("#nameInput").val()+" id='create-new-exercise' target='_blank'>Create an exercise called "+$("#nameInput").val()+"</a></td></tr>");
          exerciseFound = false;
        }
      }
    });
  }

  $("#submitSession").click(function(){
    console.log(dateInput);
    if (dateInput.isValid()) { //this will need to be changed when gyms are added into the equation
      $.ajax({
        type:"POST",
        dataType:"JSON",
        async: false, //this is required as the user would be able to double click and add the session multiple times
        data: ({session: sessionArray, date: dateInput.inputBox.val()}),
        url: "phpScripts/createSession.php", //stored in phpScripts because may need to be used not as an ajax request

        success: function(data){
          switch(data){
            case "invalid":
              $("#phpValidationFalse").fadeIn("fast");
              break;
            case "success":
              window.location.href = "../flex-fit/success.php?system=session%20add";
              break;
            default:
              console.log("Unknown Response: "+data);
          }
        },
        error: function(req) {
                alert('Error: ' + req.status);
            }
      })
    }
    else {
      dateInput.displayError();
    }
  });
  getExercises();
});
