<?php

// config for Bestfitcz/AutomotiveJsonLd
return [
    'breadcrumb' => [
        'items' => [
            // Home/root item - always present
            [
                'name' => 'Domů',
                'url' => [
                    'type' => 'url',
                    'value' => '/'
                ]
            ],
            // Other page item (for non-car pages like "Výkup vozů")
            [
                'name' => [
                    'type' => 'data',
                    'path' => ['other_page']
                ],
                'condition' => [
                    'type' => 'isset',
                    'source' => 'data',
                    'path' => ['other_page']
                ]
            ],
            // Car type category item (e.g., "Nové vozy", "Certifikované vozy", etc.)
            [
                'name' => [
                    'type' => 'data',
                    'path' => ['car_type_name']
                ],
                'url' => [
                    'type' => 'data',
                    'path' => ['car_type_url']
                ],
                'condition' => [
                    'type' => 'isset',
                    'source' => 'data',
                    'path' => ['car_type_name']
                ]
            ],
            // Search results - base level for filtered searches
            [
                'name' => [
                    'type' => 'data',
                    'path' => ['search_results', 'name']
                ],
                'url' => [
                    'type' => 'data',
                    'path' => ['search_results', 'url']
                ],
                'condition' => [
                    'type' => 'isset',
                    'source' => 'data',
                    'path' => ['search_results']
                ]
            ],
            // Manufacturer filter level
            [
                'name' => [
                    'type' => 'data',
                    'path' => ['manufacturer', 'name']
                ],
                'url' => [
                    'type' => 'data',
                    'path' => ['manufacturer', 'url']
                ],
                'condition' => [
                    'type' => 'isset',
                    'source' => 'data',
                    'path' => ['manufacturer']
                ]
            ],
            // Model filter level
            [
                'name' => [
                    'type' => 'data',
                    'path' => ['model', 'name']
                ],
                'url' => [
                    'type' => 'data',
                    'path' => ['model', 'url']
                ],
                'condition' => [
                    'type' => 'isset',
                    'source' => 'data',
                    'path' => ['model']
                ]
            ],
            // Body filter level
            [
                'name' => [
                    'type' => 'data',
                    'path' => ['body', 'name']
                ],
                'url' => [
                    'type' => 'data',
                    'path' => ['body', 'url']
                ],
                'condition' => [
                    'type' => 'isset',
                    'source' => 'data',
                    'path' => ['body']
                ]
            ],
            // Detail page - final level for car detail pages (no URL)
            [
                'name' => [
                    'type' => 'data',
                    'path' => ['detail', 'name']
                ],
                'condition' => [
                    'type' => 'isset',
                    'source' => 'data',
                    'path' => ['detail']
                ]
            ]
        ]
    ],
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
                            'src_type' => 'fce',
                            'src' => [['year']],
                            'fce_name' => 'getCarVehicleModelDate',
                            /*
                            'fce_params' => [
                                '_conditions' => [
                                    [
                                        'property' => 'car_state_id',
                                        'operator' => '==',
                                        'value' => '3'
                                    ]
                                ]
                            ]
                            */
                        ],

                    ]

                ],

            ]
        ],
        'car_list' => [
            'generate_conditions' => [
                'url_patern' => '*/vysledky-hledani',
                'url_segments' => ['nove-vozy', 'skladove-vozy', 'certifikovane-vozy', 'ojete-vozy']
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
