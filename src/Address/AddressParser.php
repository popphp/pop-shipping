<?php
/**
 * Pop PHP Framework (https://www.popphp.org/)
 *
 * @link       https://github.com/popphp/popphp-framework
 * @author     Nick Sagona, III <dev@noladev.com>
 * @copyright  Copyright (c) 2009-2026 NOLA Interactive, LLC.
 * @license    https://www.popphp.org/license     New BSD License
 */

/**
 * @namespace
 */
namespace Pop\Shipping\Address;

/**
 * Address parser class
 *
 * @category   Pop
 * @package    Pop\Shipping
 * @author     Nick Sagona, III <dev@noladev.com>
 * @copyright  Copyright (c) 2009-2026 NOLA Interactive, LLC.
 * @license    https://www.popphp.org/license     New BSD License
 * @version    3.0.0
 */
class AddressParser extends AbstractParser
{

    /**
     * Street number
     * @var ?string
     * */
    protected ?string $streetNumber = null;

    /**
     * Street name
     * @var ?string
     * */
    protected ?string $streetName = null;

    /**
     * Route type
     * @var ?string
     * */
    protected ?string $routeType = null;

    /**
     * Direction
     * @var ?string
     * */
    protected ?string $direction = null;

    /**
     * Direction position
     * @var ?int
     * */
    protected ?int $directionPosition = null;

    /**
     * Unit
     * @var ?string
     * */
    protected ?string $unit = null;

    /**
     * City
     * @var ?string
     * */
    protected ?string $city = null;

    /**
     * Postal code
     * @var ?string
     * */
    protected ?string $postalCode = null;

    /**
     * Zip 4
     * @var ?string
     * */
    protected ?string $zip4 = null;

    /**
     * State name
     * @var ?string
     * */
    protected ?string $stateName = null;

    /**
     * State code
     * @var ?string
     * */
    protected ?string $stateCode = null;

    /**
     * Country
     * @var ?string
     * */
    protected ?string $country = null;

    /**
     * Method to get street number
     *
     * @return ?string
     */
    public function getStreetNumber(): ?string
    {
        return $this->streetNumber;
    }

    /**
     * Method to get street name
     *
     * @param  bool $withRouteDirection
     * @return ?string
     */
    public function getStreetName(bool $withRouteDirection = true): ?string
    {
        $streetName = $this->streetName;
        if (!empty($this->direction) && ($withRouteDirection)) {
            if ($this->directionPosition == 1) {
                $streetName = $streetName . ' ' . $this->direction;
            } else {
                $streetName = $this->direction . ' ' . $streetName;
            }
        }

        return $streetName;
    }

    /**
     * Method to get route type
     *
     * @return ?string
     */
    public function getRouteType(): ?string
    {
        return $this->routeType;
    }

    /**
     * Method to get direction
     *
     * @return ?string
     */
    public function getDirection(): ?string
    {
        return $this->direction;
    }

    /**
     * Method to get unit
     *
     * @return ?string
     */
    public function getUnit(): ?string
    {
        return $this->unit;
    }

    /**
     * Method to get city
     *
     * @return ?string
     */
    public function getCity(): ?string
    {
        return $this->city;
    }

    /**
     * Method to get postal code
     *
     * @return ?string
     */
    public function getPostalCode(): ?string
    {
        return $this->postalCode;
    }

    /**
     * Method to get zip 4
     *
     * @return ?string
     */
    public function getZip4(): ?string
    {
        return $this->zip4;
    }

    /**
     * Method to get state name
     *
     * @return ?string
     */
    public function getStateName(): ?string
    {
        return $this->stateName;
    }

    /**
     * Method to get state code
     *
     * @return ?string
     */
    public function getStateCode(): ?string
    {
        return $this->stateCode;
    }

    /**
     * Method to get country
     *
     * @return ?string
     */
    public function getCountry(): ?string
    {
        return $this->country;
    }

    /**
     * Has street number
     *
     * @return bool
     */
    public function hasStreetNumber(): bool
    {
        return !empty($this->streetNumber);
    }

    /**
     * Has street name
     *
     * @return bool
     */
    public function hasStreetName(): bool
    {
        return !empty($this->streetName);
    }

    /**
     * Has route type
     *
     * @return bool
     */
    public function hasRouteType(): bool
    {
        return !empty($this->routeType);
    }

    /**
     * Has direction
     *
     * @return bool
     */
    public function hasDirection(): bool
    {
        return !empty($this->direction);
    }

    /**
     * Has unit
     *
     * @return bool
     */
    public function hasUnit(): bool
    {
        return !empty($this->unit);
    }

    /**
     * Has city
     *
     * @return bool
     */
    public function hasCity(): bool
    {
        return !empty($this->city);
    }

    /**
     * Has postal code
     *
     * @return bool
     */
    public function hasPostalCode(): bool
    {
        return !empty($this->postalCode);
    }

    /**
     * Has zip 4
     *
     * @return bool
     */
    public function hasZip4(): bool
    {
        return !empty($this->zip4);
    }

    /**
     * Has state name
     *
     * @return bool
     */
    public function hasStateName(): bool
    {
        return !empty($this->stateName);
    }

    /**
     * Has state code
     *
     * @return bool
     */
    public function hasStateCode(): bool
    {
        return !empty($this->stateCode);
    }

    /**
     * Has country
     *
     * @return bool
     */
    public function hasCountry(): bool
    {
        return !empty($this->country);
    }

    /**
     * Method to get full address
     *
     * @param  string  $delimiter
     * @param  bool $useStateCode
     * @param  bool $includeCountry
     * @return string
     */
    public function getFullAddress(string $delimiter = ', ', bool $useStateCode = true, bool $includeCountry = false): string
    {
        $fullAddress  = [];
        $addressLine1 = null;

        if (!empty($this->streetNumber)) {
            $addressLine1 = $this->streetNumber;
        }

        if (!empty($this->streetName)) {
            $streetName = $this->streetName;

            if (!empty($this->direction)) {
                if ($this->directionPosition == 1) {
                    $streetName = $streetName . ' ' . $this->direction;
                } else {
                    $streetName = $this->direction . ' ' . $streetName;
                }
            }

            if (!empty($this->routeType)) {
                $streetName .= ' ' . $this->routeType;
            }

            if (!empty($addressLine1)) {
                $addressLine1 .= ' ' . $streetName;
            } else {
                $addressLine1 = $streetName;
            }
        }

        if (!empty($addressLine1)) {
            $fullAddress[] = $addressLine1;
        }

        if (!empty($this->unit)) {
            $fullAddress[] = $this->unit;
        }

        if (!empty($this->city) && (!empty($this->stateCode) || !empty($this->stateName))) {
            $cityState = $this->city;
            if (($useStateCode) && !empty($this->stateCode)) {
                $cityState .= ', ' . $this->stateCode;
            } else if (!empty($this->stateName)) {
                $cityState .= ', ' . $this->stateName;
            }

            if (!empty($this->postalCode)) {
                $cityState .= ' ' . $this->postalCode;
                if (!empty($this->zip4)) {
                    $cityState .= '-' . $this->zip4;
                }
            }

            $fullAddress[] = $cityState;
        }

        if (($includeCountry) && !empty($this->country)) {
            $fullAddress[] = $this->country;
        }

        return implode($delimiter, $fullAddress);
    }

    /**
     * Parse method
     *
     * @param  ?string $address
     * @throws Exception
     * @return static
     */
    public function parse(?string $address = null): static
    {
        if (empty($this->data) && empty($address)) {
            throw new Exception('Error: You must pass an address string to the parser object.');
        }

        if ((null === $address) && !empty($this->data)) {
            $address = $this->data;
        } else if ((null !== $address) && empty($this->data)) {
            $this->data = $address;
        }

        $addressValues = new AddressValues();
        $addressArray  = $this->clean($address);
        $geoResults    = $this->parseGeo($addressArray, $addressValues);

        $streetArray = [];

        // Build street array from remaining unprocessed lines
        if (!empty($geoResults['linesProcessed'])) {
            foreach ($addressArray as $i => $addressLine) {
                if (!in_array($i, $geoResults['linesProcessed'])) {
                    $streetArray[] = $addressLine;
                }
            }
        } else {
            $streetArray = $addressArray;
        }

        $locationResults    = $this->parseLocation($streetArray, $addressValues);
        $this->streetNumber = $locationResults['streetNumber']['value'];
        $this->streetName   = $locationResults['streetName']['value'];
        $this->routeType    = $locationResults['route']['value'];
        $this->direction    = $locationResults['direction']['value'];
        $this->unit         = $locationResults['unit']['value'];
        $this->city         = $geoResults['city']['value'];
        $this->postalCode   = $geoResults['postalCode']['value'];
        $this->zip4         = $geoResults['zip4']['value'];
        $this->stateName    = $geoResults['state']['stateName'];
        $this->stateCode    = $geoResults['state']['stateCode'];
        $this->country      = $geoResults['country']['value'];

        if (!empty($locationResults['direction']['value']) && !empty($locationResults['direction']['position']) &&
            !empty($locationResults['streetName']['value']) && !empty($locationResults['streetName']['position'])) {
            if ($locationResults['direction']['position'][0] == $locationResults['streetName']['position'][0]) {
                if ($locationResults['direction']['position'][1] < $locationResults['streetName']['position'][1]) {
                    $this->directionPosition = 0;
                } else if ($locationResults['direction']['position'][1] > $locationResults['streetName']['position'][1]) {
                    $this->directionPosition = 1;
                }
            }
        }

        return $this;
    }

    /**
     * To array method
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'streetNumber' => $this->streetNumber,
            'streetName'   => $this->streetName,
            'routeType'    => $this->routeType,
            'direction'    => $this->direction,
            'unit'         => $this->unit,
            'city'         => $this->city,
            'postalCode'   => $this->postalCode,
            'zip4'         => $this->zip4,
            'stateName'    => $this->stateName,
            'stateCode'    => $this->stateCode,
            'country'      => $this->country,
        ];
    }

    /**
     * Clean method
     *
     * @param  string $address
     * @return array
     */
    public function clean(string $address): array
    {
        // Multiple spaces
        $address = preg_replace('/\s+/', ' ', $address);

        // Bad dash format
        $address = str_replace([' -', '- '], '-', $address);

        // Split into array by comma, semi-colon, newline, and/or tab delimiters
        return array_filter(array_map('trim', preg_split('/,|\t|\n|\r|;/', $address)));
    }

    /**
     * Parse geo (country, postal, city and state)
     *
     * @param  array         $addressArray
     * @param  AddressValues $addressValues
     * @return array
     */
    public function parseGeo(array $addressArray, AddressValues $addressValues): array
    {
        $city           = null;
        $state          = null;
        $stateName      = null;
        $stateCode      = null;
        $postalCode     = null;
        $zip4           = null;
        $country        = null;
        $cityPos        = null;
        $statePos       = null;
        $postalCodePos  = null;
        $zip4Pos        = null;
        $countryPos     = null;
        $linesProcessed = [];

        $countries = [
            'US' => ['US', 'U.S.', 'USA', 'U.S.A', 'United States'],
            'CA' => ['CA', 'CAN', 'Canada']
        ];

        $postalRegex = [
            'US' => '/(\d{9})|(\d{5}-\d{4}|(\d{5}))/',      // US Zip Code
            'CA' => '/[A-Za-z]\d[A-Za-z][ -]?\d[A-Za-z]\d/i' // CA Postal Code
        ];

        $usStates      = $addressValues->getStates('US');
        $caStates      = $addressValues->getStates('CA');
        $usStatesRegex = '/' . implode('|', array_keys($usStates)) . '|' . implode('|', array_values($usStates)) . '/i';
        $caStatesRegex = '/' . implode('|', array_keys($caStates)) . '|' . implode('|', array_values($caStates)) . '/i';
        $usRegex       = '/' . implode('|', $countries['US']) . '/i';
        $caRegex       = '/' . implode('|', $countries['CA']) . '/i';

        krsort($addressArray);

        foreach ($addressArray as $i => $address) {
            $cityMatches    = [];
            $stateMatches   = [];
            $postalMatches  = [];
            $countryMatches = [];

            // Attempt to find the country
            if (empty($country)) {
                if (preg_match($caRegex, $address, $countryMatches, PREG_OFFSET_CAPTURE) === 1) {
                    $country = 'CA';
                } else if (preg_match($usRegex, $address, $countryMatches, PREG_OFFSET_CAPTURE) === 1) {
                    $country = 'US';
                }

                if (isset($countryMatches[0]) && isset($countryMatches[0][0])) {
                    $country = $countryMatches[0][0];
                    if (isset($countryMatches[0][1])) {
                        $countryPos = [$i, $countryMatches[0][1]];
                        if (!in_array($i, $linesProcessed)) {
                            $linesProcessed[] = $i;
                        }
                    }
                }
            }

            // Attempt to find postal code
            if (empty($postalCode)) {
                if (preg_match($postalRegex['CA'], $address, $postalMatches, PREG_OFFSET_CAPTURE) === 1) {
                    $country = 'CA';
                    if (isset($postalMatches[0]) && isset($postalMatches[0][0])) {
                        $postalCode = $postalMatches[0][0];
                        if (isset($postalMatches[0][1])) {
                            $postalCodePos = [$i, $postalMatches[0][1]];
                            if (!in_array($i, $linesProcessed)) {
                                $linesProcessed[] = $i;
                            }
                        }
                    }
                } else {
                    preg_match_all($postalRegex['US'], $address, $postalMatches, PREG_OFFSET_CAPTURE);
                    $postalMatches = $this->filterMatches($postalMatches, true);

                    // Single line zip code
                    if ((count($postalMatches) == 1) && isset($postalMatches[0]) && isset($postalMatches[0][0]) &&
                        isset($postalMatches[0][1]) && (($postalMatches[0][1] != 0) || ($postalMatches[0][1] == 0) && (strlen($postalMatches[0][0]) == strlen($address)))) {
                        $postalCode    = $postalMatches[0][0];
                        $postalCodePos = [$i, $postalMatches[0][1]];
                        if (!in_array($i, $linesProcessed)) {
                            $linesProcessed[] = $i;
                        }
                    // Found another match that's possibly the address number, use the one at the end of the line
                    } else if (count($postalMatches) > 1) {
                        $lastMatch     = end($postalMatches);
                        $postalCode    = $lastMatch[0];
                        $postalCodePos = [$i, $lastMatch[1]];
                        if (!in_array($i, $linesProcessed)) {
                            $linesProcessed[] = $i;
                        }
                    }

                    if (!empty($postalCode) && empty($country)) {
                        $country = 'US';
                    }
                }
            }

            // Attempt to find the state
            if (empty($state)) {
                if ($country == 'CA') {
                    preg_match($caStatesRegex, $address, $stateMatches, PREG_OFFSET_CAPTURE);
                } else if ($country == 'US') {
                    preg_match($usStatesRegex, $address, $stateMatches, PREG_OFFSET_CAPTURE);
                }
            }

            if (!empty($stateMatches)) {
                if (isset($stateMatches[0]) && isset($stateMatches[0][0])) {
                    $state = $stateMatches[0][0];
                    if (isset($stateMatches[0][1])) {
                        $statePos = [$i, $stateMatches[0][1]];
                        if (!in_array($i, $linesProcessed)) {
                            $linesProcessed[] = $i;
                        }
                    }
                }
            }

            // Attempt to find city
            if (!empty($state) && !empty($country) && empty($city)) {
                $citiesRegex = '/' . implode('|', $addressValues->getCities($state, $country)) . '/i';
                preg_match($citiesRegex, $address, $cityMatches, PREG_OFFSET_CAPTURE);
                if (!empty($cityMatches)) {
                    if (isset($cityMatches[0]) && isset($cityMatches[0][0])) {
                        $city = $cityMatches[0][0];
                        if (isset($cityMatches[0][1])) {
                            $cityPos = [$i, $cityMatches[0][1]];
                            if (!in_array($i, $linesProcessed)) {
                                $linesProcessed[] = $i;
                            }
                        }
                    }
                }
            }
        }

        // Get state name
        if (!empty($state)) {
            if ((strlen($state) == 2) && isset($usStates[$state])) {
                $stateName = $usStates[$state];
                $stateCode = $state;
            } else if ((strlen($state) > 2) && in_array($state, $usStates)) {
                $stateCode = array_search($state, $usStates);
                $stateName = $state;
            }
        }

        // Attempt to get the zip 4
        if (($country == 'US') && !empty($postalCode)) {
            if (strpos($postalCode, '-') !== false) {
                [$postalCode, $zip4] = array_map('trim', explode('-', $postalCode));
                $zip4Pos             = $postalCodePos;
                $zip4Pos[1]         += 6;
            } else if (strlen($postalCode) == 9) {
                $zip4       = substr($postalCode, -4);
                $postalCode = substr($postalCode, 0, 5);
                $zip4Pos     = $postalCodePos;
                $zip4Pos[1] += 5;
            }
        }

        return [
            'city'           => ['value' => $city, 'position' => $cityPos],
            'state'          => ['value' => $state, 'position' => $statePos, 'stateName' => $stateName, 'stateCode' => $stateCode],
            'postalCode'     => ['value' => $postalCode, 'position' => $postalCodePos],
            'zip4'           => ['value' => $zip4, 'position' => $zip4Pos],
            'country'        => ['value' => $country, 'position' => $countryPos],
            'linesProcessed' => $linesProcessed,
        ];
    }

    /**
     * Parse location (street address)
     *
     * @param  array         $streetArray
     * @param  AddressValues $addressValues
     * @return array
     */
    public function parseLocation(array $streetArray, AddressValues $addressValues): array
    {
        $streetNumber    = null;
        $streetName      = null;
        $route           = null;
        $unit            = null;
        $direction       = null;
        $streetNumberPos = null;
        $streetNamePos   = null;
        $routePos        = null;
        $unitPos         = null;
        $directionPos    = null;
        $linesProcessed  = [];


        $commonRoutes      = $addressValues->getCommonRouteTypes();
        $routes            = $addressValues->getRouteTypes(true);
        $directions        = $addressValues->getDirections();
        $commonRoutesRegex = '/\s' . implode('|\s', $commonRoutes) . '/i';
        $routesRegex       = '/\s' . implode('|\s', $routes) . '/i';
        $directionsRegex   = '/' . str_replace('.', '\.', implode('|', $directions)) . '/i';

        foreach ($streetArray as $i => $address) {
            $routeMatches     = [];
            $directionMatches = [];

            // Attempt to find the country
            if (empty($route)) {
                preg_match($commonRoutesRegex, $address, $routeMatches, PREG_OFFSET_CAPTURE);
                if (empty($routeMatches)) {
                    preg_match($routesRegex, $address, $routeMatches, PREG_OFFSET_CAPTURE);
                }
                if (!empty($routeMatches)) {
                    if (isset($routeMatches[0]) && isset($routeMatches[0][0])) {
                        $route = $routeMatches[0][0];
                        if (isset($routeMatches[0][1])) {
                            $routePos = [$i, $routeMatches[0][1]];
                            if (!in_array($i, $linesProcessed)) {
                                $linesProcessed[] = $i;
                            }
                        }
                    }
                }
            }

            // Attempt to find the direction
            if (empty($direction)) {
                preg_match($directionsRegex, $address, $directionMatches, PREG_OFFSET_CAPTURE);
                if (!empty($directionMatches)) {
                    if (isset($directionMatches[0]) && isset($directionMatches[0][0])) {
                        $direction = trim($directionMatches[0][0]);
                        if (isset($directionMatches[0][1])) {
                            $directionPos = [$i, $directionMatches[0][1]];
                            if (!in_array($i, $linesProcessed)) {
                                $linesProcessed[] = $i;
                            }
                        }
                    }
                }
            }

            // Attempt to find the unit
            if (!empty($route) && !empty($routePos) && empty($unit)) {
                $curLine = ($routePos[0] != $i) ? $streetArray[$routePos[0]] : $address;
                $unit    = substr($curLine, ($routePos[1] + strlen($route)));
                $unitPos = [$routePos[0], $routePos[0] + strlen($unit)];
                $unit    = trim($unit);
                if (($unit == '.') || ($unit == ',')) {
                    $unit = null;
                }
            }

            // Attempt to find the street address
            if (!empty($route) && !empty($routePos) && empty($streetName)) {
                $curLine = ($routePos[0] != $i) ? $streetArray[$routePos[0]] : $address;
                $street  = substr($curLine, 0, $routePos[1]);

                if (!empty($direction) && strpos($street, $direction) !== false) {
                    $street = str_replace($direction, '', $street);
                }

                $street = trim(preg_replace('/\s+/', ' ', $street));

                // Assume "123 Main"
                if (strpos($street, ' ') !== false) {
                    $streetNumber = trim(substr($street, 0, strpos($street, ' ')));
                    $streetName   = trim(substr($street, (strpos($street, ' ') + 1)));

                    $streetNumberPos = [$routePos[0], strpos($address, $streetNumber)];
                    $streetNamePos   = [$routePos[0], strpos($address, $streetName)];
                } else {
                    // Assume either a street number or name
                    if (is_numeric($street)) {
                        $streetNumber    = $street;
                        $streetNumberPos = [$routePos[0], strpos($address, $street)];
                    } else {
                        $streetName    = $street;
                        $streetNamePos = [$routePos[0], strpos($address, $street)];
                    }
                }
            }
        }

        return [
            'streetNumber'   => ['value' => $streetNumber, 'position' => $streetNumberPos],
            'streetName'     => ['value' => $streetName, 'position' => $streetNamePos],
            'route'          => ['value' => $route, 'position' => $routePos],
            'unit'           => ['value' => $unit, 'position' => $unitPos],
            'direction'      => ['value' => $direction, 'position' => $directionPos],
            'linesProcessed' => $linesProcessed,
        ];
    }

    /**
     * Filter out empty matches
     *
     * @param  array $matches
     * @param  bool  $withOffset
     * @return array
     */
    protected function filterMatches(array $matches, bool $withOffset = false): array
    {
        $filteredMatches = [];

        foreach ($matches as $match) {
            foreach ($match as $m) {
                if ($withOffset) {
                    if (isset($m[0]) && isset($m[0][1]) && ($m[0][1] != -1) && !in_array($m, $filteredMatches)) {
                        $filteredMatches[] = $m;
                    }
                } else {
                    if (!empty($m) && !in_array($m, $filteredMatches)) {
                        $filteredMatches[] = $m;
                    }
                }
            }
        }

        return $filteredMatches;
    }

    /**
     * To string method
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->getFullAddress();
    }

}
