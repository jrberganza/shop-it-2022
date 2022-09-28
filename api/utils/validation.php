<?php

function validateObj(\stdClass $toCheck, array $expected)
{
    foreach ($expected as $name => $what) {
        if (isset($what["optional"]) && !$what["optional"] && !isset($toCheck->$name)) {
            return "Expected " . $what["type"] . " " . $name;
        }

        if (isset($toCheck->$name)) {
            if (gettype($toCheck->$name) != $what["type"]) {
                return "Expected " . $name . " to be a " . $what["type"];
            }

            if (isset($what["maxLength"]) && strlen($toCheck->$name) > $what["maxLength"]) {
                return "Expected " . $name . " to be at most " . $what["maxLength"] . " characters";
            }

            if (isset($what["minLength"]) && strlen($toCheck->$name) < $what["minLength"]) {
                return "Expected " . $name . " to be at least " . $what["minLength"] . " characters";
            }

            if (isset($what["max"]) && $toCheck->$name > $what["max"]) {
                return "Expected " . $name . " to be at most " . $what["max"];
            }

            if (isset($what["min"]) && $toCheck->$name < $what["min"]) {
                return "Expected " . $name . " to be at least " . $what["min"];
            }
        }
    }

    return false;
}
