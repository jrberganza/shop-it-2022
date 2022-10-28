<?php

function getfield(mixed &$obj, string $name)
{
    if (gettype($obj) == "array") {
        return $obj[$name];
    } elseif (gettype($obj) == "object") {
        return $obj->$name;
    } else {
        return null;
    }
}

function setfield(mixed &$obj, string $name, mixed $value): void
{
    if (gettype($obj) == "array") {
        $obj[$name] = $value;
    } elseif (gettype($obj) == "object") {
        $obj->$name = $value;
    } else {
        // ignore
    }
}

function hasfield(mixed &$obj, string $name): bool
{
    if (gettype($obj) == "array") {
        return isset($obj[$name]);
    } elseif (gettype($obj) == "object") {
        return isset($obj->$name);
    } else {
        return false;
    }
}

function unsetfield(mixed &$obj, string $name): void
{
    if (gettype($obj) == "array") {
        unset($obj[$name]);
    } elseif (gettype($obj) == "object") {
        unset($obj->$name);
    } else {
        // ignore
    }
}

function validate(mixed &$toCheck, array $expected)
{
    $expectedFields = array();

    foreach ($expected as $name => $what) {
        array_push($expectedFields, $name);

        if (!isset($what["optional"])) {
            $what["optional"] = false;
        }

        if ( // Check if the object doesn't have a field which is:
            !hasfield($toCheck, $name) || // It hasn't been set
            gettype(getfield($toCheck, $name)) == 'NULL' || // It is null
            (!isset($what["type"]) && gettype(getfield($toCheck, $name)) == 'string' && strlen(getfield($toCheck, $name)) == 0) // Special case: A type hasn't been given, and the field is an empty string
        ) {
            if ($what["optional"]) {
                if (isset($what["default"])) {
                    setfield($toCheck, $name, $what["default"]);
                }
                continue;
            } else {
                return "Missing field " . $name . " of type " . $what["type"];
            }
        }

        if (!isset($what["type"])) {
            $what["type"] = "string";
        }

        if (hasfield($toCheck, $name)) {
            $fieldVal = getfield($toCheck, $name);
            $fieldType = gettype($fieldVal);

            if ($what["type"] != "mixed" && $fieldType != $what["type"]) {
                if (($what["type"] == "double" || $what["type"] == "integer") && is_numeric($fieldVal)) {
                    $intVal = intval($fieldVal);
                    $floatVal = floatval($fieldVal);
                    if ($intVal != $floatVal) {
                        if ($what["type"] == "double") {
                            setfield($toCheck, $name, $floatVal);
                        } else {
                            return "Expected " . $name . " to be of type " . $what["type"] . ". Received value of type " . $fieldType;
                        }
                    } else {
                        setfield($toCheck, $name, $intVal);
                    }
                } else {
                    return "Expected " . $name . " to be of type " . $what["type"] . ". Received value of type " . $fieldType;
                }
            }

            if (isset($what["maxLength"]) && strlen($fieldVal) > $what["maxLength"]) {
                return "Expected " . $name . " to be at most " . $what["maxLength"] . " characters";
            }

            if (isset($what["minLength"]) && strlen($fieldVal) < $what["minLength"]) {
                return "Expected " . $name . " to be at least " . $what["minLength"] . " characters";
            }

            if (isset($what["max"]) && $fieldVal > $what["max"]) {
                return "Expected " . $name . " to be at most " . $what["max"];
            }

            if (isset($what["min"]) && $fieldVal < $what["min"]) {
                return "Expected " . $name . " to be at least " . $what["min"];
            }

            if (isset($what["in"]) && !in_array($fieldVal, $what["in"])) {
                return "Expected " . $name . " to be one of: " . join(", ", $what["in"]);
            }

            if (isset($what["validation"])) {
                if ($error = $what["validation"]($fieldVal, $name, $what)) {
                    return $error;
                }
            }

            if (isset($what["postProcess"])) {
                setfield($toCheck, $name, $what["postProcess"]($fieldVal, $name, $what));
            }
        }
    }

    $extrafields = array();
    foreach ($toCheck as $name => $val) {
        if (!in_array($name, $expectedFields)) {
            array_push($extrafields, $name . " (" . gettype($val) . ")");
        }
    }

    if (count($extrafields) > 0) {
        return "Extra fields " . join(", ", $extrafields) . " found";
    }

    return false;
}
