<?php

namespace Database\Seeders;

use App\Models\Outlet;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class OutletSeeder extends Seeder
{
    public function run(): void
    {
        // 1) ROLE
        $ownerRole = Role::firstOrCreate(['name' => 'owner', 'guard_name' => 'web']);

        // 2) OUTLETS (JAWA BARAT SAJA, NYEBAR)
        $outletsData = [
            // BANDUNG + CIMAHI
            ['code' => 'BTG-BDG', 'name' => 'Batagor Kings Bandung', 'address' => 'Jl. Braga, Bandung', 'latitude' => -6.9175, 'longtitude' => 107.6191, 'phone' => '081200000101', 'email' => 'batagor.bdg@outlet.com', 'is_active' => true],
            ['code' => 'MKK-BDG', 'name' => 'Mie Kocok Mang Dadang', 'address' => 'Jl. Cihampelas, Bandung', 'latitude' => -6.8937, 'longtitude' => 107.6047, 'phone' => '081200000102', 'email' => 'miekocok.bdg@outlet.com', 'is_active' => true],
            ['code' => 'SBK-BDG', 'name' => 'Seblak Jeletet Sunda', 'address' => 'Jl. Dago, Bandung', 'latitude' => -6.8839, 'longtitude' => 107.6130, 'phone' => '081200000103', 'email' => 'seblak.bdg@outlet.com', 'is_active' => true],
            ['code' => 'SRB-CMH', 'name' => 'Surabi Oncom Cimahi', 'address' => 'Jl. Gatot Subroto, Cimahi', 'latitude' => -6.8720, 'longtitude' => 107.5420, 'phone' => '081200000104', 'email' => 'surabi.cimahi@outlet.com', 'is_active' => true],

            // BOGOR + DEPOK
            ['code' => 'CTK-BGR', 'name' => 'Cilok Tusuk Bogor', 'address' => 'Jl. Pajajaran, Bogor', 'latitude' => -6.6012, 'longtitude' => 106.7990, 'phone' => '081200000105', 'email' => 'cilok.bgr@outlet.com', 'is_active' => true],
            ['code' => 'CRG-DPK', 'name' => 'Cireng Rujak Depok', 'address' => 'Jl. Margonda, Depok', 'latitude' => -6.3949, 'longtitude' => 106.8226, 'phone' => '081200000106', 'email' => 'cireng.dpk@outlet.com', 'is_active' => true],
            ['code' => 'CMB-DPK', 'name' => 'Combro Misro Depok', 'address' => 'Jl. Juanda, Depok', 'latitude' => -6.3816, 'longtitude' => 106.8335, 'phone' => '081200000107', 'email' => 'combro.dpk@outlet.com', 'is_active' => true],

            // BEKASI + KARAWANG
            ['code' => 'KPT-BKS', 'name' => 'Kupat Tahu Bekasi', 'address' => 'Jl. Ahmad Yani, Bekasi', 'latitude' => -6.2383, 'longtitude' => 106.9756, 'phone' => '081200000108', 'email' => 'kupattahu.bks@outlet.com', 'is_active' => true],
            ['code' => 'STM-KRW', 'name' => 'Sate Maranggi Karawang', 'address' => 'Jl. Tuparev, Karawang', 'latitude' => -6.3054, 'longtitude' => 107.2960, 'phone' => '081200000109', 'email' => 'maranggi.krw@outlet.com', 'is_active' => true],

            // PURWAKARTA + SUBANG
            ['code' => 'STM-PWK', 'name' => 'Sate Maranggi Purwakarta', 'address' => 'Jl. Raya Bungursari, Purwakarta', 'latitude' => -6.5569, 'longtitude' => 107.4436, 'phone' => '081200000110', 'email' => 'maranggi.pwk@outlet.com', 'is_active' => true],
            ['code' => 'BNK-SBG', 'name' => 'Bandrek & Bajigur Subang', 'address' => 'Jl. Otista, Subang', 'latitude' => -6.5710, 'longtitude' => 107.7570, 'phone' => '081200000111', 'email' => 'bandrek.sbg@outlet.com', 'is_active' => true],

            // SUKABUMI + CIANJUR
            ['code' => 'TMM-SKB', 'name' => 'Tahu Toge Gejrot Sukabumi', 'address' => 'Jl. R. Syamsudin, Sukabumi', 'latitude' => -6.9200, 'longtitude' => 106.9270, 'phone' => '081200000112', 'email' => 'tahugejrot.skb@outlet.com', 'is_active' => true],
            ['code' => 'PKY-CJR', 'name' => 'Peuyeum & Colenak Cianjur', 'address' => 'Jl. Siliwangi, Cianjur', 'latitude' => -6.8161, 'longtitude' => 107.1420, 'phone' => '081200000113', 'email' => 'peuyeum.cjr@outlet.com', 'is_active' => true],

            // SUMEDANG + GARUT
            ['code' => 'THS-SMD', 'name' => 'Tahu Sumedang Renyah', 'address' => 'Jl. Mayor Abdurahman, Sumedang', 'latitude' => -6.8570, 'longtitude' => 107.9210, 'phone' => '081200000114', 'email' => 'tahu.sumedang@outlet.com', 'is_active' => true],
            ['code' => 'DDG-GRT', 'name' => 'Dodol Garut Legendaris', 'address' => 'Jl. Ahmad Yani, Garut', 'latitude' => -7.2150, 'longtitude' => 107.9010, 'phone' => '081200000115', 'email' => 'dodol.garut@outlet.com', 'is_active' => true],

            // TASIK + PANGANDARAN
            ['code' => 'TTO-TSM', 'name' => 'Tutug Oncom Tasik', 'address' => 'Jl. HZ Mustofa, Tasikmalaya', 'latitude' => -7.3274, 'longtitude' => 108.2207, 'phone' => '081200000116', 'email' => 'tutug.tsm@outlet.com', 'is_active' => true],
            ['code' => 'ESD-PGD', 'name' => 'Es Dawet Pangandaran', 'address' => 'Jl. Pantai Barat, Pangandaran', 'latitude' => -7.6840, 'longtitude' => 108.6510, 'phone' => '081200000117', 'email' => 'dawet.pgd@outlet.com', 'is_active' => true],

            // CIREBON + KUNINGAN
            ['code' => 'NJM-CRB', 'name' => 'Nasi Jamblang Cirebon', 'address' => 'Jl. Cipto, Cirebon', 'latitude' => -6.7120, 'longtitude' => 108.5570, 'phone' => '081200000118', 'email' => 'jamblang.crb@outlet.com', 'is_active' => true],
            ['code' => 'EMP-CRB', 'name' => 'Empal Gentong Cirebon', 'address' => 'Jl. Karanggetas, Cirebon', 'latitude' => -6.7060, 'longtitude' => 108.5530, 'phone' => '081200000119', 'email' => 'empalgentong.crb@outlet.com', 'is_active' => true],
            ['code' => 'TMR-KNG', 'name' => 'Tahu Lamping Kuningan', 'address' => 'Jl. Siliwangi, Kuningan', 'latitude' => -6.9810, 'longtitude' => 108.4850, 'phone' => '081200000120', 'email' => 'tahu.kng@outlet.com', 'is_active' => true],

            // MAJALENGKA + INDRAMAYU
            ['code' => 'SIO-MJL', 'name' => 'Siomay Bandung Majalengka', 'address' => 'Jl. K.H. Abdul Halim, Majalengka', 'latitude' => -6.8360, 'longtitude' => 108.2270, 'phone' => '081200000121', 'email' => 'siomay.mjl@outlet.com', 'is_active' => true],
            ['code' => 'KRK-IDM', 'name' => 'Kerupuk & Rujak Indramayu', 'address' => 'Jl. Jenderal Sudirman, Indramayu', 'latitude' => -6.3270, 'longtitude' => 108.3240, 'phone' => '081200000122', 'email' => 'kerupuk.idm@outlet.com', 'is_active' => true],
        ];

        $outlets = [];
        foreach ($outletsData as $data) {
            $outlets[$data['code']] = Outlet::create($data);
        }

        // 3) USERS (BANYAKIN) - outlet_id awal null, nanti di-set ke outlet pertama yg dimiliki
        $usersData = [
            ['name' => 'Daffa Ramadhan', 'email' => 'daffa.owner1@gmail.com', 'phone' => '080011220001'],
            ['name' => 'Rio Oktora', 'email' => 'rio.owner2@gmail.com', 'phone' => '080011220002'],
            ['name' => 'Nabila Putri', 'email' => 'nabila.owner3@gmail.com', 'phone' => '080011220003'],
            ['name' => 'Fajar Maulana', 'email' => 'fajar.owner4@gmail.com', 'phone' => '080011220004'],
            ['name' => 'Siti Aisyah', 'email' => 'aisyah.owner5@gmail.com', 'phone' => '080011220005'],
            ['name' => 'Rizky Pratama', 'email' => 'rizky.owner6@gmail.com', 'phone' => '080011220006'],
            ['name' => 'Aulia Rahman', 'email' => 'aulia.owner7@gmail.com', 'phone' => '080011220007'],
            ['name' => 'Dewi Lestari', 'email' => 'dewi.owner8@gmail.com', 'phone' => '080011220008'],
        ];

        $users = [];
        foreach ($usersData as $ud) {
            $u = User::create([
                'outlet_id' => null,
                'name' => $ud['name'],
                'email' => $ud['email'],
                'password' => Hash::make('12345678'),
                'phone' => $ud['phone'],
                'is_active' => true,
                'email_verified_at' => now(),
            ]);
            $u->assignRole($ownerRole);
            $users[] = $u;
        }

        // 4) BAGI OUTLET: 1 user punya 2–3 outlet
        // (Kode outlet harus ada di $outlets array)
        $ownershipMap = [
            0 => ['BTG-BDG', 'MKK-BDG', 'SBK-BDG'],       // Daffa (3)
            1 => ['CTK-BGR', 'CRG-DPK'],                // Rio (2)
            2 => ['KPT-BKS', 'STM-KRW', 'STM-PWK'],       // Nabila (3)
            3 => ['BNK-SBG', 'TMM-SKB'],                // Fajar (2)
            4 => ['PKY-CJR', 'THS-SMD', 'DDG-GRT'],       // Aisyah (3)
            5 => ['TTO-TSM', 'ESD-PGD'],                // Rizky (2)
            6 => ['NJM-CRB', 'EMP-CRB', 'TMR-KNG'],       // Aulia (3)
            7 => ['SIO-MJL', 'KRK-IDM', 'SRB-CMH'],       // Dewi (3)
        ];

        foreach ($ownershipMap as $userIndex => $outletCodes) {
            $user = $users[$userIndex];

            // Set outlet_id default user = outlet pertama yg dimiliki
            $firstOwnedOutlet = $outlets[$outletCodes[0]] ?? null;
            if ($firstOwnedOutlet) {
                $user->outlet_id = $firstOwnedOutlet->id;
                $user->save();
            }

            // Set owner_id di outlet-outlet milik user tsb
            foreach ($outletCodes as $code) {
                if (! isset($outlets[$code])) {
                    continue;
                }
                $outlets[$code]->owner_id = $user->id;
                $outlets[$code]->save();
            }
        }
    }
}
