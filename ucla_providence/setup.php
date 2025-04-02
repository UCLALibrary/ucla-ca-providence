<?php
// Helper to setup Providence with environment variables.
// From https://github.com/tofran/providence-docker/blob/main/providence-setup.php
// See Providence's setup.php-dist for variable documentation.

if (getenv('CA_DEBUG') == 'true'){
  ini_set('display_errors', 1);
  ini_set('display_startup_errors', 1);
  error_reporting(E_ALL);
}

date_default_timezone_set(getenv('TZ') ?: 'Etc/UTC');

// These are all (?) the variables defined in setup.php-dist, with
// generic / default values.  These values can be overridden
// in docker compose env files, or via Helm chart values files.
$ENV_NAME_TO_DEFAULT_VALUE = [
  'CA_ADMIN_EMAIL' => 'admin@local',
  'CA_ALLOW_AUTOMATIC_UPDATE_OF_DATABASE' => null,
  'CA_ALLOW_AUTOMATIC_UPDATE_OF_VENDOR_DIR' => null,
  'CA_ALLOW_DRAG_AND_DROP_PROFILE_UPLOAD_IN_INSTALLER' => null,
  'CA_ALLOW_INSTALLER_TO_OVERWRITE_EXISTING_INSTALLS' => false,
  'CA_APP_CONFIG' => null,
  'CA_APP_DIR' => null,
  'CA_APP_DISPLAY_NAME' => 'My dockerized CollectiveAccess system',
  'CA_APP_NAME' => 'collectiveaccess',
  'CA_APP_TYPE' => null,
  'CA_BASE_DIR' => null,
  'CA_CACHE_BACKEND' => 'file',
  'CA_CACHE_FILEPATH' => null,
  'CA_CACHE_TTL' => null,
  'CA_CONF_DIR' => null,
  'CA_DB_DATABASE' => 'providance',
  'CA_DB_HOST' => 'localhost',
  'CA_DB_PASSWORD' => 'providance',
  'CA_DB_TYPE' => null,
  'CA_DB_USER' => 'providance',
  'CA_DEFAULT_LOCALE' => 'en_US',
  'CA_DEFAULT_THEME_CONFIG_DIRECTORY' => null,
  'CA_DISABLE_CONFIG_CACHING' => null,
  'CA_ENABLE_DEBUG_OUTPUT' => null,
  'CA_GOOGLE_MAPS_KEY' => '',
  'CA_LIB_DIR' => null,
  'CA_LOCAL_CONFIG_DIRECTORY' => null,
  'CA_MEMCACHED_HOST' => null,
  'CA_MEMCACHED_PORT' => null,
  'CA_MODELS_DIR' => null,
  'CA_OUT_OF_PROCESS_SEARCH_INDEXING_HOSTNAME' => 'localhost',
  'CA_OUT_OF_PROCESS_SEARCH_INDEXING_PORT' => 80,
  'CA_OUT_OF_PROCESS_SEARCH_INDEXING_PROTOCOL' => 'tcp',
  'CA_QUEUE_ENABLED' => 0,
  'CA_REDIS_DB' => null,
  'CA_REDIS_HOST' => null,
  'CA_REDIS_PORT' => null,
  'CA_SITE_HOSTNAME' => null,
  'CA_SITE_PROTOCOL' => null,
  'CA_SMTP_AUTH' => null,
  'CA_SMTP_PASSWORD' => null,
  'CA_SMTP_PORT' => null,
  'CA_SMTP_SERVER' => null,
  'CA_SMTP_SSL' => null,
  'CA_SMTP_USER' => null,
  'CA_STACKTRACE_ON_EXCEPTION' => true,
  'CA_THEME_DIR' => null,
  'CA_THEME_URL' => null,
  'CA_THEME' => null,
  'CA_THEMES_DIR' => null,
  'CA_THEMES_URL' => null,
  'CA_URL_ROOT' => null,
  'CA_USE_CLEAN_URLS' => 0,
];

// Create the constant names Providence expects.  For example,
// CA_APP_CONFIG becomes __CA_APP_CONFIG__.
foreach ($ENV_NAME_TO_DEFAULT_VALUE as $env_name => $default_value) {
  $ca_config_name = '__' . $env_name . '__';

  // None of the __CA__ constants is defined yet.  For each one,
  // see if there's an environment variable to use.
  if (!defined($ca_config_name)) {
    $config_value = getenv($env_name);

    // If the environment variable is set, use that value.
    if ($config_value !== false) {
      define($ca_config_name, $config_value);
    }
    // If no environment variable value to use, if a non-null default was set,
    // use that value.
    else if ($default_value !== null){
      define($ca_config_name, $default_value);
    }
  }
}

// Finally, continue with the standard installation process.
require(__DIR__."/app/helpers/post-setup.php");
