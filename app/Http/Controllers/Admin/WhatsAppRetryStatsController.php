<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WhatsAppMessage;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WhatsAppRetryStatsController extends Controller
{
    public function index()
    {
        $stats = $this->getRetryStats();
        
        return view('admin.whatsapp.retry-stats', compact('stats'));
    }

    public function getRetryStats()
    {
        $today = Carbon::today();
        $lastWeek = Carbon::now()->subWeek();
        $lastMonth = Carbon::now()->subMonth();

        return [
            // Estadísticas generales
            'total_retryable' => WhatsAppMessage::where('direction', 'outbound')
                ->retryable()
                ->count(),

            'total_max_retries_exceeded' => WhatsAppMessage::where('direction', 'outbound')
                ->maxRetriesExceeded()
                ->count(),

            // Estadísticas por período
            'retryable_today' => WhatsAppMessage::where('direction', 'outbound')
                ->retryable()
                ->whereDate('created_at', $today)
                ->count(),

            'retryable_week' => WhatsAppMessage::where('direction', 'outbound')
                ->retryable()
                ->where('created_at', '>=', $lastWeek)
                ->count(),

            'retryable_month' => WhatsAppMessage::where('direction', 'outbound')
                ->retryable()
                ->where('created_at', '>=', $lastMonth)
                ->count(),

            // Mensajes reenviados exitosamente
            'successful_retries_today' => WhatsAppMessage::where('direction', 'outbound')
                ->where('status', 'sent')
                ->where('retry_count', '>', 0)
                ->whereDate('sent_at', $today)
                ->count(),

            'successful_retries_week' => WhatsAppMessage::where('direction', 'outbound')
                ->where('status', 'sent')
                ->where('retry_count', '>', 0)
                ->where('sent_at', '>=', $lastWeek)
                ->count(),

            // Distribución por estado
            'status_distribution' => WhatsAppMessage::where('direction', 'outbound')
                ->where('retry_count', '>', 0)
                ->select('status', DB::raw('count(*) as count'))
                ->groupBy('status')
                ->pluck('count', 'status')
                ->toArray(),

            // Distribución por número de reintentos
            'retry_count_distribution' => WhatsAppMessage::where('direction', 'outbound')
                ->where('retry_count', '>', 0)
                ->select('retry_count', DB::raw('count(*) as count'))
                ->groupBy('retry_count')
                ->pluck('count', 'retry_count')
                ->toArray(),

            // Últimos mensajes reenviados
            'recent_retries' => WhatsAppMessage::where('direction', 'outbound')
                ->where('retry_count', '>', 0)
                ->with(['template', 'createdBy'])
                ->orderBy('updated_at', 'desc')
                ->limit(10)
                ->get(),

            // Mensajes más reenviados
            'most_retried' => WhatsAppMessage::where('direction', 'outbound')
                ->where('retry_count', '>', 0)
                ->select('recipient_phone', 'recipient_name', DB::raw('count(*) as total_messages'), DB::raw('sum(retry_count) as total_retries'))
                ->groupBy('recipient_phone', 'recipient_name')
                ->orderBy('total_retries', 'desc')
                ->limit(10)
                ->get(),
        ];
    }

    public function getChartData(Request $request)
    {
        $period = $request->get('period', '7d');
        
        switch ($period) {
            case '24h':
                $data = $this->getHourlyStats(24);
                break;
            case '7d':
                $data = $this->getDailyStats(7);
                break;
            case '30d':
                $data = $this->getDailyStats(30);
                break;
            default:
                $data = $this->getDailyStats(7);
        }

        return response()->json($data);
    }

    private function getHourlyStats($hours)
    {
        $data = [];
        
        for ($i = $hours - 1; $i >= 0; $i--) {
            $hour = Carbon::now()->subHours($i);
            $startHour = $hour->copy()->startOfHour();
            $endHour = $hour->copy()->endOfHour();

            $retryable = WhatsAppMessage::where('direction', 'outbound')
                ->retryable()
                ->whereBetween('created_at', [$startHour, $endHour])
                ->count();

            $successful = WhatsAppMessage::where('direction', 'outbound')
                ->where('status', 'sent')
                ->where('retry_count', '>', 0)
                ->whereBetween('sent_at', [$startHour, $endHour])
                ->count();

            $data['labels'][] = $hour->format('H:i');
            $data['retryable'][] = $retryable;
            $data['successful'][] = $successful;
        }

        return $data;
    }

    private function getDailyStats($days)
    {
        $data = [];
        
        for ($i = $days - 1; $i >= 0; $i--) {
            $day = Carbon::now()->subDays($i);
            $startDay = $day->copy()->startOfDay();
            $endDay = $day->copy()->endOfDay();

            $retryable = WhatsAppMessage::where('direction', 'outbound')                ->retryable()
                ->whereBetween('created_at', [$startDay, $endDay])
                ->count();

            $successful = WhatsAppMessage::where('direction', 'outbound')
                ->where('status', 'sent')
                ->where('retry_count', '>', 0)
                ->whereBetween('sent_at', [$startDay, $endDay])
                ->count();

            $data['labels'][] = $day->format('d/m');
            $data['retryable'][] = $retryable;
            $data['successful'][] = $successful;
        }

        return $data;
    }
}