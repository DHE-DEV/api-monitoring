# API Monitor System

Ein umfassendes Laravel-basiertes System zur Überwachung von APIs mit automatischen Benachrichtigungen, detailliertem Reporting und benutzerfreundlicher Web-Oberfläche.

## 📋 Inhaltsverzeichnis

- [Funktionsumfang](#funktionsumfang)
- [Installation](#installation)
- [Konfiguration](#konfiguration)
- [Artisan Befehle](#artisan-befehle)
- [Bedienungsanleitung](#bedienungsanleitung)
- [E-Mail Alerts](#e-mail-alerts)
- [Export Funktionen](#export-funktionen)
- [Technische Details](#technische-details)

## Funktionsumfang

### ✅ Monitoring Features
- **Flexibles API-Monitoring**: Unterstützt GET, POST, PUT, DELETE Requests
- **Konfigurierbare Intervalle**: 1 Minute bis 24 Stunden
- **Response Time Tracking**: Millisekunden-genaue Messung
- **Status Code Überwachung**: HTTP Status Code Tracking
- **Custom Headers & Payloads**: Vollständige Request-Konfiguration
- **Bearer Token Support**: Automatische Authentifizierung

### 📊 Reporting & Analytics
- **Detaillierte Statistiken**: 24h, 30 Tage und historische Daten
- **Erfolgsraten-Tracking**: Prozentuale Verfügbarkeit
- **Response Time Analysis**: Min/Max/Durchschnitt Auswertungen
- **Fehler-Dokumentation**: Vollständige Error-Logs
- **Sortierung & Filterung**: Nach Zeit, Status, HTTP-Code

### 🔔 Alert System
- **E-Mail Benachrichtigungen**: Automatische Alerts bei Problemen
- **Intelligente Rate Limiting**: Spam-Schutz für Notifications
- **Konfigurierbare Schwellenwerte**: Anpassbare Alert-Trigger
- **Alert-Kategorien**:
    - 🐌 Langsame Antwortzeiten
    - 🚨 HTTP-Fehler (4xx/5xx)
    - 🔴 API komplett nicht erreichbar

### 🛠️ Benutzerfreundlichkeit
- **Web-Interface**: Intuitive Dashboard-Oberfläche
- **Live-Testing**: Sofortige Test-Funktionalität
- **Excel Export**: Detaillierte Datenexporte
- **Ein-Klick Alert-Toggle**: Schnelle Alert-Verwaltung
- **Mobile-Responsive**: Optimiert für alle Geräte

## Installation

### 1. Repository klonen & Dependencies installieren
```bash
git clone <repository-url>
cd api-monitor-system
composer install
npm install && npm run build
```

### 2. Umgebung konfigurieren
```bash
cp .env.example .env
php artisan key:generate
```

### 3. Datenbank einrichten
```bash
# Datenbank-Verbindung in .env konfigurieren, dann:
php artisan migrate
```

### 4. Scheduler einrichten
Fügen Sie diese Zeile zu Ihrer Crontab hinzu:
```bash
* * * * * cd /pfad/zu/ihrem/projekt && php artisan schedule:run >> /dev/null 2>&1
```

## Konfiguration

### Umgebungsvariablen (.env)

#### 🔐 API Authentifizierung
```env
# Bearer Token für API-Requests (wird automatisch zu Headers hinzugefügt)
API_BEARER_TOKEN=your-secret-token-here
```

#### 📧 E-Mail Alert Konfiguration
```env
# Schwellenwert für langsame Antworten (in Millisekunden)
API_SLOW_RESPONSE_THRESHOLD=3000

# Rate Limiting für E-Mail Alerts (in Minuten)
API_SLOW_RESPONSE_ALERT_INTERVAL=30  # Alle 30 Min bei langsamen Antworten
API_HTTP_ERROR_ALERT_INTERVAL=15     # Alle 15 Min bei HTTP-Fehlern  
API_DOWN_ALERT_INTERVAL=5            # Alle 5 Min bei API-Ausfällen

# E-Mail Empfänger (kommagetrennt)
API_ALERT_RECIPIENTS="admin@example.com,dev@example.com"
```

#### 📬 Mail-Server Konfiguration
```env
MAIL_MAILER=smtp
MAIL_HOST=your-smtp-host
MAIL_PORT=587
MAIL_USERNAME=your-email@example.com
MAIL_PASSWORD=your-email-password
MAIL_FROM_ADDRESS="noreply@example.com"
MAIL_FROM_NAME="API Monitor System"
MAIL_ENCRYPTION=tls
```

#### 🗄️ Datenbank
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=api_monitor
DB_USERNAME=your-db-user
DB_PASSWORD=your-db-password
```

### Konfigurationswerte Erklärung

| Variable | Beschreibung | Standardwert | Beispiel |
|----------|-------------|--------------|-----------|
| `API_BEARER_TOKEN` | Automatisch hinzugefügter Bearer Token | - | `abc123xyz` |
| `API_SLOW_RESPONSE_THRESHOLD` | Schwellenwert für "langsam" (ms) | 3000 | `5000` |
| `API_SLOW_RESPONSE_ALERT_INTERVAL` | Alert-Frequenz langsame Antworten (min) | 30 | `60` |
| `API_HTTP_ERROR_ALERT_INTERVAL` | Alert-Frequenz HTTP-Fehler (min) | 15 | `30` |
| `API_DOWN_ALERT_INTERVAL` | Alert-Frequenz API-Ausfall (min) | 5 | `10` |
| `API_ALERT_RECIPIENTS` | E-Mail Empfänger (kommagetrennt) | - | `"a@x.com,b@y.com"` |

## Artisan Befehle

### Monitoring ausführen
```bash
# Alle aktiven Monitore einmalig ausführen
php artisan api:monitor

# Spezifischen Monitor ausführen
php artisan api:monitor --monitor-id=1

# Scheduler-Status prüfen
php artisan schedule:list

# Scheduler manuell ausführen (für Tests)
php artisan schedule:run
```

### Wartung & Debugging
```bash
# Logs anzeigen
tail -f storage/logs/laravel.log

# Cache leeren
php artisan cache:clear

# Konfiguration cachen
php artisan config:cache

# Wartungsmodus
php artisan down
php artisan up
```

### Nach Konfigurationsänderungen
```bash
# 1. Konfiguration neu laden
php artisan config:clear

# 2. Cache leeren
php artisan cache:clear

# 3. Optional: Konfiguration cachen (Production)
php artisan config:cache
```

## Bedienungsanleitung

### 🆕 Neuen Monitor erstellen

1. **Dashboard öffnen**: Navigieren Sie zur Hauptseite
2. **"Neuer Monitor" klicken**: Button in der Navigation
3. **Formular ausfüllen**:
    - **Name**: Beschreibender Name für den Monitor
    - **URL**: Vollständige API-URL (mit https://)
    - **HTTP-Methode**: GET, POST, PUT oder DELETE
    - **Intervall**: Überwachungsfrequenz (1-1440 Minuten)
    - **Headers**: JSON-Format, z.B. `{"Content-Type": "application/json"}`
    - **Payload**: JSON-Daten für POST/PUT Requests
4. **Testen**: "Testen (ohne Speichern)" für Validierung
5. **Speichern**: Monitor wird aktiviert und läuft automatisch

### 🔍 Monitor Details anzeigen

1. **Monitor auswählen**: Auf "Details" bei gewünschtem Monitor klicken
2. **Übersicht**: Aktuelle Konfiguration und letzter Status
3. **Statistiken**:
    - 24h Statistiken: Erfolgsrate, Durchschnittswerte
    - 30 Tage Trend: Langzeitentwicklung
4. **Ergebnisliste**:
    - Filterbar nach Zeitraum, Status, HTTP-Code
    - Sortierbar nach allen Spalten
    - Klick auf Zeile öffnet Details-Modal

### ✏️ Monitor bearbeiten

1. **"Bearbeiten" klicken**: Bei gewünschtem Monitor
2. **Einstellungen anpassen**: Alle Parameter sind änderbar
3. **E-Mail Alerts**:
    - Checkbox für Ein/Aus
    - "⚡ Schnell Ein/Aus" für sofortige Änderung
4. **Testen**: Aktuelle Einstellungen vor Speichern testen
5. **Speichern**: Änderungen werden sofort wirksam

### 🧪 Monitor testen

**Aus der Übersicht**:
- "Testen" Button bei gewünschtem Monitor
- Ergebnis wird als Popup angezeigt

**Aus dem Detailbereich**:
- "Jetzt testen" Button
- Detaillierte Anzeige der Testergebnisse

**Beim Erstellen/Bearbeiten**:
- "Testen" Button im Formular
- Validierung vor dem Speichern

### 📊 Export-Funktionen

1. **Zur Detail-Ansicht**: Monitor öffnen
2. **Filter setzen**: Gewünschten Zeitraum/Status wählen
3. **"📊 Excel Export" klicken**:
    - Export berücksichtigt aktuelle Filter
    - Datei wird automatisch heruntergeladen
    - Enthält: Zeitstempel, Status, Antwortzeiten, HTTP-Codes, Fehler

## E-Mail Alerts

### Alert-Typen

#### ⚠️ Langsame Antwortzeit
- **Trigger**: Response Time > konfigurierter Schwellenwert
- **Standard-Schwellenwert**: 3000ms
- **Rate Limiting**: Alle 30 Minuten
- **Zweck**: Performance-Monitoring

#### 🚨 HTTP-Fehler
- **Trigger**: HTTP Status Code ≠ 200
- **Beispiele**: 404, 500, 503
- **Rate Limiting**: Alle 15 Minuten
- **Zweck**: Funktionalitäts-Monitoring

#### 🔴 API nicht erreichbar
- **Trigger**: Verbindungsfehler, Timeout
- **Rate Limiting**: Alle 5 Minuten
- **Zweck**: Verfügbarkeits-Monitoring

### Alert-Verwaltung

**Global ein/ausschalten**:
- E-Mail Empfänger in `.env` entfernen/hinzufügen
- `API_ALERT_RECIPIENTS=""`

**Pro Monitor**:
- Dashboard: Ein-Klick Toggle bei jedem Monitor
- Bearbeiten-Seite: Detaillierte Einstellungen
- Grund für Deaktivierung wird gespeichert

**Rate Limiting anpassen**:
```env
# Weniger Alerts (entspannter)
API_SLOW_RESPONSE_ALERT_INTERVAL=60
API_HTTP_ERROR_ALERT_INTERVAL=30
API_DOWN_ALERT_INTERVAL=15

# Mehr Alerts (aggressiver)
API_SLOW_RESPONSE_ALERT_INTERVAL=15
API_HTTP_ERROR_ALERT_INTERVAL=5
API_DOWN_ALERT_INTERVAL=2
```

## Export Funktionen

### Excel Export
- **Formatierung**: Professionelle Tabelle mit Styling
- **Inhalte**:
    - Monitor-Informationen
    - Zeitstempel (deutsches Format)
    - Status (Erfolgreich/Fehler)
    - Antwortzeiten
    - HTTP Status Codes
    - Fehlermeldungen
- **Filterung**: Export berücksichtigt alle aktiven Filter
- **Dateiname**: `api-monitor-{name}-{datum-zeit}.xlsx`

### Nutzung
1. Gewünschten Monitor öffnen
2. Filter nach Bedarf setzen (Zeitraum, Status, HTTP-Code)
3. "📊 Excel Export" klicken
4. Datei wird automatisch heruntergeladen

## Technische Details

### Systemanforderungen
- **PHP**: ≥ 8.1
- **Laravel**: ≥ 10.x
- **MySQL**: ≥ 5.7 oder MariaDB ≥ 10.3
- **Memory**: Mindestens 256MB für PHP
- **Cron**: Für automatische Ausführung

### Datenbank-Schema
- **api_monitors**: Monitor-Konfigurationen
- **api_monitor_results**: Testergebnisse
- **sessions**: Session-Verwaltung
- **cache**: Cache-Speicher

### Performance-Optimierungen
- **Pagination**: Ergebnisse werden seitenweise geladen
- **Indexierung**: Optimierte Datenbankindizes
- **Caching**: Rate Limiting über Laravel Cache
- **Lazy Loading**: Efficient Database Queries

### Sicherheit
- **CSRF Protection**: Alle Formulare geschützt
- **SQL Injection**: Eloquent ORM verhindert Injections
- **XSS Prevention**: Blade Template Engine
- **Input Validation**: Umfassende Validierungsregeln

### Logging
```bash
# Monitor-Ausführungen
tail -f storage/logs/laravel.log | grep "API Monitor"

# E-Mail Alerts
tail -f storage/logs/laravel.log | grep "Alert email"

# Fehler-Debugging
tail -f storage/logs/laravel.log | grep "ERROR"
```

### Wartung
```bash
# Alte Daten löschen (älter als 90 Tage)
# Fügen Sie diesen Command zu app/Console/Commands hinzu:
php artisan api:cleanup --days=90

# Logs rotieren
php artisan queue:restart
```

## 🆘 Troubleshooting

### Häufige Probleme

**Cron läuft nicht**:
```bash
# Crontab prüfen
crontab -l

# Manually testen
php artisan schedule:run
```

**E-Mails werden nicht gesendet**:
```bash
# Mail-Konfiguration testen
php artisan tinker
>>> Mail::raw('Test', function($msg) { $msg->to('test@example.com'); });
```

**Monitor läuft nicht**:
```bash
# Einzelnen Monitor testen
php artisan api:monitor --monitor-id=1
```

**Berechtigungsfehler**:
```bash
# Storage-Berechtigungen
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

---

## 📞 Support

Bei Fragen oder Problemen:
1. Logs prüfen: `storage/logs/laravel.log`
2. Konfiguration validieren: `php artisan config:show`
3. Scheduler-Status: `php artisan schedule:list`

**Entwickelt für zuverlässiges API-Monitoring** 🚀
