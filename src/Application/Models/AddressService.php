<?php
/**
 * @copyright 2015-2019 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
namespace Application\Models;
use Application\Url;

class AddressService
{
    private static function jsonRequest($url): ?array
    {
        $response = Url::get($url);
        if ($response) {
            return json_decode($response);
        }
    }
	/**
	 * Return an array of street information
	 *
	 * The array must have the streetName as the key.
	 * The value must be the street_id.
	 */
	public static function searchStreets(string $query): ?array
	{
        $url = new Url(ADDRESS_SERVICE.'/streets');
        $url->format = 'json';
        $url->street = $query;

        return self::jsonRequest($url);
	}

	/**
	 * Returns an array of streets that intersect the provided street
	 *
	 * The array must have streetName as the key
	 * The value must be the street_id
	 *
	 * @param string $streetName
	 * @return array
	 */
	public static function intersectingStreets(int $street_id): ?array
	{
        $url = new Url(ADDRESS_SERVICE.'/streets/intersectingStreets/'.$street_id);
        $url->format = 'json';

        return self::jsonRequest($url);
	}

	/**
	 * @param array   Multiple intersections as json data from the request
	 */
	public static function intersections(int $street_id_1, int $street_id_2): ?array
	{
        $url = new Url(ADDRESS_SERVICE.'/streets/intersections');
        $url->format = 'json';
        $url->street_id_1 = $street_id_1;
        $url->street_id_2 = $street_id_2;

        return self::jsonRequest($url);
	}
}
