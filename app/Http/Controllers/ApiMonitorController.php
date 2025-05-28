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

    public function index()
    {
        $monitors = ApiMonitor::with('latestResult')->get();
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
        ]);

        if ($validated['headers']) {
            $validated['headers'] = json_decode($validated['headers'], true);
        }
        if ($validated['payload']) {
            $validated['payload'] = json_decode($validated['payload'], true);
        }

        // Checkbox explizit behandeln
        $validated['is_active'] = $request->has('is_active') && $request->get('is_active') == '1';

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
        $result = $this->apiMonitorService->executeMonitor($apiMonitor);

        return response()->json([
            'success' => $result->success,
            'response_time_ms' => $result->response_time_ms,
            'status_code' => $result->status_code,
            'error_message' => $result->error_message,
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
