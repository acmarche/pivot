<?php

declare(strict_types=1);

namespace AcMarche\PivotAi\Enums;

enum TypeOffreEnum: int
{
    case HOTEL = 1;
    case GITE = 2;
    case BED_AND_BREAKFAST = 3;
    case HOLIDAY_HOME = 4;
    case CAMPING = 5;
    case BUDGET_HOLIDAY = 6;
    case HOLIDAY_RESORT = 7;
    case ROUTE = 8;
    case EVENT = 9;
    case DISCOVERY_RECREATION = 11;
    case TOUR_GUIDE = 12;
    case ARTICLE = 13;
    case TOURISM_ORGANISATION = 14;
    case INDIVIDUAL_PACKAGE = 15;
    case GROUP_PACKAGE = 16;
    case TRAVEL_AGENCY = 17;
    case MICE_FACILITY = 18;
    case MICE_ORGANISER = 19;
    case MICE_SERVICE_PROVIDER = 20;
    case MICE_RECREATION = 21;
    case OTHER_ACCOMMODATION = 25;
    case MOTORHOME_AREA = 26;
    case EDUCATIONAL_ANIMATION = 257;
    case LOCAL_PRODUCER = 258;
    case CRAFTMAN = 259;
    case LOCAL_SHOP = 260;
    case RESTAURANT = 261;
    case LOCAL_PRODUCT = 267;
    case POINT_OF_INTEREST = 269;
    case ACCOMMODATIONS = 270;

    public function urn(): string
    {
        return 'urn:typ:' . $this->value;
    }

    public function code(): string
    {
        return match ($this) {
            self::HOTEL => 'HTL',
            self::GITE => 'GIT',
            self::BED_AND_BREAKFAST => 'CHB',
            self::HOLIDAY_HOME => 'MBL',
            self::CAMPING => 'CMP',
            self::BUDGET_HOLIDAY => 'BDG',
            self::HOLIDAY_RESORT => 'VLG',
            self::ROUTE => 'ITB',
            self::EVENT => 'EVT',
            self::DISCOVERY_RECREATION => 'ALD',
            self::TOUR_GUIDE => 'GTR',
            self::ARTICLE => 'ART',
            self::TOURISM_ORGANISATION => 'OGT',
            self::INDIVIDUAL_PACKAGE => 'FTI',
            self::GROUP_PACKAGE => 'FTG',
            self::TRAVEL_AGENCY => 'AGV',
            self::MICE_FACILITY => 'MIF',
            self::MICE_ORGANISER => 'MOG',
            self::MICE_SERVICE_PROVIDER => 'MPR',
            self::MICE_RECREATION => 'MDV',
            self::OTHER_ACCOMMODATION => 'ATH',
            self::MOTORHOME_AREA => 'AMH',
            self::EDUCATIONAL_ANIMATION => 'APD',
            self::LOCAL_PRODUCER => 'PRD',
            self::CRAFTMAN => 'ATS',
            self::LOCAL_SHOP => 'BTQ',
            self::RESTAURANT => 'RST',
            self::LOCAL_PRODUCT => 'PDT',
            self::POINT_OF_INTEREST => 'POI',
            self::ACCOMMODATIONS => 'GHB',
        };
    }

    public function label(): string
    {
        return match ($this) {
            self::HOTEL => 'Hotel',
            self::GITE => 'Gîte',
            self::BED_AND_BREAKFAST => 'Bed and Breakfast',
            self::HOLIDAY_HOME => 'Holiday home',
            self::CAMPING => 'Camping',
            self::BUDGET_HOLIDAY => 'Budget Holiday',
            self::HOLIDAY_RESORT => 'Holiday resort',
            self::ROUTE => 'Route',
            self::EVENT => 'Event',
            self::DISCOVERY_RECREATION => 'Discovery and recreation',
            self::TOUR_GUIDE => 'Tour guide',
            self::ARTICLE => 'Article',
            self::TOURISM_ORGANISATION => 'Tourism organisation',
            self::INDIVIDUAL_PACKAGE => 'Individual package',
            self::GROUP_PACKAGE => 'Group package',
            self::TRAVEL_AGENCY => 'Travel agency',
            self::MICE_FACILITY => 'MICE - Facility',
            self::MICE_ORGANISER => 'MICE - Organiser',
            self::MICE_SERVICE_PROVIDER => 'MICE - Service provider',
            self::MICE_RECREATION => 'MICE - Recreation',
            self::OTHER_ACCOMMODATION => 'Other accommodation',
            self::MOTORHOME_AREA => 'Motorhome area',
            self::EDUCATIONAL_ANIMATION => 'Educational animation',
            self::LOCAL_PRODUCER => 'Local producer',
            self::CRAFTMAN => 'Craftman',
            self::LOCAL_SHOP => 'Local shop',
            self::RESTAURANT => 'Restaurant',
            self::LOCAL_PRODUCT => 'Local product',
            self::POINT_OF_INTEREST => 'Point of interest',
            self::ACCOMMODATIONS => 'Accommodations',
        };
    }

    public static function fromCode(string $code): ?self
    {
        foreach (self::cases() as $case) {
            if ($case->code() === $code) {
                return $case;
            }
        }

        return null;
    }

    public static function fromUrn(string $urn): ?self
    {
        if (preg_match('/^urn:typ:(\d+)$/', $urn, $matches)) {
            return self::tryFrom((int) $matches[1]);
        }

        return null;
    }

    public static function accommodations(): array
    {
        return [
            self::HOTEL,
            self::GITE,
            self::BED_AND_BREAKFAST,
            self::HOLIDAY_HOME,
            self::CAMPING,
            self::BUDGET_HOLIDAY,
            self::HOLIDAY_RESORT,
            self::OTHER_ACCOMMODATION,
            self::MOTORHOME_AREA,
          //  self::ACCOMMODATIONS,
        ];
    }
}
