# Best Practices for Git and VSCode SFTP Extension

This document outlines best practices for using Git for version control and the VSCode SFTP extension for deployment in your WordPress development workflow, with the goal of creating an Airbnb-like real estate rental management system.

## Git Best Practices

### 1. Branching Strategy

*   **Feature Branches:** Always create a new branch for each new feature, bug fix, or task. This isolates your work and prevents direct modifications to the main codebase.
    ```bash
    git checkout -b feature/your-feature-name
    ```
*   **Main Branch Stability:** The `main` (or `master`) branch should always contain stable, production-ready code. Never commit directly to `main`.

### 2. Commit Early and Often

*   **Atomic Commits:** Make small, focused commits that address a single logical change. This makes it easier to track changes, revert mistakes, and review code.
*   **Descriptive Messages:** Write clear, concise, and descriptive commit messages.
    *   Start with a short summary (50-72 characters) in the imperative mood.
    *   Optionally, add a more detailed body explaining *why* the change was made.
    ```bash
    git add .
    git commit -m "feat: Add booking calendar shortcode to property template" -m "This commit integrates the MotoPress booking calendar shortcode into the single property template to display availability and booking forms."
    ```

### 3. Pull Before Push

*   Always pull the latest changes from the remote repository before pushing your own to avoid merge conflicts and ensure your work is based on the most up-to-date code.
    ```bash
    git pull origin main
    git push origin feature/your-feature-name
    ```

### 4. Use `.gitignore`

*   Prevent unnecessary files (e.g., WordPress core, local configuration, IDE files, `node_modules`) from being tracked by Git.
    ```
    # Example .gitignore content for WordPress
    /wp-content/uploads/
    /wp-content/cache/
    /wp-config.php
    .env
    .vscode/
    *.log
    ```

### 5. Code Reviews (Pull Requests)

*   Utilize pull requests (or merge requests) for code review. This ensures code quality, catches potential issues, and facilitates knowledge sharing within the team.

## VSCode SFTP Extension Best Practices

### 1. Secure Authentication

*   **SSH Keys:** Prioritize SSH key-based authentication over password-based authentication for enhanced security.
    *   Configure `privateKeyPath` in your `sftp.json`.
    ```json
    {
        "host": "your-sftp-host.com",
        "protocol": "sftp",
        "port": 22,
        "username": "your-username",
        "remotePath": "/var/www/html/wp-content/themes/houzez-child/",
        "privateKeyPath": "/home/youruser/.ssh/id_rsa",
        "uploadOnSave": true
    }
    ```
*   **Permissions:** Ensure your private key file has restricted permissions (e.g., `chmod 400 ~/.ssh/id_rsa`).

### 2. `sftp.json` Configuration

*   **`uploadOnSave`:** Set `"uploadOnSave": true` to automatically upload files to the remote server whenever you save changes locally. This streamlines your development workflow.
    ```json
    {
        // ... other configurations
        "uploadOnSave": true
    }
    ```
*   **`remotePath`:** Define the correct remote path for your project to ensure files are uploaded to the right location on the server.

### 3. Careful Synchronization

*   **Initial Setup:** When setting up a new project or synchronizing for the first time, be mindful of the direction of synchronization.
    *   If the remote is the source of truth, use "SFTP: Download Project".
    *   If your local is the source of truth, use "SFTP: Sync Local -> Remote".
*   **Avoid Overwriting:** Always be aware of which files are newer to prevent accidental overwrites. The `uploadOnSave` feature helps manage this for individual file changes.

### 4. Use the Correct Extension

*   Ensure you are using the actively maintained VSCode SFTP extension by Natizyskunk (a fork of the original liximomo extension) for the latest features and bug fixes.
