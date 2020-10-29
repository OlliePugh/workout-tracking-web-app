$(document).ready(function(){ //runs this function when the page has fully loaded
  var currentYear = new Date().getFullYear(); //gets the current year
  function returnValue(option){ //this allows me to return a value from an undefined function
    if (option){return true;}
    else{return false;}
  }

  function formValid(){
    let inputValidArray = []
    for (i = 0; i < inputObjectArray.length; i++){ //loops the amount of times that there are input objects
      inputValidArray.push(inputObjectArray[i].auth()); //runs the display error on each input object
    }
    if (!inputValidArray.includes(false)){
      return true;
    }
    else{ return false; }
  }

  class Input{ //this makes a class that all inputs should be assigned to
    constructor(input, error){
      this.inputBox = input; //jQuery Object of the input box
      this.errorText = error; //jQuery Object of the error text
      this.defaultErrorString = this.errorText.text();
      this.required = (this.inputBox.prop("required") || (this.inputBox.attr("data-required") == "true")) //return true if the inputBox has attribute required and false if not
      this.unique = this.inputBox.attr("data-unique"); //USED FOR KNOWING IF NEEDED TO QUERY IN DATABASE
      if (this.unique != ""){
        this.dbTable = this.inputBox.attr("data-table");
      }
      this.errorText.hide(); //hides the error text
      this.liveErrors(); //starts listener that updates error text when clicking out of the input box
    }
    displayError(){ //function that will decide whether the error text needs displaying
      var valid = this.auth(); //sees if the value of the input box is valid
      //QUERY DB TO SEE IF TAKEN
      if (!valid){
        this.errorText.slideDown("fast"); //shows the error text
      }
      else{ //runs if the value is valid
        this.errorText.slideUp("fast"); //hides the error text
      }
    }
    auth(){
      try{var value = this.inputBox.val();}
      catch(err){var value = undefined;} //asssigns the value of the input box to 'value'
      if ((!this.required && value=="") || this.isValid(value)){ //if not required the value can be nothing, but if it is required then the value must be validated
        if((this.unique==undefined || this.unique!="" && this.existsInDB())){
          return true; //says the value is valid
        }
        else{return false;}
      }
      else{ return false;} //says the value is not valid
      }

    liveErrors(){
      this.inputBox.change($.proxy(this, 'displayError'));
      if(this.confirmBox != undefined){
        this.confirmBox.change($.proxy(this, 'displayError')); //this is used so when a new password object is used the confirmed box is checked too
        }
    }
    existsInDB(){ //Only been tested to work with email
      let _this = this;
      var free;
      $.ajax({
        async: false, //had to have async false to allow the value of this function to return true
        type: "POST",
        dataType: "JSON",
        data:({value: _this.inputBox.val()}),
        url: "/ajaxPages/emailInDatabase.php", //this calls the script of the type the value that is stored in html

        success: function(data){ //data is the echo'd returned by the url
          if ($.trim(data) == "Taken"){ //if the value == taken
            _this.errorText.text("An account with that " + _this.unique +" already exists.")
            free = false;
          }
          else if ($.trim(data) == "Free"){
            _this.errorText.text(_this.defaultErrorString);
            free = true;
          }
          else{console.log("Email Validation Error")}
          }
      });
      return free;
    }
  }

  class NameInput extends Input { //declaration of a class that has a parent of class Input
    isValid(val){
      val = $.trim(val);
      return ((val != "") && (val.length <= 35 && val.length > 0 && (RegExp(/^[a-z\-\s]+$/i).test(val)))); //a valid name will make this statement true
    }
  }

  class EmailInput extends Input {
    isValid(val){
      val = $.trim(val);
      return ((val != "") && (val.length < 255 && val.length >= 3) && (/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(val))); //a vaild email will make this statement true
    }
  }

  class NumberInput extends Input{
    constructor(input, error){
      super(input, error); //calls the constructor class of the parent class (Input) with the parameters input and error

      let smallest = parseFloat(this.inputBox.attr("data-min"));
      let biggest = parseFloat(this.inputBox.attr("data-max"));

      if (!isNaN(smallest)){ //checks to see if the value smallest is a number (error trapping)
        this.min = smallest; //assigns the object variable of min to the value smallest
      }
      else { this.min = 0 } //if smallest is not a number then it will be assigned to 0

      if (!isNaN(biggest)){ //checks to see if the value biggest is a number (error trapping)
        this.max = biggest;
      }
    }
    isValid(val){
      val = $.trim(val);
      if ((this.max != undefined && (val >= this.min && val <= this.max)) && ((val != "" && this.required) || (val == "" && !this.required))){ //this statement is true if min and max values ARE specified
        return true;
      }
      else if (this.max == undefined && ((val != "" && this.required) || (val == "" && !this.required))) { //this statement is true if min and max values are NOT specified
        return true;
      }
      else{ return false; }
    }
  }

  class TextInArrayInput extends Input{ //HAS NOT BEEN TESTED
    constructor (arr){
      super(input,error);
      this.arrayToSearch = arr;
      this.liveErrors();
    }
    isValid(val){
      val = $.trim(val);
      return (arrayToSearch.includes(val));
    }
  }

  class MonthInput extends Input{
    isValid(val){
      val = $.trim(val);
      return(val <= 12 && val > 0);
    }
  }

  class YearInput extends Input {
    isValid(val){
      val = $.trim(val);
      let old = this.inputBox.attr("data-oldest");
      return (val <= currentYear && val > currentYear - old);
    }
  }

  class PhoneInput extends Input{
    isValid(val){
      val = $.trim(val);
      return ((val != "") && (val.length == 11 && (/^\d+$/.test(val)) && (val.substring(0,2) == "07")));
    }
  }

  class NewPasswordInput extends Input {
    constructor(input, error){
      super(input, error)
      this.confirmBox = $("#" + this.inputBox.attr("data-pass-confirm"));
      this.liveErrors();
    }

    isValid(val){
      let confirmVal = this.confirmBox.val();
      let passValidity = checkPassword(val);
      if ((val == confirmVal) && (passValidity === true)){
        return true;
      }
      else if (passValidity !== true) {
        this.errorText.text(passValidity);
        return false;
      }

      else if(val != confirmVal){
        this.errorText.text("Passwords do not match")
        return false
      }
      else{console.log("Password check error")}
    }
   }

  class RadioInput extends Input{
    isValid(val){
      val = $.trim(val);
      return (($("input[name="+this.inputBox.attr("data-radio-name")+"]:checked").val()) != undefined);
    }
  }

  class DateInput {
    constructor(dayInput,monthInput, yearInput, error){
      this.dayInput = dayInput;
      this.monthInput = monthInput;
      this.yearInput = yearInput;
      this.errorText = error;
      this.liveErrors();
    }

    auth(){
      let day = Number(this.dayInput.val());
      let month = Number(this.monthInput.val());
      let year = Number(this.yearInput.val());
      let date = new Date(`${year}-${month}-${day}`); //https://medium.com/@esganzerla/simple-date-validation-with-javascript-caea0f71883c
      let isValidDate = (Boolean(+date) && date.getDate() == day);

      return ((isValidDate) && (year > (new Date().getFullYear() - 125)));
    }

    displayError(){ //function that will decide whether the error text needs displaying
      var valid = this.auth(); //sees if the value of the input box is valid
      if (!valid){
        this.errorText.slideDown("fast"); //shows the error text
      }
      else{ //runs if the value is valid
        this.errorText.slideUp("fast"); //hides the error text
      }
    }

    liveErrors(){
      this.dayInput.change($.proxy(this, 'displayError'));
      this.monthInput.change($.proxy(this, 'displayError'));
      this.yearInput.change($.proxy(this, 'displayError'));
    }
  }

  //Creating all of the input objects
  var inputArray = document.getElementsByClassName("validate");
  var inputObjectArray= [];

  for (i=0; i<(inputArray.length); i++){
    inputType = inputArray[i].getAttribute("data-input-type");
    switch (inputType) {
      case "name":
        inputObjectArray.push(new NameInput($("#"+inputArray[i].getAttribute("id")),$("#"+inputArray[i].getAttribute("data-error")))); //creates a new name input object
        break;
      case "email":
        inputObjectArray.push(new EmailInput($("#"+inputArray[i].getAttribute("id")),$("#"+inputArray[i].getAttribute("data-error")))); //creates a new email input object
        break;
      case "number":
        inputObjectArray.push(new NumberInput($("#"+inputArray[i].getAttribute("id")),$("#"+inputArray[i].getAttribute("data-error")))); //creates a new number input object
        break;
      case "phone":
        inputObjectArray.push(new PhoneInput($("#"+inputArray[i].getAttribute("id")),$("#"+inputArray[i].getAttribute("data-error"))));
        break;
      case "newPassword":
        inputObjectArray.push(new NewPasswordInput($("#"+inputArray[i].getAttribute("id")),$("#"+inputArray[i].getAttribute("data-error"))));
        break;
      case "radio":
        inputObjectArray.push(new RadioInput($("#"+inputArray[i].getAttribute("id")),$("#"+inputArray[i].getAttribute("data-error"))));
        break
      case "date":
        inputObjectArray.push(new DateInput($("#"+inputArray[i].getAttribute("id")+" #dobDay"),$("#"+inputArray[i].getAttribute("id")+" #dobMonth"),$("#"+inputArray[i].getAttribute("id")+" #dobYear"),$("#"+inputArray[i].getAttribute("data-error"))));
        break
      default: //this runs if there was not a data type that matched what was requested
        console.log("Invaild Data Type to Validate: " + inputType)
    }
  }

  $("#submitButton").click(function(){
    for (i = 0; i < inputObjectArray.length; i++){ //loops the amount of times that there are input objects
      inputObjectArray[i].displayError(); //runs the display error on each input object
    }
    if (formValid()){
      $("form").submit()
    }
  });
});
