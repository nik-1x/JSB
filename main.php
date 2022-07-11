<?php

require_once "JSBInit.php";

$db = new JSBJson("Users", "if not exists", false, true);

$db->columns([
    ["id", "integer", "AUTO_INCREMENT"],
    ["first_name", "string"],
    ["last_name", "string"],
    ["middle_name", "string"]
]);

$db->add(["Name1", "LastName1", "MiddleName1"]); // Adding user
$db->add(["Name2", "LastName2", "MiddleName2"]); // Adding user

// EXPORT ALL DATA:
echo json_encode($db->export(), JSON_PRETTY_PRINT);
// GET BY NAME: echo $db->get_by_value("first_name", "Name1");
// GET BY ID: echo json_encode($db->get_by_ids(0), 128);
// GET BY IDs: echo json_encode($db->get_by_ids([0, 1]), 128);
