document.addEventListener('alpine:init', () => {
    Alpine.data('customPluginSettings', () => ({
        loading: true,
        saving: false,
        settings: {
            enable_feature_1: false,
            enable_feature_2: false,
            custom_message: ''
        },
        notification: {
            show: false,
            type: 'success',
            message: ''
        },

        init() {
            this.fetchSettings();
        },

        fetchSettings() {
            this.loading = true;
            fetch(`${customPluginData.root}custom-plugin/v1/settings`, {
                method: 'GET',
                headers: {
                    'X-WP-Nonce': customPluginData.nonce
                }
            })
            .then(response => response.json())
            .then(data => {
                this.settings = data;
                this.loading = false;
            })
            .catch(error => {
                console.error('Error fetching settings:', error);
                this.showNotification('error', 'Failed to load settings.');
                this.loading = false;
            });
        },

        saveSettings() {
            this.saving = true;
            fetch(`${customPluginData.root}custom-plugin/v1/settings`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-WP-Nonce': customPluginData.nonce
                },
                body: JSON.stringify(this.settings)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    this.showNotification('success', data.message);
                } else {
                    this.showNotification('error', 'Something went wrong.');
                }
                this.saving = false;
            })
            .catch(error => {
                console.error('Error saving settings:', error);
                this.showNotification('error', 'Failed to save settings.');
                this.saving = false;
            });
        },

        showNotification(type, message) {
            this.notification.type = type;
            this.notification.message = message;
            this.notification.show = true;
            
            setTimeout(() => {
                this.notification.show = false;
            }, 3000);
        }
    }));
});
