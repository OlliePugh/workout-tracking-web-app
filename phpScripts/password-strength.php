<?
function checkPassword($pwd) {
    if (strlen($pwd) < 8) {
        return("Password too short");
    }
    if (!preg_match("~[0-9]~", $pwd)) {hp 
    }
    if (!preg_match("~[a-zA-Z]~", $pwd)) {
        return("Password must include at least one letter");
    }
    return (true);
}
//modified https://stackoverflow.com/questions/10752862/password-strength-check-in-php
?>
