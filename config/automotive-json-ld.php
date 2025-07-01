<?php

// config for Bestfitcz/AutomotiveJsonLd
return [
    'page_type' => [
        'detail' => [
            'elements' => [

                'car_brand' => [
                    'schema' => '\Spatie\SchemaOrg\Brand()',
                    'elements' => [
                        'name' => [
                            'src_type' => 'car',
                            'src' => [['manufacturer','name']],
                        ]
                    ]
                ],

                'dealer_postal_address' => [
                    'schema' => '\Spatie\SchemaOrg\PostalAddress()',
                    'elements' => [
                        'streetAddress' => [
                            'src_type' => 'car',
                            'src' => [['dealer', 'street']],
                        ],
                        'addressLocality' => [
                            'src_type' => 'car',
                            'src' => [['dealer', 'city']],
                        ],
                        'postalCode' => [
                            'src_type' => 'car',
                            'src' => [['dealer', 'zip']],
                        ],
                        'addressCountry' => [
                            'src_type' => 'str',
                            'params' => ['CZ'],
                        ],
                    ]
                ],

                'dealer_geo' => [
                    'schema' => '\Spatie\SchemaOrg\GeoCoordinates()',
                    'elements' => [
                        'latitude' => [
                            'src_type' => 'car',
                            'src' => [['dealer', 'latitude']],
                        ],
                        'longitude' => [
                            'src_type' => 'car',
                            'src' => [['dealer', 'longitude']],
                        ],
                    ]
                ],

                'dealer' => [
                    'schema' => '\Spatie\SchemaOrg\AutoDealer()',
                    'elements' => [
                        'name' => [
                            'src_type' => 'car',
                            'src' => [['dealer', 'name']],
                        ],
                        'brand' => [
                            'src_type' => 'car',
                            'src' => [['dealer', 'brand']],
                        ],
                        'latitude' => [
                            'src_type' => 'car',
                            'src' => [['dealer', 'latitude']],
                        ],
                        'longitude' => [
                            'src_type' => 'car',
                            'src' => [['dealer', 'longitude']],
                        ],
                        'url' => [
                            'src_type' => 'car',
                            'src' => [['dealer', 'web']],
                        ],
                        'telephone' => [
                            'src_type' => 'car',
                            'src' => [
                                ['dealer_contacts', 'telephone'],
                                ['dealer_contacts', 'mobile']
                            ]
                        ],
                        'email' => [
                            'src_type' => 'car',
                            'src' => [['dealer_contacts', 'email']],
                        ],
                        'address' => [
                            'src_type' => 'object',
                            'src' => ['dealer_postal_address']
                        ],
                        'geo' => [
                            'src_type' => 'object',
                            'src' => ['dealer_geo']
                        ]
                    ]
                ],

                'engine' => [
                    'schema' => '\Spatie\SchemaOrg\EngineSpecification()',
                    'elements' => [
                        'engineDisplacement' => [
                            'src_type' => 'car',
                            'src' => [['volume']],
                            'appends' => ' cm³'
                        ],
                        'enginePower' => [
                            'src_type' => 'car',
                            'src' => [['power']],
                            'appends' => ' kW'
                        ],
                        'fuelType' => [
                            'src_type' => 'car',
                            'src' => [['fuel', 'name']],
                        ]
                    ],
                ],
                'car_image' => [
                    'schema' => '\Spatie\SchemaOrg\ImageObject()',
                    'elements' => [
                        'src_type' => 'fce',
                        'src' => [['images',0]],
                        'fce_name' => 'getCarImageObjectsArray',
                        'fce_params' => [
                            'url' => "[['url']['1680_1024']]",
                            'contentUrl' => "[['url']['1680_1024']]",
                            'thumbnailUrl' => "[['url']['515_387']]",
                        ]
                    ],
                ],
                'car_parameters' => [
                    'schema' => '\Spatie\SchemaOrg\PropertyValue()',
                    'elements' => [
                        'src_type' => 'fce',
                        'src' => [['parameter_groups_with_parameters']],
                        'fce_name' => 'getCarParametersArray',
                        'fce_params' => [
                            'name' => "['name']",
                            'propertyID' => "['groupName']",
                        ]
                    ],
                ],
                'price_without_VAT' => [
                    'schema' => '\Spatie\SchemaOrg\PriceSpecification()',
                    'elements' => [
                        'src_type' => 'fce',
                        'src' => [['price_without_VAT']],
                        'fce_name' => 'getCarPriceSpecificationWithoutVat',
                    ],
                ],
                'car_program_warranty' => [
                    'schema' => '\Spatie\SchemaOrg\WarrantyPromise()',
                    'elements' => [
                        'src_type' => 'fce',
                        'src' => [['program','name']],
                        'fce_name' => 'getCarProgramWarranty',
                    ],
                ],
                'item_condition' => [
                    'schema' => '\Spatie\SchemaOrg\PriceSpecification()',
                    'elements' => [
                        'src_type' => 'fce',
                        'src' => [['car_state_id']],
                        'fce_name' => 'getCarStageCondition',
                    ],
                ],
                'car_offer' => [
                    'schema' => '\Spatie\SchemaOrg\Offer()',
                    'elements' => [
                        /*
                        'offeredBy' => [
                            'src_type' => 'object',
                            'src' => ['dealer'],
                        ],
                        */
                        'priceCurrency' => [
                            'src_type' => 'str',
                            'src' => ['CZK'],
                        ],
                        'price' => [
                            'src_type' => 'car',
                            'src' => [['price']],
                        ],
                        'priceSpecification' => [
                            'src_type' => 'object',
                            'src' => ['price_without_VAT'],
                        ],
                        'itemCondition' => [
                            'src_type' => 'object',
                            'src' => ['item_condition'],
                        ],
                        'availability' => [
                            'src_type' => 'str',
                            'params' => ['https://schema.org/InStock'],
                        ],
                        'url' => [
                            'src_type' => 'car',
                            'src' => [['url_detail']],
                        ],
                        'warranty' => [
                            'src_type' => 'object',
                            'src' => ['car_program_warranty'],
                        ]
                    ]
                ],
                'car_kilometers' => [
                    'schema' => '\Spatie\SchemaOrg\QuantitativeValue()',
                    'elements' => [
                        'unitCode' => [
                            'src_type' => 'str',
                            'src' => ['KMT'],
                        ],
                        'value' => [
                            'src_type' => 'car',
                            'src' => [['kilometers']],
                        ]
                    ]
                ],

                'car' => [
                    'schema' => '\Spatie\SchemaOrg\Car()',
                    'elements' => [
                        'brand' => [
                            'src_type' => 'object',
                            'src' => ['car_brand'],
                        ],
                        'manufacturer' => [
                            'src_type' => 'car',
                            'src' => [['manufacturer', 'name']],
                        ],
                        'model' => [
                            'src_type' => 'car',
                            'src' => [['model', 'name']],
                        ],
                        'name' => [
                            'src_type' => 'car',
                            'src' => [['full_title']],
                        ],
                        'description' => [
                            'src_type' => 'car',
                            'src' => [['description']],
                        ],
                        'itemCondition' => [
                            'src_type' => 'object',
                            'src' => ['item_condition'],
                        ],
                        'mileageFromOdometer' => [
                            'src_type' => 'object',
                            'src' => ['car_kilometers'],
                        ],
                        'dateVehicleFirstRegistered' => [
                            'src_type' => 'eval',
                            'params' => [
                                ['year' => '"{$this->year}-"'],
                                ['month' => 'sprintf("%02d", $this->month)'],
                                ['day' => '"01"'],
                            ], // "{$this->year}-" . sprintf("%02d", $this->month) . "-01"
                        ],
                        'vehicleIdentificationNumber' => [
                            'src_type' => 'car',
                            'src' => [['vin']],
                        ],
                        'fuelType' => [
                            'src_type' => 'car',
                            'src' => [['fuel', 'name']],
                        ],
                        'vehicleTransmission' => [
                            'src_type' => 'car',
                            'src' => [['transmission', 'name']],
                        ],
                        'numberOfDoors' => [
                            'src_type' => 'car',
                            'src' => [['doors']],
                        ],
                        'seatingCapacity' => [
                            'src_type' => 'car',
                            'src' => [['places']],
                        ],
                        'color' => [
                            'src_type' => 'car',
                            'src' => [['color', 'name']],
                        ],
                        'bodyType' => [
                            'src_type' => 'car',
                            'src' => [['body', 'name']],
                        ],
                        'vehicleEngine' => [
                            'src_type' => 'object',
                            'src' => ['engine'],
                        ],
                        'image' => [
                            'src_type' => 'object',
                            'src' => ['car_image'],
                        ],
                        'parameters' => [
                            'src_type' => 'object',
                            'src' => ['car_parameters'],
                        ],
                        'offers' => [
                            'src_type' => 'object',
                            'src' => ['car_offer'],
                        ],
                        'vehicleModelDate' => [
                            'src_type' => 'str',
                            'src' => ['2001'],
                        ],

                    ]

                ],

            ]
        ],
        'test' => [
            'elements' => [
                'dealer_postal_address' => [
                    'schema' => '\Spatie\SchemaOrg\PostalAddress()',
                    'elements' => [
                        'streetAddress' => [
                            'src_type' => 'car',
                            'src' => [['dealer', 'street']],
                        ],
                        'addressLocality' => [
                            'src_type' => 'car',
                            'src' => [['dealer', 'city']],
                        ],
                        'postalCode' => [
                            'src_type' => 'car',
                            'src' => [['dealer', 'zip']],
                        ],
                        'addressCountry' => [
                            'src_type' => 'str',
                            'params' => ['CZ'],
                        ],
                    ]
                ],
                'dealer_geo' => [
                    'schema' => '\Spatie\SchemaOrg\GeoCoordinates()',
                    'elements' => [
                        'latitude' => [
                            'src_type' => 'car',
                            'src' => [['dealer', 'latitude']],
                        ],
                        'longitude' => [
                            'src_type' => 'car',
                            'src' => [['dealer', 'longitude']],
                        ],
                    ]
                ],
                'dealer' => [
                    'schema' => '\Spatie\SchemaOrg\AutoDealer()',
                    'elements' => [
                        'name' => [
                            'src_type' => 'car',
                            'src' => [['dealer', 'name']],
                        ],
                        'brand' => [
                            'src_type' => 'car',
                            'src' => [['dealer', 'brand']],
                        ],
                        'latitude' => [
                            'src_type' => 'car',
                            'src' => [['dealer', 'latitude']],
                        ],
                        'longitude' => [
                            'src_type' => 'car',
                            'src' => [['dealer', 'longitude']],
                        ],
                        'url' => [
                            'src_type' => 'car',
                            'src' => [['dealer', 'web']],
                        ],
                        'telephone' => [
                            'src_type' => 'car',
                            'src' => [
                                ['dealer_contacts', 'telephone'],
                                ['dealer_contacts', 'mobile']
                            ]
                        ],
                        'email' => [
                            'src_type' => 'car',
                            'src' => [['dealer_contacts', 'email']],
                        ],
                        'address' => [
                            'src_type' => 'object',
                            'src' => ['dealer_postal_address']
                        ],
                        'geo' => [
                            'src_type' => 'object',
                            'src' => ['dealer_geo']
                        ]
                    ]
                ],
                'engine' => [
                    'schema' => '\Spatie\SchemaOrg\EngineSpecification()',
                    'elements' => [
                        'engineDisplacement' => [
                            'src_type' => 'car',
                            'src' => [['volume']],
                            'appends' => ' cm³'
                        ],
                        'enginePower' => [
                            'src_type' => 'car',
                            'src' => [['power']],
                            'appends' => ' kW'
                        ],
                        'fuelType' => [
                            'src_type' => 'car',
                            'src' => [['fuel', 'name']],
                        ]
                    ],
                ],
                'car_image' => [
                    'schema' => '\Spatie\SchemaOrg\ImageObject()',
                    'elements' => [
                        'src_type' => 'fce',
                        'src' => [['images',0]],
                        'fce_name' => 'getCarImageObjectsArray',
                        'fce_params' => [
                            'url' => "[['url']['1680_1024']]",
                            'contentUrl' => "[['url']['1680_1024']]",
                            'thumbnailUrl' => "[['url']['515_387']]",
                        ]
                    ],
                ],
                'car_parameters' => [
                    'schema' => '\Spatie\SchemaOrg\PropertyValue()',
                    'elements' => [
                        'src_type' => 'fce',
                        'src' => [['parameter_groups_with_parameters']],
                        'fce_name' => 'getCarParametersArray',
                        'fce_params' => [
                            'name' => "['name']",
                            'propertyID' => "['groupName']",
                        ]
                    ],
                ],
                'car' => [
                    'schema' => '\Spatie\SchemaOrg\Car()',
                    'elements' => [
                        'manufacturer' => [
                            'src_type' => 'car',
                            'src' => [['manufacturer', 'name']],
                        ],
                        'model' => [
                            'src_type' => 'car',
                            'src' => [['model', 'name']],
                        ],
                        'name' => [
                            'src_type' => 'car',
                            'src' => [['full_name']],
                        ],
                        'description' => [
                            'src_type' => 'car',
                            'src' => [['description']],
                        ],
                        'mileageFromOdometer' => [
                            'src_type' => 'car',
                            'src' => [['kilometers']],
                        ],
                        'dateVehicleFirstRegistered' => [
                            'src_type' => 'eval',
                            'params' => [
                                ['year' => '"{$this->year}-"'],
                                ['month' => 'sprintf("%02d", $this->month)'],
                                ['day' => '"01"'],
                            ], // "{$this->year}-" . sprintf("%02d", $this->month) . "-01"
                        ],
                        'vehicleIdentificationNumber' => [
                            'src_type' => 'car',
                            'src' => [['vin']],
                        ],
                        'fuelType' => [
                            'src_type' => 'car',
                            'src' => [['fuel', 'name']],
                        ],
                        'vehicleTransmission' => [
                            'src_type' => 'car',
                            'src' => [['transmission', 'name']],
                        ],
                        'numberOfDoors' => [
                            'src_type' => 'car',
                            'src' => [['doors']],
                        ],
                        'seatingCapacity' => [
                            'src_type' => 'car',
                            'src' => [['places']],
                        ],
                        'color' => [
                            'src_type' => 'car',
                            'src' => [['color', 'name']],
                        ],
                        'bodyType' => [
                            'src_type' => 'car',
                            'src' => [['body', 'name']],
                        ],
                        'vehicleEngine' => [
                            'src_type' => 'object',
                            'src' => ['engine'],
                        ],
                        'image' => [
                            'src_type' => 'object',
                            'src' => ['car_image'],
                        ],
                        'parameters' => [
                            'src_type' => 'object',
                            'src' => ['car_parameters'],
                        ],
                    ]

                ],
                'price_without_VAT' => [
                    'schema' => '\Spatie\SchemaOrg\PriceSpecification()',
                    'elements' => [
                        'src_type' => 'fce',
                        'src' => [['price_without_VAT']],
                        'fce_name' => 'getCarPriceSpecificationWithoutVat',
                    ],
                ],
                'item_condition' => [
                    'schema' => '\Spatie\SchemaOrg\PriceSpecification()',
                    'elements' => [
                        'src_type' => 'fce',
                        'src' => [['car_state_id']],
                        'fce_name' => 'getCarStageCondition',
                    ],
                ],
                'car_program_warranty' => [
                    'schema' => '\Spatie\SchemaOrg\WarrantyPromise()',
                    'elements' => [
                        'src_type' => 'fce',
                        'src' => [['program','name']],
                        'fce_name' => 'getCarProgramWarranty',
                    ],
                ],
                'offer' => [
                    'schema' => '\Spatie\SchemaOrg\Offer()',
                    'elements' => [
                        'itemOffered' => [
                            'src_type' => 'object',
                            'src' => ['car'],
                        ],
                        'offeredBy' => [
                            'src_type' => 'object',
                            'src' => ['dealer'],
                        ],
                        'priceCurrency' => [
                            'src_type' => 'str',
                            'src' => ['CZK'],
                        ],
                        'price' => [
                            'src_type' => 'car',
                            'src' => [['price']],
                        ],
                        'priceSpecification' => [
                            'src_type' => 'object',
                            'src' => ['price_without_VAT'],
                        ],
                        'itemCondition' => [
                            'src_type' => 'object',
                            'src' => ['item_condition'],
                        ],
                        'availability' => [
                            'src_type' => 'str',
                            'params' => ['https://schema.org/InStock'],
                        ],
                        'url' => [
                            'src_type' => 'car',
                            'src' => [['url_detail']],
                        ],
                        'warranty' => [
                            'src_type' => 'object',
                            'src' => ['car_program_warranty'],
                        ]
                    ]
                ]

            ]
        ],
    ]
];
