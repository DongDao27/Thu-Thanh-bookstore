<?php

namespace App\Services;

use App\Exceptions\BusinessException;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;

class RevenueService
{
    public function report(string $from, string $to, string $granularity): array
    {
        if (config('store.revenue_policy') !== 'approved_v1') {
            throw new BusinessException('REVENUE_POLICY_NOT_APPROVED', 'The proposed revenue policy requires owner approval.', 503);
        }
        $start = CarbonImmutable::parse($from, 'Asia/Ho_Chi_Minh')->utc();
        $end = CarbonImmutable::parse($to, 'Asia/Ho_Chi_Minh')->utc();
        $daily = [];
        for ($day = CarbonImmutable::parse($from); $day->lt(CarbonImmutable::parse($to)); $day = $day->addDay()) {
            $daily[$day->toDateString()] = null;
        }
        $zero = ['accepted_sales' => 0, 'merchandise_refunds' => 0, 'net_sales' => 0, 'shipping_collected' => 0, 'shipping_refunds' => 0, 'unallocated_receipts' => 0, 'unallocated_refunds' => 0];
        $sales = DB::table('payment_receipts as r')->join('orders as o', 'o.id', '=', 'r.order_id')->where('r.allocation', 'accepted')->where('o.payment_status', 'paid')->where('o.paid_at', '>=', $start)->where('o.paid_at', '<', $end)
            ->selectRaw('DATE(o.paid_at + INTERVAL \'7 hours\') AS day, SUM(o.items_subtotal - o.discount_total) AS accepted_sales, SUM(o.shipping_fee) AS shipping_collected')->groupBy('day')->get();
        foreach ($sales as $row) {
            $daily[$row->day] = [...$zero, 'accepted_sales' => (int) $row->accepted_sales, 'shipping_collected' => (int) $row->shipping_collected];
        }
        $refunds = DB::table('refunds as f')->join('payment_receipts as r', 'r.id', '=', 'f.receipt_id')->where('f.refunded_at', '>=', $start)->where('f.refunded_at', '<', $end)
            ->selectRaw("DATE(f.refunded_at + INTERVAL '7 hours') AS day, SUM(CASE WHEN r.allocation = 'accepted' THEN f.merchandise_amount ELSE 0 END) AS merchandise_refunds, SUM(CASE WHEN r.allocation = 'accepted' THEN f.shipping_amount ELSE 0 END) AS shipping_refunds, SUM(CASE WHEN r.allocation != 'accepted' THEN f.amount ELSE 0 END) AS unallocated_refunds")->groupBy('day')->get();
        foreach ($refunds as $row) {
            $daily[$row->day] ??= $zero;
            foreach (['merchandise_refunds', 'shipping_refunds', 'unallocated_refunds'] as $field) {
                $daily[$row->day][$field] += (int) $row->$field;
            }
        }
        $unallocated = DB::table('payment_receipts')->where('allocation', '!=', 'accepted')->where('verified_at', '>=', $start)->where('verified_at', '<', $end)
            ->selectRaw('DATE(verified_at + INTERVAL \'7 hours\') AS day, SUM(received_amount) AS amount')->groupBy('day')->get();
        foreach ($unallocated as $row) {
            $daily[$row->day] ??= $zero;
            $daily[$row->day]['unallocated_receipts'] += (int) $row->amount;
        }
        $series = [];
        $totals = $zero;
        foreach ($daily as $day => $row) {
            $row ??= $zero;
            $date = CarbonImmutable::parse($day, 'Asia/Ho_Chi_Minh');
            $period = match ($granularity) {
                'week' => $date->startOfWeek()->toDateString(), 'month' => $date->startOfMonth()->toDateString(), default => $day
            };
            $series[$period] ??= $zero;
            $row['net_sales'] = $row['accepted_sales'] - $row['merchandise_refunds'];
            foreach ($zero as $field => $value) {
                $series[$period][$field] += $row[$field];
                $totals[$field] += $row[$field];
            }
        }
        ksort($series);
        $rows = [];
        foreach ($series as $period => $values) {
            $rows[] = ['period' => $period, ...$values];
        }

        return ['from' => $from, 'to' => $to, 'timezone' => 'Asia/Ho_Chi_Minh', 'totals' => $totals, 'series' => $rows];
    }
}
