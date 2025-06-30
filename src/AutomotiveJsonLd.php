<?php

namespace Bestfitcz\AutomotiveJsonLd;

use App\Models\Car;
use Exception;

class AutomotiveJsonLd {
    public array $car;
    public array $configuration;

    public function __construct(array $car, array $configuration)
    {
        $this->car = $car;
        $this->configuration = $configuration;
    }

    public function generatePageJsonLd()
    {
        //ray($this->car);
        //ray($this->configuration);

        // AutoDealer schema with extended properties
        $autoDealerObj = new \Spatie\SchemaOrg\AutoDealer();
        $autoDealerObj->name($this->car['dealer']['name']);
        $autoDealerObj->brand($this->car['dealer']['brand']);
        $autoDealerObj->latitude($this->car['dealer']['latitude']);
        $autoDealerObj->longitude($this->car['dealer']['longitude']);

        if (!empty($this->car['dealer']['web'])) {
            $autoDealerObj->url($this->car['dealer']['web']);
        }

        if (!empty($this->car['dealer_contacts']['tel'])) {
            $autoDealerObj->telephone($this->car['dealer_contacts']['tel']);
        } elseif (!empty($this->car['dealer_contacts']['mobile'])) {
            $autoDealerObj->telephone($this->car['dealer_contacts']['mobile']);
        }

        if (!empty($this->car['dealer_contacts']['email'])) {
            $autoDealerObj->email($this->car['dealer_contacts']['email']);
        }

        $autoDealerPostalAddressObj = new \Spatie\SchemaOrg\PostalAddress();
        $autoDealerPostalAddressObj->streetAddress($this->car['dealer']['street']);
        $autoDealerPostalAddressObj->addressLocality($this->car['dealer']['city']);
        $autoDealerPostalAddressObj->postalCode($this->car['dealer']['zip']);
        $autoDealerPostalAddressObj->addressCountry('CZ');
        $autoDealerObj->address($autoDealerPostalAddressObj);

        $geoObj = new \Spatie\SchemaOrg\GeoCoordinates();
        $geoObj->latitude($this->car['dealer']['latitude']);
        $geoObj->longitude($this->car['dealer']['longitude']);
        $autoDealerObj->geo($geoObj);

        // Car schema with extended properties
        $carObj = new \Spatie\SchemaOrg\Car();
        $carObj->name($this->car['full_title']);
        $carObj->manufacturer($this->car['manufacturer']['name']);
        $carObj->model($this->car['model']['name']);

        if (!empty($this->car['description'])) {
            $carObj->description($this->car['description']);
        }

        $carObj->mileageFromOdometer($this->car['kilometers']);
        $carObj->dateVehicleFirstRegistered("{$this->car['year']}-" . sprintf("%02d", $this->car['month']) . "-01");

        if (!empty($this->car['vin'])) {
            $carObj->vehicleIdentificationNumber($this->car['vin']);
        }

        if (!empty($this->car['fuel']['name'])) {
            $carObj->fuelType($this->car['fuel']['name']);
        }

        if (!empty($this->car['transmission']['name'])) {
            $carObj->vehicleTransmission($this->car['transmission']['name']);
        }

        if (!empty($this->car['doors'])) {
            $carObj->numberOfDoors($this->car['doors']);
        }

        if (!empty($this->car['places'])) {
            $carObj->seatingCapacity($this->car['places']);
        }

        if (!empty($this->car['color']['name'])) {
            $carObj->color($this->car['color']['name']);
        }

        if (!empty($this->car['body']['name'])) {
            $carObj->bodyType($this->car['body']['name']);
        }

        if (!empty($this->car['volume'])) {
            $engineObj = new \Spatie\SchemaOrg\EngineSpecification();
            $engineObj->engineDisplacement($this->car['volume'] . ' cm³');
            if (!empty($this->car['power'])) {
                $engineObj->enginePower($this->car['power'] . ' kW');
            }
            if (!empty($this->car['fuel']['name'])) {
                $engineObj->fuelType($this->car['fuel']['name']);
            }
            $carObj->vehicleEngine($engineObj);
        }

        if (!empty($this->car['images']) && is_array($this->car['images'])) {
            $imageObjects = [];
            foreach (array_slice($this->car['images'], 0, 5) as $image) {
                if (!empty($image['url']['1680_1024'])) {
                    $imageObj = new \Spatie\SchemaOrg\ImageObject();
                    $imageObj->url($image['url']['1680_1024']);
                    $imageObj->contentUrl($image['url']['1680_1024']);
                    if (!empty($image['url']['515_387'])) {
                        $imageObj->thumbnailUrl($image['url']['515_387']);
                    }
                    $imageObjects[] = $imageObj;
                }
            }
            if (!empty($imageObjects)) {
                $carObj->image($imageObjects);
            }
        }

        if (!empty($this->car['parameter_groups_with_parameters']) && is_array($this->car['parameter_groups_with_parameters'])) {
            $additionalProperties = [];
            foreach ($this->car['parameter_groups_with_parameters'] as $groupName => $parameters) {
                if (is_array($parameters)) {
                    foreach ($parameters as $parameter) {
                        if (!empty($parameter['name'])) {
                            $propertyObj = new \Spatie\SchemaOrg\PropertyValue();
                            $propertyObj->name($parameter['name']);
                            $propertyObj->propertyID($groupName);
                            $additionalProperties[] = $propertyObj;
                        }
                    }
                }
            }
            if (!empty($additionalProperties)) {
                $carObj->additionalProperty($additionalProperties);
            }
        }

        // Offer schema with extended properties
        $offerObj = new \Spatie\SchemaOrg\Offer();
        $offerObj->itemOffered($carObj);
        $offerObj->offeredBy($autoDealerObj);
        $offerObj->priceCurrency('CZK');
        $offerObj->price($this->car['price']);

        if (!empty($this->car['price_without_VAT'])) {
            $priceSpecObj = new \Spatie\SchemaOrg\PriceSpecification();
            $priceSpecObj->price($this->car['price_without_VAT']);
            $priceSpecObj->priceCurrency('CZK');
            $priceSpecObj->valueAddedTaxIncluded(false);
            $offerObj->priceSpecification($priceSpecObj);
        }

        switch ($this->car['car_state_id'] ?? $this->car['car_state']['id'] ?? null) {
            case '1':
                $offerObj->itemCondition('https://schema.org/UsedCondition');
                break;
            case '2':
                $offerObj->itemCondition('https://schema.org/DemoCondition');
                break;
            case '3':
                $offerObj->itemCondition('https://schema.org/NewCondition');
                break;
        }

        $offerObj->availability('https://schema.org/InStock');
        $offerObj->url($this->car['url_detail']);

        if (!empty($this->car['program']['name'])) {
            $warrantyObj = new \Spatie\SchemaOrg\WarrantyPromise();
            $warrantyObj->name($this->car['program']['name']);
            $offerObj->warranty($warrantyObj);
        }

        return $offerObj->toScript();
    }

    public function generateDetailPageJsonLd()
    {
        $detailConfig = $this->configuration['page_type']['detail'] ?? [];

        if (empty($detailConfig['elements'])) {
            return '';
        }

        $objects = [];
        $this->buildObjectsFromConfig($detailConfig['elements'], $objects);
        //ray($objects);
        // Return the main offer object script
        if (isset($objects['offer']) && is_object($objects['offer'])) {
            try {
                return $objects['offer']->toScript();
            } catch (Exception $e) {
                return '';
            }
        }
        return '';
    }

    private function buildObjectsFromConfig($elements, &$objects)
    {
        // First pass: create all objects
        foreach ($elements as $elementName => $elementConfig) {
            if (!isset($elementConfig['schema'])) {
                continue;
            }

            $schemaClass = $this->parseSchemaClass($elementConfig['schema']);
            if (!$schemaClass) {
                continue;
            }

            $objects[$elementName] = new $schemaClass();
        }

        // Second pass: populate objects (so object references work)
        foreach ($elements as $elementName => $elementConfig) {
            if (isset($objects[$elementName]) && isset($elementConfig['elements'])) {
                $result = $this->populateObjectFromConfig($objects[$elementName], $elementConfig['elements'], $objects);

                // If the result is a direct value (from function call), replace the object
                if ($result !== null) {
                    $objects[$elementName] = $result;
                }
                // If it's a function call that returned null, remove the object
                elseif (isset($elementConfig['elements']['src_type']) && $elementConfig['elements']['src_type'] === 'fce') {
                    unset($objects[$elementName]);
                }
            }
        }
    }

    private function populateObjectFromConfig($object, $elements, &$objects)
    {
        // Check if this is a direct function call configuration
        if (isset($elements['src_type'])) {
            $value = $this->getValueFromConfig($elements, $objects);
            if ($value !== null) {
                return $value; // Return the value instead of trying to set properties
            }
        }

        foreach ($elements as $property => $propertyConfig) {
            if (is_array($propertyConfig) && isset($propertyConfig['src_type'])) {
                $value = $this->getValueFromConfig($propertyConfig, $objects);
                if ($value !== null) {
                    // Special handling for certain properties
                    if ($property === 'parameters' && is_array($value)) {
                        $object->additionalProperty($value);
                    } elseif ($property === 'image') {
                        // Add the single image object to the car
                        $object->image($value);
                    } else {
                        // Only set property if value is not null
                        $object->$property($value);
                    }
                }
            }
        }
    }

    private function getValueFromConfig($config, &$objects = [])
    {
        switch ($config['src_type']) {
            case 'car':
                return $this->getCarValue($config['src'] ?? []);

            case 'str':
                return $config['params'][0] ?? ($config['src'][0] ?? '');

            case 'object':
                // Return reference to already built object
                $objName = is_array($config['src']) ? $config['src'][0] : $config['src'];
                if (isset($objects[$objName])) {
                    return $objects[$objName];
                }
                // Return null if object doesn't exist (like when warranty function returned null)
                return null;

            case 'eval':
                return $this->evaluateExpression($config['params'] ?? []);

            case 'fce':
                return $this->callFunction($config);

            default:
                return null;
        }
    }

    private function getCarValue($srcPath)
    {
        if (empty($srcPath)) {
            return null;
        }

        // Handle multiple fallback sources
        if (isset($srcPath[0]) && is_array($srcPath[0])) {
            foreach ($srcPath as $fallbackPath) {
                $value = $this->extractValueFromPath($fallbackPath);
                if ($value !== null) {
                    return $value;
                }
            }
            return null;
        } else {
            return $this->extractValueFromPath($srcPath);
        }
    }

    private function extractValueFromPath($path)
    {
        $value = $this->car;

        foreach ($path as $key) {
            if (isset($value[$key])) {
                $value = $value[$key];
            } else {
                return null;
            }
        }

        return $value;
    }

    private function parseSchemaClass($schemaString)
    {
        // Remove parentheses and return class name
        $className = str_replace(['()', '\\\\'], ['', '\\'], $schemaString);

        if (class_exists($className)) {
            return $className;
        }

        return null;
    }

    private function evaluateExpression($params)
    {
        // Handle date formatting based on config structure
        $result = '';
        foreach ($params as $param) {
            foreach ($param as $key => $expression) {
                if ($key === 'year') {
                    $result .= $this->car['year'] . '-';
                } elseif ($key === 'month') {
                    $result .= sprintf("%02d", $this->car['month']);
                } elseif ($key === 'day') {
                    $result .= '01';
                }
            }
        }
        return $result;
    }

    private function callFunction($config)
    {
        $functionName = $config['fce_name'] ?? null;
        if (!$functionName || !method_exists($this, $functionName)) {
            return null;
        }

        $srcData = $this->getCarValue($config['src'] ?? []);

        $fceParams = $config['fce_params'] ?? [];
        return $this->$functionName($srcData, $fceParams);
    }

    private function getCarImageObjectsArray($image, $params)
    {
        // Handle single image object
        if (!is_array($image) || empty($image['url']['1680_1024'])) {
            return null;
        }

        $imageObj = new \Spatie\SchemaOrg\ImageObject();

        // Process parameters dynamically
        foreach ($params as $property => $pathExpression) {
            $value = $this->extractValueFromPathExpression($image, $pathExpression);
            if ($value !== null) {
                $imageObj->$property($value);
            }
        }

        return $imageObj;
    }

    private function getCarParametersArray($parameterGroups, $params)
    {
        if (!is_array($parameterGroups)) {
            return [];
        }

        $additionalProperties = [];
        foreach ($parameterGroups as $groupName => $parameters) {
            if (is_array($parameters)) {
                foreach ($parameters as $parameter) {
                    if (!empty($parameter['name'])) {
                        $propertyObj = new \Spatie\SchemaOrg\PropertyValue();
                        $propertyObj->name($parameter['name']);
                        $propertyObj->propertyID($groupName);
                        $additionalProperties[] = $propertyObj;
                    }
                }
            }
        }

        return $additionalProperties;
    }

    private function getCarPriceSpecificationWithoutVat($priceWithoutVat, $params)
    {
        if (empty($priceWithoutVat)) {
            return null;
        }

        $priceSpecObj = new \Spatie\SchemaOrg\PriceSpecification();
        $priceSpecObj->price($priceWithoutVat);
        $priceSpecObj->priceCurrency('CZK');
        $priceSpecObj->valueAddedTaxIncluded(false);

        return $priceSpecObj;
    }

    private function getCarStageCondition($carStateId, $params)
    {
        switch ($carStateId) {
            case '1':
                return 'https://schema.org/UsedCondition';
            case '2':
                return 'https://schema.org/DemoCondition';
            case '3':
                return 'https://schema.org/NewCondition';
            default:
                return null;
        }
    }

    private function getCarProgramWarranty($programName, $params)
    {
        if (empty($programName)) {
            return null;
        }

        $warrantyObj = new \Spatie\SchemaOrg\WarrantyPromise();
        $warrantyObj->name($programName);

        return $warrantyObj;
    }

    private function extractValueFromPathExpression($data, $pathExpression)
    {
        // Handle path expressions like "['name']" or "[['url']['1680_1024']]"
        $pathExpression = trim($pathExpression, '"\'');

        // Remove brackets and split by '][' to get path segments
        $pathExpression = trim($pathExpression, '[]');
        $pathSegments = explode("']['", $pathExpression);

        $value = $data;
        foreach ($pathSegments as $segment) {
            $segment = trim($segment, '"\'');
            if (isset($value[$segment])) {
                $value = $value[$segment];
            } else {
                return null;
            }
        }

        return $value;
    }

}
