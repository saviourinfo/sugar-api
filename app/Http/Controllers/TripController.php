<?php
namespace App\Http\Controllers;

use App\Models\Trip;
use Illuminate\Http\Request;

class TripController extends Controller
{
    public function index(Request $request)
    {
        $query = Trip::orderBy('trip_date', 'desc');
        if ($request->from) $query->where('trip_date', '>=', $request->from);
        if ($request->to)   $query->where('trip_date', '<=', $request->to);
        return response()->json(['success' => true, 'data' => $query->get()]);
    }

    public function store(Request $request)
    {
        $data           = $this->calcValues($request->all());
        $supplyRows     = $this->calcSupply($request->supply ?? [], $data['total_cost']);
        $data['supply'] = $supplyRows;

        // Derive total_sell and net_profit from supply rows
        $data['total_sell'] = array_sum(array_column($supplyRows, 'total_amount'));
        $data['net_profit'] = $data['total_sell'] - $data['total_cost'];

        $trip = Trip::create($data);
        return response()->json(['success' => true, 'data' => $trip], 201);
    }

    public function show($id)
    {
        $trip = Trip::findOrFail($id);
        return response()->json(['success' => true, 'data' => $trip]);
    }

    public function update(Request $request, $id)
    {
        $trip           = Trip::findOrFail($id);
        $data           = $this->calcValues($request->all());
        $supplyRows     = $this->calcSupply($request->supply ?? [], $data['total_cost']);
        $data['supply'] = $supplyRows;

        // Derive total_sell and net_profit from supply rows
        $data['total_sell'] = array_sum(array_column($supplyRows, 'total_amount'));
        $data['net_profit'] = $data['total_sell'] - $data['total_cost'];

        $trip->update($data);
        return response()->json(['success' => true, 'data' => $trip->fresh()]);
    }

    public function destroy($id)
    {
        Trip::findOrFail($id)->delete();
        return response()->json(['success' => true]);
    }

    public function dashStats()
    {
        $trips   = Trip::all();
        $summary = [
            'total_trips'   => $trips->count(),
            'total_qty'     => $trips->sum('purchase_qty_qtl'),
            'total_revenue' => $trips->sum('total_sell'),
            'total_cost'    => $trips->sum('total_cost'),
            'total_profit'  => $trips->sum('net_profit'),
            'avg_profit'    => $trips->avg('net_profit'),
        ];
        return response()->json(['success' => true, 'data' => ['summary' => $summary]]);
    }

    public function nextSr()
    {
        $last = Trip::orderBy('sr_no', 'desc')->first();
        $next = $last ? str_pad((int) $last->sr_no + 1, 2, '0', STR_PAD_LEFT) : '01';
        return response()->json(['success' => true, 'next_sr' => $next]);
    }

    private function calcValues(array $d): array
    {
        $qty     = (float) ($d['purchase_qty_qtl']  ?? 0);
        $rate    = (float) ($d['purchase_rate_qtl'] ?? 0);
        $gst_pct = (float) ($d['gst_percent']       ?? 5);

        $purchase_amount = $qty * $rate;
        $gst_amount      = $purchase_amount * ($gst_pct / 100);
        $net_purchase    = $purchase_amount + $gst_amount;

        $fare   = (float) ($d['fare_total']   ?? 0);
        $labour = (float) ($d['labour_total'] ?? 0);
        $tempo  = (float) ($d['tempo_total']  ?? 0);

        $total_cost         = $net_purchase + $fare + $labour + $tempo;
        $avg_purchase_price = $qty > 0 ? $total_cost / $qty : 0;

        return array_merge($d, compact(
            'purchase_amount', 'gst_amount', 'net_purchase',
            'total_cost', 'avg_purchase_price'
        ));
    }

    private function calcSupply(array $supply, float $total_cost): array
    {
        $rows = [];
        foreach ($supply as $row) {
            $bags         = (int)   ($row['qty_bags']   ?? 0);
            $weight       = $bags / 2;
            $rate         = (float) ($row['rate_sell']  ?? 0);
            $amt          = $weight * $rate;
            $rows[]       = array_merge($row, [
                'weight_qtl'   => $weight,
                'total_amount' => $amt,
            ]);
        }
        return $rows;
    }
}
