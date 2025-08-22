# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

This is a Laravel package that generates JSON-LD structured data for automotive listings. The package helps create Schema.org-compliant JSON-LD scripts for cars, dealers, and offers, improving SEO and search engine visibility for automotive websites.

## Key Architecture

### Core Components

- **AutomotiveJsonLd.php**: Main class that handles JSON-LD generation with two approaches:
  - `generatePageJsonLd()`: Legacy method with hardcoded Schema.org object creation
  - `generateDetailPageJsonLd()`: New configurable method using the configuration system

- **Configuration System**: Uses `config/automotive-json-ld.php` to define Schema.org objects declaratively:
  - `page_type.detail.elements`: Main configuration for detail pages
  - `page_type.test.elements`: Test configuration
  - Each element defines a Schema.org class and its properties with data sources

### Data Source Types

The configuration system supports multiple data source types (`src_type`):
- `car`: Extract values from car data array using path notation
- `str`: Use literal string values
- `object`: Reference other schema objects
- `eval`: Evaluate expressions (primarily for date formatting)
- `fce`: Call custom functions with parameters

### Custom Functions

The class includes specialized functions for complex data transformations:
- `getCarImageObjectsArray()`: Handles image objects with conditional logic
- `getCarParametersArray()`: Converts parameter groups to PropertyValue objects
- `getCarPriceSpecificationWithoutVat()`: Creates price specifications
- `getCarStageCondition()`: Maps car state IDs to Schema.org conditions
- `getCarProgramWarranty()`: Creates warranty objects
- `getCarVehicleModelDate()`: Formats vehicle model dates with conditions

## Common Development Tasks

### Testing
```bash
composer test
```

### Installation Commands
```bash
composer require bestfitcz/automotive-json-ld
php artisan vendor:publish --tag="automotive-json-ld-migrations"
php artisan migrate
php artisan vendor:publish --tag="automotive-json-ld-config"
php artisan vendor:publish --tag="automotive-json-ld-views"
```

## Dependencies

- PHP 7.4+ or 8.1-8.4
- Laravel 10.0+, 11.0+, or 12.0+
- spatie/schema-org: ^3.23 (for Schema.org object creation)

## Configuration Structure

The configuration uses a hierarchical approach where each element:
1. Defines a Schema.org class (`schema` key)
2. Specifies properties and their data sources (`elements` key)
3. Supports nested object references and complex data transformations

When modifying the configuration, ensure all referenced objects exist and maintain proper dependency order for object references.