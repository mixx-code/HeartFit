<?php

namespace App\Http\Controllers;

use App\Models\OrderDeliveryStatus;
use App\Models\MealPackages;
use App\Models\PackageType;
use App\Models\MenuMakanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardCustomerController extends Controller
{
    public function index(Request $request)
    {
        $tz   = 'Asia/Jakarta';
        $date = now($tz)->toDateString();

        $items = \App\Models\OrderDeliveryStatus::with(['mealPackage', 'menuMakanan'])
            ->whereDate('delivery_date', $date)
            ->orderByRaw("FIELD(status_siang, 'pending','sedang dikirim','sampai','gagal dikirim')")
            ->orderByRaw("FIELD(status_malam, 'pending','sedang dikirim','sampai','gagal dikirim')")
            ->get();

        // Ambil data paket dari database dengan error handling
        try {
            // Ambil package types
            $packageTypes = PackageType::orderBy('id')->get();

            // Ambil meal packages per package type
            $mealPackagesByType = MealPackages::all()->groupBy('package_type_id');
            
            // Debug: Log data
            \Log::info('Package Types:', $packageTypes->toArray());
            \Log::info('Meal Packages by Type:', $mealPackagesByType->toArray());

            // Cek meal packages per package type dan ambil dari database
            foreach ($packageTypes as $type) {
                $packageTypeId = $type->id;
                
                // Ambil meal packages untuk package type ini
                $typeMealPackages = $mealPackagesByType->get($packageTypeId, collect());
                
                // Update meal packages di type
                $type->meal_packages = $typeMealPackages;
            }

            // Ambil menu makanan aktif - SAMAKAN DENGAN ORDER CONTROLLER
            $menus = MenuMakanan::where('batch', 'I')
                ->get(['id', 'nama_menu', 'serve_days', 'spec_menu', 'foto_makanan']) 
                ->map(function ($m) {
                    $serve = is_array($m->serve_days) ? $m->serve_days : [];
                    $serve = array_values(array_filter(array_map(fn($v) => (int) $v, $serve), fn($n) => $n >= 1 && $n <= 31));

                    return [
                        'id'         => $m->id,
                        'nama_menu'  => $m->nama_menu,
                        'serve_days' => $serve,                  // <- array angka siap pakai
                        'spec_menu'  => $m->spec_menu ?? [],     // <- array asosiatif (section => [items])
                        'foto_makanan' => $m->foto_makanan ?? [], // <- array foto paths 
                    ];
                })
                ->values()
                ->toArray();
            
            // Debug: Log total menu yang diambil
            \Log::info("Total menus retrieved: " . count($menus));
            \Log::info('First 3 menus:', array_slice($menus, 0, 3));

            // Debug: Log data untuk troubleshooting
            \Log::info('Package Types:', $packageTypes->toArray());
            \Log::info('All Menus:', $menus);
            
            // Debug foto_makanan untuk setiap menu
            if (!empty($menus)) {
                foreach ($menus as $index => $menu) {
                    $fotoCount = isset($menu['foto_makanan']) ? count($menu['foto_makanan']) : 0;
                    \Log::info("Menu {$index}: {$menu['nama_menu']}, foto_count: {$fotoCount}");
                }
            }

            \Log::info('About to enter try-catch block for package building');

            // Format data untuk view dengan ID unik
            $packages = [];
            \Log::info('Starting to build packages array');
            
            foreach ($packageTypes as $type) {
                $packageKey = strtolower($type->packageType);
                \Log::info("Processing package: {$packageKey}");
                
                // Perbaikan filtering logic untuk menu - TAMPILKAN SEMUA MENU
                $filteredMenus = array_filter($menus, function($menu) use ($type) {
                    // Debug log untuk setiap menu
                    \Log::info("Menu: {$menu['nama_menu']}, spec_menu: " . json_encode($menu['spec_menu']) . ", targetType: {$type->packageType}");
                    
                    // Handle spec_menu yang berbentuk object dengan keys "Makan Siang" dan "Makan Malam"
                    if (isset($menu['spec_menu']) && is_array($menu['spec_menu']) && !empty($menu['spec_menu'])) {
                        // Tampilkan semua menu yang memiliki spec_menu (tanpa filter package type)
                        // Karena spec_menu berisi daftar makanan, bukan package type
                        \Log::info("Menu {$menu['nama_menu']} included - has spec_menu structure");
                        return true;
                    }
                    
                    // Fallback: jika spec_menu tidak ada atau bukan array, exclude
                    return false;
                });

                $packages[$packageKey] = [
                    'id' => $type->id, // ID unik dari database
                    'type' => $type->packageType,
                    'meal_packages' => $type->meal_packages ?? collect(),
                    'menus' => array_values($filteredMenus) // Reset array keys
                ];
                
                // Debug log hasil filtering
                \Log::info("Package {$packageKey} menus count: " . count($filteredMenus));
            }
        } catch (\Exception $e) {
            // Log error
            \Log::error('Error loading package data: ' . $e->getMessage());
            
            // Fallback data dengan menu real dari database
            try {
                $allMenus = MenuMakanan::orderBy('nama_menu')->get();
                
                // Debug: Log semua menu yang tersedia
                \Log::info('Fallback menus available:', $allMenus->toArray());
                
                // Debug detail untuk setiap menu
                foreach ($allMenus as $index => $menu) {
                    \Log::info("Raw menu {$index}: " . json_encode([
                        'nama_menu' => $menu->nama_menu,
                        'foto_makanan' => $menu->foto_makanan,
                        'foto_type' => gettype($menu->foto_makanan),
                        'foto_count' => is_array($menu->foto_makanan) ? count($menu->foto_makanan) : 0
                    ]));
                }
                
                // Convert to array format yang sama dengan query utama
                $allMenusArray = $allMenus->map(function ($m) {
                    $serve = is_array($m->serve_days) ? $m->serve_days : [];
                    $serve = array_values(array_filter(array_map(fn($v) => (int) $v, $serve), fn($n) => $n >= 1 && $n <= 31));

                    return [
                        'id'         => $m->id,
                        'nama_menu'  => $m->nama_menu,
                        'serve_days' => $serve,
                        'spec_menu'  => $m->spec_menu ?? [],
                        'foto_makanan' => $m->foto_makanan ?? [], // Tambahkan foto
                    ];
                })->toArray();
                
                // Coba filter menu untuk setiap paket
                $regulerMenus = array_filter($allMenusArray, function($menu) {
                    return isset($menu['spec_menu']) && is_array($menu['spec_menu']) && !empty($menu['spec_menu']);
                });
                $regulerMenus = array_slice($regulerMenus, 0, 3); // Ambil 3 pertama
                
                $premiumMenus = array_filter($allMenusArray, function($menu) {
                    return isset($menu['spec_menu']) && is_array($menu['spec_menu']) && !empty($menu['spec_menu']);
                });
                $premiumMenus = array_slice($premiumMenus, 0, 3); // Ambil 3 pertama
                
                $personalMenus = array_filter($allMenusArray, function($menu) {
                    return isset($menu['spec_menu']) && is_array($menu['spec_menu']) && !empty($menu['spec_menu']);
                });
                $personalMenus = array_slice($personalMenus, 0, 3); // Ambil 3 pertama
                
                $packages = [
                    'reguler' => [
                        'id' => 1,
                        'type' => 'Reguler',
                        'meal_packages' => collect([
                            (object) ['price' => 50000, 'detail_paket' => 'Paket hemat dengan menu seimbang', 'porsi_paket' => '1 Porsi', 'total_hari' => 7]
                        ]),
                        'menus' => $regulerMenus
                    ],
                    'premium' => [
                        'id' => 2,
                        'type' => 'Premium', 
                        'meal_packages' => collect([
                            (object) ['price' => 170000, 'detail_paket' => 'makan (Siang dan Malam)', 'porsi_paket' => '2 kali makan (siang dan malam)', 'total_hari' => 1]
                        ]),
                        'menus' => $premiumMenus
                    ],
                    'personal' => [
                        'id' => 3,
                        'type' => 'Personal',
                        'meal_packages' => collect([
                            (object) ['price' => 700000, 'detail_paket' => 'Paket personal dengan konsultasi', 'porsi_paket' => '1 Porsi', 'total_hari' => 7]
                        ]),
                        'menus' => $personalMenus
                    ]
                ];
                
                \Log::info('Fallback packages created:', array_map(fn($p) => [
                    'type' => $p['type'],
                    'menu_count' => count($p['menus']) // Gunakan count() bukan ->count()
                ], $packages));
                
            } catch (\Exception $menuEx) {
                \Log::error('Error loading fallback menus: ' . $menuEx->getMessage());
                
                // Ultimate fallback
                $packages = [
                    'reguler' => ['id' => 1, 'type' => 'Reguler', 'meal_packages' => collect(), 'menus' => collect()],
                    'premium' => ['id' => 2, 'type' => 'Premium', 'meal_packages' => collect(), 'menus' => collect()],
                    'personal' => ['id' => 3, 'type' => 'Personal', 'meal_packages' => collect(), 'menus' => collect()]
                ];
            }
        }

        return view('customers.dashboard', compact('items', 'date', 'packages'));
    }
}
