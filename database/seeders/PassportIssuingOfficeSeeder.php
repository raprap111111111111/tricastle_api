<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PassportIssuingOffice;

class PassportIssuingOfficeSeeder extends Seeder
{
    public function run(): void
    {
        $offices = [
            // ─── LUZON ───────────────────────────────────────────────────────────
            [
                'region'  => 'Luzon',
                'name'    => 'DFA Regional Consular Office – Angeles',
                'address' => '3rd Floor, Marquee Mall, Pulung Maragul, Angeles City',
            ],
            [
                'region'  => 'Luzon',
                'name'    => 'DFA Regional Consular Office – Antipolo',
                'address' => '3rd Floor, SM Cherry Foodarama, Marikina-Infanta Highway, Antipolo City, Rizal',
            ],
            [
                'region'  => 'Luzon',
                'name'    => 'DFA Regional Consular Office – Baguio City',
                'address' => 'Upper Basement, SM City Baguio, Luneta Hill, Upper Session Road corner Gov. Pack Road, Baguio City',
            ],
            [
                'region'  => 'Luzon',
                'name'    => 'DFA Regional Consular Office – Batangas',
                'address' => '2nd Floor, Robinsons Place Lipa, JP Laurel Highway, Mataas na Lupa, Lipa City',
            ],
            [
                'region'  => 'Luzon',
                'name'    => 'DFA Regional Consular Office – Calasiao',
                'address' => '2nd Floor, Robinsons Place Pangasinan, Brgy. San Miguel, Calasiao, Pangasinan',
            ],
            [
                'region'  => 'Luzon',
                'name'    => 'DFA Regional Consular Office – Cavite',
                'address' => '2nd Floor, SM City Dasmariñas, Brgy. Sampaloc, Dasmariñas, Cavite',
            ],
            [
                'region'  => 'Luzon',
                'name'    => 'DFA Regional Consular Office – Ilocos Norte',
                'address' => 'Robinsons Place Ilocos Norte, Brgy. San Francisco, San Nicolas, Ilocos Norte',
            ],
            [
                'region'  => 'Luzon',
                'name'    => 'DFA Regional Consular Office – La Union',
                'address' => '2nd Floor, Manna Mall, Marcos Highway corner Diversion Road, Pagdaraoan, City of San Fernando, La Union',
            ],
            [
                'region'  => 'Luzon',
                'name'    => 'DFA Regional Consular Office – Legazpi',
                'address' => '3rd Floor, Pacific Mall Legazpi, F. Imperial Street corner Circumferential Road, Brgy. Capantawan, Landco Business Park, Legazpi City',
            ],
            [
                'region'  => 'Luzon',
                'name'    => 'DFA Regional Consular Office – Lucena',
                'address' => '3rd Floor, Pacific Mall Lucena, M.L. Tagarao Street, Barangay III, Lucena City',
            ],
            [
                'region'  => 'Luzon',
                'name'    => 'DFA Regional Consular Office – Malolos',
                'address' => '3rd Floor, Malolos Central Transport Terminal and Commercial Hub by Xentro Mall, Brgy. Bulihan, Malolos City, Bulacan',
            ],
            [
                'region'  => 'Luzon',
                'name'    => 'DFA Regional Consular Office – Pampanga',
                'address' => '2nd Floor, Robinsons Starmills, City of San Fernando, Pampanga',
            ],
            [
                'region'  => 'Luzon',
                'name'    => 'DFA Passport Service Program – Paniqui, Tarlac',
                'address' => 'R-9, Waltermart, Paniqui, Tarlac',
            ],
            [
                'region'  => 'Luzon',
                'name'    => 'DFA Regional Consular Office – Puerto Princesa',
                'address' => '2nd Level, Robinsons Place Palawan, National Highway, Brgy. San Manuel, Puerto Princesa City',
            ],
            [
                'region'  => 'Luzon',
                'name'    => 'DFA Regional Consular Office – San Pablo',
                'address' => '2nd Floor, SM City San Pablo, Riverina Residential & Commercial Estates, Maharlika Highway, Brgy. San Rafael, San Pablo City, Laguna',
            ],
            [
                'region'  => 'Luzon',
                'name'    => 'DFA Regional Consular Office – Santiago, Isabela',
                'address' => 'Maharlika Highway, Brgy. Mabini, Santiago City, Isabela',
            ],
            [
                'region'  => 'Luzon',
                'name'    => 'DFA Regional Consular Office – Tuguegarao',
                'address' => 'Regional Government Center, Carig Sur, Tuguegarao City, Cagayan',
            ],

            // ─── VISAYAS ─────────────────────────────────────────────────────────
            [
                'region'  => 'Visayas',
                'name'    => 'DFA Regional Consular Office – Bacolod',
                'address' => '3rd Floor, Robinsons Place Bacolod, Brgy. Mandalagan, Bacolod City',
            ],
            [
                'region'  => 'Visayas',
                'name'    => 'DFA Regional Consular Office – Cebu',
                'address' => '4th Level, Pacific Mall – Metro Mandaue, UN Avenue corner M.C. Briones Street, Brgy. Estancia, Mandaue City',
            ],
            [
                'region'  => 'Visayas',
                'name'    => 'Passport Service Program – Cebu POW (SM Seaside)',
                'address' => 'SM Seaside, Mountain View Wing Atrium (by appointment only), Cebu City',
            ],
            [
                'region'  => 'Visayas',
                'name'    => 'DFA Regional Consular Office – Dumaguete',
                'address' => '2nd Floor, Robinsons Place South Road, Calindagan, Dumaguete City',
            ],
            [
                'region'  => 'Visayas',
                'name'    => 'DFA Regional Consular Office – Iloilo',
                'address' => '3rd Floor, Robinsons Place Iloilo (Quezon Wing), Iloilo City',
            ],
            [
                'region'  => 'Visayas',
                'name'    => 'DFA Regional Consular Office – Tacloban',
                'address' => '3rd Level, Robinsons North Tacloban, Brgy. 91, Abucay, Tacloban City',
            ],

            // ─── MINDANAO ────────────────────────────────────────────────────────
            [
                'region'  => 'Mindanao',
                'name'    => 'DFA Regional Consular Office – Butuan',
                'address' => '3rd Level, Robinsons Place Butuan, J.C. Aquino Avenue, Butuan City',
            ],
            [
                'region'  => 'Mindanao',
                'name'    => 'DFA Regional Consular Office – Cagayan de Oro',
                'address' => '5th Floor, SM CDO Downtown Premier, Claro M. Recto Avenue corner Osmeña Street, Cagayan de Oro City',
            ],
            [
                'region'  => 'Mindanao',
                'name'    => 'DFA Regional Consular Office – Clarin',
                'address' => 'Clarin Town Center, Clarin, Misamis Occidental',
            ],
            [
                'region'  => 'Mindanao',
                'name'    => 'DFA Regional Consular Office – Cotabato',
                'address' => 'Alim Street, Kidapawan City, Cotabato',
            ],
            [
                'region'  => 'Mindanao',
                'name'    => 'DFA Regional Consular Office – Davao',
                'address' => '3rd Floor, SM City Davao, Quimpo Boulevard, Ecoland Subdivision, Brgy. Matina, Davao City',
            ],
            [
                'region'  => 'Mindanao',
                'name'    => 'DFA Regional Consular Office – General Santos',
                'address' => 'Ground Floor, Robinsons Place GenSan, Jose Catolico Sr. Avenue, Lagao, General Santos City',
            ],
            [
                'region'  => 'Mindanao',
                'name'    => 'DFA Regional Consular Office – Tagum',
                'address' => 'Gaisano Mall of Tagum, National Highway, Tagum City, Davao del Norte',
            ],
            [
                'region'  => 'Mindanao',
                'name'    => 'DFA Regional Consular Office – Zamboanga',
                'address' => 'Go Velayo Building, Veterans Avenue, Zamboanga City, Zamboanga del Sur',
            ],
        ];

        foreach ($offices as $office) {
            PassportIssuingOffice::updateOrCreate(
                ['name' => $office['name']], 
                [
                    'region'    => $office['region'],
                    'address'   => $office['address'],
                    'is_active' => true,
                ]
            );
        }
    }
}