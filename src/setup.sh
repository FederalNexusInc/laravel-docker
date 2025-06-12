#!/bin/sh

# This script automates common setup commands for a Laravel/PHP application
# inside a Docker container. It assumes 'composer' and 'php artisan' are
# available in the container's PATH.

# Function to display messages
log_message() {
  echo "--> $1"
}

# --- Composer Install ---
log_message "Running composer install..."
# 'composer install' might ask for confirmation in some cases (e.g., if it needs to delete files).
# 'yes |' pipes 'y' to any interactive prompts, effectively answering 'yes'.
yes | composer install --no-interaction --prefer-dist --optimize-autoloader

# Check if composer install was successful
if [ $? -ne 0 ]; then
  log_message "Composer install failed. Exiting."
  exit 1
fi
log_message "Composer install completed successfully."


# --- PHP Artisan Migrate ---
log_message "Running php artisan migrate..."
# 'php artisan migrate' can sometimes ask for confirmation in production environments
# if there are pending migrations. 'yes |' ensures it proceeds.
yes | php artisan migrate --force

if [ $? -ne 0 ]; then
  log_message "PHP artisan migrate failed. Exiting."
  exit 1
fi
log_message "PHP artisan migrate completed successfully."


# --- PHP Artisan DB:Seed ---
log_message "Running php artisan db:seed..."
# db:seed usually doesn't prompt, but piping 'yes' is harmless.
yes | php artisan db:seed --force

if [ $? -ne 0 ]; then
  log_message "PHP artisan db:seed failed. Exiting."
  exit 1
fi
log_message "PHP artisan db:seed completed successfully."


# --- PHP Artisan Shield:Generate --all ---
log_message "Running php artisan shield:generate --all..."
# This command might prompt, so 'yes |' is used.
yes | php artisan shield:generate --all --panel=admin

if [ $? -ne 0 ]; then
  log_message "PHP artisan shield:generate --all failed. Exiting."
  exit 1
fi
log_message "PHP artisan shield:generate --all completed successfully."


# --- PHP Artisan Shield:Super-Admin ---
log_message "Running php artisan shield:super-admin..."
# This command typically prompts for user details or confirmation,
# so 'yes |' is essential here.
yes | php artisan shield:super-admin

if [ $? -ne 0 ]; then
  log_message "PHP artisan shield:super-admin failed. Exiting."
  exit 1
fi
log_message "PHP artisan shield:super-admin completed successfully."


# --- PHP Artisan db:seed --class=RoleSeeder ---
log_message "Running php artisan db:seed --class=RoleSeeder..."
# This command typically prompts for user details or confirmation,
# so 'yes |' is essential here.
yes | php artisan db:seed --class=RoleSeeder --force

if [ $? -ne 0 ]; then
  log_message "PHP artisan db:seed --class=RoleSeeder failed. Exiting."
  exit 1
fi
log_message "PHP artisan db:seed --class=RoleSeeder completed successfully."

log_message "All setup commands completed successfully!"
