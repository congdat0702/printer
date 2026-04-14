<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\PrintHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class PrintHistoryController extends Controller
{
    // Method để lấy tất cả lịch sử in, phân trang ngay trong controller
    public function index(Request $request)
    {
        $perPage = 30; // Số lượng item trên mỗi trang

        $printHistories = PrintHistory::orderBy('printed_at', 'desc')
            ->paginate($perPage); // Dùng phương thức paginate

        // Lấy danh sách SĐT trong trang hiện tại để đếm tổng số lần in cực kỳ tối ưu (1 query)
        $phones = $printHistories->pluck('recipient_phone')->unique();
        $counts = PrintHistory::whereIn('recipient_phone', $phones)
            ->selectRaw('recipient_phone, COUNT(*) as count')
            ->groupBy('recipient_phone')
            ->pluck('count', 'recipient_phone');

        // Chuyển dữ liệu thành mảng để gửi về Inertia
        $printHistories->getCollection()->transform(function ($history) use ($counts) {
            return [
                'id' => $history->id,
                'name' => $history->recipient_name,
                'phone' => $history->recipient_phone,
                'address' => $history->recipient_address,
                'printer' => $history->printed_by,
                'time' => $history->printed_at ? \Carbon\Carbon::parse($history->printed_at)->toDateTimeString() : null,
                'sender_name' => $history->sender_name,
                'sender_phone' => $history->sender_phone,
                'sender_address' => $history->sender_address,
                'shipping_unit' => $history->shipping_unit,
                'cod' => $history->cod,
                'product_name' => $history->product_name,
                'payer' => $history->payer,
                'quantity' => $history->quantity,
                'total_prints' => $counts[$history->recipient_phone] ?? 1,
            ];
        });

        return Inertia::render('PrintHistory', [
            'printHistories' => $printHistories
        ]);
    }

    // Method để lưu một lịch sử in mới
    public function store(Request $request)
    {
        $request->validate([
            'recipient_name' => 'required|string|max:255',
            'recipient_phone' => 'required|string|max:255',
            'recipient_address' => 'nullable|string|max:255',
            'sender_name' => 'nullable|string|max:255',
            'sender_phone' => 'nullable|string|max:255',
            'sender_address' => 'nullable|string|max:255',
            'shipping_unit' => 'nullable|string|max:255',
            'cod' => 'nullable|string|max:255',
            'product_name' => 'nullable|string|max:255',
            'payer' => 'nullable|string|max:255',
            'quantity' => 'nullable|integer',
        ]);

        $user = Auth::user();

        PrintHistory::create([
            'recipient_name' => $request->recipient_name,
            'recipient_phone' => $request->recipient_phone,
            'recipient_address' => $request->recipient_address,
            'printed_by' => $user ? $user->name : 'Unknown User',
            'printed_at' => now(),
            'sender_name' => $request->sender_name,
            'sender_phone' => $request->sender_phone,
            'sender_address' => $request->sender_address,
            'shipping_unit' => $request->shipping_unit,
            'cod' => $request->cod,
            'product_name' => $request->product_name,
            'payer' => $request->payer,
            'quantity' => $request->quantity ?? 1,
        ]);

        return response()->json(['success' => true]);
    }

    // Xóa một lịch sử in theo id
    public function destroy($id)
    {
        $history = PrintHistory::findOrFail($id);
        $history->delete();
        return response()->json(['success' => true]);
    }
}
