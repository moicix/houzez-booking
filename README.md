# Houzez WordPress Project

This repository contains a WordPress project centered around the **Houzez real estate theme**. It includes a custom child theme and a bespoke plugin designed to integrate Houzez with the **MotoPress Hotel Booking plugin** for comprehensive property booking management.

## Key Components

*   **WordPress:** The core content management system.
*   **Houzez Theme:** A premium theme for real estate listings.
*   **Houzez Child Theme (`houzez-child`):** For theme customizations, located in `wp-content/themes/houzez-child/`.
*   **MotoPress Hotel Booking Plugin:** Handles booking functionalities.
*   **Houzez Booking Integration Plugin (`houzez-booking`):** A custom plugin (`wp-content/plugins/houzez-booking/`) that bridges Houzez and MotoPress Hotel Booking, including a custom REST API for bookings and availability.

## Setup and Installation

This is a standard WordPress project. To get it running:

1.  **Environment:** Ensure you have a local web server (e.g., Apache/Nginx) with PHP (7.4+) and MySQL installed.
2.  **Database:** Create a new MySQL database.
3.  **Configuration:** Update `wp-config.php` with your database credentials. Note that core configuration is loaded from `../configs/wp-config-hosting.php`.
4.  **Files:** Place all project files in your web server's document root.
5.  **WordPress Installation:** Complete the standard WordPress installation process via your web browser.
6.  **Plugins/Themes:** Activate the Houzez theme, Houzez child theme, MotoPress Hotel Booking plugin, and the custom `houzez-booking` plugin.

## Running the Project

Once installed, the project runs as a typical WordPress site. Access it via your configured domain or IP address in a web browser.

## Important Notes

*   **Debugging:** `WP_DEBUG` is currently enabled in `wp-config.php`. Remember to disable this in production environments.
*   **Custom Plugin API:** The `houzez-booking` plugin exposes a REST API for managing bookings. Refer to `wp-content/plugins/houzez-booking/README.md` for API documentation and usage examples.
*   **Integration Status:** The integration between Houzez and MotoPress Hotel Booking via the `houzez-booking` plugin is still under active development. The core logic for API calls and data synchronization is not yet fully implemented. Refer to `GEMINI.md` for more details on the current integration status.
