<?php

/**
 * CTimeZone provides work with timezones
 *
 * PUBLIC:					PROTECTED:					PRIVATE:
 * ----------               ----------                  ----------
 * __construct
 * setTimeZone
 * getTimeZone
 * getLocales
 * getTimeZones
 *
 */
class CTimeZone
{
    /**
     * Class default constructor
     */
    function __construct()
    {
    }

    /**
     * Returns a nested array of timezones by continents
     * @return array
     */
    public static function getTimeZones()
    {
        return array(
            'Pacific/Apia'=>'[GMT-11:00] West Samoa Time (MIT)',
            'Pacific/Marquesas'=>'[GMT-09:30] Marquesas Time (Pacific / Marquesas)',
            'America/Anchorage'=>'[GMT-09:00] Alaska Standard Time (AST)',
            'America/Los_Angeles'=>'[GMT-08:00] Pacific Standard Time (US & Canada)',
            'America/Phoenix'=>'[GMT-07:00] Mountain Standard Time (US / Arizona)',
            'America/Chicago'=>'[GMT-06:00] Central Standard Time (US & Canada)',
            'Pacific/Easter'=>'[GMT-06:00] Easter Is. Time (Pacific / Easter)',
            'America/Havana'=>'[GMT-05:00] Central Standard Time (America / Havana)',
            'America/Bogota'=>'[GMT-05:00] Colombia Time (America / Bogota)',
            'America/Caracas'=>'[GMT-04:00] Venezuela Time (America / Caracas)',
            'America/Asuncion'=>'[GMT-04:00] Paraguay Time (America / Asuncion)',
            'America/Santiago'=>'[GMT-04:00] Chile Time (America / Santiago)',
            'America/Cuiaba'=>'[GMT-04:00] Amazon Standard Time (America / Cuiaba)',
            'America/La_Paz'=>'[GMT-04:00] Bolivia Time (America / La Paz)',
            'Atlantic/Stanley'=>'[GMT-04:00] Falkland Is. Time (Atlantic / Stanley)',
            'America/St_Johns'=>'[GMT-03:30] Newfoundland Standard Time (America / St Johns)',
            'America/Argentina/Buenos_Aires'=>'[GMT-03:00] Argentine Time (AGT)',
            'America/Godthab'=>'[GMT-03:00] Western Greenland Time (America / Godthab)',
            'America/Montevideo'=>'[GMT-03:00] Uruguay Time (America / Montevideo)',
            'America/Sao_Paulo'=>'[GMT-03:00] Brazil Time (BET)',
            'America/Miquelon'=>'[GMT-03:00] Pierre & Miquelon Standard Time (America / Miquelon)',
            'America/Noronha'=>'[GMT-02:00] Fernando de Noronha Time (America / Noronha)',
            'Atlantic/Cape_Verde'=>'[GMT-01:00] Cape Verde Time (Atlantic / Cape Verde)',
            'Atlantic/Azores'=>'[GMT-01:00] Azores Time (Atlantic / Azores)',
            'Africa/Casablanca'=>'[GMT+00:00] Western European Time (Africa / Casablanca)',
            'Africa/Algiers'=>'[GMT+01:00] Central European Time (Africa / Algiers)',
            'Africa/Windhoek'=>'[GMT+01:00] Western African Time (Africa / Windhoek)',
            'Africa/Johannesburg'=>'[GMT+02:00] South Africa Standard Time (Africa / Johannesburg)',
            'Asia/Jerusalem'=>'[GMT+02:00] Israel Standard Time (Asia / Jerusalem)',
            'Asia/Tehran'=>'[GMT+03:30] Iran Standard Time (Asia / Tehran)',
            'Europe/Moscow'=>'[GMT+03:00] Moscow Standard Time (Europe / Moscow)',
            'Asia/Dubai'=>'[GMT+04:00] Gulf Standard Time (Asia / Dubai)',
            'Asia/Yerevan'=>'[GMT+04:00] Armenia Time (NET)',
            'Asia/Baku'=>'[GMT+04:00] Azerbaijan Time (Asia / Baku)',
            'Indian/Mauritius'=>'[GMT+04:00] Mauritius Time (Indian / Mauritius)',
            'Asia/Kabul'=>'[GMT+04:30] Afghanistan Time (Asia / Kabul)',
            'Asia/Tashkent'=>'[GMT+05:00] Uzbekistan Time (Asia / Tashkent)',
            'Asia/Yekaterinburg'=>'[GMT+05:00] Yekaterinburg Time (Asia / Yekaterinburg)',
            'Asia/Almaty'=>'[GMT+06:00] Alma-Ata Time (Asia / Almaty)',
            'Asia/Rangoon'=>'[GMT+06:30] Myanmar Time (Asia / Rangoon)',
            'Asia/Novosibirsk'=>'[GMT+06:00] Novosibirsk Time (Asia / Novosibirsk)',
            'Asia/Hong_Kong'=>'[GMT+08:00] Hong Kong Time (Asia / Hong Kong)',
            'Asia/Krasnoyarsk'=>'[GMT+07:00] Krasnoyarsk Time (Asia / Krasnoyarsk)',
            'Asia/Singapore'=>'[GMT+08:00] Singapore Time (Asia / Singapore)',
            'Australia/Perth'=>'[GMT+08:00] Western Standard Time (Australia) (Australia / Perth)',
            'Asia/Irkutsk'=>'[GMT+08:00] Irkutsk Time (Asia / Irkutsk)',
            'Asia/Tokyo'=>'[GMT+09:00] Japan Standard Time (JST)',
            'Asia/Seoul'=>'[GMT+09:00] Korea Standard Time (Asia / Seoul)',
            'Australia/Adelaide'=>'[GMT+09:30] Central Standard Time (South Australia) (Australia / Adelaide)',
            'Australia/Darwin'=>'[GMT+09:30] Central Standard Time (Northern Territory) (ACT)',
            'Australia/Brisbane'=>'[GMT+10:00] Eastern Standard Time (Queensland) (Australia / Brisbane)',
            'Australia/Sydney'=>'[GMT+10:00] Eastern Standard Time (New South Wales) (Australia / Sydney)',
            'Asia/Yakutsk'=>'[GMT+09:00] Yakutsk Time (Asia / Yakutsk)',
            'Pacific/Noumea'=>'[GMT+11:00] New Caledonia Time (Pacific / Noumea)',
            'Asia/Vladivostok'=>'[GMT+10:00] Vladivostok Time (Asia / Vladivostok)',
            'Pacific/Norfolk'=>'[GMT+11:30] Norfolk Time (Pacific / Norfolk)',
            'Asia/Anadyr'=>'[GMT+12:00] Anadyr Time (Asia / Anadyr)',
            'Pacific/Auckland'=>'[GMT+12:00] New Zealand Standard Time (Pacific / Auckland)',
            'Pacific/Fiji'=>'[GMT+12:00] Fiji Time (Pacific / Fiji)',
            'Asia/Magadan'=>'[GMT+11:00] Magadan Time (Asia / Magadan)',
            'Pacific/Chatham'=>'[GMT+12:45] Chatham Standard Time (Pacific / Chatham)',
            'Pacific/Tongatapu'=>'[GMT+13:00] Tonga Time (Pacific / Tongatapu)',
            'Pacific/Kiritimati'=>'[GMT+14:00] Line Is. Time (Pacific / Kiritimati)'
        );
    }
}