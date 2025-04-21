<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class HelperService
{
    function decodeJWTToken($token)
    {
        $parts = explode('.', $token);
        if (count($parts) !== 3) {
            return 'Invalid JWT format';
        }

        $payload = json_decode(base64_decode(str_replace(['-', '_'], ['+', '/'], $parts[1])), true);
        return $payload ?: 'Invalid payload';
    }
    function XMLtoJSON($xml) {
        $xml = preg_replace('/xmlns[^=]*="[^"]*"/i', '', $xml);
        $xml = preg_replace('/(<\/?)[a-zA-Z0-9]+:/', '$1', $xml);
        $cleanXml = simplexml_load_string($xml, "SimpleXMLElement", LIBXML_NOCDATA);
        return json_decode(json_encode($cleanXml), true);
    }
    function codeToCountry($code) {
        $countries = [
            'cok' => 'Kochi',
            'amm' => 'Amman',
            'khi' => 'Karachi',
            'shj' => 'Sharjah',
            'isb' => 'Islamabad',
            'lhe' => 'Lahore',
            'lhr' => 'London Heathrow Airport',
            'ruh' => 'Riyadh King Khālid International Airport',
            'bah' => 'Bahrain',
        ];
        return $countries[strtolower($code)] ?? 'Unknown';
    }
}
