<?php

class personInfo
{
    public $firstName;
    public $lastName;
    public $phones = [];
    public $currentID;


    public function __construct($firstName, $lastName, $phones, $currentID) {
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->phones[] = $phones;
        $this->currentID = $currentID;
    }

    public function addPhoneNumber($number) {
        array_push($this->phones, $number);
    }

    public function phonesStrRepresentation() {
        foreach (array_keys($this->phones, null) as $key) {
            unset($this->phones[$key]);
        }
        return join(", ", $this->phones);
    }
}

