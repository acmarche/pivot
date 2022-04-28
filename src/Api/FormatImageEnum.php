<?php

namespace AcMarche\Pivot\Api;

enum FormatImageEnum:string
{
    case IMG_THB_XSF = '75x75';
    case IMG_THB_XSW = '75x-1';
    case IMG_THB_XSH = '-1x75';
    case IMG_THB_SF = '150x150';
    case IMG_THB_SW = '150x-1';
    case IMG_THB_SH = '-1x150';
    case IMG_THB_MF = '300x300';
    case IMG_THB_MW = '300x-1';
    case IMG_THB_MH = '-1x300';
    case IMG_THB_LF = '480x480';
    case IMG_THB_LW = '480x-1';
    case IMG_THB_LH = '-1x480';
    case IMG_ICO_XS = '-1x16';
    case IMG_ICO_S = '-1x24';
    case IMG_ICO_M = '-1x32';
    case IMG_ICO_L = '-1x48';
    case IMG_ICO_XL = '-1x64';
}