<?php
/*
 * Register Validator Class
 * @author    Alexander Høi Nielsen
 * @url       http://Prohoei.dk
 * Version	  1.0
 */
 
class validatorClass {

    private static $fejl = array();

    public static function getFejl() {
        return self::$fejl;
    }
	
    public static function sameInput($input, $input2, $name = "") {
        if ($input == $input2) {
            return true;
        }
        self::$fejl[$name] = "Password doesn't match";
        return false;
    }
    
    public static function lenghtInput($input, $name = "") {
		$new = strlen($input);
        if ($new > 5) {
            return true;
        }
        self::$fejl[$name] = "Password to short! minimum 6 letters";
        return false;
    }
	
	public static function hasnumberandletter($input, $name = ""){
        if(preg_match('/[A-Za-z]/', $input)
                && preg_match('/[0-9]/', $input)){
            return true;
        }
        self::$fejl[$name] = "Password requires at least 1 number and 1 letter";
        return false;
    }
	
	public static function validMail($mail, $name = ""){
        if(!filter_var($mail, FILTER_VALIDATE_EMAIL) === false){
            return true;
        }
        self::$fejl[$name] = $mail." is not a valid email address";
        return false;
    }
}