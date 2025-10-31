## MotoPress Hotel Booking REST API

The MotoPress Hotel Booking REST API allows you to programmatically interact with your hotel booking data. It's built on top of the WordPress REST API.

### Authentication

To use the API, you need to generate access tokens (consumer key and consumer secret) in your WordPress admin dashboard under `Accommodation > Settings > Advanced`. These credentials must be included in your API requests for authentication.

### Base URL

The base URL for the API is: `https://your-domain.com/wp-json/mphb/v1`

---

### Resources and Endpoints

Here are the available resources and their endpoints:

#### Bookings
*   `GET /bookings`: List all bookings.
*   `POST /bookings`: Create a new booking.
*   `GET /bookings/{id}`: Retrieve a specific booking.
*   `POST | PUT | PATCH /bookings/{id}`: Update a booking.
*   `DELETE /bookings/{id}`: Delete a booking.
*   `GET /bookings/availability`: Search for available accommodations.
*   `POST | PUT | PATCH /bookings/batch`: Batch update bookings.

#### Payments
*   `GET /payments`: List all payments.
*   `POST /payments`: Create a new payment.
*   `GET /payments/{id}`: Retrieve a specific payment.
*   `POST | PUT | PATCH /payments/{id}`: Update a payment.
*   `DELETE /payments/{id}`: Delete a payment.
*   `POST | PUT | PATCH /payments/batch`: Batch update payments.

#### Accommodations
*   `GET /accommodations`: List all accommodations.
*   `POST /accommodations`: Create a new accommodation.
    *   **Parameters:** `accommodation_type_id` (integer, required), `title` (string), `excerpt` (string).
*   `GET /accommodations/{id}`: Retrieve a specific accommodation.
*   `POST | PUT | PATCH /accommodations/{id}`: Update an accommodation.
*   `DELETE /accommodations/{id}`: Delete an accommodation.
*   `POST | PUT | PATCH /accommodations/batch`: Batch update accommodations.

#### Accommodation Types
*   *   `POST /accommodation_types`: Create a new accommodation type.
    *   **Parameters:** `title` (string, required), `description` (string), `excerpt` (string), `adults` (integer), `children` (integer), `total_capacity` (integer), `bed_type` (string), `size` (number), `view` (string), `services` (array), `categories` (array), `tags` (array), `amenities` (array), `attributes` (array).
*   Other endpoints are available for managing accommodation types, categories, tags, amenities, services, images, and attributes.

#### Coupons
*   `GET /coupons`: List all coupons.
*   `POST /coupons`: Create a new coupon.
*   `GET /coupons/{id}`: Retrieve a specific coupon.
*   `POST | PUT | PATCH /coupons/{id}`: Update a coupon.
*   `DELETE /coupons/{id}`: Delete a coupon.
*   `POST | PUT | PATCH /coupons/batch`: Batch update coupons.

#### Rates
*   `GET /rates`: List all rates.
*   `POST /rates`: Create a new rate.
*   `GET /rates/{id}`: Retrieve a specific rate.
*   `POST | PUT | PATCH /rates/{id}`: Update a rate.
*   `DELETE /rates/{id}`: Delete a rate.
*   `POST | PUT | PATCH /rates/batch`: Batch update rates.

#### Seasons
*   `GET /seasons`: List all seasons.
*   `POST /seasons`: Create a new season.
    *   **Parameters:** `title` (string, required), `description` (string), `start_date` (string, required), `end_date` (string, required), `days` (array), `accommodation_types` (array), `priority` (integer).
*   `GET /seasons/{id}`: Retrieve a specific season.
*   `POST | PUT | PATCH /seasons/{id}`: Update a season.
*   `DELETE /seasons/{id}`: Delete a season.
*   `POST | PUT | PATCH /seasons/batch`: Batch update seasons.

#### Settings
*   `GET /booking_rules`: Retrieve booking rules.
*   `POST | PUT | PATCH /booking_rules`: Update booking rules.
*   `GET /taxes_and_fees`: Retrieve taxes and fees settings.
*   `POST | PUT | PATCH /taxes_and_fees`: Update taxes and fees settings.

#### Batch Operations
*   Batch updates are available for most resources via `POST | PUT | PATCH` requests to `/resource/batch` endpoints.
