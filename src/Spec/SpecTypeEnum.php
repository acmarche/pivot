<?php

namespace AcMarche\Pivot\Spec;

enum SpecTypeEnum: string
{
    case EMAIL = "EMail";
    case URL = "Url";
    case URL_FACEBOOK = "URLFacebook";
    case URL_TRIPADVISOR = "'URLTripadvisor'";
    case STRING = "String";
    case STRINGML = "StringML";
    case BOOLEAN = "Boolean";
    case DATE = "Date";
    case OBJECT = "Object";
    case TEXTML = "TextML";
    case CURRENCY = "Currency";
    case PHONE = "Phone";
    case GSM = "GSM";
}