<?php

class ParsedQuery
{
    public string $query = "";
    public string $types = "";
    public array $params = [];
}

function parseSql(string $query, array $params)
{
    $parsed = new ParsedQuery();

    $currI = 0;
    $prevParamEnd = -1;
    $nextIndex = 0;
    while ($nextIndex = strpos($query, '@{', $nextIndex + 1)) {
        $paramEnd = strpos($query, '}', $nextIndex);
        $typeSep = strpos($query, ':', $nextIndex);

        $queryPart = substr($query, $prevParamEnd + 1, $nextIndex - ($prevParamEnd + 1));
        $type = substr($query, $nextIndex + 2, $typeSep - ($nextIndex + 2));
        $paramName = substr($query, $typeSep + 1, $paramEnd - ($typeSep + 1));

        $parsed->query .= $queryPart . '?';
        $parsed->types .= $type;
        $parsed->params[$currI] = &$params[$paramName];

        $currI++;
        $prevParamEnd = $paramEnd;
    }

    $queryPart = substr($query, $prevParamEnd + 1, strlen($query) - ($prevParamEnd + 1));
    $parsed->query .= $queryPart;

    return $parsed;
}
