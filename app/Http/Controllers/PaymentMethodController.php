<?php

namespace App\Http\Controllers;

use App\Models\PaymentMethod;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

/**
 * Dormitory Profile > Payment Methods: the admin decides how tenants can
 * pay (e-wallets, bank accounts, cash), with account details and an
 * optional QR code. Tenant billing and move-in payment screens read from
 * here, so edits show up for tenants immediately.
 */
class PaymentMethodController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $data = $this->validated($request);

        if ($data['type'] === 'cash') {
            return response()->json(['message' => 'Cash is already set up. Edit the existing Cash method instead.'], 422);
        }

        $method = new PaymentMethod($data);
        $method->sort_order = (int) PaymentMethod::max('sort_order') + 1;

        if ($request->hasFile('qr')) {
            $method->qr_path = $request->file('qr')->store('payment-qr', 'public');
        }

        $method->save();

        return response()->json(['message' => 'Payment method added.', 'method' => $method->toClientArray()], 201);
    }

    public function update(Request $request, PaymentMethod $paymentMethod): JsonResponse
    {
        $data = $this->validated($request);

        if ($paymentMethod->type === 'cash' && $data['type'] !== 'cash') {
            return response()->json(['message' => 'Cash is always available to tenants, so its type can\'t be changed.'], 422);
        }
        if ($paymentMethod->type !== 'cash' && $data['type'] === 'cash') {
            return response()->json(['message' => 'Cash is already set up. Edit the existing Cash method instead.'], 422);
        }
        $paymentMethod->fill($data);

        if ($request->hasFile('qr') || $request->boolean('remove_qr') || $data['type'] === 'cash') {
            $this->deleteQr($paymentMethod);
        }

        if ($request->hasFile('qr') && $data['type'] !== 'cash') {
            $paymentMethod->qr_path = $request->file('qr')->store('payment-qr', 'public');
        }

        $paymentMethod->save();

        return response()->json(['message' => 'Payment method updated.', 'method' => $paymentMethod->toClientArray()]);
    }

    public function destroy(PaymentMethod $paymentMethod): JsonResponse
    {
        if ($paymentMethod->type === 'cash') {
            return response()->json(['message' => 'Cash is always available to tenants and can\'t be deleted.'], 422);
        }

        $this->deleteQr($paymentMethod);
        $paymentMethod->delete();

        return response()->json(['message' => 'Payment method deleted.']);
    }

    private function validated(Request $request): array
    {
        $online = $request->input('type') !== 'cash';

        $data = $request->validate([
            'type' => ['required', Rule::in(PaymentMethod::TYPES)],
            'name' => ['required', 'string', 'max:60'],
            'account_name' => [$online ? 'required' : 'nullable', 'string', 'max:120'],
            'account_number' => [$online ? 'required' : 'nullable', 'string', 'max:60'],
            'instructions' => ['nullable', 'string', 'max:500'],
            'qr' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ], [
            'account_name.required' => 'Enter the name on the account tenants will send money to.',
            'account_number.required' => 'Enter the mobile or account number tenants will send money to.',
        ]);

        unset($data['qr']);

        if (! $online) {
            $data['account_name'] = null;
            $data['account_number'] = null;
        }

        return $data;
    }

    private function deleteQr(PaymentMethod $method): void
    {
        if ($method->qr_path) {
            Storage::disk('public')->delete($method->qr_path);
            $method->qr_path = null;
        }
    }
}
