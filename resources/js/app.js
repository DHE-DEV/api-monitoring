// resources/js/app.js - Vollständige Version mit Dashboard-Erweiterungen

import './bootstrap';
import Alpine from 'alpinejs';

// Bestehende Alpine.js Komponenten (erweitert)
Alpine.data('userManagement', () => ({
    // User Management Logik
    users: [],
    loading: false,
    selectedUser: null,
    showCreateForm: false,

    async loadUsers() {
        this.loading = true;
        try {
            const response = await fetch('/api/users', {
                headers: {
                    'Authorization': `Bearer ${localStorage.getItem('api_token')}`,
                    'Accept': 'application/json'
                }
            });
            const data = await response.json();
            this.users = data.users || data;
        } catch (error) {
            console.error('Error loading users:', error);
            this.$dispatch('show-notification', {
                type: 'error',
                message: 'Fehler beim Laden der Benutzer'
            });
        } finally {
            this.loading = false;
        }
    },

    async createUser(userData) {
        try {
            const response = await fetch('/api/users', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${localStorage.getItem('api_token')}`,
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify(userData)
            });

            if (response.ok) {
                await this.loadUsers(); // Refresh list
                this.showCreateForm = false;
                this.$dispatch('show-notification', {
                    type: 'success',
                    message: 'Benutzer erfolgreich erstellt'
                });
            }
        } catch (error) {
            this.$dispatch('show-notification', {
                type: 'error',
                message: 'Fehler beim Erstellen des Benutzers'
            });
        }
    }
}));

Alpine.data('apiMonitor', () => ({
    // API Monitor Dashboard Logik (erweitert)
    monitors: [],
    stats: {},
    loading: false,
    selectedMonitor: null,

    async loadMonitors() {
        this.loading = true;
        try {
            const response = await fetch('/api/monitors', {
                headers: {
                    'Authorization': `Bearer ${localStorage.getItem('api_token')}`,
                    'Accept': 'application/json'
                }
            });
            const data = await response.json();
            this.monitors = data.monitors || data;
        } catch (error) {
            console.error('Error loading monitors:', error);
        } finally {
            this.loading = false;
        }
    },

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
    },

    async deleteMonitor(monitorId) {
        if (!confirm('Möchten Sie diesen Monitor wirklich löschen?')) {
            return;
        }

        try {
            const response = await fetch(`/api/monitors/${monitorId}`, {
                method: 'DELETE',
                headers: {
                    'Authorization': `Bearer ${localStorage.getItem('api_token')}`,
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });

            if (response.ok) {
                await this.loadMonitors(); // Refresh list
                this.$dispatch('show-notification', {
                    type: 'success',
                    message: 'Monitor erfolgreich gelöscht'
                });
            }
        } catch (error) {
            this.$dispatch('show-notification', {
                type: 'error',
                message: 'Fehler beim Löschen des Monitors'
            });
        }
    }
}));

Alpine.data('notifications', () => ({
    // Notification System (erweitert)
    notifications: [],

    show(notification) {
        const id = Date.now() + Math.random();
        this.notifications.push({
            id: id,
            ...notification
        });

        setTimeout(() => {
            this.remove(id);
        }, notification.duration || 5000);
    },

    remove(id) {
        this.notifications = this.notifications.filter(n => n.id !== id);
    }
}));

// NEUE Dashboard-Komponenten
Alpine.data('dashboardStats', () => ({
    stats: {},
    loading: false,
    lastUpdated: null,

    async refreshStats() {
        this.loading = true;

        try {
            const response = await fetch('/dashboard/data', {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
                }
            });

            if (response.ok) {
                const data = await response.json();
                this.stats = data.stats;
                this.lastUpdated = new Date().toLocaleTimeString('de-DE');

                this.$dispatch('show-notification', {
                    type: 'success',
                    message: 'Dashboard-Daten aktualisiert',
                    duration: 2000
                });
            }
        } catch (error) {
            console.error('Stats refresh error:', error);
            this.$dispatch('show-notification', {
                type: 'error',
                message: 'Fehler beim Laden der Statistiken'
            });
        } finally {
            this.loading = false;
        }
    }
}));

Alpine.data('quickActions', () => ({
    actions: [],
    loading: false,

    async loadActions() {
        this.loading = true;

        try {
            const response = await fetch('/dashboard/quick-actions', {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
                }
            });

            if (response.ok) {
                const data = await response.json();
                this.actions = data.actions;
            }
        } catch (error) {
            console.error('Quick actions error:', error);
        } finally {
            this.loading = false;
        }
    },

    init() {
        this.loadActions();
    }
}));

Alpine.data('realTimeUpdates', () => ({
    enabled: true,
    interval: null,
    updateFrequency: 30000, // 30 Sekunden

    init() {
        if (this.enabled) {
            this.startUpdates();
        }

        // Page Visibility API - Updates pausieren wenn Tab nicht aktiv
        document.addEventListener('visibilitychange', () => {
            if (document.hidden) {
                this.stopUpdates();
            } else if (this.enabled) {
                this.startUpdates();
            }
        });
    },

    startUpdates() {
        this.stopUpdates(); // Sicherstellen dass kein Duplikat läuft

        this.interval = setInterval(() => {
            // Dashboard-Update Event senden
            window.dispatchEvent(new CustomEvent('auto-refresh-dashboard'));
        }, this.updateFrequency);
    },

    stopUpdates() {
        if (this.interval) {
            clearInterval(this.interval);
            this.interval = null;
        }
    },

    toggleUpdates() {
        this.enabled = !this.enabled;

        if (this.enabled) {
            this.startUpdates();
            this.$dispatch('show-notification', {
                type: 'info',
                message: 'Auto-Updates aktiviert'
            });
        } else {
            this.stopUpdates();
            this.$dispatch('show-notification', {
                type: 'info',
                message: 'Auto-Updates deaktiviert'
            });
        }
    }
}));

Alpine.data('searchComponent', () => ({
    query: '',
    results: [],
    loading: false,
    showResults: false,

    async search() {
        if (this.query.length < 2) {
            this.results = [];
            this.showResults = false;
            return;
        }

        this.loading = true;

        try {
            const response = await fetch(`/api/search?q=${encodeURIComponent(this.query)}`, {
                headers: {
                    'Accept': 'application/json',
                    'Authorization': `Bearer ${localStorage.getItem('api_token')}`
                }
            });

            if (response.ok) {
                const data = await response.json();
                this.results = data.results || [];
                this.showResults = true;
            }
        } catch (error) {
            console.error('Search error:', error);
        } finally {
            this.loading = false;
        }
    },

    selectResult(result) {
        this.query = result.title;
        this.showResults = false;

        if (result.url) {
            window.location.href = result.url;
        }
    },

    clearSearch() {
        this.query = '';
        this.results = [];
        this.showResults = false;
    }
}));

Alpine.data('themeToggle', () => ({
    darkMode: false,

    init() {
        this.darkMode = localStorage.getItem('darkMode') === 'true';
        this.updateTheme();
    },

    toggle() {
        this.darkMode = !this.darkMode;
        this.updateTheme();
        localStorage.setItem('darkMode', this.darkMode);
    },

    updateTheme() {
        if (this.darkMode) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    }
}));

// Login Form Komponente (für auth.login)
Alpine.data('loginForm', () => ({
    loading: false,
    form: {
        email: '',
        password: '',
        remember: false
    },
    errors: {},

    async submitLogin() {
        this.loading = true;
        this.errors = {};

        try {
            const response = await fetch('/login', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify(this.form)
            });

            const data = await response.json();

            if (data.success) {
                this.$dispatch('show-notification', {
                    type: 'success',
                    message: data.message
                });

                setTimeout(() => {
                    window.location.href = data.redirect || '/dashboard';
                }, 1000);
            } else {
                this.errors = data.errors || {};

                this.$dispatch('show-notification', {
                    type: 'error',
                    message: data.message || 'Anmeldung fehlgeschlagen'
                });
            }
        } catch (error) {
            console.error('Login error:', error);
            this.$dispatch('show-notification', {
                type: 'error',
                message: 'Ein unerwarteter Fehler ist aufgetreten.'
            });
        } finally {
            this.loading = false;
        }
    }
}));

// Dashboard Data Komponente (für dashboard.index)
Alpine.data('dashboardData', () => ({
    loading: false,
    stats: {},
    recentMonitors: [],
    systemStatus: {
        online: true,
        uptime: '99.9%'
    },
    showCreateMonitor: false,

    init() {
        this.refreshData();

        // Auto-refresh alle 30 Sekunden
        setInterval(() => {
            this.refreshData();
        }, 30000);

        // Event Listeners
        this.$el.addEventListener('auto-refresh-dashboard', () => {
            this.refreshData();
        });
    },

    async refreshData() {
        this.loading = true;

        try {
            const response = await fetch('/dashboard/data', {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });

            if (response.ok) {
                const data = await response.json();
                this.updateData(data);
            }
        } catch (error) {
            console.error('Dashboard refresh error:', error);
            this.$dispatch('show-notification', {
                type: 'error',
                message: 'Fehler beim Aktualisieren der Dashboard-Daten'
            });
        } finally {
            this.loading = false;
        }
    },

    updateData(data) {
        if (data.stats) {
            this.stats = { ...this.stats, ...data.stats };
        }
        if (data.recent_monitors) {
            this.recentMonitors = data.recent_monitors;
        }

        this.$dispatch('show-notification', {
            type: 'info',
            message: 'Dashboard-Daten aktualisiert',
            duration: 2000
        });
    },

    getSuccessRateColor(active, total) {
        const rate = (active / Math.max(total, 1)) * 100;
        if (rate >= 90) return 'text-green-600';
        if (rate >= 70) return 'text-yellow-600';
        return 'text-red-600';
    },

    getSuccessRateText(active, total) {
        const rate = Math.round((active / Math.max(total, 1)) * 100);
        if (rate >= 90) return 'Ausgezeichnet';
        if (rate >= 70) return 'Gut';
        return 'Verbesserung nötig';
    }
}));

// Alpine.js starten
window.Alpine = Alpine;
Alpine.start();

// Global Error Handler
window.addEventListener('error', (event) => {
    console.error('Global error:', event.error);

    window.dispatchEvent(new CustomEvent('show-notification', {
        detail: {
            type: 'error',
            message: 'Ein unerwarteter Fehler ist aufgetreten'
        }
    }));
});

// Global Helper Functions
window.apiCall = async (url, options = {}) => {
    const token = localStorage.getItem('api_token');

    const defaultOptions = {
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
            ...(token && { 'Authorization': `Bearer ${token}` })
        }
    };

    return fetch(url, { ...defaultOptions, ...options });
};

window.showNotification = (type, message, duration = 5000) => {
    window.dispatchEvent(new CustomEvent('show-notification', {
        detail: { type, message, duration }
    }));
};

// Format Helper Functions
window.formatBytes = (bytes) => {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
};

window.formatDuration = (ms) => {
    if (ms < 1000) return ms + 'ms';
    if (ms < 60000) return (ms / 1000).toFixed(1) + 's';
    return (ms / 60000).toFixed(1) + 'min';
};

// Event Delegation für dynamische Inhalte
document.addEventListener('DOMContentLoaded', () => {
    // Global Event Listeners hier hinzufügen

    // Notification Event Handler
    document.addEventListener('show-notification', (event) => {
        // Falls keine Alpine.js Notification-Komponente aktiv ist
        if (!window.Alpine.findClosest(document.body, '[x-data*="notifications"]')) {
            console.log('Notification:', event.detail);
        }
    });
});

console.log('Enhanced Dashboard Alpine.js components loaded successfully! 🚀');
