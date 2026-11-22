<?php

namespace App\Http\Controllers;

use App\Models\Shipment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class CustomerProfileController extends Controller
{
    /**
     * Menampilkan halaman profil Customer.
     */
    public function index()
    {
        $user = auth()->user();

        $addresses = $user->customerAddresses()
            ->orderByDesc('is_primary')
            ->latest()
            ->get();

        // Kelengkapan profil: kolom mana saja yang masih kosong.
        $fields = [
            'name' => 'Nama lengkap',
            'email' => 'Email',
            'phone' => 'Nomor telepon',
            'whatsapp_number' => 'Nomor WhatsApp',
            'birth_date' => 'Tanggal lahir',
            'gender' => 'Jenis kelamin',
            'address' => 'Alamat lengkap',
            'city' => 'Kota',
            'province' => 'Provinsi',
            'postal_code' => 'Kode pos',
            'country' => 'Negara',
            'profile_photo_path' => 'Foto profil',
        ];

        $missing = [];

        foreach ($fields as $column => $label) {
            $value = $user->getAttribute($column);

            if ($value === null || $value === '') {
                $missing[] = $label;
            }
        }

        $completion = (int) round((count($fields) - count($missing)) / count($fields) * 100);

        // Ringkasan pengiriman milik Customer ini.
        $shipmentCount = Shipment::where('user_id', $user->id)->count();
        $deliveredCount = Shipment::where('user_id', $user->id)
            ->where('status', 'DELIVERED')
            ->count();

        return view('customer.profil.index', compact(
            'user',
            'addresses',
            'completion',
            'missing',
            'shipmentCount',
            'deliveredCount'
        ));
    }

    /**
     * Memperbarui informasi pribadi dan kontak Customer.
     */
    public function update(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'email' => [
                'required',
                'email',
                'max:150',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'phone' => ['nullable', 'string', 'max:30'],
            'whatsapp_number' => ['nullable', 'string', 'max:30'],
            'birth_date' => ['nullable', 'date', 'before:today'],
            'gender' => ['nullable', Rule::in(['Laki-laki', 'Perempuan', 'Lainnya'])],
            'address' => ['nullable', 'string', 'max:1000'],
            'city' => ['nullable', 'string', 'max:100'],
            'province' => ['nullable', 'string', 'max:100'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'country' => ['nullable', 'string', 'max:100'],
            'profile_photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'remove_photo' => ['sometimes', 'boolean'],
        ]);

        unset($validated['profile_photo'], $validated['remove_photo']);

        if ($request->hasFile('profile_photo')) {
            // Foto baru menggantikan foto lama.
            if ($user->profile_photo_path) {
                Storage::disk('public')->delete($user->profile_photo_path);
            }

            $validated['profile_photo_path'] = $request
                ->file('profile_photo')
                ->store('customer-profiles', 'public');
        } elseif ($request->boolean('remove_photo') && $user->profile_photo_path) {
            // Hapus foto tanpa menggantinya.
            Storage::disk('public')->delete($user->profile_photo_path);

            $validated['profile_photo_path'] = null;
        }

        $validated['profile_updated_at'] = now();

        $user->update($validated);

        return redirect()
            ->route('customer.profil.index')
            ->with('success', 'Informasi profil berhasil diperbarui.');
    }

    /**
     * Memperbarui password Customer.
     */
    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'current_password.current_password' => 'Password saat ini tidak sesuai.',
            'password.min' => 'Password baru minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password baru tidak cocok.',
        ]);

        auth()->user()->update([
            'password' => $validated['password'],
            'password_changed_at' => now(),
        ]);

        return redirect()
            ->route('customer.profil.index')
            ->with('success', 'Password berhasil diperbarui.');
    }

    /**
     * Memperbarui preferensi notifikasi Customer.
     */
    public function updatePreferences(Request $request)
    {
        $request->validate([
            'notify_shipment_updates' => ['sometimes', 'boolean'],
            'notify_tracking_updates' => ['sometimes', 'boolean'],
            'notify_delay_alerts' => ['sometimes', 'boolean'],
            'notify_exception_alerts' => ['sometimes', 'boolean'],
            'notify_email' => ['sometimes', 'boolean'],
        ]);

        auth()->user()->update([
            'notify_shipment_updates' => $request->boolean('notify_shipment_updates'),
            'notify_tracking_updates' => $request->boolean('notify_tracking_updates'),
            'notify_delay_alerts' => $request->boolean('notify_delay_alerts'),
            'notify_exception_alerts' => $request->boolean('notify_exception_alerts'),
            'notify_email' => $request->boolean('notify_email'),
        ]);

        return redirect()
            ->route('customer.profil.index')
            ->with('success', 'Preferensi notifikasi berhasil disimpan.');
    }

    /**
     * Menambahkan alamat alternatif Customer.
     */
    public function storeAddress(Request $request)
    {
        $validated = $request->validate([
            'label' => ['required', 'string', 'max:100'],
            'recipient_name' => ['required', 'string', 'max:150'],
            'phone' => ['required', 'string', 'max:30'],
            'address' => ['required', 'string', 'max:1000'],
            'city' => ['required', 'string', 'max:100'],
            'province' => ['required', 'string', 'max:100'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'country' => ['required', 'string', 'max:100'],
        ]);

        $user = auth()->user();

        DB::transaction(function () use ($user, $validated) {
            // Alamat pertama otomatis menjadi alamat utama.
            $validated['is_primary'] = !$user->customerAddresses()->exists();

            $user->customerAddresses()->create($validated);
        });

        return redirect()
            ->route('customer.profil.index')
            ->with('success', 'Alamat berhasil ditambahkan.');
    }

    /**
     * Memperbarui alamat milik Customer.
     */
    public function updateAddress(Request $request, $id)
    {
        $validated = $request->validate([
            'label' => ['required', 'string', 'max:100'],
            'recipient_name' => ['required', 'string', 'max:150'],
            'phone' => ['required', 'string', 'max:30'],
            'address' => ['required', 'string', 'max:1000'],
            'city' => ['required', 'string', 'max:100'],
            'province' => ['required', 'string', 'max:100'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'country' => ['required', 'string', 'max:100'],
        ]);

        auth()->user()
            ->customerAddresses()
            ->findOrFail($id)
            ->update($validated);

        return redirect()
            ->route('customer.profil.index')
            ->with('success', 'Alamat berhasil diperbarui.');
    }

    /**
     * Menghapus alamat milik Customer.
     */
    public function destroyAddress($id)
    {
        $user = auth()->user();

        $address = $user->customerAddresses()->findOrFail($id);

        DB::transaction(function () use ($user, $address) {
            $wasPrimary = $address->is_primary;

            $address->delete();

            // Jika yang dihapus alamat utama, alamat tertua menjadi utama.
            if ($wasPrimary) {
                $user->customerAddresses()
                    ->oldest()
                    ->first()
                    ?->update(['is_primary' => true]);
            }
        });

        return redirect()
            ->route('customer.profil.index')
            ->with('success', 'Alamat berhasil dihapus.');
    }

    /**
     * Menjadikan alamat tertentu sebagai alamat utama.
     */
    public function setPrimaryAddress($id)
    {
        $user = auth()->user();

        $address = $user->customerAddresses()->findOrFail($id);

        DB::transaction(function () use ($user, $address) {
            $user->customerAddresses()->update(['is_primary' => false]);

            $address->update(['is_primary' => true]);
        });

        return redirect()
            ->route('customer.profil.index')
            ->with('success', 'Alamat utama berhasil diubah.');
    }
}
