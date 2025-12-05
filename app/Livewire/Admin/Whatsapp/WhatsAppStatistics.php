<?php

namespace App\Livewire\Admin\Whatsapp;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\WhatsAppMessage;
use App\Models\WhatsAppScheduledMessage;
use App\Models\WhatsAppTemplate;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class WhatsAppStatistics extends Component
{
    use WithPagination;

    // Filters
    public $dateRange = '7days'; // 7days, 30days, 90days, 1year, custom
    public $dateFrom = '';
    public $dateTo = '';
    public $userId = '';
    public $templateId = '';
    public $status = '';
    
    // UI State
    public $loading = false;
    public $error = '';
    public $success = '';
    public $activeTab = 'overview';
    public $chartType = 'line'; // line, bar, pie
    
    // Data
    public $users = [];
    public $templates = [];
    public $perPage = 10;

    public function mount()
    {
        $this->loadFiltersData();
        $this->setDefaultDates();
    }

    public function loadFiltersData()
    {
        try {
            $this->users = \App\Models\User::select('id', 'name', 'email')
                ->orderBy('name')
                ->get();
                
            $this->templates = WhatsAppTemplate::select('id', 'name')
                ->where('is_active', true)
                ->orderBy('name')
                ->get();
        } catch (\Exception $e) {
            Log::error('Error loading filter data: ' . $e->getMessage());
            $this->users = collect();
            $this->templates = collect();
        }
    }

    public function setDefaultDates()
    {
        switch ($this->dateRange) {
            case '7days':
                $this->dateFrom = now()->subDays(7)->format('Y-m-d');
                $this->dateTo = now()->format('Y-m-d');
                break;
            case '30days':
                $this->dateFrom = now()->subDays(30)->format('Y-m-d');
                $this->dateTo = now()->format('Y-m-d');
                break;
            case '90days':
                $this->dateFrom = now()->subDays(90)->format('Y-m-d');
                $this->dateTo = now()->format('Y-m-d');
                break;
            case '1year':
                $this->dateFrom = now()->subYear()->format('Y-m-d');
                $this->dateTo = now()->format('Y-m-d');
                break;
            case 'custom':
                if (empty($this->dateFrom) || empty($this->dateTo)) {
                    $this->dateFrom = now()->subDays(30)->format('Y-m-d');
                    $this->dateTo = now()->format('Y-m-d');
                }
                break;
        }
    }

    public function updatedDateRange($value)
    {
        if ($value !== 'custom') {
            $this->setDefaultDates();
        }
    }

    public function getStatisticsProperty()
    {
        try {
            $dateFrom = Carbon::parse($this->dateFrom)->startOfDay();
            $dateTo = Carbon::parse($this->dateTo)->endOfDay();

            // Basic statistics
            $totalMessages = WhatsAppMessage::whereBetween('created_at', [$dateFrom, $dateTo])->count();
            $sentMessages = WhatsAppMessage::whereBetween('created_at', [$dateFrom, $dateTo])
                ->where('status', 'sent')
                ->count();
            $deliveredMessages = WhatsAppMessage::whereBetween('created_at', [$dateFrom, $dateTo])
                ->where('status', 'delivered')
                ->count();
            $readMessages = WhatsAppMessage::whereBetween('created_at', [$dateFrom, $dateTo])
                ->where('status', 'read')
                ->count();
            $failedMessages = WhatsAppMessage::whereBetween('created_at', [$dateFrom, $dateTo])
                ->where('status', 'failed')
                ->count();

            // Delivery rates
            $deliveryRate = $totalMessages > 0 ? round(($deliveredMessages / $totalMessages) * 100, 2) : 0;
            $readRate = $deliveredMessages > 0 ? round(($readMessages / $deliveredMessages) * 100, 2) : 0;
            $failureRate = $totalMessages > 0 ? round(($failedMessages / $totalMessages) * 100, 2) : 0;

            // Template usage
            $templateUsage = WhatsAppMessage::whereBetween('created_at', [$dateFrom, $dateTo])
                ->whereNotNull('template_id')
                ->count();

            $manualMessages = $totalMessages - $templateUsage;

            // Scheduled messages
            $scheduledMessages = WhatsAppScheduledMessage::whereBetween('created_at', [$dateFrom, $dateTo])->count();
            $scheduledSent = WhatsAppScheduledMessage::whereBetween('created_at', [$dateFrom, $dateTo])
                ->where('status', 'sent')
                ->count();

            // Daily breakdown
            $dailyStats = WhatsAppMessage::select(
                    DB::raw('DATE(created_at) as date'),
                    DB::raw('COUNT(*) as total'),
                    DB::raw('SUM(CASE WHEN status = "sent" THEN 1 ELSE 0 END) as sent'),
                    DB::raw('SUM(CASE WHEN status = "delivered" THEN 1 ELSE 0 END) as delivered'),
                    DB::raw('SUM(CASE WHEN status = "read" THEN 1 ELSE 0 END) as read'),
                    DB::raw('SUM(CASE WHEN status = "failed" THEN 1 ELSE 0 END) as failed')
                )
                ->whereBetween('created_at', [$dateFrom, $dateTo])
                ->groupBy('date')
                ->orderBy('date')
                ->get();

            // Top users
            $topUsers = WhatsAppMessage::select(
                    'created_by',
                    DB::raw('COUNT(*) as total_messages'),
                    DB::raw('SUM(CASE WHEN status = "delivered" THEN 1 ELSE 0 END) as delivered_messages')
                )
                ->whereBetween('created_at', [$dateFrom, $dateTo])
                ->groupBy('created_by')
                ->orderByDesc('total_messages')
                ->limit(10)
                ->get();

            // Top templates
            $topTemplates = WhatsAppMessage::select(
                    'template_id',
                    DB::raw('COUNT(*) as usage_count'),
                    DB::raw('AVG(CASE WHEN status = "delivered" THEN 1 ELSE 0 END) * 100 as delivery_rate')
                )
                ->whereBetween('created_at', [$dateFrom, $dateTo])
                ->whereNotNull('template_id')
                ->groupBy('template_id')
                ->orderByDesc('usage_count')
                ->limit(10)
                ->get();

            return [
                'total' => $totalMessages,
                'sent' => $sentMessages,
                'delivered' => $deliveredMessages,
                'read' => $readMessages,
                'failed' => $failedMessages,
                'delivery_rate' => $deliveryRate,
                'read_rate' => $readRate,
                'failure_rate' => $failureRate,
                'template_usage' => $templateUsage,
                'manual_messages' => $manualMessages,
                'scheduled' => $scheduledMessages,
                'scheduled_sent' => $scheduledSent,
                'daily_stats' => $dailyStats,
                'top_users' => $topUsers,
                'top_templates' => $topTemplates
            ];

        } catch (\Exception $e) {
            Log::error('Error getting statistics: ' . $e->getMessage());
            return [
                'total' => 0,
                'sent' => 0,
                'delivered' => 0,
                'read' => 0,
                'failed' => 0,
                'delivery_rate' => 0,
                'read_rate' => 0,
                'failure_rate' => 0,
                'template_usage' => 0,
                'manual_messages' => 0,
                'scheduled' => 0,
                'scheduled_sent' => 0,
                'daily_stats' => collect(),
                'top_users' => collect(),
                'top_templates' => collect()
            ];
        }
    }

    public function getFilteredMessagesProperty()
    {
        try {
            $dateFrom = Carbon::parse($this->dateFrom)->startOfDay();
            $dateTo = Carbon::parse($this->dateTo)->endOfDay();

            $query = WhatsAppMessage::with(['user', 'template', 'student'])
                ->whereBetween('created_at', [$dateFrom, $dateTo]);

            if ($this->userId) {
                $query->where('created_by', $this->userId);
            }

            if ($this->templateId) {
                $query->where('template_id', $this->templateId);
            }

            if ($this->status) {
                $query->where('status', $this->status);
            }

            return $query->orderBy('created_at', 'desc')
                ->paginate($this->perPage);

        } catch (\Exception $e) {
            Log::error('Error getting filtered messages: ' . $e->getMessage());
            return collect();
        }
    }

    public function exportData($format = 'csv')
    {
        try {
            $statistics = $this->getStatisticsProperty();
            $filename = 'whatsapp_statistics_' . now()->format('Y-m-d_H-i-s');

            if ($format === 'csv') {
                return $this->exportToCsv($statistics, $filename);
            } elseif ($format === 'json') {
                return $this->exportToJson($statistics, $filename);
            }

        } catch (\Exception $e) {
            $this->error = 'Error al exportar datos: ' . $e->getMessage();
            Log::error('Error exporting data: ' . $e->getMessage());
        }
    }

    private function exportToCsv($data, $filename)
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '.csv"'
        ];

        return response()->stream(function () use ($data) {
            $handle = fopen('php://output', 'w');
            
            // Headers
            fputcsv($handle, ['Métrica', 'Valor']);
            
            // Data
            fputcsv($handle, ['Total de Mensajes', $data['total']]);
            fputcsv($handle, ['Mensajes Enviados', $data['sent']]);
            fputcsv($handle, ['Mensajes Entregados', $data['delivered']]);
            fputcsv($handle, ['Mensajes Leídos', $data['read']]);
            fputcsv($handle, ['Mensajes Fallidos', $data['failed']]);
            fputcsv($handle, ['Tasa de Entrega', $data['delivery_rate'] . '%']);
            fputcsv($handle, ['Tasa de Lectura', $data['read_rate'] . '%']);
            fputcsv($handle, ['Tasa de Fallo', $data['failure_rate'] . '%']);
            fputcsv($handle, ['Uso de Plantillas', $data['template_usage']]);
            fputcsv($handle, ['Mensajes Manuales', $data['manual_messages']]);
            fputcsv($handle, ['Mensajes Programados', $data['scheduled']]);
            
            fclose($handle);
        }, 200, $headers);
    }

    private function exportToJson($data, $filename)
    {
        return response()->json($data)
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '.json"');
    }

    public function resetFilters()
    {
        $this->dateRange = '7days';
        $this->userId = '';
        $this->templateId = '';
        $this->status = '';
        $this->setDefaultDates();
        $this->resetPage();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        return view('livewire.admin.whatsapp.whatsapp-statistics', [
            'statistics' => $this->getStatisticsProperty(),
            'messages' => $this->getFilteredMessagesProperty()
        ]);
    }
}