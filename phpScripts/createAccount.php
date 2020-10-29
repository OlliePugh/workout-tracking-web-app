<?php
  class User{
    public $firstName;
    public $surname;
    public $email;
    public $password;
    public $confirmPass;
    public $gender;
    public $dobDay;
    public $dobMonth;
    public $dobYear;
    public $dob;
    public $token;
    function __construct($fN, $sN, $mail, $pass, $passConf, $sex, $birthDay, $birthMonth, $birthYear,$mob) {
      $this->firstName = trim(ucwords(strtolower($fN))); //ucwords capitalises each of the first letters
      $this->surname = trim(ucwords(strtolower($sN))); //trim removes any whitepace before or after the content starts
      $this->email = trim(strtolower($mail));
      $this->password = $pass;
      $this->hashPassword = md5($pass);
      $this->confirmPass =$passConf;
      $this->gender = $sex;
      $this->dobDay = $birthDay;
      $this->dobMonth = $birthMonth;
      $this->dobYear = $birthYear;
      $this->dob = ($birthYear."-".$birthMonth."-".$birthDay);
      $this->mobile = $mob;
      $this->token = uniqid();
    }
  }

  $user = new User($_POST["fName"],$_POST["sName"],$_POST["email"],$_POST["password"],$_POST["passwordConfirm"],$_POST["gender"],$_POST["dobDay"],$_POST["dobMonth"],$_POST["dobYear"],$_POST["mobile"]);
  $msg = "";

  function nameInput($val) {
    return ((isset($val)) && ((strlen($val) <= 35 && strlen($val) > 0)) && (preg_match("/^[a-z\-\s]+$/i",$val)));
  }

  function emailInput($val) {
    include "connectToDB.php";
    $stmt = $mysqli->prepare('SELECT * FROM users WHERE Email = ?'); //check if already in database
    $stmt->bind_param("s",$val);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    return (($result->num_rows == 0) && (isset($val)) && ((strlen($val) <= 255 && strlen($val) > 3)) && (filter_var($val, FILTER_VALIDATE_EMAIL)));
  }

  function numberInput($val,$min,$max){
    return ((isset($val)) && ((floatval($val) <= $max && floatval($val) > $min)) && !(ctype_alpha($val)));
  }

  function dateInput($day,$month,$year){
    if ((date("Y") - 125) < intval($year)){ #assumes oldest person is 125 years old
      return (checkdate(intval($month),intval($day),intval($year))); //data acctually exists
    }
    else{return false;}
  }

  function passwordInput($val,$confVal){
    include "password-strength.php";
    return(($val == $confVal) && (checkPassword($val) === true)); //checkPassword will return string if false or return true if it is valid
  }

  function mobileInput($val){
    return ((empty($val)) || ((!empty($val)) && (strlen($val) == 11) && (substr($val, 0, 2) == "07") && is_numeric($val)));
    }

  if (!nameInput($user->firstName)){
    $msg = ($msg."</br>Invalid First Name.");
  }

  if (!nameInput($user->surname)){
    $msg = ($msg."</br>Invalid Surname.");
  }

  if (!emailInput($user->email)){
    $msg = ($msg."</br>Invalid Email.");
  }

  if (!passwordInput($user->password,$user->confirmPass)){
    $msg = ($msg."</br>Invalid Password.");
  }

  $genderOptions = ["m","f","o"];
  if (!in_array($user->gender,$genderOptions)){ //ensures the gender entered is a recognised gender
    $msg = ($msg."</br>Unrecognised Gender.");
  }

  if (!dateInput($user->dobDay,$user->dobMonth,$user->dobYear)){
    $msg = ($msg."</br>Invalid Email.");
  }

if (!mobileInput($user->mobile)){
    $msg = ($msg."</br>Invalid Mobile Number.");
  }

  if (empty($msg)){ //this means all data is valid and we should send it to the database
    $firstTitle = ucwords(strtolower($user->firstName)); //making lowercase then title the variableb 
    $surnameTitle = ucwords(strtolower($user->surname));
    $lowerEmail = strtolower($user->email);
    if(empty($user->mobile)){
      $user->mobile = null;
    }
    include "connectToDB.php";
    $stmt = $mysqli->prepare('INSERT INTO users (FirstName,Surname,Email,Dob,Gender,Mobile) VALUES (?,?,?,?,?,?);'); #creates a query that uses place holders
    $stmt->bind_param("ssssss", $firstTitle, $surnameTitle, $lowerEmail, $user->dob, $user->gender, $user->mobile); #replaces the ? with each variable, and will be seen as a string, not as a command
    $stmt->execute(); #executes the statement (stmt)
    $latestID=($mysqli->insert_id);
    $stmt = $mysqli->prepare('INSERT INTO passwords (UserID,hashedPass) VALUES (?,?)');
    $stmt->bind_param("ss",$latestID,$user->hashPassword);
    $stmt->execute();
    $stmt = $mysqli->prepare('INSERT INTO unverified_users (UserID,Token) VALUES (?,?)'); //adds the user to the unverified table
    $stmt->bind_param("ss",$latestID, $user->token);
    $stmt->execute();
    $stmt->close(); #closes the connection to the db

    $email = $user->email;
    $sender = "verify-email@flex-fit.co.uk";
    $subject = "Authenticate Account";
    $message = "Click link to authenticate account: https://www.flex-fit.co.uk/account-verification/verify-account.php?token=".$user->token;
    $headers = 'From:' . $sender;
    //$headers = "Content-Type: text/html; charset=UTF-8\r\n";  // (https://stackoverflow.com/questions/11238953/send-html-in-email-via-php)

    mail($email, $subject, $message, $headers);

    header("Location: ../account-verification/email-sent.php?email=".$email);
    exit;
  }
  else{
    header("Location: ../signup.php?state=rejected");
    exit;
  }
?>
