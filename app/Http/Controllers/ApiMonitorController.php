<?php
// app/Http/Controllers/ApiMonitorController.php
namespace App\Http\Controllers;

use App\Models\ApiMonitor;
use App\Models\ApiMonitorResult;
use App\Services\ApiMonitorService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Carbon\Carbon;

class ApiMonitorController extends Controller
{
    public function __construct(private ApiMonitorService $apiMonitorService)
    {
    }

    private function checkPermission($permission)
    {
        $user = auth()->user();

        // Debug-Log
        \Log::info("Permission check", [
            'user_id' => $user->id,
            'user_email' => $user->email,
            'permission' => $permission,
            'primary_role_id' => $user->primary_role_id,
            'legacy_role' => $user->role,
            'has_permission' => $user->hasPermission($permission)
        ]);

        if (!$user->hasPermission($permission)) {
            // Fallback für SuperAdmins
            if ($user->role === 'superadmin' || ($user->primaryRole && $user->primaryRole->name === 'superadmin')) {
                \Log::info("SuperAdmin fallback granted for permission: {$permission}");
                return true;
            }

            abort(403, "Keine Berechtigung für: {$permission}. Bitte kontaktieren Sie den Administrator.");
        }

        return true;
    }

    public function index()
    {
        // Debug: zur Kontrolle, ob dieser Punkt erreicht wird
        \Log::debug("ApiMonitorController@index aufgerufen", [
            'user_id' => auth()->id(),
            'role' => auth()->user()->role,
            'primary_role' => optional(auth()->user()->primaryRole)->name,
            'has_permission' => auth()->user()->hasPermission('view_monitors'),
        ]);

        $monitors = ApiMonitor::with('latestResult')->paginate(15);

        return view('api-monitor.index', compact('monitors'));
    }


    public function create()
    {
        return view('api-monitor.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'url' => 'required|url',
            'method' => ['required', Rule::in(['GET', 'POST', 'PUT', 'DELETE'])],
            'headers' => 'nullable|json',
            'payload' => 'nullable|json',
            'interval_minutes' => 'required|integer|min:1|max:1440',
            'is_active' => 'boolean',
            'email_alerts_enabled' => 'boolean',
        ]);

        // JSON Strings zu Arrays konvertieren
        if ($validated['headers']) {
            $validated['headers'] = json_decode($validated['headers'], true);
        }
        if ($validated['payload']) {
            $validated['payload'] = json_decode($validated['payload'], true);
        }

        // Checkbox explizit behandeln
        $validated['is_active'] = $request->has('is_active') && $request->get('is_active') == '1';
        $validated['email_alerts_enabled'] = $request->has('email_alerts_enabled') && $request->get('email_alerts_enabled') == '1';

        ApiMonitor::create($validated);

        return redirect()->route('api-monitor.index')
            ->with('success', 'API Monitor erfolgreich erstellt');
    }

    public function show(ApiMonitor $apiMonitor, Request $request)
    {
        $query = $apiMonitor->results();

        // Zeitraum-Filter anwenden
        $timeFilter = $request->get('time_filter', 'all');
        switch ($timeFilter) {
            case 'today':
                $query->whereDate('executed_at', today());
                break;
            case 'week':
                $query->where('executed_at', '>=', now()->startOfWeek());
                break;
            case 'month':
                $query->where('executed_at', '>=', now()->startOfMonth());
                break;
            case 'year':
                $query->where('executed_at', '>=', now()->startOfYear());
                break;
        }

        // Status-Filter
        if ($request->get('status_filter') === 'errors') {
            $query->where('success', false);
        }

        // HTTP Code Filter
        if ($request->filled('http_code_filter')) {
            $query->where('status_code', $request->get('http_code_filter'));
        }

        // Sortierung
        $sortBy = $request->get('sort', 'executed_at');
        $sortDirection = $request->get('direction', 'desc');

        $allowedSorts = ['executed_at', 'response_time_ms', 'status_code', 'success'];
        if (in_array($sortBy, $allowedSorts)) {
            $query->orderBy($sortBy, $sortDirection);
        } else {
            $query->orderBy('executed_at', 'desc');
        }

        $results = $query->paginate(100)->withQueryString();

        // Verfügbare HTTP Codes für Filter
        $availableHttpCodes = $apiMonitor->results()
            ->whereNotNull('status_code')
            ->distinct()
            ->pluck('status_code')
            ->sort()
            ->values();

        return view('api-monitor.show', compact('apiMonitor', 'results', 'availableHttpCodes'));
    }

    public function edit(ApiMonitor $apiMonitor)
    {
        return view('api-monitor.edit', compact('apiMonitor'));
    }

    public function update(Request $request, ApiMonitor $apiMonitor)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'url' => 'required|url',
            'method' => ['required', Rule::in(['GET', 'POST', 'PUT', 'DELETE'])],
            'headers' => 'nullable|json',
            'payload' => 'nullable|json',
            'interval_minutes' => 'required|integer|min:1|max:1440',
            'is_active' => 'boolean',
            'email_alerts_enabled' => 'boolean',
        ]);

        if ($validated['headers']) {
            $validated['headers'] = json_decode($validated['headers'], true);
        }
        if ($validated['payload']) {
            $validated['payload'] = json_decode($validated['payload'], true);
        }

        // Checkbox explizit behandeln
        $validated['is_active'] = $request->has('is_active') && $request->get('is_active') == '1';
        $validated['email_alerts_enabled'] = $request->has('email_alerts_enabled') && $request->get('email_alerts_enabled') == '1';

        // Wenn E-Mail-Alerts deaktiviert werden, Zeitstempel setzen
        if (!$validated['email_alerts_enabled'] && $apiMonitor->email_alerts_enabled) {
            $validated['email_alerts_disabled_at'] = now();
            $validated['email_alerts_disabled_by'] = 'Manual';
            $validated['email_alerts_disabled_reason'] = $request->get('disable_reason', 'Manuell deaktiviert');
        }

        // Wenn E-Mail-Alerts aktiviert werden, Felder zurücksetzen
        if ($validated['email_alerts_enabled'] && !$apiMonitor->email_alerts_enabled) {
            $validated['email_alerts_disabled_at'] = null;
            $validated['email_alerts_disabled_by'] = null;
            $validated['email_alerts_disabled_reason'] = null;
        }

        $apiMonitor->update($validated);

        return redirect()->route('api-monitor.index')
            ->with('success', 'API Monitor erfolgreich aktualisiert');
    }

    public function destroy(ApiMonitor $apiMonitor)
    {
        $apiMonitor->delete();

        return redirect()->route('api-monitor.index')
            ->with('success', 'API Monitor erfolgreich gelöscht');
    }

    public function test(ApiMonitor $apiMonitor)
    {
        try {
            \Log::info("Testing monitor: " . $apiMonitor->name);

            $result = $this->apiMonitorService->executeMonitor($apiMonitor);

            \Log::info("Test result", [
                'success' => $result->success,
                'response_time' => $result->response_time_ms,
                'status_code' => $result->status_code,
            ]);

            return response()->json([
                'success' => $result->success,
                'response_time_ms' => $result->response_time_ms,
                'status_code' => $result->status_code,
                'error_message' => $result->error_message,
            ]);

        } catch (\Exception $e) {
            \Log::error("Test method error: " . $e->getMessage(), [
                'monitor_id' => $apiMonitor->id,
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'response_time_ms' => 0,
                'status_code' => null,
                'error_message' => 'Server error: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function toggleEmailAlerts(Request $request, ApiMonitor $apiMonitor)
    {
        $action = $request->get('action'); // 'enable' oder 'disable'
        $reason = $request->get('reason', '');

        if ($action === 'disable') {
            $apiMonitor->disableEmailAlerts($reason, 'Manual');
            $message = 'E-Mail-Benachrichtigungen deaktiviert';
        } else {
            $apiMonitor->enableEmailAlerts();
            $message = 'E-Mail-Benachrichtigungen aktiviert';
        }

        return response()->json([
            'success' => true,
            'message' => $message,
            'email_alerts_enabled' => $apiMonitor->email_alerts_enabled,
            'email_alerts_status' => $apiMonitor->email_alerts_status
        ]);
    }

    public function export(ApiMonitor $apiMonitor, Request $request)
    {
        $query = $apiMonitor->results();

        // Gleiche Filter wie in show() anwenden
        $timeFilter = $request->get('time_filter', 'all');
        switch ($timeFilter) {
            case 'today':
                $query->whereDate('executed_at', today());
                break;
            case 'week':
                $query->where('executed_at', '>=', now()->startOfWeek());
                break;
            case 'month':
                $query->where('executed_at', '>=', now()->startOfMonth());
                break;
            case 'year':
                $query->where('executed_at', '>=', now()->startOfYear());
                break;
        }

        if ($request->get('status_filter') === 'errors') {
            $query->where('success', false);
        }

        if ($request->filled('http_code_filter')) {
            $query->where('status_code', $request->get('http_code_filter'));
        }

        // Sortierung
        $sortBy = $request->get('sort', 'executed_at');
        $sortDirection = $request->get('direction', 'desc');

        $allowedSorts = ['executed_at', 'response_time_ms', 'status_code', 'success'];
        if (in_array($sortBy, $allowedSorts)) {
            $query->orderBy($sortBy, $sortDirection);
        } else {
            $query->orderBy('executed_at', 'desc');
        }

        $results = $query->get();

        // Excel erstellen
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header
        $sheet->setCellValue('A1', 'Monitor Name');
        $sheet->setCellValue('B1', 'URL');
        $sheet->setCellValue('C1', 'HTTP Methode');
        $sheet->setCellValue('D1', 'Zeitpunkt');
        $sheet->setCellValue('E1', 'Status');
        $sheet->setCellValue('F1', 'Antwortzeit (ms)');
        $sheet->setCellValue('G1', 'HTTP Code');
        $sheet->setCellValue('H1', 'Fehler');

        // Header-Style
        $headerStyle = [
            'font' => ['bold' => true],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'E5E7EB']
            ]
        ];
        $sheet->getStyle('A1:H1')->applyFromArray($headerStyle);

        // Daten
        $row = 2;
        foreach ($results as $result) {
            $sheet->setCellValue('A' . $row, $apiMonitor->name);
            $sheet->setCellValue('B' . $row, $apiMonitor->url);
            $sheet->setCellValue('C' . $row, $apiMonitor->method);
            $sheet->setCellValue('D' . $row, $result->executed_at->format('d.m.Y H:i:s'));
            $sheet->setCellValue('E' . $row, $result->success ? 'Erfolgreich' : 'Fehler');
            $sheet->setCellValue('F' . $row, $result->response_time_ms);
            $sheet->setCellValue('G' . $row, $result->status_code ?? '');
            $sheet->setCellValue('H' . $row, $result->error_message ?? '');
            $row++;
        }

        // Auto-size columns
        foreach (range('A', 'H') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        // Download
        $filename = 'api-monitor-' . $apiMonitor->name . '-' . now()->format('Y-m-d-H-i-s') . '.xlsx';

        $writer = new Xlsx($spreadsheet);

        return response()->streamDownload(function() use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }
}
