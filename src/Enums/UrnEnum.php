<?php

declare(strict_types=1);

namespace AcMarche\PivotAi\Enums;

enum UrnEnum: string
{
    // === Categories ===
    case CAT_CLASSLAB = 'urn:cat:classlab';
    case CAT_EQPSRV = 'urn:cat:eqpsrv';
    case CAT_ACCUEIL = 'urn:cat:accueil';
    case CAT_IDENT = 'urn:cat:ident';
    case CAT_DESC = 'urn:cat:desc';
    case CAT_DESCMARKET = 'urn:cat:descmarket';
    case CAT_RESUME = 'urn:cat:resume';
    case CAT_MEDIA = 'urn:cat:media';
    case CAT_MOYCOM = 'urn:cat:moycom';
    case CAT_TARIF = 'urn:cat:tarif';
    case CAT_FILTRE = 'urn:cat:filtre';

    // === Classification fields (urn:cat:classlab) ===
    case CLASS_VALUE = 'urn:fld:class:value';
    case CLASS_TITLE = 'urn:fld:class:title';
    case CLASS_SUPERIOR = 'urn:fld:class:superior';
    case MICHELIN_STARS = 'urn:fld:class:michstar:value';
    case MICHELIN_TITLE = 'urn:fld:class:michstar:title';
    case BIB_GOURMAND = 'urn:fld:class:bibgour';
    case GAULT_MILLAU_TOQUES = 'urn:fld:class:gaultmiltoq:value';
    case GAULT_MILLAU_TITLE = 'urn:fld:class:gaultmiltoq:title';

    // === Circuit/Route fields ===
    case CIRCUIT_TYPE = 'urn:fld:typecirc';
    case RECOMMENDATION = 'urn:fld:reco';
    case DISTANCE = 'urn:fld:dist';

    // === Equipment & Services (urn:cat:eqpsrv) ===
    case EQP_WIRED_INTERNET = 'urn:fld:eqpsrv:accwebfil';
    case EQP_WIFI = 'urn:fld:eqpsrv:accwebwifi';
    case EQP_WIFI_ROOMS = 'urn:fld:eqpsrv:accwebwifichb';
    case EQP_AIR_CONDITIONING = 'urn:fld:eqpsrv:airco';
    case EQP_ELEVATOR = 'urn:fld:eqpsrv:ascenseur';
    case EQP_PLAYGROUND = 'urn:fld:eqpsrv:airjeuenf';
    case EQP_BAR = 'urn:fld:eqpsrv:bar';
    case EQP_BBQ = 'urn:fld:eqpsrv:bbq';
    case EQP_SHOP = 'urn:fld:eqpsrv:boutiq';
    case EQP_CAFETERIA = 'urn:fld:eqpsrv:cafet';
    case EQP_TASTING_ROOM = 'urn:fld:eqpsrv:deguplace';
    case EQP_SHOWER = 'urn:fld:eqpsrv:douche';
    case EQP_LINEAR_DRAINAGE = 'urn:fld:eqpsrv:dralincomp';
    case EQP_SCREEN = 'urn:fld:eqpsrv:ecran';
    case EQP_BUBBLE_BATH = 'urn:fld:eqpsrv:bainbul';
    case EQP_HAMMAM = 'urn:fld:eqpsrv:hammam';
    case EQP_BUS_STOP = 'urn:fld:eqpsrv:haltautoc';
    case EQP_HOSTS = 'urn:fld:eqpsrv:hotes';
    case EQP_GARDEN = 'urn:fld:eqpsrv:jrdparc';
    case EQP_LAUNDRY_LINEN = 'urn:fld:eqpsrv:lavlin';
    case EQP_DISHWASHING = 'urn:fld:eqpsrv:lavvai';
    case EQP_EQUIPMENT_RENTAL = 'urn:fld:eqpsrv:locmat';
    case EQP_GARDEN_FURNITURE = 'urn:fld:eqpsrv:mobjrd';
    case EQP_CLEANING = 'urn:fld:eqpsrv:nettoy';
    case EQP_PARKING = 'urn:fld:eqpsrv:parking';
    case EQP_PAID_PARKING = 'urn:fld:eqpsrv:parkingautoc';
    case EQP_POOL = 'urn:fld:eqpsrv:pisc';
    case EQP_OUTDOOR_POOL = 'urn:fld:eqpsrv:piscair';
    case EQP_INDOOR_POOL = 'urn:fld:eqpsrv:piscind';
    case EQP_PICNIC_AREA = 'urn:fld:eqpsrv:piqniq';
    case EQP_PICNIC_TABLES = 'urn:fld:eqpsrv:tablepiqniq';
    case EQP_RESTAURANT = 'urn:fld:eqpsrv:resto';
    case EQP_PROJECTOR = 'urn:fld:eqpsrv:retrproj';
    case EQP_SEMINAR_ROOM = 'urn:fld:eqpsrv:salsemin';
    case EQP_SAUNA = 'urn:fld:eqpsrv:sauna';
    case EQP_LINEN_SECURITY = 'urn:fld:eqpsrv:seclin';
    case EQP_SECURITY_GUARD = 'urn:fld:eqpsrv:securgard';
    case EQP_SOUND_SYSTEM = 'urn:fld:eqpsrv:sonor';
    case EQP_TECHNICAL = 'urn:fld:eqpsrv:tech';
    case EQP_THIRD_PARTY_CLOTHING = 'urn:fld:eqpsrv:terclo';
    case EQP_COUNTER_SALES = 'urn:fld:eqpsrv:ventcompt';
    case EQP_CARD_PAYMENT = 'urn:fld:eqpsrv:ventecarte';
    case EQP_ONLINE_SALES = 'urn:fld:eqpsrv:ventligne';
    case EQP_MARKET_SALES = 'urn:fld:eqpsrv:ventmarche';
    case EQP_SALES_ON_PREMISES = 'urn:fld:eqpsrv:ventsurpl';
    case EQP_CLOAKROOM = 'urn:fld:eqpsrv:vest';
    case EQP_TOILETS = 'urn:fld:eqpsrv:wc';
    case EQP_WELLNESS = 'urn:fld:eqpsrv:wellness';
    case EQP_OTHER = 'urn:fld:eqpsrvautre';

    // === Welcome & Accessibility (urn:cat:accueil) ===
    case WELCOME_PEDESTRIAN = 'urn:fld:infusgped';
    case WELCOME_PEDESTRIAN_PMR = 'urn:fld:infusgpedpmr';
    case WELCOME_PEDESTRIAN_CHILD = 'urn:fld:infusgpedpou';
    case WELCOME_TRAIL = 'urn:fld:infusgtrail';
    case WELCOME_EQUESTRIAN = 'urn:fld:infusgequ';
    case WELCOME_ROAD_CYCLING = 'urn:fld:infusgvtc';
    case WELCOME_MOUNTAIN_BIKE = 'urn:fld:infusgvtt';
    case WELCOME_CYCLO_TOURISM = 'urn:fld:infusgvelotour';
    case WELCOME_CYCLO_TOURISM_PMR = 'urn:fld:infusgvelotourpmr';
    case WELCOME_ROAD_CYCLING_A29B = 'urn:fld:infusgvtc:a29b';
    case OPENING_INDICATIONS = 'urn:fld:ouvind';

    // === Event category fields (urn:fld:catevt:*) ===
    case EVENT_CATEGORY_PREFIX = 'urn:fld:catevt:';
    case EVT_TYPE = 'urn:fld:typeevt';

    // === Culinary specialty fields (urn:fld:specculi:*) ===
    // These are dynamic — parsed by prefix 'urn:fld:specculi:'

    case CULINARY_SPECIALTY_PREFIX = 'urn:fld:specculi:';

    // === Identity/Description fields ===
    case HOMEPAGE = 'urn:fld:homepage';
    case NAME_OFFER = 'urn:fld:nomofr';
    case DESC_MARKET = 'urn:fld:descmarket';
    case DESC_MARKET_SHORT = 'urn:fld:descmarket20';
    case SIGNAGE = 'urn:fld:signal';
    case ADDRESS_SUMMARY = 'urn:fld:adr';

    // === Labels ===
    case LABEL_BIKE_FRIENDLY = 'urn:fld:label:bvvelo';

    // === Communication fields ===
    case PHONE1 = 'urn:fld:phone1';
    case MAIL1 = 'urn:fld:mail1';
    case URL_WEB = 'urn:fld:urlweb';
    case URL_FACEBOOK = 'urn:fld:urlfacebook';

    // === Link types ===
    case LINK_MEDIA_DEFAULT = 'urn:lnk:media:defaut';

    /**
     * @return array<string, string> Equipment URNs mapped to short names
     */
    public static function equipmentMap(): array
    {
        return [
            self::EQP_WIRED_INTERNET->value => 'wiredInternet',
            self::EQP_WIFI->value => 'wifi',
            self::EQP_WIFI_ROOMS->value => 'wifiRooms',
            self::EQP_AIR_CONDITIONING->value => 'airConditioning',
            self::EQP_ELEVATOR->value => 'elevator',
            self::EQP_PLAYGROUND->value => 'playground',
            self::EQP_BAR->value => 'bar',
            self::EQP_BBQ->value => 'bbq',
            self::EQP_SHOP->value => 'shop',
            self::EQP_CAFETERIA->value => 'cafeteria',
            self::EQP_TASTING_ROOM->value => 'tastingRoom',
            self::EQP_SHOWER->value => 'shower',
            self::EQP_LINEAR_DRAINAGE->value => 'linearDrainage',
            self::EQP_SCREEN->value => 'screen',
            self::EQP_BUBBLE_BATH->value => 'bubbleBath',
            self::EQP_HAMMAM->value => 'hammam',
            self::EQP_BUS_STOP->value => 'busStop',
            self::EQP_HOSTS->value => 'hosts',
            self::EQP_GARDEN->value => 'garden',
            self::EQP_LAUNDRY_LINEN->value => 'laundryLinen',
            self::EQP_DISHWASHING->value => 'dishwashing',
            self::EQP_EQUIPMENT_RENTAL->value => 'equipmentRental',
            self::EQP_GARDEN_FURNITURE->value => 'gardenFurniture',
            self::EQP_CLEANING->value => 'cleaning',
            self::EQP_PARKING->value => 'parking',
            self::EQP_PAID_PARKING->value => 'paidParking',
            self::EQP_POOL->value => 'pool',
            self::EQP_OUTDOOR_POOL->value => 'outdoorPool',
            self::EQP_INDOOR_POOL->value => 'indoorPool',
            self::EQP_PICNIC_AREA->value => 'picnicArea',
            self::EQP_PICNIC_TABLES->value => 'picnicTables',
            self::EQP_RESTAURANT->value => 'restaurant',
            self::EQP_PROJECTOR->value => 'projector',
            self::EQP_SEMINAR_ROOM->value => 'seminarRoom',
            self::EQP_SAUNA->value => 'sauna',
            self::EQP_LINEN_SECURITY->value => 'linenSecurity',
            self::EQP_SECURITY_GUARD->value => 'securityGuard',
            self::EQP_SOUND_SYSTEM->value => 'soundSystem',
            self::EQP_TECHNICAL->value => 'technical',
            self::EQP_THIRD_PARTY_CLOTHING->value => 'thirdPartyClothing',
            self::EQP_COUNTER_SALES->value => 'counterSales',
            self::EQP_CARD_PAYMENT->value => 'cardPayment',
            self::EQP_ONLINE_SALES->value => 'onlineSales',
            self::EQP_MARKET_SALES->value => 'marketSales',
            self::EQP_SALES_ON_PREMISES->value => 'salesOnPremises',
            self::EQP_CLOAKROOM->value => 'cloakroom',
            self::EQP_TOILETS->value => 'toilets',
            self::EQP_WELLNESS->value => 'wellness',
        ];
    }

    /**
     * @return array<string, string> Welcome URNs mapped to short names
     */
    public static function welcomeMap(): array
    {
        return [
            self::WELCOME_PEDESTRIAN->value => 'pedestrian',
            self::WELCOME_PEDESTRIAN_PMR->value => 'pedestrianPmr',
            self::WELCOME_PEDESTRIAN_CHILD->value => 'pedestrianChild',
            self::WELCOME_TRAIL->value => 'trail',
            self::WELCOME_EQUESTRIAN->value => 'equestrian',
            self::WELCOME_ROAD_CYCLING->value => 'roadCycling',
            self::WELCOME_MOUNTAIN_BIKE->value => 'mountainBike',
            self::WELCOME_CYCLO_TOURISM->value => 'cycloTourism',
            self::WELCOME_CYCLO_TOURISM_PMR->value => 'cycloTourismPmr',
            self::WELCOME_ROAD_CYCLING_A29B->value => 'roadCyclingA29b',
        ];
    }
}
