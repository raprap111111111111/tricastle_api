<?php

namespace Database\Seeders;

use App\Models\PassportIssuingOffice;
use Illuminate\Database\Seeder;

class PassportIssuingOfficeSeeder extends Seeder
{
    public function run(): void
    {
        $offices = [
            // ─── NCR / MANILA ────────────────────────────────────────────────
            [
                'region'  => 'NCR',
                'name'    => 'DFA Aseana (Main Office / Consular Office Manila)',
                'address' => 'ASEANA Business Park, Bradco Avenue corner Macapagal Boulevard, Brgy. Tambo, Parañaque City',
            ],
            [
                'region'  => 'NCR',
                'name'    => 'DFA NCR-East (SM Megamall)',
                'address' => '7th Floor, Building C, SM Megamall, EDSA corner Julia Vargas Avenue, Mandaluyong City',
            ],
            [
                'region'  => 'NCR',
                'name'    => 'DFA NCR-West (SM City Manila)',
                'address' => '5th Floor, SM City Manila, Natividad Almeda Lopez Street corner San Marcelino Street, Ermita, Manila',
            ],
            [
                'region'  => 'NCR',
                'name'    => 'DFA NCR-Northeast (Ali Mall)',
                'address' => 'Level 2, Ali Mall Government Center, Araneta City, Cubao, Quezon City',
            ],
            [
                'region'  => 'NCR',
                'name'    => 'DFA NCR-South (Festival Mall Alabang)',
                'address' => '3rd Level, West Wing, Festival Mall, Alabang, Muntinlupa City',
            ],
            [
                'region'  => 'NCR',
                'name'    => 'DFA NCR-Central (Robinsons Galleria)',
                'address' => 'Lower Ground Floor, Lingkod Pinoy Center, Robinsons Galleria, West Lane, EDSA corner Ortigas Avenue, Quezon City',
            ],
            [
                'region'  => 'NCR',
                'name'    => 'DFA NCR-North (Robinsons Novaliches)',
                'address' => '3rd Floor, Lingkod Pinoy Center Building B, Robinsons Novaliches, Quirino Highway, Barangay Pasong Putik, Quezon City',
            ],

            // ─── LUZON ───────────────────────────────────────────────────────
            [
                'region'  => 'Luzon',
                'name'    => 'DFA Regional Consular Office – Angeles',
                'address' => 'Ground Level, Tech Hub 2, SM City Clark Expansion, Malabanias, Angeles City, Pampanga',
            ],
            [
                'region'  => 'Luzon',
                'name'    => 'DFA Regional Consular Office – Antipolo',
                'address' => '3rd Floor, SM Cherry Foodarama / SM Center Antipolo, Marikina-Infanta Highway, Mayamot, Antipolo City, Rizal',
            ],
            [
                'region'  => 'Luzon',
                'name'    => 'DFA Regional Consular Office – Baguio City',
                'address' => 'Upper Basement, SM City Baguio, Luneta Hill, Upper Session Road corner Gov. Pack Road, Baguio City',
            ],
            [
                'region'  => 'Luzon',
                'name'    => 'DFA Regional Consular Office – Balanga',
                'address' => '3rd Level, The Bunker, Provincial Capitol Compound, Balanga City, Bataan',
            ],
            [
                'region'  => 'Luzon',
                'name'    => 'DFA Regional Consular Office – Batangas (Lipa)',
                'address' => '2nd Floor, Robinsons Place Lipa, JP Laurel Highway, Mataas na Lupa, Lipa City, Batangas',
            ],
            [
                'region'  => 'Luzon',
                'name'    => 'DFA Regional Consular Office – Calasiao',
                'address' => '2nd Floor, Robinsons Place Pangasinan, Brgy. San Miguel, Calasiao, Pangasinan',
            ],
            [
                'region'  => 'Luzon',
                'name'    => 'DFA Regional Consular Office – Candon',
                'address' => 'Level 1, Candon City Arena, Bagani Campo, Bypass Road, Candon City, Ilocos Sur',
            ],
            [
                'region'  => 'Luzon',
                'name'    => 'DFA Regional Consular Office – Cavite (Dasmariñas)',
                'address' => '2nd Floor, SM City Dasmariñas, Governor’s Drive corner Aguinaldo Highway, Brgy. Sampaloc, Dasmariñas City, Cavite',
            ],
            [
                'region'  => 'Luzon',
                'name'    => 'DFA Regional Consular Office – Ilocos Norte',
                'address' => 'Robinsons Place Ilocos / Robinsons Ilocos, Valdez Center, Brgy. San Francisco, San Nicolas, Ilocos Norte',
            ],
            [
                'region'  => 'Luzon',
                'name'    => 'DFA Regional Consular Office – La Union',
                'address' => 'CSI Mall La Union / Manna Mall area, Brgy. Biday / Pagdaraoan, City of San Fernando, La Union',
            ],
            [
                'region'  => 'Luzon',
                'name'    => 'DFA Regional Consular Office – Legazpi',
                'address' => '3rd Floor, Pacific Mall Legazpi, Landco Business Park, Legazpi City, Albay',
            ],
            [
                'region'  => 'Luzon',
                'name'    => 'DFA Regional Consular Office – Lucena',
                'address' => '3rd Floor, Pacific Mall Lucena, M.L. Tagarao Street, Barangay III, Lucena City, Quezon',
            ],
            [
                'region'  => 'Luzon',
                'name'    => 'DFA Regional Consular Office – Malolos',
                'address' => '3rd Floor, Malolos Central Transport Terminal and Commercial Hub by Xentro Mall, Brgy. Bulihan, Malolos City, Bulacan',
            ],
            [
                'region'  => 'Luzon',
                'name'    => 'DFA Regional Consular Office – Olongapo',
                'address' => 'Level 3, SM City Olongapo Central, Olongapo City, Zambales',
            ],
            [
                'region'  => 'Luzon',
                'name'    => 'DFA Regional Consular Office – Pampanga',
                'address' => '2nd Floor, Robinsons Starmills, City of San Fernando, Pampanga',
            ],
            [
                'region'  => 'Luzon',
                'name'    => 'DFA Passport Service Program – Paniqui, Tarlac',
                'address' => 'Waltermart Paniqui, Paniqui, Tarlac',
            ],
            [
                'region'  => 'Luzon',
                'name'    => 'DFA Regional Consular Office – Puerto Princesa',
                'address' => '2nd Level, Robinsons Place Palawan, National Highway, Brgy. San Manuel, Puerto Princesa City, Palawan',
            ],
            [
                'region'  => 'Luzon',
                'name'    => 'DFA Regional Consular Office – San Pablo',
                'address' => '2nd Floor, SM City San Pablo, Maharlika Highway, Brgy. San Rafael, San Pablo City, Laguna',
            ],
            [
                'region'  => 'Luzon',
                'name'    => 'DFA Regional Consular Office – Santiago, Isabela',
                'address' => '3rd Floor, Robinsons Place Santiago / Maharlika Highway, Brgy. Mabini, Santiago City, Isabela',
            ],
            [
                'region'  => 'Luzon',
                'name'    => 'DFA Regional Consular Office – Tuguegarao',
                'address' => 'Ground Floor / Regional Government Center, Carig Sur, Tuguegarao City, Cagayan',
            ],

            // ─── VISAYAS ─────────────────────────────────────────────────────
            [
                'region'  => 'Visayas',
                'name'    => 'DFA Regional Consular Office – Antique',
                'address' => 'Lower Ground Floor / 3F, CityMall Antique or Robinsons Antique, National Highway, San Jose de Buenavista, Antique',
            ],
            [
                'region'  => 'Visayas',
                'name'    => 'DFA Regional Consular Office – Bacolod',
                'address' => '3rd Floor, Robinsons Place Bacolod, Brgy. Mandalagan, Bacolod City',
            ],
            [
                'region'  => 'Visayas',
                'name'    => 'DFA Regional Consular Office – Cebu',
                'address' => '3rd Floor, Robinsons Galleria Cebu, Gen. Maxilom Avenue Extension, Sergio Osmeña Jr. Blvd., Cebu City',
            ],
            [
                'region'  => 'Visayas',
                'name'    => 'DFA Regional Consular Office – Dumaguete',
                'address' => '2nd Floor, Robinsons Place Dumaguete, South Road, Calindagan, Dumaguete City',
            ],
            [
                'region'  => 'Visayas',
                'name'    => 'DFA Regional Consular Office – Iloilo',
                'address' => '3rd Floor, Robinsons Place Iloilo (Quezon Wing), Iloilo City',
            ],
            [
                'region'  => 'Visayas',
                'name'    => 'DFA Regional Consular Office – Tacloban',
                'address' => '3rd Level, Robinsons North Tacloban, Brgy. 91 Abucay, Tacloban City',
            ],
            [
                'region'  => 'Visayas',
                'name'    => 'DFA Regional Consular Office – Tagbilaran',
                'address' => '5th Floor, Alturas Mall / Island City Mall area, Tagbilaran City, Bohol',
            ],

            // ─── MINDANAO ────────────────────────────────────────────────────
            [
                'region'  => 'Mindanao',
                'name'    => 'DFA Regional Consular Office – Butuan',
                'address' => '3rd Level, Robinsons Place Butuan, J.C. Aquino Avenue, Butuan City',
            ],
            [
                'region'  => 'Mindanao',
                'name'    => 'DFA Regional Consular Office – Cagayan de Oro',
                'address' => '5th Floor, BPO Tower, SM CDO Downtown Premier, Claro M. Recto Avenue corner Osmeña Street, Cagayan de Oro City',
            ],
            [
                'region'  => 'Mindanao',
                'name'    => 'DFA Regional Consular Office – Clarin',
                'address' => 'Clarin Town Center, Ozamiz-Oroquieta National Highway, Clarin, Misamis Occidental',
            ],
            [
                'region'  => 'Mindanao',
                'name'    => 'DFA Regional Consular Office – Cotabato (Kidapawan)',
                'address' => 'Alim Street, Barangay Poblacion, Kidapawan City, Cotabato',
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
                'name'    => 'DFA Regional Consular Office – Pagadian',
                'address' => '2nd Floor, C3 Mall (City Commercial Center), Rizal Avenue, Santiago District, Pagadian City, Zamboanga del Sur',
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