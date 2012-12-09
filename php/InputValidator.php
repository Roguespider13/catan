<?php
    class InputValidator {
        public function InputValidator() { }

        public function validateUsername($userName) {
            return preg_match("#^[a-zA-Z0-9]{5,20}$#", $userName);
        }

        public function validatePassword($password) {
            return preg_match("#^[a-zA-Z0-9]{15,50}$#", $password);
        }
	}
?>
