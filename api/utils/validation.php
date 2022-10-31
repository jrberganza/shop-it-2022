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

$VALIDATION_EMPTY_VAL = new \stdClass;

function domToJson(array $domlist, array $schema): mixed
{
    global $VALIDATION_EMPTY_VAL;

    if ($schema["type"] == "object") {
        if (count($domlist) != 1) {
            return $VALIDATION_EMPTY_VAL;
        }
        $objEl = $domlist[0];

        $map = array();
        foreach ($objEl->childNodes as $dom) {
            if (!isset($map[$dom->nodeName])) {
                $map[$dom->nodeName] = array();
            }

            array_push($map[$dom->nodeName], $dom);
        }

        $obj = new \stdClass;

        foreach ($schema["children"] as $name => $chSchema) {
            $converted = domToJson($map[$name], $chSchema);
            if (gettype($converted) != "object") {
                $obj->$name = domToJson($map[$name], $chSchema);
            } elseif ($converted != $VALIDATION_EMPTY_VAL) {
                $obj->$name = domToJson($map[$name], $chSchema);
            }
        }

        return $obj;
    } elseif ($schema["type"] == "array") {
        $arr = array();

        foreach ($domlist as $dom) {
            $converted = domToJson([$dom], $schema["child"]);
            if (gettype($converted) != "object") {
                array_push($arr, $converted);
            } elseif ($converted != $VALIDATION_EMPTY_VAL) {
                array_push($arr, $converted);
            }
        }

        return $arr;
    } elseif (
        $schema["type"] == "string" ||
        $schema["type"] == "integer" ||
        $schema["type"] == "double" ||
        $schema["type"] == "boolean"
    ) {
        if (count($domlist) != 1) {
            return $VALIDATION_EMPTY_VAL;
        }
        $stringEl = $domlist[0];

        if ($stringEl->childNodes->count() != 1) {
            return $VALIDATION_EMPTY_VAL;
        }

        if ($stringEl->firstChild->nodeType != XML_TEXT_NODE) {
            return $VALIDATION_EMPTY_VAL;
        }

        $value = $stringEl->firstChild->textContent;
        if ($schema["type"] == "string") {
            return $value;
        } elseif ($schema["type"] == "integer") {
            return is_numeric($value) ? intval($value) : $VALIDATION_EMPTY_VAL;
        } elseif ($schema["type"] == "double") {
            return is_numeric($value) ? doubleval($value) : $VALIDATION_EMPTY_VAL;
        } elseif ($schema["type"] == "boolean") {
            return ($value == "true" || $value == "1") ? true
                : ($value == "false" || $value == "0" ? false
                    : $VALIDATION_EMPTY_VAL);
        }
    } else {
        return $schema["type"];
    }
}
