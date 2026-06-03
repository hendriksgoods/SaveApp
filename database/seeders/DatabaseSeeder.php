<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Campaign;
use App\Models\Donation;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // USERS

        $admin = User::updateOrCreate(
            ['email' => 'admin@savethem.id'],
            [
                'name'         => 'Admin SaveThem',
                'phone'        => '081200000000',
                'password'     => Hash::make('admin1234'),
                'role'         => 'admin',
            ]
        );

        $pg = User::updateOrCreate(
            ['email' => 'penggalang@email.com'],
            [
                'name'         => 'Budi Santoso',
                'phone'        => '081211111111',
                'password'     => Hash::make('budi1234'),
                'role'         => 'penggalang',
                'is_verified'  => true,
                'verified_at'  => now(),
            ]
        );

        User::updateOrCreate(
            ['email' => 'donatur@email.com'],
            [
                'name'         => 'Siti Rahayu',
                'phone'        => '081222222222',
                'password'     => Hash::make('siti1234'),
                'role'         => 'donatur',
            ]
        );

        // CAMPAIGN 1

        $c1Slug = Str::slug('Bantu Operasi Jantung Bayi Azka');

        $c1 = Campaign::updateOrCreate(
            ['slug' => $c1Slug],
            [
                'user_id'       => $pg->id,
                'category'      => 'Kesehatan',
                'full_name_ktp' => 'Budi Santoso',
                'ktp_number'    => '3201234567890001',
                'phone'         => '081211111111',
                'occupation'    => 'Guru Honorer',
                'title'         => 'Bantu Operasi Jantung Bayi Azka',
                'story'         => 'Bayi Azka berusia 6 bulan didiagnosis memiliki kelainan jantung bawaan yang memerlukan operasi segera.',
                'description'   => 'Azka adalah bayi mungil berusia 6 bulan yang lahir dengan kelainan jantung bawaan...',
                'fund_purpose'  => "Biaya operasi jantung\nBiaya ICU pasca operasi\nObat-obatan dan pemeriksaan lanjutan",
                'location'      => 'Jakarta Selatan, DKI Jakarta',
                'target_amount' => 150000000,
                'raised_amount' => 87500000,
                'duration_days' => 60,
                'deadline'      => now()->addDays(18),
                'fund_detail'   => "1. Biaya Operasi Jantung...\n2. Perawatan ICU...\n3. Obat & Pemeriksaan...",
                'reason'        => 'Kami sangat membutuhkan bantuan...',
                'status'        => 'active',
                'is_urgent'     => true,
            ]
        );

        // CAMPAIGN 2

        $c2Slug = Str::slug('Beasiswa Anak Yatim Piatu Panti Harapan');

        $c2 = Campaign::updateOrCreate(
            ['slug' => $c2Slug],
            [
                'user_id'       => $pg->id,
                'category'      => 'Pendidikan',
                'full_name_ktp' => 'Budi Santoso',
                'ktp_number'    => '3201234567890001',
                'phone'         => '081211111111',
                'occupation'    => 'Guru Honorer',
                'title'         => 'Beasiswa Anak Yatim Piatu Panti Harapan',
                'story'         => 'Panti Harapan menampung 50 anak yatim piatu...',
                'description'   => 'Panti Harapan telah berdiri selama 10 tahun...',
                'fund_purpose'  => "Biaya SPP\nSeragam\nBuku\nTransportasi",
                'location'      => 'Bandung, Jawa Barat',
                'target_amount' => 100000000,
                'raised_amount' => 45000000,
                'duration_days' => 90,
                'deadline'      => now()->addDays(45),
                'fund_detail'   => "1. SPP\n2. Seragam\n3. Buku\n4. Transportasi",
                'reason'        => 'Kami sangat membutuhkan bantuan...',
                'status'        => 'active',
                'is_urgent'     => false,
            ]
        );

        // DONATIONS

        $donationAmounts1 = [50000, 100000, 200000, 75000, 150000, 500000, 250000];

        foreach ($donationAmounts1 as $amount) {
            Donation::create([
                'campaign_id'    => $c1->id,
                'donor_name'     => 'Seseorang Telah',
                'donor_email'    => 'donor@test.com',
                'amount'         => $amount,
                'message'        => 'Semangat! Semoga Azka segera sembuh 🙏',
                'payment_status' => 'paid',
                'transaction_id' => 'TXN-' . strtoupper(uniqid()),
                'is_anonymous'   => false,
            ]);
        }

        $donationAmounts2 = [25000, 50000, 100000];

        foreach ($donationAmounts2 as $amount) {
            Donation::create([
                'campaign_id'    => $c2->id,
                'donor_name'     => 'Seseorang Telah',
                'donor_email'    => 'anonim@test.com',
                'amount'         => $amount,
                'message'        => 'Semoga bermanfaat untuk adik-adik 📚',
                'payment_status' => 'paid',
                'transaction_id' => 'TXN-' . strtoupper(uniqid()),
                'is_anonymous'   => true,
            ]);
        }
    }
}