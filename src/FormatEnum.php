<?php

namespace AcMarche\Pivot;

enum FormatEnum: string
{
    case JSON_HEADER = 'application/json';
    case JSON_FTM = 'json';
    case XML_HEADER = 'application/xml';
    case XML_FMT = 'xml';
}