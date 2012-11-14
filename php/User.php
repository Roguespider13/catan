<?php
    require_once 'InputValidator.php';

    class User {
        private $id;
        private $username;
        private $password;
        private $password2;
        private $validator;
        
        public function User() {
            $this->username = "";
            $this->password = "";
            $this->password2 = "";
            $this->customerType = "";
            $this->agreeWithTerms = "";
            $this->validator = new InputValidator();
        }
        
        public function setUsername($username) {
            if ($this->validator->validateUsername($username)!== 1)
                throw new Exception("Invalid username");
            
            // Throw exception if username is already taken
            
            $this->username = $username;
        }

        public function checkPassword($p) {
            if ($this->validator->validatePassword($p)!== 1)
                throw new Exception("Invalid password");
        }
        
        public function comparePasswords($p1, $p2) {
            $this->checkPassword($p1);
            if ($p1 !== $p2)
                throw new Exception("Passwords do not match");
        }
    }
?>