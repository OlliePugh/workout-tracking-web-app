function checkPassword(pw) {

    if (pw.length < 8) {
        return("Password too short");
    }

    if (!pw.match(".*[0-9].*")) {
        return("Password must include at least one number");
    }

    if (!pw.match(".*[a-zA-Z].*")) {
        return("Password must include at least one letter");
    }

    return true;

    //https://regex101.com/ HELP FROM

}
