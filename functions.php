<?php


const USERNAME = 'kpoldr';
const PASSWORD = '8c05';

function editPersonInfo($firstName, $lastName, $phones, $personId) {
    $address = 'mysql:host=db.mkalmo.xyz;dbname=kpoldr';
    $connection = new PDO($address, USERNAME, PASSWORD,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

    if ($connection->errorCode()) {
        return null;
    }

    $stmt = $connection->prepare(
        'UPDATE contacts SET firstName = :firstName, lastName = :lastName where contacts.id = :id');

    $stmt->bindValue(':firstName', $firstName);
    $stmt->bindValue(':lastName', $lastName);
    $stmt->bindValue(':id', $personId);
    $stmt->execute();

    for ($i = 0; $i < 3; $i++) {
        $stmt = $connection->prepare(
            'UPDATE phones SET number = :number where personPhone_id = :pId AND contact_ID = :id');

        $stmt->bindValue(':number', $phones[$i]);
        $stmt->bindValue(':pId', $i + 1);
        $stmt->bindValue(':id', $personId);
        $stmt->execute();
    }

}


function addPersonInfo($firstName, $lastName, $phones) {

    $address = 'mysql:host=db.mkalmo.xyz;dbname=kpoldr';
    $connection = new PDO($address, USERNAME, PASSWORD,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);


    if ($connection->errorCode()) {
        return null;
    }

    $stmt = $connection->prepare(
        'INSERT INTO contacts (firstName, lastName) values (:firstName, :lastName)');

    $stmt->bindValue(':firstName', $firstName);
    $stmt->bindValue(':lastName', $lastName);
    $stmt->execute();

    $stmt = $connection->prepare(
        'SELECT max(id) from contacts');
    $stmt->execute();

    foreach (array_keys($phones, '') as $key) {
        $phones[$key] = null;
    }

    foreach ($stmt as $row) {
        if (array_key_exists('max(id)', $row)) {
            $id = $row['max(id)'];
        }
    }

    $pId = 1;

    foreach ($phones as $phone) {
        $stmt = $connection->prepare(
            'INSERT INTO phones (number , contact_ID, personPhone_id) values (:phone, :id, :pId)');
        $stmt->bindValue(':phone', $phone);
        $stmt->bindValue(':id', $id);
        $stmt->bindValue(':pId', $pId);
        $stmt->execute();
        $pId++;
    }
}

function getPersonInfo($personId) {
    $address = 'mysql:host=db.mkalmo.xyz;dbname=kpoldr';
    $connection = new PDO($address, USERNAME, PASSWORD,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

    if ($personId === -1) {
        $stmt = $connection->prepare(
            'select * from contacts, phones where contacts.id = phones.contact_ID');
        $stmt->execute();

    } else {
        $stmt = $connection->prepare(
            'select * from contacts join phones on contacts.id = phones.contact_ID where 
                contact_ID = :person_id;');

        $stmt->bindValue(':person_id', $personId);
        $stmt->execute();
    }

    $contacts = [];

    foreach ($stmt as $row) {
        if (array_key_exists($row['id'], $contacts)) {
            $contacts[$row['id']]->addPhoneNumber($row['number']);
        } else {
            $firstName = $row['firstName'];
            $lastName = $row['lastName'];
            $phone = $row['number'];
            $currentId = $row['id'];
            $contacts[$row['id']] = new personInfo($firstName, $lastName, $phone, $currentId);
        }
    }

    return $contacts;
}

function newPerson($firstName, $lastName, $phones) {
    $person = new personInfo($firstName, $lastName, array_shift($phones), -1);
    $person->addPhoneNumber(array_shift($phones));
    $person->addPhoneNumber(array_shift($phones));
    return $person;
}

function validate($firstName, $lastName) {
    $errors = [];

    if (strlen($firstName) < 2) {
        $errors[] = "Eesnimi peab olema vähemalt 2 tähemärki.";
    }

    if (strlen($lastName) < 2) {
        $errors[] = "Perekonnanimi peab olema vähemalt 2 tähemärki.";
    }

    return $errors;

}
