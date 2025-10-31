# Project Overview

This project aims to create an Airbnb-like real estate rental management system within a WordPress environment. It uses the **Houzez theme** as a foundation and integrates the **MotoPress Hotel Booking plugin** to handle bookings, availability, and pricing.

The primary development focus is on a custom plugin, **`houzez-booking`**, which acts as a bridge between the Houzez theme and the MotoPress Hotel Booking plugin. This custom plugin handles the synchronization of properties, provides an API for managing bookings, and allows for the display of booking forms and availability calendars on property pages.

## Key Technologies

*   **Backend:** PHP, WordPress
*   **Frontend:** JavaScript (jQuery), CSS
*   **Theme:** Houzez
*   **Plugins:**
    *   MotoPress Hotel Booking (core booking engine)
    *   houzez-booking (custom integration plugin)
*   **Version Control:** Git
*   **Deployment:** VSCode SFTP Extension

## Building and Running

This is a WordPress project, so there is no traditional build process. The development workflow is as follows:

1.  **Local Development:**
    *   It is assumed that you have a local WordPress environment (e.g., using XAMPP, MAMP, or Docker) with the Houzez theme, MotoPress Hotel Booking plugin, and the `houzez-booking` plugin installed.
    *   Changes are made to the files locally.

2.  **Deployment:**
    *   The VSCode SFTP extension is used to deploy changes to a remote server.
    *   It is recommended to set `"uploadOnSave": true` in your `sftp.json` configuration for automatic deployment of saved files.

## Development Plan

1.  **Automatic Accommodation Creation:** When a new property is created in Houzez, automatically create a corresponding "accommodation type" and "accommodation" in MotoPress via the REST API.
2.  **Seasons and Pricing:** Implement functionality to manage seasons and pricing for each property to allow for dynamic rates based on the time of year.
3.  **Calendar Display:** Display the MotoPress booking calendar on the single property pages of the Houzez theme.

## Development Conventions

### Git Workflow

*   **Branching:** Create a new branch for each feature or bug fix.
    ```bash
    git checkout -b feature/your-feature-name
    ```
*   **Commits:** Make small, atomic commits with clear and descriptive messages.
*   **Pull Requests:** Use pull requests for code review before merging into the `main` branch.

### `houzez-booking` Plugin

*   **Structure:** The plugin follows a standard WordPress plugin structure, with separate directories for includes, admin functionality, and assets.
*   **API:** The plugin provides a REST API for interacting with bookings. The base URL is `/wp-json/houzez-booking/v1/`.
*   **Shortcodes:** Use shortcodes to embed booking functionality into Houzez pages and templates (e.g., `[houzez_booking_availability property_id="123"]`).
*   **Customization:** The plugin provides hooks (actions and filters) for further customization.

### Child Theme

*   Customizations to the Houzez theme's appearance or functionality should be made in the `houzez-child` theme to ensure they are not overwritten by theme updates.
