<?php

namespace Bestfitcz\AutomotiveJsonLd;

use App\Models\Car;
use Exception;
use Illuminate\Http\Request;

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

        // Return the main offer object script
        // TODO: we should mark one of the object in configuration as the main -> will be echoed to a script in the page
        if (isset($objects['car']) && is_object($objects['car'])) {
            try {
                return $objects['car']->toScript();
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
                //ray($objects,$objName,$config);
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
                    $result .= '-01';
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

        // Check conditions if defined (using reserved key '_conditions')
        if (isset($params['_conditions']) && is_array($params['_conditions'])) {
            foreach ($params['_conditions'] as $condition) {
                if (!$this->evaluateCondition($condition)) {
                    return null; // Condition not met, don't generate image
                }
            }
        }

        $imageObj = new \Spatie\SchemaOrg\ImageObject();

        // Process parameters dynamically (skip reserved keys starting with '_')
        foreach ($params as $property => $pathExpression) {
            if (strpos($property, '_') === 0) {
                continue; // Skip reserved keys like '_conditions'
            }

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

        // Check conditions if defined (using reserved key '_conditions')
        if (isset($params['_conditions']) && is_array($params['_conditions'])) {
            foreach ($params['_conditions'] as $condition) {
                if (!$this->evaluateCondition($condition)) {
                    return []; // Condition not met, return empty array
                }
            }
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

    private function getCarVehicleModelDate($carYear, $params)
    {
        if (empty($carYear)) {
            return null;
        }

        // Check conditions if defined (using reserved key '_conditions')
        if (isset($params['_conditions']) && is_array($params['_conditions'])) {
            foreach ($params['_conditions'] as $condition) {
                if (!$this->evaluateCondition($condition)) {
                    return null; // Condition not met, don't generate vehicleModelDate
                }
            }
        }

        $vehicleModelDateStr = $carYear;

        return $vehicleModelDateStr;
    }

    private function evaluateCondition($condition)
    {
        if (!isset($condition['property']) || !isset($condition['operator']) || !isset($condition['value'])) {
            return true; // Invalid condition, assume true
        }

        $property = $condition['property'];
        $operator = $condition['operator'];
        $expectedValue = $condition['value'];

        // Get the actual value from car data
        $actualValue = $this->getCarValue([[$property]]);
        if ($actualValue === null) {
            return false;
        }

        // Evaluate the condition based on operator
        switch ($operator) {
            case '==':
            case 'equals':
                return $actualValue == $expectedValue;

            case '!=':
            case 'not_equals':
                return $actualValue != $expectedValue;

            case '>':
            case 'greater':
                return $actualValue > $expectedValue;

            case '>=':
            case 'greater_equals':
                return $actualValue >= $expectedValue;

            case '<':
            case 'lower':
                return $actualValue < $expectedValue;

            case '<=':
            case 'lower_equals':
                return $actualValue <= $expectedValue;

            case 'in':
                return is_array($expectedValue) && in_array($actualValue, $expectedValue);

            case 'not_in':
                return is_array($expectedValue) && !in_array($actualValue, $expectedValue);

            default:
                return true; // Unknown operator, assume true
        }
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

    public function generatePageBreadcrumbList($pagetype = null)
    {
        $requestData = [
            'route_name' => request()->route() ? request()->route()->getName() : '',
            'route_parameters' => request()->route() ? request()->route()->parameters() : [],
            'query_parameters' => request()->query(),
            'current_url' => request()->url(),
            'segment1' => request()->segment(1),
            'pagetype' => $pagetype ?? null
        ];

        // Build breadcrumb data internally based on request and car data
        $breadcrumbData = $this->buildBreadcrumbData($requestData);

        $breadcrumbConfig = $this->configuration['breadcrumb'] ?? [];

        if (empty($breadcrumbConfig)) {
            return '';
        }

        // Build breadcrumb list
        $breadcrumbList = new \Spatie\SchemaOrg\BreadcrumbList();
        $listItems = [];

        // Process breadcrumb items from config
        if (isset($breadcrumbConfig['items']) && is_array($breadcrumbConfig['items'])) {
            $position = 1;
            foreach ($breadcrumbConfig['items'] as $itemConfig) {
                // Check if item should be included based on conditions
                if (isset($itemConfig['condition'])) {
                    if (!$this->evaluateBreadcrumbCondition($itemConfig['condition'], $breadcrumbData)) {
                        continue;
                    }
                }

                $listItem = new \Spatie\SchemaOrg\ListItem();
                $listItem->position($position);

                // Set name
                if (isset($itemConfig['name'])) {
                    $name = $this->getBreadcrumbValue($itemConfig['name'], $breadcrumbData);
                    if ($name) {
                        $listItem->name($name);
                    }
                }

                // Set URL
                if (isset($itemConfig['url'])) {
                    $url = $this->getBreadcrumbValue($itemConfig['url'], $breadcrumbData);
                    if ($url) {
                        $listItem->item($url);
                    }
                }

                $listItems[] = $listItem;
                $position++;
            }
        }

        if (!empty($listItems)) {
            $breadcrumbList->itemListElement($listItems);
            return $breadcrumbList->toScript();
        }

        return '';
    }

    private function getBreadcrumbValue($config, $breadcrumbData)
    {
        if (is_string($config)) {
            return $config;
        }

        if (!is_array($config) || !isset($config['type'])) {
            return null;
        }

        switch ($config['type']) {
            case 'static':
                return $config['value'] ?? '';

            case 'car':
                if (isset($config['path'])) {
                    return $this->getCarValue([$config['path']]);
                }
                break;

            case 'data':
                if (isset($config['path']) && is_array($config['path'])) {
                    return $this->extractValueFromData($breadcrumbData, $config['path']);
                }
                break;

            case 'url':
                if (isset($config['value'])) {
                    return $this->makeUrl($config['value']);
                }
                break;
        }

        return null;
    }

    private function extractValueFromData($data, $path)
    {
        $value = $data;
        foreach ($path as $key) {
            if (isset($value[$key])) {
                $value = $value[$key];
            } else {
                return null;
            }
        }
        return $value;
    }

    private function evaluateBreadcrumbCondition($condition, $breadcrumbData)
    {
        if (!isset($condition['type'])) {
            return true;
        }

        switch ($condition['type']) {
            case 'isset':
                if (isset($condition['path'])) {
                    if ($condition['source'] === 'car') {
                        return $this->getCarValue([$condition['path']]) !== null;
                    } elseif ($condition['source'] === 'data') {
                        return $this->extractValueFromData($breadcrumbData, $condition['path']) !== null;
                    }
                }
                break;

            case 'equals':
                if (isset($condition['path']) && isset($condition['value'])) {
                    $actualValue = null;
                    if ($condition['source'] === 'car') {
                        $actualValue = $this->getCarValue([$condition['path']]);
                    } elseif ($condition['source'] === 'data') {
                        $actualValue = $this->extractValueFromData($breadcrumbData, $condition['path']);
                    }
                    return $actualValue == $condition['value'];
                }
                break;
        }

        return true;
    }

    private function buildBreadcrumbData($requestData = [])
    {
        $breadcrumbData = [];

        // Extract data from request
        $routeName = $requestData['route_name'] ?? '';
        $routeParameters = $requestData['route_parameters'] ?? [];
        $queryParameters = $requestData['query_parameters'] ?? [];
        $currentUrl = $requestData['current_url'] ?? '';
        $segment1 = $requestData['segment1'] ?? '';
        $pagetype = $requestData['pagetype'] ?? '';

        // Detect page type and build breadcrumb structure
        $isHomePage = empty($segment1) || $segment1 === '' || $currentUrl === $this->makeUrl('/');
        $isCarTypePage = in_array($segment1, ['nove-vozy', 'certifikovane-vozy', 'ojete-vozy', 'skladove-vozy']);
        $isListPage = str_contains($routeName, 'list') || str_contains($currentUrl, 'vysledky-hledani');
        $isDetailPage = str_contains($routeName, 'detail') || (!empty($this->car) && isset($this->car['url_detail']));
        $isOtherPage = !$isHomePage && !$isCarTypePage && !$isListPage && !$isDetailPage;

        // Build breadcrumb structure based on page type and query parameters
        if ($isHomePage) {
            // Home page: Just "Domů"
            $breadcrumbData['home_only'] = true;
        } elseif ($isOtherPage) {
            // Other pages like "Výkup vozů": Home > Page name
            $breadcrumbData['other_page'] = $this->getPageNameFromUrl($segment1, $currentUrl);
        } elseif ($isCarTypePage || $isListPage || $isDetailPage) {
            // Car-related pages: Progressive breadcrumb structure

            // Determine car type information
            $carTypeInfo = $this->getCarTypeInfo($pagetype, $segment1);
            $breadcrumbData['car_type_name'] = $carTypeInfo['name'];
            $breadcrumbData['car_type_url'] = $carTypeInfo['url'];
            $breadcrumbData['car_type_slug'] = $carTypeInfo['slug'];

            if ($isCarTypePage && empty($queryParameters) && !$isListPage && !$isDetailPage) {
                // Just category homepage: Home > Category (no search results level)
                // The car type info is already set above, no additional breadcrumbs needed
            } else {
                // Build progressive search breadcrumbs
                $this->buildProgressiveBreadcrumbs($breadcrumbData, $queryParameters, $routeParameters, $carTypeInfo, $isDetailPage);
            }
        }

        return $breadcrumbData;
    }

    private function getCarTypeInfo($pagetype, $segment1)
    {
        // Map pagetype or URL segment to car type info
        $carTypes = [
            'stockcars' => ['name' => 'Nové vozy', 'slug' => 'nove-vozy'],
            'certifiedcars' => ['name' => 'Certifikované vozy', 'slug' => 'certifikovane-vozy'],
            'usedcars' => ['name' => 'Ojeté vozy', 'slug' => 'ojete-vozy']
        ];

        // Try pagetype first
        if (!empty($pagetype) && isset($carTypes[$pagetype])) {
            $info = $carTypes[$pagetype];
        } else {
            // Fallback to URL segment
            $urlToPagetype = [
                'nove-vozy' => 'stockcars',
                'skladove-vozy' => 'stockcars',
                'certifikovane-vozy' => 'certifiedcars',
                'ojete-vozy' => 'usedcars'
            ];

            $pagetypeFromUrl = $urlToPagetype[$segment1] ?? 'stockcars';
            $info = $carTypes[$pagetypeFromUrl];
        }

        return [
            'name' => $info['name'],
            'slug' => $info['slug'],
            'url' => $this->makeUrl('/' . $info['slug'])
        ];
    }

    private function getPageNameFromUrl($segment1, $currentUrl)
    {
        // Map common page URLs to names
        $pageNames = [
            'vykup-vozu' => 'Výkup vozů',
            'financovani' => 'Financování',
            'pojisteni' => 'Pojištění',
            'servis' => 'Servis'
        ];

        return $pageNames[$segment1] ?? ucfirst($segment1);
    }

    private function buildProgressiveBreadcrumbs(&$breadcrumbData, $queryParameters, $routeParameters, $carTypeInfo, $isDetailPage)
    {
        // Base search results item
        $breadcrumbData['search_results'] = [
            'name' => $carTypeInfo['name'] . ' - Výsledky hledání',
            'url' => $this->buildListUrl($this->getPagetypeFromCarType($carTypeInfo['slug']), [])
        ];

        // Extract filter hierarchy from query parameters or route parameters for detail pages
        $manufacturerFilter = $this->extractQueryParam($queryParameters, 'manufacturer');
        $modelFilter = $this->extractQueryParam($queryParameters, 'model');
        $bodyFilter = $this->extractQueryParam($queryParameters, 'body');

        // For detail pages, extract from route parameters if not found in query parameters
        if ($isDetailPage && (empty($manufacturerFilter) || empty($modelFilter) || empty($bodyFilter))) {
            $routeParams = $this->extractDetailPageRouteParams($routeParameters);
            if ($routeParams) {
                $manufacturerFilter = $manufacturerFilter ?: $routeParams['manufacturer'];
                $modelFilter = $modelFilter ?: $routeParams['model'];
                $bodyFilter = $bodyFilter ?: $routeParams['body'];
            }
        }

        // Build progressive filter levels
        if (!empty($manufacturerFilter)) {
            $manufacturerName = $this->getFilterDisplayName('manufacturer', $manufacturerFilter);
            if ($manufacturerName) {
                $breadcrumbData['manufacturer'] = [
                    'name' => $carTypeInfo['name'] . ' - Výsledky hledání - ' . $manufacturerName,
                    'url' => $this->buildListUrl($this->getPagetypeFromCarType($carTypeInfo['slug']), [
                        'manufacturer' => [$manufacturerFilter]
                    ])
                ];

                if (!empty($modelFilter)) {
                    $modelName = $this->getFilterDisplayName('model', $modelFilter);
                    if ($modelName) {
                        $breadcrumbData['model'] = [
                            'name' => $carTypeInfo['name'] . ' - Výsledky hledání - ' . $manufacturerName . ' - ' . $modelName,
                            'url' => $this->buildListUrl($this->getPagetypeFromCarType($carTypeInfo['slug']), [
                                'manufacturer' => [$manufacturerFilter],
                                'model' => [$modelFilter]
                            ])
                        ];

                        if (!empty($bodyFilter)) {
                            $bodyName = $this->getFilterDisplayName('body', $bodyFilter);
                            if ($bodyName) {
                                $breadcrumbData['body'] = [
                                    'name' => $carTypeInfo['name'] . ' - Výsledky hledání - ' . $manufacturerName . ' - ' . $modelName . ' - ' . $bodyName,
                                    'url' => $this->buildListUrl($this->getPagetypeFromCarType($carTypeInfo['slug']), [
                                        'manufacturer' => [$manufacturerFilter],
                                        'model' => [$modelFilter],
                                        'body' => [$bodyFilter]
                                    ])
                                ];
                            }
                        }
                    }
                }
            }
        }

        // Detail page final item (no URL for current page)
        if ($isDetailPage) {
            $detailTitle = $carTypeInfo['name'] . ' - Detail vozu';
            if (!empty($this->car['full_title'])) {
                $detailTitle .= ' - ' . $this->car['full_title'];
            }
            $breadcrumbData['detail'] = [
                'name' => $detailTitle,
                'url' => null
            ];
        }
    }

    private function extractQueryParam($queryParameters, $paramName)
    {
        if (empty($queryParameters[$paramName])) {
            return null;
        }

        $param = $queryParameters[$paramName];

        // Handle array format like manufacturer[0]=citroen
        if (is_array($param)) {
            return !empty($param[0]) ? $param[0] : null;
        }

        return $param;
    }

    private function extractDetailPageRouteParams($routeParameters)
    {
        // Extract manufacturer, model, and body from detail page route parameters
        // Detail page URL pattern: /{car-type}/detail-vozu/{manufacturer}/{model}/{body}/{version}/{id}

        $extracted = [
            'manufacturer' => null,
            'model' => null,
            'body' => null
        ];

        // Check if we have the expected route parameters
        if (isset($routeParameters['manufacturer'])) {
            $extracted['manufacturer'] = $routeParameters['manufacturer'];
        }

        if (isset($routeParameters['model'])) {
            $extracted['model'] = $routeParameters['model'];
        }

        if (isset($routeParameters['body'])) {
            $extracted['body'] = $routeParameters['body'];
        }

        // If any values are found, return the array, otherwise null
        if ($extracted['manufacturer'] || $extracted['model'] || $extracted['body']) {
            return $extracted;
        }

        return null;
    }

    private function getFilterDisplayName($filterType, $filterValue)
    {
        // Try to get display name from car data first
        if (!empty($this->car)) {
            switch ($filterType) {
                case 'manufacturer':
                    if (isset($this->car['manufacturer']['name']) &&
                        strtolower($this->car['manufacturer']['slug']) === strtolower($filterValue)) {
                        return $this->car['manufacturer']['name'];
                    }
                    break;
                case 'model':
                    if (isset($this->car['model']['name']) &&
                        strtolower($this->car['model']['slug']) === strtolower($filterValue)) {
                        return $this->car['model']['name'];
                    }
                    break;
                case 'body':
                    if (isset($this->car['body']['name']) &&
                        strtolower($this->car['body']['slug']) === strtolower($filterValue)) {
                        return $this->car['body']['name'];
                    }
                    break;
            }
        }

        // Fallback to capitalize the slug
        return ucfirst(str_replace('-', ' ', $filterValue));
    }

    private function getPagetypeFromCarType($carTypeSlug)
    {
        $slugToPagetype = [
            'nove-vozy' => 'stockcars',
            'certifikovane-vozy' => 'certifiedcars',
            'ojete-vozy' => 'usedcars'
        ];

        return $slugToPagetype[$carTypeSlug] ?? 'stockcars';
    }

    private function buildListUrl($routePrefix, $params = [])
    {
        // Map route prefix to actual route name
        $routeMap = [
            'stockcars' => 'stockcars.list',
            'certifiedcars' => 'certifiedcars.list',
            'usedcars' => 'usedcars.list'
        ];

        $routeName = $routeMap[$routePrefix] ?? 'stockcars.list';

        // Check if route exists
        if (app('router')->has($routeName)) {
            return route($routeName, $params);
        }

        // Fallback to URL construction
        $urlMap = [
            'stockcars' => '/nove-vozy/vysledky-hledani',
            'certifiedcars' => '/certifikovane-vozy/vysledky-hledani',
            'usedcars' => '/ojete-vozy/vysledky-hledani'
        ];

        $baseUrl = $urlMap[$routePrefix] ?? '/nove-vozy/vysledky-hledani';

        if (!empty($params)) {
            $queryString = http_build_query($params);
            return $this->makeUrl($baseUrl . '?' . $queryString);
        }

        return $this->makeUrl($baseUrl);
    }

    private function makeUrl($path)
    {
        // Check if Laravel's url helper is available
        if (function_exists('url')) {
            return url($path);
        }

        // Fallback to simple URL construction
        // This is for testing or when Laravel app is not fully bootstrapped
        if (strpos($path, 'http') === 0) {
            return $path;
        }

        // Assume we're constructing a relative URL
        return $path;
    }

    /**
     * Generate ItemList JSON-LD for car listing pages
     *
     * @param array $cars Array of car objects from listing page
     * @return string JSON-LD script tag or empty string
     */
    public function generateCarList(array $cars)
    {
        if (empty($cars) ||
            !request()->is($this->configuration['page_type']['car_list']['generate_conditions']['url_patern']) ||
            !in_array(request()->segment(1), $this->configuration['page_type']['car_list']['generate_conditions']['url_segments'])
        ) {
            return '';
        }

        try {
            // Create ItemList schema
            $itemList = new \Spatie\SchemaOrg\ItemList();
            $itemList->itemListOrder('https://schema.org/ItemListOrderAscending');
            $itemList->numberOfItems(count($cars));

            $listItems = [];
            $position = 1;

            foreach ($cars as $carData) {
                // Convert car data to array if it's an object
                if (is_object($carData)) {
                    $carData = (array) $carData;
                }

                // Create ListItem
                $listItem = new \Spatie\SchemaOrg\ListItem();
                $listItem->position($position);

                // Create simplified Car object
                $car = $this->createSimplifiedCarObject($carData);
                if ($car) {
                    $listItem->item($car);
                    $listItems[] = $listItem;
                }

                $position++;
            }

            if (!empty($listItems)) {
                $itemList->itemListElement($listItems);
                return $itemList->toScript();
            }
        } catch (\Exception $e) {
            // Silently fail and return empty string
            return '';
        }

        return '';
    }

    /**
     * Create simplified Car object for listing pages
     *
     * @param array $carData Single car data from listing
     * @return \Spatie\SchemaOrg\Car|null
     */
    private function createSimplifiedCarObject(array $carData)
    {
        try {
            $car = new \Spatie\SchemaOrg\Car();

            // Basic car information
            if (!empty($carData['manufacturer']['name']) && !empty($carData['model']['name'])) {
                $car->name($carData['manufacturer']['name'] . ' ' . $carData['model']['name']);
                $car->manufacturer($carData['manufacturer']['name']);
                $car->model($carData['model']['name']);

                // Add brand (required by Google)
                $brand = new \Spatie\SchemaOrg\Brand();
                $brand->name($carData['manufacturer']['name']);
                $car->brand($brand);
            }

            // Add version as description if available
            if (!empty($carData['version'])) {
                $car->description($carData['version']);
            }

            // Vehicle specifications - mileageFromOdometer with QuantitativeValue (required by Google)
            if (!empty($carData['kilometers'])) {
                $mileage = new \Spatie\SchemaOrg\QuantitativeValue();
                $mileage->value($carData['kilometers']);
                $mileage->unitCode('KMT'); // UN/CEFACT code for kilometers
                $car->mileageFromOdometer($mileage);
            }

            // Add vehicleModelDate (required by Google)
            if (!empty($carData['year'])) {
                $car->vehicleModelDate((string)$carData['year']);

                // Also keep dateVehicleFirstRegistered if month is available
                if (!empty($carData['month'])) {
                    $yearMonth = $carData['year'] . '-' . sprintf("%02d", $carData['month']) . '-01';
                    $car->dateVehicleFirstRegistered($yearMonth);
                } else {
                    $car->dateVehicleFirstRegistered((string)$carData['year']);
                }
            }

            // Add VIN (Vehicle Identification Number) if available
            if (!empty($carData['vin'])) {
                $car->vehicleIdentificationNumber($carData['vin']);
            }

            if (!empty($carData['fuel']['name'])) {
                $car->fuelType($carData['fuel']['name']);
            }

            if (!empty($carData['transmission']['name'])) {
                $car->vehicleTransmission($carData['transmission']['name']);
            }

            // Car condition
            $carStateId = $carData['car_state_id'] ?? $carData['car_state']['id'] ?? null;
            if ($carStateId) {
                $condition = $this->getCarStageCondition($carStateId, []);
                if ($condition) {
                    $car->itemCondition($condition);
                }
            }

            // Image
            if (!empty($carData['image_thumbnail_list'])) {
                $car->image($carData['image_thumbnail_list']);
            } elseif (!empty($carData['images'][0]['url']['515_387'])) {
                $car->image($carData['images'][0]['url']['515_387']);
            }

            // URL to detail page
            if (!empty($carData['url_detail'])) {
                $car->url($carData['url_detail']);
            }

            // Create Offer with price and seller
            $offer = new \Spatie\SchemaOrg\Offer();

            if (!empty($carData['price'])) {
                $offer->price($carData['price']);
                $offer->priceCurrency('CZK');
            }

            $offer->availability('https://schema.org/InStock');

            if (!empty($carData['url_detail'])) {
                $offer->url($carData['url_detail']);
            }

            // Add dealer as seller
            if (!empty($carData['dealer'])) {
                $dealer = $this->createSimplifiedDealerObject($carData['dealer']);
                if ($dealer) {
                    $offer->seller($dealer);
                }
            }

            $car->offers($offer);

            return $car;

        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Create simplified AutoDealer object for listing pages
     *
     * @param array $dealerData Dealer information
     * @return \Spatie\SchemaOrg\AutoDealer|null
     */
    private function createSimplifiedDealerObject(array $dealerData)
    {
        try {
            $dealer = new \Spatie\SchemaOrg\AutoDealer();

            if (!empty($dealerData['name'])) {
                $dealer->name($dealerData['name']);
            }

            // Create postal address
            if (!empty($dealerData['street']) || !empty($dealerData['city'])) {
                $address = new \Spatie\SchemaOrg\PostalAddress();

                if (!empty($dealerData['street'])) {
                    $address->streetAddress($dealerData['street']);
                }

                if (!empty($dealerData['city'])) {
                    $address->addressLocality($dealerData['city']);
                }

                if (!empty($dealerData['zip'])) {
                    $address->postalCode($dealerData['zip']);
                }

                $address->addressCountry('CZ');
                $dealer->address($address);
            }

            return $dealer;

        } catch (\Exception $e) {
            return null;
        }
    }

}
