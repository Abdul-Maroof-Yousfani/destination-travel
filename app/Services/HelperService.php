<?php

namespace App\Services;

use DOMDocument;
use SimpleXMLElement;
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
    function XMLtoJSONEmirate($xml) {
        libxml_use_internal_errors(true);
        $dom = new DOMDocument();
        $dom->preserveWhiteSpace = false;
        $dom->loadXML($xml);
        $simpleXml = simplexml_import_dom($dom);
        if ($simpleXml === false) {
            $errors = libxml_get_errors();
            libxml_clear_errors();
            return ["msg" => "Failed to parse XML", $errors, $xml];
        }
        $simpleXmlNoNs = $this->removeNamespaces($simpleXml);
        return json_decode(json_encode($simpleXmlNoNs), true);
    }
    
    function removeNamespaces(SimpleXMLElement $xml) {
        $xmlString = $xml->asXML();
        $xmlString = preg_replace('/xmlns[^=]*="[^"]*"/i', '', $xmlString);
        $xmlString = preg_replace('/(<\/?)[\w\d]+:/', '$1', $xmlString);
        return simplexml_load_string($xmlString, 'SimpleXMLElement', LIBXML_NOCDATA);
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
