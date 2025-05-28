<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>API Monitor System - Benutzeranleitung</title>
    <style>
        @page {
            size: A4;
            margin: 2.5cm;
        }

        body {
            font-family: 'Arial', sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: none;
            margin: 0;
            padding: 0;
        }

        .title-page {
            text-align: center;
            padding-top: 5cm;
            page-break-after: always;
        }

        .title-page h1 {
            font-size: 36px;
            color: #2c3e50;
            margin-bottom: 30px;
            font-weight: bold;
        }

        .title-page h2 {
            font-size: 24px;
            color: #34495e;
            margin-bottom: 50px;
            font-weight: normal;
        }

        .title-page .version {
            font-size: 18px;
            color: #7f8c8d;
            margin-top: 100px;
        }

        .title-page .company {
            font-size: 16px;
            color: #95a5a6;
            margin-top: 50px;
        }

        .toc {
            page-break-after: always;
            padding-top: 2cm;
        }

        .toc h2 {
            font-size: 24px;
            color: #2c3e50;
            border-bottom: 2px solid #3498db;
            padding-bottom: 10px;
            margin-bottom: 30px;
        }

        .toc ul {
            list-style: none;
            padding: 0;
        }

        .toc li {
            margin-bottom: 8px;
            padding-left: 20px;
        }

        .toc .chapter {
            font-weight: bold;
            margin-top: 20px;
            margin-bottom: 10px;
            padding-left: 0;
        }

        .toc .section {
            margin-left: 20px;
        }

        .toc .subsection {
            margin-left: 40px;
            font-size: 14px;
            color: #666;
        }

        .content {
            counter-reset: chapter;
        }

        h1 {
            font-size: 28px;
            color: #2c3e50;
            border-bottom: 3px solid #3498db;
            padding-bottom: 15px;
            margin-top: 40px;
            margin-bottom: 25px;
            page-break-before: always;
            counter-increment: chapter;
        }

        h1::before {
            content: counter(chapter) ". ";
        }

        h2 {
            font-size: 22px;
            color: #34495e;
            margin-top: 30px;
            margin-bottom: 20px;
            border-left: 5px solid #3498db;
            padding-left: 15px;
        }

        h3 {
            font-size: 18px;
            color: #2c3e50;
            margin-top: 25px;
            margin-bottom: 15px;
            font-weight: bold;
        }

        h4 {
            font-size: 16px;
            color: #34495e;
            margin-top: 20px;
            margin-bottom: 10px;
            font-weight: bold;
        }

        p {
            margin-bottom: 12px;
            text-align: justify;
        }

        ul, ol {
            margin-bottom: 15px;
            padding-left: 25px;
        }

        li {
            margin-bottom: 5px;
        }

        .info-box {
            background-color: #e8f4fd;
            border-left: 5px solid #3498db;
            padding: 15px;
            margin: 20px 0;
        }

        .warning-box {
            background-color: #ffeaa7;
            border-left: 5px solid #fdcb6e;
            padding: 15px;
            margin: 20px 0;
        }

        .success-box {
            background-color: #d5f4e6;
            border-left: 5px solid #00b894;
            padding: 15px;
            margin: 20px 0;
        }

        .code {
            background-color: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 4px;
            padding: 10px;
            font-family: 'Courier New', monospace;
            font-size: 14px;
            margin: 10px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }

        th {
            background-color: #f8f9fa;
            font-weight: bold;
        }

        .step-number {
            background-color: #3498db;
            color: white;
            border-radius: 50%;
            width: 25px;
            height: 25px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            margin-right: 10px;
        }

        .highlight {
            background-color: #fff3cd;
            padding: 2px 5px;
            border-radius: 3px;
        }

        .footer {
            position: fixed;
            bottom: 1cm;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 12px;
            color: #666;
        }
    </style>
</head>
<body>
<!-- Titelblatt -->
<div class="title-page">
    <h1>API Monitor System</h1>
    <h2>Benutzeranleitung</h2>

    <div class="version">
        Version 1.0<br>
        Stand: Mai 2025
    </div>

    <div class="company">
        Professionelle Service-Überwachung<br>
        Benutzerhandbuch
    </div>
</div>

<!-- Inhaltsverzeichnis -->
<div class="toc">
    <h2>Inhaltsverzeichnis</h2>
    <ul>
        <li class="chapter">1. Einführung</li>
        <li class="section">1.1 Was ist das API Monitor System?</li>
        <li class="section">1.2 Hauptfunktionen</li>
        <li class="section">1.3 Anwendungsbereiche</li>

        <li class="chapter">2. Grundlagen</li>
        <li class="section">2.1 Systemzugang</li>
        <li class="section">2.2 Dashboard-Übersicht</li>
        <li class="section">2.3 Grundlegende Navigation</li>

        <li class="chapter">3. Monitore verwalten</li>
        <li class="section">3.1 Neuen Monitor erstellen</li>
        <li class="section">3.2 Monitor-Konfiguration</li>
        <li class="section">3.3 Monitore testen</li>
        <li class="section">3.4 Monitore bearbeiten</li>
        <li class="section">3.5 Monitore löschen</li>

        <li class="chapter">4. Überwachung und Benachrichtigungen</li>
        <li class="section">4.1 E-Mail-Benachrichtigungen</li>
        <li class="section">4.2 Alert-Kategorien</li>
        <li class="section">4.3 Benachrichtigungen verwalten</li>

        <li class="chapter">5. Berichte und Statistiken</li>
        <li class="section">5.1 Monitor-Details anzeigen</li>
        <li class="section">5.2 Statistiken verstehen</li>
        <li class="section">5.3 Daten filtern und sortieren</li>
        <li class="section">5.4 Excel-Export</li>

        <li class="chapter">6. Problembehandlung</li>
        <li class="section">6.1 Häufige Probleme</li>
        <li class="section">6.2 Fehlermeldungen verstehen</li>
        <li class="section">6.3 Lösungsansätze</li>

        <li class="chapter">7. Best Practices</li>
        <li class="section">7.1 Empfohlene Konfigurationen</li>
        <li class="section">7.2 Wartung und Pflege</li>
        <li class="section">7.3 Tipps für den täglichen Einsatz</li>
    </ul>
</div>

<div class="content">
    <!-- Kapitel 1: Einführung -->
    <h1>Einführung</h1>

    <h2>Was ist das API Monitor System?</h2>
    <p>Das API Monitor System ist eine professionelle Lösung zur kontinuierlichen Überwachung von Online-Services, Websites und Programmierschnittstellen (APIs). Das System funktioniert als automatisierter Wächter, der rund um die Uhr die Verfügbarkeit und Leistung Ihrer digitalen Services überprüft.</p>

    <div class="info-box">
        <strong>Automatisierte Überwachung:</strong> Das System arbeitet vollständig automatisch und benötigt nach der Einrichtung keine manuelle Überwachung.
    </div>

    <h2>Hauptfunktionen</h2>
    <p>Das API Monitor System bietet folgende Kernfunktionen:</p>

    <h3>Kontinuierliche Überwachung</h3>
    <ul>
        <li>Automatische Prüfung Ihrer Services in konfigurierbaren Intervallen</li>
        <li>Unterstützung verschiedener HTTP-Methoden (GET, POST, PUT, DELETE)</li>
        <li>Anpassbare Überwachungsintervalle von 1 Minute bis 24 Stunden</li>
        <li>Gleichzeitige Überwachung mehrerer Services</li>
    </ul>

    <h3>Intelligente Benachrichtigungen</h3>
    <ul>
        <li>Sofortige E-Mail-Alerts bei Problemen</li>
        <li>Verschiedene Warnstufen für unterschiedliche Problemtypen</li>
        <li>Spam-Schutz durch intelligentes Rate Limiting</li>
        <li>Individuelle Konfiguration pro Monitor</li>
    </ul>

    <h3>Umfassende Berichterstattung</h3>
    <ul>
        <li>Detaillierte Statistiken über Verfügbarkeit und Performance</li>
        <li>Historische Datenanalyse</li>
        <li>Export-Funktionen für externe Analysen</li>
        <li>Grafische Darstellung von Trends</li>
    </ul>

    <h2>Anwendungsbereiche</h2>

    <h3>Website-Monitoring</h3>
    <p>Überwachen Sie die Erreichbarkeit Ihrer Unternehmenswebsite und erhalten Sie sofort Benachrichtigungen bei Ausfällen. Besonders wichtig für Unternehmen, deren Online-Präsenz geschäftskritisch ist.</p>

    <h3>E-Commerce Überwachung</h3>
    <p>Stellen Sie sicher, dass kritische Funktionen wie Warenkorb, Checkout-Prozess oder Zahlungsabwicklung kontinuierlich funktionieren. Vermeiden Sie Umsatzverluste durch nicht erkannte Ausfälle.</p>

    <h3>API-Überwachung</h3>
    <p>Überwachen Sie Programmierschnittstellen, die von Ihren Anwendungen oder Partnern genutzt werden. Stellen Sie die Kontinuität Ihrer digitalen Services sicher.</p>

    <h3>Service Level Monitoring</h3>
    <p>Überwachen Sie die Performance Ihrer Services und stellen Sie sicher, dass vereinbarte Service Level Agreements (SLAs) eingehalten werden.</p>

    <h3>Drittanbieter-Services</h3>
    <p>Überwachen Sie externe Services, von denen Ihr Unternehmen abhängig ist, um proaktiv auf Ausfälle reagieren zu können.</p>

    <!-- Kapitel 2: Grundlagen -->
    <h1>Grundlagen</h1>

    <h2>Systemzugang</h2>
    <p>Der Zugang zum API Monitor System erfolgt über einen Standard-Webbrowser. Sie benötigen lediglich die URL des Systems und entsprechende Zugangsdaten.</p>

    <div class="info-box">
        <strong>Unterstützte Browser:</strong> Das System funktioniert mit allen modernen Webbrowsern wie Chrome, Firefox, Safari und Edge.
    </div>

    <h3>Erstmaliger Zugang</h3>
    <ol>
        <li>Öffnen Sie Ihren bevorzugten Webbrowser</li>
        <li>Geben Sie die bereitgestellte System-URL ein</li>
        <li>Das Dashboard wird automatisch geladen</li>
    </ol>

    <h2>Dashboard-Übersicht</h2>
    <p>Das Dashboard ist die zentrale Benutzeroberfläche des Systems. Hier erhalten Sie einen Überblick über alle konfigurierten Monitore und deren aktuellen Status.</p>

    <h3>Hauptnavigation</h3>
    <p>Die Navigation befindet sich im oberen Bereich der Seite und bietet folgende Optionen:</p>
    <ul>
        <li><strong>Dashboard:</strong> Zurück zur Hauptübersicht</li>
        <li><strong>Neuer Monitor:</strong> Erstellen eines neuen Überwachungsmonitors</li>
    </ul>

    <h3>Monitor-Tabelle</h3>
    <p>Die Haupttabelle zeigt alle konfigurierten Monitore mit folgenden Informationen:</p>

    <table>
        <tr>
            <th>Spalte</th>
            <th>Beschreibung</th>
        </tr>
        <tr>
            <td>Name</td>
            <td>Bezeichnung des Monitors mit Status-Indikator</td>
        </tr>
        <tr>
            <td>URL</td>
            <td>Überwachte Adresse und HTTP-Methode</td>
        </tr>
        <tr>
            <td>Intervall</td>
            <td>Prüfungsfrequenz in Minuten</td>
        </tr>
        <tr>
            <td>Status</td>
            <td>Aktueller Zustand (OK/Fehler/Unbekannt)</td>
        </tr>
        <tr>
            <td>E-Mail Alerts</td>
            <td>Status der Benachrichtigungen</td>
        </tr>
        <tr>
            <td>Letzte Antwortzeit</td>
            <td>Performance-Indikator der letzten Prüfung</td>
        </tr>
        <tr>
            <td>Aktionen</td>
            <td>Verfügbare Operationen für den Monitor</td>
        </tr>
    </table>

    <h2>Grundlegende Navigation</h2>

    <h3>Status-Indikatoren verstehen</h3>
    <p>Jeder Monitor wird mit einem farbigen Status-Indikator dargestellt:</p>
    <ul>
        <li><strong>Grün:</strong> Monitor ist aktiv und funktioniert ordnungsgemäß</li>
        <li><strong>Rot:</strong> Monitor ist aktiv, aber es wurden Probleme erkannt</li>
        <li><strong>Grau:</strong> Monitor ist deaktiviert</li>
    </ul>

    <h3>Aktions-Buttons</h3>
    <p>Für jeden Monitor stehen folgende Aktionen zur Verfügung:</p>
    <ul>
        <li><strong>Details:</strong> Öffnet die ausführliche Ansicht mit Statistiken</li>
        <li><strong>Bearbeiten:</strong> Ermöglicht die Änderung der Monitor-Konfiguration</li>
        <li><strong>Testen:</strong> Führt eine sofortige Prüfung durch</li>
        <li><strong>Löschen:</strong> Entfernt den Monitor permanent</li>
    </ul>

    <!-- Kapitel 3: Monitore verwalten -->
    <h1>Monitore verwalten</h1>

    <h2>Neuen Monitor erstellen</h2>
    <p>Die Erstellung eines neuen Monitors erfolgt über ein strukturiertes Formular, das Sie durch alle notwendigen Konfigurationsschritte führt.</p>

    <h3>Monitor-Erstellung starten</h3>
    <ol>
        <li>Klicken Sie auf den Button "Neuer Monitor" in der oberen Navigation</li>
        <li>Das Erstellungsformular wird geöffnet</li>
        <li>Füllen Sie alle erforderlichen Felder aus</li>
        <li>Testen Sie die Konfiguration vor dem Speichern</li>
        <li>Speichern Sie den Monitor</li>
    </ol>

    <h2>Monitor-Konfiguration</h2>

    <h3>Grundeinstellungen</h3>

    <h4>Name</h4>
    <p>Vergeben Sie einen aussagekräftigen Namen für Ihren Monitor. Dieser Name wird in der Übersicht und in E-Mail-Benachrichtigungen verwendet.</p>
    <div class="info-box">
        <strong>Empfehlung:</strong> Verwenden Sie beschreibende Namen wie "Hauptwebsite Startseite" oder "Online-Shop Checkout-Prozess" anstatt technischer Bezeichnungen.
    </div>

    <h4>URL</h4>
    <p>Geben Sie die vollständige URL ein, die überwacht werden soll. Die URL muss mit http:// oder https:// beginnen.</p>
    <div class="code">
        Beispiele:<br>
        https://www.ihre-website.de<br>
        https://api.ihr-service.com/health<br>
        http://intern.ihr-unternehmen.de/status
    </div>

    <h4>HTTP-Methode</h4>
    <p>Wählen Sie die HTTP-Methode aus, die für die Überwachung verwendet werden soll:</p>
    <ul>
        <li><strong>GET:</strong> Standard für Website-Aufrufe und einfache API-Abfragen</li>
        <li><strong>POST:</strong> Zum Senden von Daten, z.B. beim Testen von Formularen</li>
        <li><strong>PUT:</strong> Für Update-Operationen</li>
        <li><strong>DELETE:</strong> Für Lösch-Operationen</li>
    </ul>

    <div class="warning-box">
        <strong>Wichtiger Hinweis:</strong> Bei GET und DELETE Requests werden keine Payload-Daten gesendet. Das Payload-Feld wird automatisch ausgeblendet.
    </div>

    <h4>Überwachungsintervall</h4>
    <p>Legen Sie fest, wie oft der Monitor ausgeführt werden soll. Das Intervall kann zwischen 1 Minute und 1440 Minuten (24 Stunden) gewählt werden.</p>

    <table>
        <tr>
            <th>Service-Typ</th>
            <th>Empfohlenes Intervall</th>
            <th>Begründung</th>
        </tr>
        <tr>
            <td>Kritische Produktionssysteme</td>
            <td>1-5 Minuten</td>
            <td>Schnelle Reaktion bei Problemen</td>
        </tr>
        <tr>
            <td>Standard-Websites</td>
            <td>5-15 Minuten</td>
            <td>Ausgewogen zwischen Überwachung und Systemlast</td>
        </tr>
        <tr>
            <td>Interne Services</td>
            <td>15-30 Minuten</td>
            <td>Regelmäßige Kontrolle ohne hohe Frequenz</td>
        </tr>
        <tr>
            <td>Batch-Systeme</td>
            <td>60+ Minuten</td>
            <td>Angepasst an Verarbeitungszyklen</td>
        </tr>
    </table>

    <h3>Erweiterte Einstellungen</h3>

    <h4>Headers</h4>
    <p>Konfigurieren Sie zusätzliche HTTP-Headers im JSON-Format. Headers werden für Authentifizierung, Content-Type Definition oder andere HTTP-Protokoll-Anforderungen verwendet.</p>

    <div class="code">
        Beispiel Headers:<br>
        {<br>
        &nbsp;&nbsp;"Content-Type": "application/json",<br>
        &nbsp;&nbsp;"Accept": "application/json",<br>
        &nbsp;&nbsp;"User-Agent": "API-Monitor/1.0"<br>
        }
    </div>

    <div class="info-box">
        <strong>Automatische Authentifizierung:</strong> Wenn ein Bearer Token in der Systemkonfiguration hinterlegt ist, wird dieser automatisch zu den Headers hinzugefügt.
    </div>

    <h4>Payload</h4>
    <p>Für POST und PUT Requests können Sie Daten im JSON-Format mitschicken. Dies ist nützlich zum Testen von API-Endpunkten, die Eingabedaten erwarten.</p>

    <div class="code">
        Beispiel Payload:<br>
        {<br>
        &nbsp;&nbsp;"action": "health_check",<br>
        &nbsp;&nbsp;"timestamp": "2025-01-01T12:00:00Z"<br>
        }
    </div>

    <h2>Monitore testen</h2>
    <p>Bevor Sie einen Monitor aktivieren, sollten Sie die Konfiguration testen. Das System bietet mehrere Möglichkeiten zum Testen.</p>

    <h3>Test während der Erstellung</h3>
    <ol>
        <li>Füllen Sie alle erforderlichen Felder im Erstellungsformular aus</li>
        <li>Klicken Sie auf "Testen (ohne Speichern)"</li>
        <li>Das System führt eine Test-Anfrage durch</li>
        <li>Sie erhalten eine Rückmeldung über Erfolg oder Fehler</li>
    </ol>

    <div class="success-box">
        <strong>Empfehlung:</strong> Testen Sie immer vor dem Speichern, um Konfigurationsfehler zu vermeiden.
    </div>

    <h3>Adhoc-Tests aus der Übersicht</h3>
    <p>Sie können jeden Monitor jederzeit manuell testen:</p>
    <ol>
        <li>Klicken Sie in der Monitor-Übersicht auf "Testen"</li>
        <li>Das System führt sofort eine Prüfung durch</li>
        <li>Das Ergebnis wird als Popup angezeigt</li>
        <li>Die Seite wird aktualisiert, um den neuen Status zu zeigen</li>
    </ol>

    <h3>Test-Ergebnisse interpretieren</h3>
    <p>Nach einem Test erhalten Sie folgende Informationen:</p>
    <ul>
        <li><strong>Status:</strong> Erfolgreich oder Fehlgeschlagen</li>
        <li><strong>Antwortzeit:</strong> Dauer der Anfrage in Millisekunden</li>
        <li><strong>HTTP-Code:</strong> Vom Server zurückgegebener Status-Code</li>
        <li><strong>Fehlermeldung:</strong> Detaillierte Beschreibung bei Problemen</li>
    </ul>

    <h2>Monitore bearbeiten</h2>
    <p>Bestehende Monitore können jederzeit angepasst werden, ohne dass historische Daten verloren gehen.</p>

    <h3>Bearbeitungsmodus öffnen</h3>
    <ol>
        <li>Klicken Sie bei dem gewünschten Monitor auf "Bearbeiten"</li>
        <li>Das Bearbeitungsformular wird mit den aktuellen Einstellungen geladen</li>
        <li>Nehmen Sie die gewünschten Änderungen vor</li>
        <li>Testen Sie die neuen Einstellungen</li>
        <li>Speichern Sie die Änderungen</li>
    </ol>

    <h3>Wichtige Überlegungen bei Änderungen</h3>
    <ul>
        <li><strong>URL-Änderungen:</strong> Testen Sie immer, wenn Sie die URL ändern</li>
        <li><strong>Intervall-Anpassungen:</strong> Berücksichtigen Sie die Systemlast bei sehr kurzen Intervallen</li>
        <li><strong>Method-Änderungen:</strong> Überprüfen Sie Payload und Header bei Methoden-Wechsel</li>
    </ul>

    <div class="warning-box">
        <strong>Wichtiger Hinweis:</strong> Änderungen werden sofort wirksam. Der Monitor verwendet die neuen Einstellungen ab der nächsten automatischen Prüfung.
    </div>

    <h2>Monitore löschen</h2>
    <p>Nicht mehr benötigte Monitore können permanent entfernt werden.</p>

    <h3>Löschvorgang</h3>
    <ol>
        <li>Klicken Sie bei dem gewünschten Monitor auf "Löschen"</li>
        <li>Bestätigen Sie die Sicherheitsabfrage</li>
        <li>Der Monitor wird mit allen historischen Daten entfernt</li>
    </ol>

    <div class="warning-box">
        <strong>Achtung:</strong> Das Löschen eines Monitors ist unwiderruflich. Alle historischen Daten und Statistiken gehen verloren.
    </div>

    <!-- Kapitel 4: Überwachung und Benachrichtigungen -->
    <h1>Überwachung und Benachrichtigungen</h1>

    <h2>E-Mail-Benachrichtigungen</h2>
    <p>Das System versendet automatische E-Mail-Benachrichtigungen bei erkannten Problemen. Die Benachrichtigungen sind intelligent gestaltet und vermeiden Spam durch übermäßige Meldungen.</p>

    <h3>Konfiguration der Benachrichtigungen</h3>
    <p>E-Mail-Benachrichtigungen können für jeden Monitor individuell aktiviert oder deaktiviert werden. Die Einstellung erfolgt über das Dashboard oder das Bearbeitungsformular.</p>

    <h4>Aktivierung/Deaktivierung</h4>
    <ul>
        <li><strong>Dashboard:</strong> Klicken Sie auf den Status-Button in der "E-Mail Alerts" Spalte</li>
        <li><strong>Bearbeitungsseite:</strong> Verwenden Sie die Checkbox "E-Mail-Benachrichtigungen aktiviert"</li>
        <li><strong>Schnell-Toggle:</strong> Nutzen Sie den "Schnell Ein/Aus" Button für sofortige Änderungen</li>
    </ul>

    <h2>Alert-Kategorien</h2>
    <p>Das System unterscheidet zwischen verschiedenen Arten von Problemen und versendet entsprechend kategorisierte Benachrichtigungen.</p>

    <h3>Langsame Antwortzeiten</h3>
    <p><strong>Auslöser:</strong> Die Antwortzeit überschreitet den konfigurierten Schwellenwert (Standard: 3000 Millisekunden)</p>
    <p><strong>Häufigkeit:</strong> Maximal alle 30 Minuten</p>
    <p><strong>Zweck:</strong> Frühwarnung vor Performance-Problemen</p>

    <div class="info-box">
        <strong>Interpretation:</strong> Langsame Antwortzeiten können auf Serverüberlastung, Netzwerkprobleme oder ineffiziente Datenverarbeitung hinweisen.
    </div>

    <h3>HTTP-Fehler</h3>
    <p><strong>Auslöser:</strong> Der Server gibt einen HTTP-Status-Code ungleich 200 zurück</p>
    <p><strong>Häufigkeit:</strong> Maximal alle 15 Minuten</p>
    <p><strong>Zweck:</strong> Erkennung von Funktionsstörungen</p>

    <p>Häufige HTTP-Fehler-Codes:</p>
    <ul>
        <li><strong>404:</strong> Seite oder Ressource nicht gefunden</li>
        <li><strong>500:</strong> Interner Server-Fehler</li>
        <li><strong>503:</strong> Service nicht verfügbar</li>
        <li><strong>401:</strong> Authentifizierung fehlgeschlagen</li>
        <li><strong>403:</strong> Zugriff verweigert</li>
    </ul>

    <h3>API komplett nicht erreichbar</h3>
    <p><strong>Auslöser:</strong> Verbindungsfehler, Timeouts oder komplette Nichterreichbarkeit</p>
    <p><strong>Häufigkeit:</strong> Maximal alle 5 Minuten</p>
    <p><strong>Zweck:</strong> Sofortige Warnung bei kritischen Ausfällen</p>

    <div class="warning-box">
        <strong>Kritische Warnung:</strong> Diese Art von Alert deutet auf schwerwiegende Probleme hin, die sofortige Aufmerksamkeit erfordern.
    </div>

    <h2>Benachrichtigungen verwalten</h2>

    <h3>Individuelle Monitor-Einstellungen</h3>
    <p>Jeder Monitor kann individuell konfiguriert werden:</p>

    <h4>Über das Dashboard</h4>
    <ol>
        <li>Suchen Sie den gewünschten Monitor in der Tabelle</li>
        <li>Klicken Sie auf den Status-Button in der "E-Mail Alerts" Spalte</li>
        <li>Wählen Sie "Aktivieren" oder "Deaktivieren"</li>
        <li>Geben Sie optional einen Grund für die Änderung ein</li>
        <li>Bestätigen Sie die Änderung</li>
    </ol>

    <h4>Über die Bearbeitungsseite</h4>
    <ol>
        <li>Klicken Sie auf "Bearbeiten" bei dem gewünschten Monitor</li>
        <li>Scrollen Sie zum Bereich "E-Mail-Benachrichtigungen"</li>
        <li>Aktivieren oder deaktivieren Sie die Checkbox</li>
        <li>Nutzen Sie den "Schnell Ein/Aus" Button für sofortige Änderungen</li>
        <li>Speichern Sie die Einstellungen</li>
    </ol>

    <h3>Deaktivierungsprotokoll</h3>
    <p>Das System protokolliert, wann und warum E-Mail-Benachrichtigungen deaktiviert wurden:</p>
    <ul>
        <li><strong>Zeitpunkt:</strong> Wann wurden die Alerts deaktiviert</li>
        <li><strong>Benutzer:</strong> Wer hat die Deaktivierung vorgenommen</li>
        <li><strong>Grund:</strong> Angegebener Grund für die Deaktivierung</li>
    </ul>

    <h3>Temporäre Deaktivierung</h3>
    <p>Für geplante Wartungsarbeiten können Benachrichtigungen temporär deaktiviert werden:</p>

    <ol>
        <li>Deaktivieren Sie die E-Mail-Alerts vor der Wartung</li>
        <li>Geben Sie als Grund "Geplante Wartung" an</li>
        <li>Aktivieren Sie die Alerts nach Abschluss der Wartung wieder</li>
    </ol>

    <div class="info-box">
        <strong>Best Practice:</strong> Dokumentieren Sie geplante Wartungen durch entsprechende Deaktivierungsgründe.
    </div>

    <!-- Kapitel 5: Berichte und Statistiken -->
    <h1>Berichte und Statistiken</h1>

    <h2>Monitor-Details anzeigen</h2>
    <p>Die Detail-Ansicht bietet umfassende Informationen über die Performance und Verfügbarkeit eines Monitors.</p>

    <h3>Detail-Ansicht öffnen</h3>
    <ol>
        <li>Klicken Sie bei dem gewünschten Monitor auf "Details"</li>
        <li>Die Detail-Seite wird geöffnet</li>
        <li>Sie erhalten eine vollständige Übersicht über den Monitor</li>
    </ol>

    <h3>Informationsabschnitte</h3>

    <h4>Monitor-Konfiguration</h4>
    <p>Zeigt die aktuelle Konfiguration des Monitors:</p>
    <ul>
        <li>URL und HTTP-Methode</li>
        <li>Überwachungsintervall</li>
        <li>Status der E-Mail-Benachrichtigungen</li>
        <li>Aktueller Monitor-Status</li>
    </ul>

    <h4>Konfigurierte Headers und Payload</h4>
    <p>Falls konfiguriert, werden die verwendeten HTTP-Headers und Payload-Daten angezeigt.</p>

    <h2>Statistiken verstehen</h2>

    <h3>24-Stunden Statistiken</h3>
    <p>Bietet einen Überblick über die Performance der letzten 24 Stunden:</p>

    <table>
        <tr>
            <th>Metrik</th>
            <th>Beschreibung</th>
            <th>Interpretation</th>
        </tr>
        <tr>
            <td>Erfolgsrate</td>
            <td>Prozentsatz erfolgreicher Anfragen</td>
            <td>Höher ist besser (Ziel: >99%)</td>
        </tr>
        <tr>
            <td>Durchschnittliche Antwortzeit</td>
            <td>Mittlere Dauer aller Anfragen</td>
            <td>Indikator für Performance</td>
        </tr>
        <tr>
            <td>Min/Max Antwortzeit</td>
            <td>Schnellste und langsamste Antwort</td>
            <td>Zeigt Performance-Schwankungen</td>
        </tr>
        <tr>
            <td>Anzahl Anfragen</td>
            <td>Gesamtzahl der Prüfungen</td>
            <td>Bestätigt ordnungsgemäße Ausführung</td>
        </tr>
    </table>

    <h3>30-Tage Trend</h3>
    <p>Langzeit-Analyse der Monitor-Performance:</p>
    <ul>
        <li><strong>Erfolgsrate:</strong> Trend der Verfügbarkeit</li>
        <li><strong>Performance-Entwicklung:</strong> Änderungen in der Antwortzeit</li>
        <li><strong>Ausfallzeiten:</strong> Geschätzte Downtime in Minuten</li>
        <li><strong>Häufigkeit der Prüfungen:</strong> Konsistenz der Überwachung</li>
    </ul>

    <div class="info-box">
        <strong>SLA-Berechnung:</strong> Die 30-Tage Statistiken eignen sich zur Überprüfung von Service Level Agreements.
    </div>

    <h2>Daten filtern und sortieren</h2>
    <p>Die Ergebnisliste kann nach verschiedenen Kriterien gefiltert und sortiert werden, um spezifische Analysen durchzuführen.</p>

    <h3>Verfügbare Filter</h3>

    <h4>Zeitraum-Filter</h4>
    <ul>
        <li><strong>Alle:</strong> Komplette Historie</li>
        <li><strong>Heute:</strong> Nur Ergebnisse des aktuellen Tages</li>
        <li><strong>Diese Woche:</strong> Ergebnisse der laufenden Woche</li>
        <li><strong>Dieser Monat:</strong> Ergebnisse des aktuellen Monats</li>
        <li><strong>Dieses Jahr:</strong> Ergebnisse des laufenden Jahres</li>
    </ul>

    <h4>Status-Filter</h4>
    <ul>
        <li><strong>Alle:</strong> Erfolgreiche und fehlgeschlagene Anfragen</li>
        <li><strong>Nur Fehler:</strong> Ausschließlich fehlgeschlagene Anfragen</li>
    </ul>

    <h4>HTTP-Code Filter</h4>
    <p>Filtert Ergebnisse nach spezifischen HTTP-Status-Codes. Verfügbare Codes werden dynamisch basierend auf den vorhandenen Daten angezeigt.</p>

    <h3>Sortieroptionen</h3>
    <p>Klicken Sie auf die Spaltenüberschriften, um die Ergebnisse zu sortieren:</p>
    <ul>
        <li><strong>Zeitpunkt:</strong> Chronologische Sortierung</li>
        <li><strong>Status:</strong> Gruppierung nach Erfolg/Fehler</li>
        <li><strong>Antwortzeit:</strong> Performance-basierte Sortierung</li>
        <li><strong>HTTP-Code:</strong> Sortierung nach Status-Codes</li>
    </ul>

    <h3>Detaillierte Ergebnis-Analyse</h3>
    <p>Klicken Sie auf eine beliebige Zeile in der Ergebnisliste, um detaillierte Informationen zu einer spezifischen Anfrage zu erhalten:</p>

    <ul>
        <li><strong>Request-Informationen:</strong> URL, Methode, Zeitpunkt</li>
        <li><strong>Response-Details:</strong> Status, Antwortzeit, HTTP-Code</li>
        <li><strong>Fehlermeldungen:</strong> Detaillierte Beschreibung bei Problemen</li>
        <li><strong>Response Body:</strong> Vollständiger Antwort-Inhalt</li>
    </ul>

    <h2>Excel-Export</h2>
    <p>Das System bietet professionelle Excel-Export-Funktionen für detaillierte Analysen und Dokumentation.</p>

    <h3>Export erstellen</h3>
    <ol>
        <li>Öffnen Sie die Detail-Ansicht des gewünschten Monitors</li>
        <li>Konfigurieren Sie die gewünschten Filter</li>
        <li>Klicken Sie auf "Excel Export"</li>
        <li>Die Datei wird automatisch heruntergeladen</li>
    </ol>

    <h3>Export-Inhalte</h3>
    <p>Der Excel-Export enthält folgende Informationen:</p>

    <table>
        <tr>
            <th>Spalte</th>
            <th>Inhalt</th>
        </tr>
        <tr>
            <td>Monitor Name</td>
            <td>Bezeichnung des Monitors</td>
        </tr>
        <tr>
            <td>URL</td>
            <td>Überwachte Adresse</td>
        </tr>
        <tr>
            <td>HTTP Methode</td>
            <td>Verwendete HTTP-Methode</td>
        </tr>
        <tr>
            <td>Zeitpunkt</td>
            <td>Ausführungszeitpunkt (deutsches Format)</td>
        </tr>
        <tr>
            <td>Status</td>
            <td>Erfolgreich/Fehler</td>
        </tr>
        <tr>
            <td>Antwortzeit (ms)</td>
            <td>Performance-Messung</td>
        </tr>
        <tr>
            <td>HTTP Code</td>
            <td>Server-Antwort-Code</td>
        </tr>
        <tr>
            <td>Fehler</td>
            <td>Detaillierte Fehlerbeschreibung</td>
        </tr>
    </table>

    <h3>Export-Features</h3>
    <ul>
        <li><strong>Professionelle Formatierung:</strong> Übersichtliche Tabellen mit Styling</li>
        <li><strong>Filter-Berücksichtigung:</strong> Export enthält nur gefilterte Daten</li>
        <li><strong>Automatische Spaltenbreite:</strong> Optimale Darstellung aller Inhalte</li>
        <li><strong>Header-Hervorhebung:</strong> Deutliche Kennzeichnung der Spaltenüberschriften</li>
    </ul>

    <div class="info-box">
        <strong>Dateiname:</strong> Exports werden automatisch mit Monitor-Name und Zeitstempel benannt (z.B. "api-monitor-Hauptwebsite-2025-01-15-14-30-25.xlsx").
    </div>

    <!-- Kapitel 6: Problembehandlung -->
    <h1>Problembehandlung</h1>

    <h2>Häufige Probleme</h2>

    <h3>Monitor zeigt konstant Fehler</h3>

    <h4>Symptome</h4>
    <ul>
        <li>Monitor-Status ist dauerhaft rot</li>
        <li>Kontinuierliche Fehler-Meldungen</li>
        <li>Häufige E-Mail-Benachrichtigungen</li>
    </ul>

    <h4>Mögliche Ursachen</h4>
    <ul>
        <li>Service ist tatsächlich nicht verfügbar</li>
        <li>Falsche URL-Konfiguration</li>
        <li>Authentifizierungsprobleme</li>
        <li>Firewall oder Netzwerk-Beschränkungen</li>
        <li>Fehlerhafte Headers oder Payload</li>
    </ul>

    <h4>Lösungsansätze</h4>
    <ol>
        <li><strong>Manuelle Verifikation:</strong> Öffnen Sie die URL manuell im Browser</li>
        <li><strong>Konfiguration prüfen:</strong> Überprüfen Sie URL, Methode und Parameter</li>
        <li><strong>Test-Funktion nutzen:</strong> Verwenden Sie die integrierte Test-Funktion</li>
        <li><strong>Netzwerk prüfen:</strong> Stellen Sie sicher, dass das System Zugriff auf die URL hat</li>
        <li><strong>Temporäre Deaktivierung:</strong> Deaktivieren Sie E-Mail-Alerts bei bekannten Problemen</li>
    </ol>

    <h3>Sehr langsame Antwortzeiten</h3>

    <h4>Symptome</h4>
    <ul>
        <li>Antwortzeiten über dem konfigurierten Schwellenwert</li>
        <li>Benachrichtigungen über langsame Performance</li>
        <li>Schwankende Response-Zeiten</li>
    </ul>

    <h4>Mögliche Ursachen</h4>
    <ul>
        <li>Server-Überlastung</li>
        <li>Netzwerk-Latenz</li>
        <li>Ineffiziente Datenverarbeitung</li>
        <li>Externe Abhängigkeiten</li>
    </ul>

    <h4>Lösungsansätze</h4>
    <ol>
        <li><strong>Schwellenwert anpassen:</strong> Erhöhen Sie den Grenzwert für langsame Antworten</li>
        <li><strong>Überwachungsintervall reduzieren:</strong> Weniger häufige Prüfungen bei bekannten Performance-Problemen</li>
        <li><strong>Service-Performance analysieren:</strong> Untersuchen Sie die Ursachen der langsamen Antworten</li>
        <li><strong>Load-Testing:</strong> Überprüfen Sie die Server-Kapazität</li>
    </ol>

    <h3>Keine E-Mail-Benachrichtigungen</h3>

    <h4>Symptome</h4>
    <ul>
        <li>Erwartete E-Mails werden nicht empfangen</li>
        <li>Alerts sind konfiguriert, aber keine Nachrichten kommen an</li>
    </ul>

    <h4>Überprüfungsschritte</h4>
    <ol>
        <li><strong>E-Mail-Alert Status:</strong> Prüfen Sie, ob Benachrichtigungen für den Monitor aktiviert sind</li>
        <li><strong>Spam-Ordner:</strong> Kontrollieren Sie Ihren Spam/Junk-Ordner</li>
        <li><strong>E-Mail-Adresse:</strong> Stellen Sie sicher, dass Ihre E-Mail-Adresse korrekt konfiguriert ist</li>
        <li><strong>Rate Limiting:</strong> Benachrichtigungen werden möglicherweise durch Spam-Schutz begrenzt</li>
        <li><strong>System-Konfiguration:</strong> Kontaktieren Sie den Administrator zur Überprüfung der Mail-Konfiguration</li>
    </ol>

    <h3>Monitor läuft nicht automatisch</h3>

    <h4>Symptome</h4>
    <ul>
        <li>Keine neuen Einträge in der Ergebnisliste</li>
        <li>Letzte Prüfung liegt lange zurück</li>
        <li>Manuelle Tests funktionieren</li>
    </ul>

    <h4>Überprüfungsschritte</h4>
    <ol>
        <li><strong>Monitor-Status:</strong> Stellen Sie sicher, dass der Monitor aktiv (grüner Status) ist</li>
        <li><strong>System-Status:</strong> Das automatische Überwachungssystem könnte gestoppt sein</li>
        <li><strong>Administrator kontaktieren:</strong> Systemweite Probleme erfordern administrative Unterstützung</li>
    </ol>

    <h2>Fehlermeldungen verstehen</h2>

    <h3>Verbindungsfehler</h3>
    <div class="code">
        "Connection timeout" oder "Could not resolve host"
    </div>
    <p><strong>Bedeutung:</strong> Das System kann die angegebene URL nicht erreichen.</p>
    <p><strong>Lösung:</strong> Überprüfen Sie die URL und stellen Sie sicher, dass der Service erreichbar ist.</p>

    <h3>HTTP-Fehler</h3>
    <div class="code">
        "HTTP 404 - Not Found" oder "HTTP 500 - Internal Server Error"
    </div>
    <p><strong>Bedeutung:</strong> Der Server ist erreichbar, aber gibt einen Fehler-Code zurück.</p>
    <p><strong>Lösung:</strong> Überprüfen Sie die URL-Korrektheit und den Status des Services.</p>

    <h3>Authentifizierungsfehler</h3>
    <div class="code">
        "HTTP 401 - Unauthorized" oder "HTTP 403 - Forbidden"
    </div>
    <p><strong>Bedeutung:</strong> Zugriff verweigert aufgrund fehlender oder ungültiger Authentifizierung.</p>
    <p><strong>Lösung:</strong> Überprüfen Sie die konfigurierten Headers und Bearer Token.</p>

    <h3>JSON-Formatfehler</h3>
    <div class="code">
        "Invalid JSON format in headers/payload"
    </div>
    <p><strong>Bedeutung:</strong> Die eingegebenen Headers oder Payload-Daten sind nicht im gültigen JSON-Format.</p>
    <p><strong>Lösung:</strong> Korrigieren Sie die JSON-Syntax oder lassen Sie die Felder leer.</p>

    <h2>Lösungsansätze</h2>

    <h3>Systematische Problemdiagnose</h3>
    <ol>
        <li><strong>Problem isolieren:</strong> Betrifft es einen Monitor oder mehrere?</li>
        <li><strong>Zeitpunkt bestimmen:</strong> Seit wann tritt das Problem auf?</li>
        <li><strong>Manuelle Verifikation:</strong> Funktioniert der Service außerhalb des Monitors?</li>
        <li><strong>Konfiguration überprüfen:</strong> Sind alle Einstellungen korrekt?</li>
        <li><strong>Test durchführen:</strong> Nutzen Sie die integrierte Test-Funktion</li>
    </ol>

    <h3>Präventive Maßnahmen</h3>
    <ul>
        <li><strong>Regelmäßige Überprüfung:</strong> Kontrollieren Sie wöchentlich den Status Ihrer Monitore</li>
        <li><strong>Wartungsplanung:</strong> Deaktivieren Sie Alerts vor geplanten Wartungen</li>
        <li><strong>Dokumentation:</strong> Halten Sie Änderungen an Ihren Services fest</li>
        <li><strong>Backup-Überwachung:</strong> Konfigurieren Sie mehrere Monitore für kritische Services</li>
    </ul>

    <!-- Kapitel 7: Best Practices -->
    <h1>Best Practices</h1>

    <h2>Empfohlene Konfigurationen</h2>

    <h3>Monitor-Namenskonvention</h3>
    <p>Verwenden Sie eine einheitliche Namenskonvention für bessere Übersichtlichkeit:</p>

    <div class="code">
        Empfohlenes Format:<br>
        [System/Service] - [Komponente] - [Umgebung]<br><br>
        Beispiele:<br>
        "Webshop - Startseite - Produktion"<br>
        "API - Benutzerauthentifizierung - Staging"<br>
        "CRM - Dashboard - Test"
    </div>

    <h3>Intervall-Konfiguration</h3>

    <table>
        <tr>
            <th>Service-Kritikalität</th>
            <th>Empfohlenes Intervall</th>
            <th>Begründung</th>
        </tr>
        <tr>
            <td>Mission Critical</td>
            <td>1-2 Minuten</td>
            <td>Sofortige Erkennung von Ausfällen</td>
        </tr>
        <tr>
            <td>Geschäftskritisch</td>
            <td>5 Minuten</td>
            <td>Schnelle Reaktion bei Problemen</td>
        </tr>
        <tr>
            <td>Standard</td>
            <td>15 Minuten</td>
            <td>Ausgewogene Überwachung</td>
        </tr>
        <tr>
            <td>Weniger kritisch</td>
            <td>30-60 Minuten</td>
            <td>Grundüberwachung ohne hohe Systemlast</td>
        </tr>
    </table>

    <h3>E-Mail-Alert Strategie</h3>

    <h4>Empfänger-Gruppierung</h4>
    <ul>
        <li><strong>Kritische Services:</strong> Direkte Benachrichtigung an On-Call Team</li>
        <li><strong>Standard Services:</strong> Benachrichtigung an Entwicklungsteam</li>
        <li><strong>Interne Services:</strong> Benachrichtigung an System-Administratoren</li>
    </ul>

    <h4>Schwellenwert-Konfiguration</h4>
    <ul>
        <li><strong>Web-Anwendungen:</strong> 3000ms für langsame Antworten</li>
        <li><strong>APIs:</strong> 1000ms für langsame Antworten</li>
        <li><strong>Interne Services:</strong> 5000ms für langsame Antworten</li>
    </ul>

    <h2>Wartung und Pflege</h2>

    <h3>Regelmäßige Überprüfungen</h3>

    <h4>Wöchentliche Aufgaben</h4>
    <ul>
        <li>Dashboard-Review: Überprüfung aller Monitor-Status</li>
        <li>Performance-Trends analysieren</li>
        <li>Fehlerhafte Monitore identifizieren und korrigieren</li>
        <li>E-Mail-Alert Status kontrollieren</li>
    </ul>

    <h4>Monatliche Aufgaben</h4>
    <ul>
        <li>Monitor-Konfigurationen auf Aktualität prüfen</li>
        <li>Nicht mehr benötigte Monitore entfernen</li>
        <li>Performance-Schwellenwerte anpassen</li>
        <li>Statistiken für SLA-Berichte exportieren</li>
    </ul>

    <h4>Quartalsweise Aufgaben</h4>
    <ul>
        <li>Vollständige Überprüfung aller URLs</li>
        <li>Monitoring-Strategie evaluieren</li>
        <li>System-Performance analysieren</li>
        <li>Dokumentation aktualisieren</li>
    </ul>

    <h3>Monitoring-Hygiene</h3>

    <h4>Aktualität sicherstellen</h4>
    <ul>
        <li>URLs regelmäßig auf Gültigkeit prüfen</li>
        <li>Authentifizierungs-Token aktualisieren</li>
        <li>Nicht mehr existierende Services entfernen</li>
        <li>Neue kritische Services hinzufügen</li>
    </ul>

    <h4>Redundanz vermeiden</h4>
    <ul>
        <li>Doppelte Monitore für dieselbe URL identifizieren</li>
        <li>Ähnliche Monitore zusammenfassen</li>
        <li>Test-Monitore regelmäßig aufräumen</li>
    </ul>

    <h2>Tipps für den täglichen Einsatz</h2>

    <h3>Effiziente Nutzung des Dashboards</h3>

    <h4>Status-Monitoring</h4>
    <ul>
        <li>Nutzen Sie die Farb-Kodierung für schnelle Übersicht</li>
        <li>Priorisieren Sie rote (fehlerhafte) Monitore</li>
        <li>Überwachen Sie Trends in den Antwortzeiten</li>
    </ul>

    <h4>Proaktive Wartung</h4>
    <ul>
        <li>Deaktivieren Sie Alerts vor geplanten Wartungen</li>
        <li>Nutzen Sie aussagekräftige Deaktivierungsgründe</li>
        <li>Reaktivieren Sie Alerts nach Wartungsende</li>
    </ul>

    <h3>Troubleshooting-Workflow</h3>

    <ol>
        <li><strong>Problem identifizieren:</strong> Welcher Monitor zeigt Fehler?</li>
        <li><strong>Umfang bestimmen:</strong> Einzelproblem oder systemweiter Ausfall?</li>
        <li><strong>Sofortmaßnahmen:</strong> Kritische Services zuerst behandeln</li>
        <li><strong>Root Cause Analysis:</strong> Ursache des Problems ermitteln</li>
        <li><strong>Lösung implementieren:</strong> Problem beheben</li>
        <li><strong>Monitoring anpassen:</strong> Konfiguration bei Bedarf optimieren</li>
    </ol>

    <h3>Reporting und Dokumentation</h3>

    <h4>Regelmäßige Berichte</h4>
    <ul>
        <li><strong>Wöchentlich:</strong> Status-Summary für Management</li>
        <li><strong>Monatlich:</strong> SLA-Compliance Berichte</li>
        <li><strong>Quartalsweise:</strong> Trend-Analysen und Verbesserungsvorschläge</li>
    </ul>

    <h4>Incident-Dokumentation</h4>
    <ul>
        <li>Schwerwiegende Ausfälle dokumentieren</li>
        <li>Excel-Exports für Post-Incident Reviews nutzen</li>
        <li>Lessons Learned in Monitoring-Strategie einarbeiten</li>
    </ul>

    <h3>Eskalations-Prozesse</h3>

    <h4>Automatische Eskalation</h4>
    <ul>
        <li>Kritische Services: Sofortige Benachrichtigung</li>
        <li>Wiederholte Fehler: Eskalation nach definierten Kriterien</li>
        <li>Längere Ausfälle: Automatische Weiterleitung an höhere Ebenen</li>
    </ul>

    <h4>Manuelle Eskalation</h4>
    <ul>
        <li>Bewertung der Geschäftsauswirkungen</li>
        <li>Kommunikation mit relevanten Stakeholdern</li>
        <li>Koordination von Lösungsmaßnahmen</li>
    </ul>

    <div class="success-box">
        <strong>Erfolgsfaktor:</strong> Die konsequente Anwendung dieser Best Practices führt zu einer zuverlässigen und effizienten Service-Überwachung.
    </div>

    <!-- Anhang -->
    <h1>Anhang</h1>

    <h2>Schnellreferenz</h2>

    <h3>Wichtige Aktionen im Überblick</h3>

    <table>
        <tr>
            <th>Aktion</th>
            <th>Navigation</th>
            <th>Hinweise</th>
        </tr>
        <tr>
            <td>Neuen Monitor erstellen</td>
            <td>Dashboard → "Neuer Monitor"</td>
            <td>Immer vor Speichern testen</td>
        </tr>
        <tr>
            <td>Monitor bearbeiten</td>
            <td>Dashboard → "Bearbeiten"</td>
            <td>Änderungen werden sofort aktiv</td>
        </tr>
        <tr>
            <td>Monitor testen</td>
            <td>Dashboard → "Testen"</td>
            <td>Für Sofort-Überprüfung</td>
        </tr>
        <tr>
            <td>Details anzeigen</td>
            <td>Dashboard → "Details"</td>
            <td>Umfassende Statistiken</td>
        </tr>
        <tr>
            <td>E-Mail Alerts umschalten</td>
            <td>Dashboard → Alert-Status klicken</td>
            <td>Grund für Änderung angeben</td>
        </tr>
        <tr>
            <td>Excel Export</td>
            <td>Monitor-Details → "Excel Export"</td>
            <td>Berücksichtigt aktive Filter</td>
        </tr>
    </table>

    <h3>Empfohlene Konfigurationswerte</h3>

    <table>
        <tr>
            <th>Parameter</th>
            <th>Kritische Services</th>
            <th>Standard Services</th>
            <th>Weniger wichtige Services</th>
        </tr>
        <tr>
            <td>Überwachungsintervall</td>
            <td>1-2 Minuten</td>
            <td>5-15 Minuten</td>
            <td>30-60 Minuten</td>
        </tr>
        <tr>
            <td>Schwellenwert langsame Antwort</td>
            <td>1000ms</td>
            <td>3000ms</td>
            <td>5000ms</td>
        </tr>
        <tr>
            <td>E-Mail Alerts</td>
            <td>Immer aktiviert</td>
            <td>Normalerweise aktiviert</td>
            <td>Nach Bedarf</td>
        </tr>
    </table>

    <h2>Fehlerbehebung Checkliste</h2>

    <h3>Monitor zeigt Fehler</h3>
    <ol>
        <li>URL manuell im Browser testen</li>
        <li>Monitor-Konfiguration überprüfen</li>
        <li>Integrierte Test-Funktion nutzen</li>
        <li>Netzwerk-Konnektivität prüfen</li>
        <li>Headers und Payload validieren</li>
        <li>Bei andauernden Problemen: Administrator kontaktieren</li>
    </ol>

    <h3>Keine E-Mail-Benachrichtigungen</h3>
    <ol>
        <li>E-Mail-Alert Status im Dashboard prüfen</li>
        <li>Spam-Ordner kontrollieren</li>
        <li>Rate Limiting berücksichtigen</li>
        <li>System-Konfiguration durch Administrator prüfen lassen</li>
    </ol>

    <h3>Langsame Performance</h3>
    <ol>
        <li>Aktuelle Antwortzeiten analysieren</li>
        <li>Schwellenwerte überprüfen</li>
        <li>Service-Performance untersuchen</li>
        <li>Überwachungsintervall anpassen</li>
    </ol>

    <h2>Glossar</h2>

    <table>
        <tr>
            <th>Begriff</th>
            <th>Definition</th>
        </tr>
        <tr>
            <td>API</td>
            <td>Application Programming Interface - Programmierschnittstelle für die Kommunikation zwischen Softwaresystemen</td>
        </tr>
        <tr>
            <td>HTTP</td>
            <td>Hypertext Transfer Protocol - Standard-Protokoll für die Übertragung von Webinhalten</td>
        </tr>
        <tr>
            <td>JSON</td>
            <td>JavaScript Object Notation - Datenformat für strukturierte Informationen</td>
        </tr>
        <tr>
            <td>Payload</td>
            <td>Nutzdaten, die bei HTTP-Anfragen übertragen werden</td>
        </tr>
        <tr>
            <td>Response Time</td>
            <td>Antwortzeit - Zeit zwischen Anfrage und vollständiger Antwort</td>
        </tr>
        <tr>
            <td>Status Code</td>
            <td>HTTP-Status-Code - Numerische Codes, die den Erfolg oder Fehler einer Anfrage anzeigen</td>
        </tr>
        <tr>
            <td>Uptime</td>
            <td>Verfügbarkeitszeit - Zeitraum, in dem ein Service ordnungsgemäß funktioniert</td>
        </tr>
        <tr>
            <td>SLA</td>
            <td>Service Level Agreement - Vereinbarung über Serviceleistungen und deren Qualität</td>
        </tr>
        <tr>
            <td>Rate Limiting</td>
            <td>Begrenzung der Häufigkeit von Aktionen zur Vermeidung von Spam oder Systemüberlastung</td>
        </tr>
        <tr>
            <td>Bearer Token</td>
            <td>Authentifizierungs-Token für sichere API-Zugriffe</td>
        </tr>
    </table>

    <h2>Support und weitere Informationen</h2>

    <h3>Bei technischen Problemen</h3>
    <p>Wenn Sie auf Probleme stoßen, die Sie nicht selbst lösen können:</p>

    <ol>
        <li><strong>Problemdokumentation:</strong> Erstellen Sie Screenshots und notieren Sie Fehlermeldungen</li>
        <li><strong>Systeminformationen sammeln:</strong> Welcher Monitor, seit wann, welche Fehlermeldung</li>
        <li><strong>Administrator kontaktieren:</strong> Wenden Sie sich an Ihren IT-Support oder Systemadministrator</li>
    </ol>

    <h3>Nützliche Informationen für den Support</h3>
    <ul>
        <li>Name des betroffenen Monitors</li>
        <li>Zeitpunkt des Auftretens</li>
        <li>Genaue Fehlermeldung</li>
        <li>Screenshots der Benutzeroberfläche</li>
        <li>Bereits durchgeführte Lösungsversuche</li>
    </ul>

    <h3>Schulungen und Weiterbildung</h3>
    <p>Für eine optimale Nutzung des Systems empfehlen wir:</p>
    <ul>
        <li>Regelmäßige Schulungen für neue Benutzer</li>
        <li>Best Practice Workshops</li>
        <li>Dokumentation aktueller Monitoring-Strategien</li>
    </ul>

    ---

    <div style="margin-top: 50px; padding-top: 20px; border-top: 2px solid #3498db; text-align: center;">
        <p><strong>API Monitor System - Benutzeranleitung</strong></p>
        <p>Version 1.0 | Stand: Mai 2025</p>
        <p>© 2025 - Alle Rechte vorbehalten</p>
    </div>
</div>

<div class="footer">
    API Monitor System - Benutzeranleitung | Seite <span id="pageNumber"></span>
</div>

<script>
    // Automatische Seitennummerierung
    window.onload = function() {
        const pageNumber = document.getElementById('pageNumber');
        if (pageNumber) {
            pageNumber.textContent = 'wird beim Drucken automatisch eingefügt';
        }
    };

    // Druck-optimierte Darstellung
    window.addEventListener('beforeprint', function() {
        document.body.style.fontSize = '12px';
        document.body.style.lineHeight = '1.4';
    });

    window.addEventListener('afterprint', function() {
        document.body.style.fontSize = '';
        document.body.style.lineHeight = '';
    });
</script>
</body>
</html>
