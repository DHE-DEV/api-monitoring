import './bootstrap';
import Alpine from 'alpinejs';

// Alpine.js Komponenten registrieren
Alpine.data('userManagement', () => ({
    // User Management Logik
    users: [],
    loading: false,

    async loadUsers() {
        this.loading = true;
        try {
            const response = await fetch('/api/users', {
                headers: {
                    'Authorization': `Bearer ${localStorage.getItem('api_token')}`,
                    'Accept': 'application/json'
                }
            });
            this.users = await response.json();
        } catch (error) {
            console.error('Error loading users:', error);
        } finally {
            this.loading = false;
        }
    }
}));

Alpine.data('apiMonitor', () => ({
    // API Monitor Dashboard Logik
    monitors: [],
    stats: {},

    async testMonitor(monitorId) {
        try {
            const response = await fetch(`/api/monitors/${monitorId}/test`, {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${localStorage.getItem('api_token')}`,
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });

            const result = await response.json();

            if (result.success) {
                this.$dispatch('show-notification', {
                    type: 'success',
                    message: `Test erfolgreich! Antwortzeit: ${result.response_time}ms`
                });
            } else {
                this.$dispatch('show-notification', {
                    type: 'error',
                    message: `Test fehlgeschlagen: ${result.error}`
                });
            }
        } catch (error) {
            this.$dispatch('show-notification', {
                type: 'error',
                message: 'Fehler beim Testen: ' + error.message
            });
        }
    }
}));

Alpine.data('notifications', () => ({
    // Notification System
    notifications: [],

    show(notification) {
        this.notifications.push({
            id: Date.now(),
            ...notification
        });

        setTimeout(() => {
            this.remove(notification.id);
        }, 5000);
    },

    remove(id) {
        this.notifications = this.notifications.filter(n => n.id !== id);
    }
}));

// Alpine.js starten
window.Alpine = Alpine;
Alpine.start();
