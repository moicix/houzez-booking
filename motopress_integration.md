# MotoPress Integration Plan

This document outlines the plan for integrating the MotoPress Hotel Booking plugin with the Houzez theme to create an Airbnb-like real estate rental management system.

## Step 1: Create Accommodation on Property Creation

The first step is to automatically create a MotoPress accommodation type and accommodation whenever a new property is created in Houzez.

### 1.1. Create Accommodation Type

**Endpoint:** `POST /wp-json/mphb/v1/accommodation_types`

**Parameters:**

*   **`title`** (string, **required**): The title of the property.
*   `description` (string): The main description of the property.
*   `excerpt` (string): A short description of the property.
*   `adults` (integer): The maximum number of adults the property can accommodate.
*   `children` (integer): The maximum number of children the property can accommodate.
*   `total_capacity` (integer): The total capacity (adults + children).
*   `bed_type` (string): A description of the bed types (e.g., "1 King, 2 Twin").
*   `size` (number): The size of the property in square meters.
*   `view` (string): A description of the view (e.g., "Ocean View").
*   `services` (array): An array of service IDs to associate with the accommodation type.
*   `categories` (array): An array of category IDs to associate with the accommodation type.
*   `tags` (array): An array of tag IDs to associate with the accommodation type.
*   `amenities` (array): An array of amenity IDs to associate with the accommodation type.
*   `attributes` (array): An array of attribute objects to associate with the accommodation type.

### 1.2. Create Accommodation

**Endpoint:** `POST /wp-json/mphb/v1/accommodations`

**Parameters:**

*   **`accommodation_type_id`** (integer, **required**): The ID of the accommodation type created in the previous step.
*   `title` (string): The title of the accommodation. This can be the same as the accommodation type title.
*   `excerpt` (string): A short description of the accommodation.

## Step 2: Manage Seasons and Availability

The second step is to manage pricing and availability for different time periods using seasons.

### 2.1. Create a Season

**Endpoint:** `POST /wp-json/mphb/v1/seasons`

**Parameters:**

*   **`title`** (string, **required**): The name of the season (e.g., "High Season", "Low Season").
*   `description` (string): A description of the season.
*   **`start_date`** (string, **required**): The start date of the season in `Y-m-d` format.
*   **`end_date`** (string, **required**): The end date of the season in `Y-m-d` format.
*   `days` (array): An array of strings for the weekdays this season applies to (e.g., `["friday", "saturday"]`).
    *   Possible values: `"sunday"`, `"monday"`, `"tuesday"`, `"wednesday"`, `"thursday"`, `"friday"`, `"saturday"`.
*   `accommodation_types` (array): An array of accommodation type IDs this season applies to.
*   `priority` (integer): The priority of the season, used to resolve overlapping seasons.
