<?php

/**
 * Settings page template with Alpine.js and Tailwind-like CSS
 */

// Prevent direct access
if (!defined('ABSPATH')) {
  exit;
}
?>

<div class="wrap" x-data="customPluginSettings" x-init="init()">
  <h1 class="wp-heading-inline mb-6"><?php echo esc_html(get_admin_page_title()); ?></h1>

  <div class="custom-plugin-container max-w-4xl mt-5">

    <!-- Notifications -->
    <div x-show="notification.show"
      x-transition:enter="transition ease-out duration-300"
      x-transition:enter-start="opacity-0 transform -translate-y-2"
      x-transition:enter-end="opacity-100 transform translate-y-0"
      x-transition:leave="transition ease-in duration-200"
      x-transition:leave-start="opacity-100 transform translate-y-0"
      x-transition:leave-end="opacity-0 transform -translate-y-2"
      :class="notification.type === 'success' ? 'bg-green-100 border-l-4 border-green-500 text-green-700' : 'bg-red-100 border-l-4 border-red-500 text-red-700'"
      class="p-4 mb-6 rounded shadow-sm"
      style="display: none;">
      <p class="m-0" x-text="notification.message"></p>
    </div>

    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
      <h2 class="text-xl font-semibold mb-4 border-b pb-2 text-gray-800"><?php _e('General Settings', 'custom-plugin'); ?></h2>

      <!-- Loading State -->
      <div x-show="loading" class="animate-pulse space-y-4">
        <div class="h-4 bg-gray-200 rounded w-3/4"></div>
        <div class="h-4 bg-gray-200 rounded w-1/2"></div>
        <div class="h-10 bg-gray-200 rounded"></div>
      </div>

      <!-- Settings Form -->
      <form @submit.prevent="saveSettings" x-show="!loading" style="display: none;">

        <div class="mb-6">
          <label class="flex items-center space-x-3 cursor-pointer">
            <input type="checkbox" x-model="settings.enable_feature_1" class="form-checkbox h-5 w-5 text-blue-600 rounded transition duration-150 ease-in-out">
            <span class="text-gray-700 font-medium"><?php _e('Enable Feature 1', 'custom-plugin'); ?></span>
          </label>
          <p class="mt-1 text-sm text-gray-500 ml-8"><?php _e('Enable custom meta tags in head section.', 'custom-plugin'); ?></p>
        </div>

        <div class="mb-6">
          <label class="flex items-center space-x-3 cursor-pointer">
            <input type="checkbox" x-model="settings.enable_feature_2" class="form-checkbox h-5 w-5 text-blue-600 rounded transition duration-150 ease-in-out">
            <span class="text-gray-700 font-medium"><?php _e('Enable Feature 2', 'custom-plugin'); ?></span>
          </label>
          <p class="mt-1 text-sm text-gray-500 ml-8"><?php _e('Add custom message to single posts.', 'custom-plugin'); ?></p>
        </div>

        <div class="mb-6" x-show="settings.enable_feature_2" x-transition>
          <label for="custom_message" class="block text-sm font-medium text-gray-700 mb-2"><?php _e('Custom Message', 'custom-plugin'); ?></label>
          <textarea id="custom_message" x-model="settings.custom_message" rows="4" class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md p-2" placeholder="<?php _e('Enter your custom message here...', 'custom-plugin'); ?>"></textarea>
          <p class="mt-1 text-sm text-gray-500"><?php _e('This message will be displayed when Feature 2 is enabled.', 'custom-plugin'); ?></p>
        </div>

        <div class="flex items-center justify-end pt-4 border-t">
          <button type="submit"
            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed"
            :disabled="saving">
            <svg x-show="saving" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span x-text="saving ? '<?php _e('Saving...', 'custom-plugin'); ?>' : '<?php _e('Save Settings', 'custom-plugin'); ?>'"></span>
          </button>
        </div>
      </form>
    </div>

    <!-- Info Card -->
    <div class="bg-white rounded-lg shadow-md p-6">
      <h2 class="text-xl font-semibold mb-4 border-b pb-2 text-gray-800"><?php _e('Plugin Information', 'custom-plugin'); ?></h2>
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="p-4 bg-gray-50 rounded-lg">
          <p class="text-sm text-gray-500 uppercase tracking-wider"><?php _e('Version', 'custom-plugin'); ?></p>
          <p class="text-lg font-bold text-gray-900"><?php echo CUSTOM_PLUGIN_VERSION; ?></p>
        </div>
        <div class="p-4 bg-gray-50 rounded-lg">
          <p class="text-sm text-gray-500 uppercase tracking-wider"><?php _e('WordPress', 'custom-plugin'); ?></p>
          <p class="text-lg font-bold text-gray-900"><?php echo get_bloginfo('version'); ?></p>
        </div>
        <div class="p-4 bg-gray-50 rounded-lg">
          <p class="text-sm text-gray-500 uppercase tracking-wider"><?php _e('PHP', 'custom-plugin'); ?></p>
          <p class="text-lg font-bold text-gray-900"><?php echo PHP_VERSION; ?></p>
        </div>
      </div>
    </div>

  </div>
</div>