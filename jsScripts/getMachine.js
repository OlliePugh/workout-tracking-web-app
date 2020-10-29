$( document ).ready(function() {

  var types = ["amount","time"];

  function textValidation(val,min,max){
    if (val != undefined && val.length > min && val.length < max ){
      return true;
    }
    else{
      return false;
    }
  }

  function textInArray(val,arr){
    return arr.includes(val);
  }

  function toggleMachineChosenOn(m){
    $("#machine-selector").fadeOut("fast");
    $("#machine-name-manage").fadeIn("fast");
    $("#machine-name").text(m);
    $("#machineInput").val(m);
  }

  function toggleMachineChosenOff(){
    $("#machineInput").val("");
    getMachines();
    $("#machine-selector").fadeIn("fast");
    $("#machine-name-manage").fadeOut("fast");
    $("#machine-name").text("");
  }

  class Input{
    constructor(input, error){
      this.inputBox = input; //jQuery Object of the input box
      this.errorText = error; //jQuery Object of the error text

      this.inputBox.change($.proxy(this, 'displayError'));
    }
  }

  class textInput extends Input{
    constructor(input,error,min,max){
      super(input,error)
      this.minimum = min;
      this.maximum = max;
    }
    isValid(){
      if(textValidation(this.inputBox.val(),this.minimum,this.maximum)){
        return true;
      }
      else{
        return false;}
    }
    displayError(){ //function that will decide whether the error text needs displaying
      var valid = textValidation(this.inputBox.val(),this.minimum,this.maximum); //sees if the value of the input box is valid
      if (!valid){
        console.log(this.inputBox.val())
        this.errorText.slideDown("fast"); //shows the error text
      }
      //else if TAKEN && this.unique{SHOW DIFFERENT MESSAGE}
      else{ //runs if the value is valid
        this.errorText.slideUp("fast"); //hides the error text
      }
    }
  }

  class TextInArrayInput extends Input{
    constructor(input,error,arr){
      super(input,error);
      this.arrayToSearch = arr;
    }
    displayError(){ //function that will decide whether the error text needs displaying
      var valid = textInArray(this.inputBox.val(),this.arrayToSearch); //sees if the value of the input box is valid
      if (!valid){
        this.errorText.slideDown("fast"); //shows the error text
      }
      //else if TAKEN && this.unique{SHOW DIFFERENT MESSAGE}
      else{ //runs if the value is valid
        this.errorText.slideUp("fast"); //hides the error text
      }
    }
    isValid(){
      if(textInArray(this.inputBox.val(),this.arrayToSearch)){
        return true;
      }
      else{
        return false;}
    }
  }

  $("#machineTable tbody").on('click', 'tr', function () {
    if($(this).attr("name")!=undefined){
      toggleMachineChosenOn($(this).attr("name"));
    }
  });

   var machineFound = false;

  function getMachines(){ //this function will change the table that contains all of the unique machines
    $.ajax({
      type: "POST",
      dataType: "JSON",
      data:({value: $("#machineInput").val()}),
      url: "ajaxPages/getMachines.php",

      success: function(data){ //data is the echo'd returned by the url

        $("#machineTable tbody").empty();
        if (data.length !== 0){ //if the array that is returned contains things then display them
          for (i=0;i<data.length;i++){
            $("#machineTable tbody").append("<tr id='"+ data[i].replace(/\s/g, "").toLowerCase()+"' name='" + data[i] +"'><td>"+ data[i]+ "</td></tr>"); //STILL NEEDS TO BE CLICKABLE BOXES
          }
          machineFound = true;
        }
        else if (data.length === 0){ //if the array is empty it means that they matched no machines in the db
          $("#machineTable tbody").append("<tr><td>No machines found called "+$("#machineInput").val()+"</td></tr>");
          machineFound = false;
        }
      }
    });
  }


  getMachines(); //this will update the machines list when the page opens

  $("#machineInput").on('keyup', function() {
    getMachines();
  });

  $("#machine-change").click(function(){
    toggleMachineChosenOff();
  });

  nameObject = new textInput($("#nameInput"),$("#nameInvalid"),0,255);
  typeObject = new TextInArrayInput($("#typeInput"),$("#typeInvalid"),types);
  var isNew;

  $("#createButton").click(function(){
    nameObject.displayError();
    typeObject.displayError();

    $.ajax({
      type: "POST",
      dataType: "JSON",
      async: false,
      data:({value: $("#nameInput").val()}),
      url: "ajaxPages/newExerciseExists.php",

      success: function(data){ //data is the echo'd returned by the url
        if (data != 0){ //if a result is found in the database an error message needs to be shown
          $("#exerciseExists").slideDown("fast");
          isNew = false;
        }
        else{ //if no value is found in the database hide the error message
          $("#exerciseExists").slideUp("fast");
          isNew = true;
        }
      }
    });
      if (nameObject.isValid(1,255) && typeObject.isValid(1,25) && machineFound && isNew){
        $("form").submit();
      }
    });
});
