<?php

function validateObj(&$toCheck, array $expected)
{
    foreach ($expected as $name => $what) {
        if (!isset($what["optional"])) {
            $what["optional"] = false;
        }

        if (!isset($toCheck->$name) || $toCheck->$name == null) {
            if ($what["optional"]) {
                if (isset($what["default"])) {
                    $toCheck->$name = $what["default"];
                }
                continue;
            } else {
                return "Expected " . $name . " to be a " . $what["type"];
            }
        }

        if (isset($toCheck->$name)) {
            $fieldType = gettype($toCheck->$name);
            if ($fieldType != $what["type"]) {
                if ($what["type"] == "double" && $fieldType == "integer") {
                    // ignore
                } else {
                    return "Expected " . $name . " to be a " . $what["type"];
                }
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

            if (isset($what["in"]) && !in_array($toCheck->$name, $what["in"])) {
                return "Expected " . $name . " to be one of: " . join(", ", $what["in"]);
            }

            if (isset($what["validation"])) {
                if ($error = $what["validation"]($toCheck->$name, $what, $name)) {
                    return $error;
                }
            }

            if (isset($what["postProcess"])) {
                $toCheck->$name = $what["postProcess"]($toCheck->$name, $what, $name);
            }
        }
    }

    return false;
}

function validateParams(array &$toCheck, array $expected)
{
    foreach ($expected as $name => $what) {
        if (!isset($what["optional"])) {
            $what["optional"] = false;
        }

        if (!isset($toCheck[$name]) || $toCheck[$name] == "") {
            if ($what["optional"]) {
                if (isset($what["default"])) {
                    $toCheck[$name] = $what["default"];
                }
                continue;
            } else {
                return "Expected " . $name . " to be a " . $what["type"];
            }
        }

        if (isset($toCheck[$name])) {
            if (isset($what["maxLength"]) && strlen($toCheck[$name]) > $what["maxLength"]) {
                return "Expected " . $name . " to be at most " . $what["maxLength"] . " characters";
            }

            if (isset($what["minLength"]) && strlen($toCheck[$name]) < $what["minLength"]) {
                return "Expected " . $name . " to be at least " . $what["minLength"] . " characters";
            }

            if (isset($what["in"]) && !in_array($toCheck[$name], $what["in"])) {
                return "Expected " . $name . " to be one of: " . join(", ", $what["in"]);
            }

            if (isset($what["validation"])) {
                if ($error = $what["validation"]($toCheck[$name], $what, $name)) {
                    return $error;
                }
            }

            if (isset($what["postProcess"])) {
                $toCheck[$name] = $what["postProcess"]($toCheck[$name], $what, $name);
            }
        }
    }

    return false;
}
