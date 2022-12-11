<?php

require_once "vendor/tpl.php";
require_once "functions.php";
require_once "personInfo.php";

$cmd = "show_list_page";
if (isset($_GET["cmd"])) {
    $cmd = $_GET["cmd"];
}

if (isset($_GET["person_id"])) {
    $personId = $_GET["person_id"];
}

if (isset($person)) {
    $person = newPersonWithNoID("", "", [null, null, null]);
}

$errors = [];

if ($cmd === "edit") {
    $firstName = $_POST["firstName"];
    $lastName = $_POST["lastName"];
    $phone = [$_POST["phone1"], $_POST["phone2"], $_POST["phone3"]];

    $errors = validate($firstName, $lastName);

    if (count($errors) > 0) {
        $person = newPerson($_POST["firstName"], $_POST["lastName"], $phone);
        $cmd = "show_edit_page";
    } else {
        editPersonInfo($firstName, $lastName, $phone, $personId);
        header("Location: ?cmd=show_list_page");
    }
}

if ($cmd === "add") {
    $firstName = $_POST["firstName"];
    $lastName = $_POST["lastName"];
    $phone = [$_POST["phone1"], $_POST["phone2"], $_POST["phone3"]];

    $errors = validate($firstName, $lastName);

    if (count($errors) > 0) {
        $person = newPerson($_POST["firstName"], $_POST["lastName"], $phone);
        $cmd = "show_add_page";

    } else {
        addPersonInfo($firstName, $lastName, $phone);
        $personInfo = getPersonInfo(-1);
        header("Location: ?cmd=show_list_page");
    }
}

if ($cmd === "show_edit_page") {

    if (!isset($person)) {
        $personInfo = getPersonInfo($personId);
        $person = array_shift($personInfo);
    }

    $data = ["subTemplate" => "edit.html", 'person' => $person, 'errors' => $errors];
    print renderTemplate("main.html", $data);
}

if ($cmd === "show_list_page") {

    if ($cmd) {
        $personInfo = getPersonInfo(-1);
    }

    $data = ["subTemplate" => "list.html", 'personInfo' => $personInfo];
    print renderTemplate("main.html", $data);

}
if ($cmd === "show_add_page") {
    if (!isset($person)) {
        $person = newPerson("", "", [null, null, null]);
    }

    $data = ["subTemplate" => "add.html", 'person' => $person, 'errors' => $errors];
    print renderTemplate("main.html", $data);

}
