<?php
    require_once 'InputValidator.php';

    class User {
        private $username;
        private $validator;

        public function User() {
            $this->username = "";
            $this->validator = new InputValidator();
        }

        public function setUsername($username) {
            if ($this->validator->validateUsername($username)!== 1)
                throw new Exception("Invalid username or password");

            // Throw exception if username is already taken

            $this->username = $username;
        }

        public function checkPassword($p) {
            if ($this->validator->validatePassword($p)!== 1)
                throw new Exception("Invalid username or password");
        }

        public function comparePasswords($p1, $p2) {
            $this->checkPassword($p1);
            if ($p1 !== $p2)
                throw new Exception("Passwords do not match");
        }
    }
?>
