<?php

namespace AcMarche\Pivot\Spec;

enum SpecTypeEnum: string
{
    case TEL = "Phone";
    case EMAIL = "EMail";
    case URL = "Url";
    case STRING = "String";
    case BOOLEAN = "Boolean";
    case DATE = "Date";
    case OBJECT = "Object";
    case TEXTML = "TextML";
}