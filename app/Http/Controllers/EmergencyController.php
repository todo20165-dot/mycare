<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;

class EmergencyController extends Controller
{
    /**
     * عرض صفحة زر الطوارئ
     */
    public function showEmergencyButton()
    {
        $user = Auth::user();
        $familyMembers = $user->familyLinksAsPatient()
            ->with('family_member')
            ->where('status', 'approved')
            ->get();

        return view('emergency.button', compact('familyMembers'));
    }

    /**
     * تفعيل زر الطوارئ مع الموقع الجغرافي
     */
    public function triggerEmergency(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'address' => 'nullable|string',
        ]);

        $user = Auth::user();
        $latitude = $request->latitude;
        $longitude = $request->longitude;
        $address = $request->address ?? "الموقع: $latitude, $longitude";

        // تسجيل حالة الطوارئ
        Log::warning("حالة طوارئ من المريض {$user->name}", [
            'user_id' => $user->id,
            'latitude' => $latitude,
            'longitude' => $longitude,
            'address' => $address,
            'timestamp' => now(),
        ]);

        // إرسال إشعارات للأهل
        $this->notifyFamilyMembers($user, $latitude, $longitude, $address);

        // إرسال إشعار للأطباء المرتبطين
        $this->notifyDoctors($user, $latitude, $longitude, $address);

        return response()->json([
            'success' => true,
            'message' => 'تم إرسال تنبيه الطوارئ بنجاح',
            'location' => [
                'latitude' => $latitude,
                'longitude' => $longitude,
                'address' => $address,
            ],
        ]);
    }

    /**
     * إرسال إشعارات للأهل
     */
    private function notifyFamilyMembers(User $patient, $latitude, $longitude, $address)
    {
        $familyMembers = $patient->familyLinksAsPatient()
            ->with('family_member')
            ->where('status', 'approved')
            ->get();

        foreach ($familyMembers as $link) {
            $familyMember = $link->family_member;

            // إنشاء إشعار في قاعدة البيانات
            Notification::create([
                'user_id' => $familyMember->id,
                'title' => '🚨 تنبيه طوارئ',
                'message' => "{$patient->name} يحتاج مساعدة عاجلة. الموقع: $address",
                'type' => 'emergency',
                'data' => json_encode([
                    'patient_id' => $patient->id,
                    'latitude' => $latitude,
                    'longitude' => $longitude,
                    'address' => $address,
                ]),
            ]);

            // يمكن إضافة إرسال بريد إلكتروني أو رسالة SMS هنا
            Log::info("إشعار طوارئ مرسل إلى {$familyMember->name}");
        }
    }

    /**
     * إرسال إشعارات للأطباء المرتبطين
     */
    private function notifyDoctors(User $patient, $latitude, $longitude, $address)
    {
        $doctors = $patient->doctors()
            ->with('pivot')
            ->wherePivot('status', 'approved')
            ->get();

        foreach ($doctors as $doctor) {
            // إنشاء إشعار في قاعدة البيانات
            Notification::create([
                'user_id' => $doctor->id,
                'title' => '🚨 تنبيه طوارئ من مريضك',
                'message' => "المريض {$patient->name} يحتاج مساعدة عاجلة. الموقع: $address",
                'type' => 'emergency',
                'data' => json_encode([
                    'patient_id' => $patient->id,
                    'patient_name' => $patient->name,
                    'patient_phone' => $patient->phone,
                    'latitude' => $latitude,
                    'longitude' => $longitude,
                    'address' => $address,
                ]),
            ]);

            Log::info("إشعار طوارئ مرسل إلى الطبيب {$doctor->name}");
        }
    }

    /**
     * الحصول على أقرب المستشفيات باستخدام Overpass API
     */
    public function getNearestHospitals(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $latitude = $request->latitude;
        $longitude = $request->longitude;

        // استعلام Overpass API للبحث عن المستشفيات والمراكز الصحية والمرافق الطبية القريبة
        $overpassQuery = "
            [out:json][timeout:25];
            (
                node[\"amenity\"~\"hospital|clinic|doctors|dentist|pharmacy\"](around:10000,{$latitude},{$longitude});
                way[\"amenity\"~\"hospital|clinic|doctors|dentist|pharmacy\"](around:10000,{$latitude},{$longitude});
                relation[\"amenity\"~\"hospital|clinic|doctors|dentist|pharmacy\"](around:10000,{$latitude},{$longitude});
                node[\"healthcare\"~\"hospital|clinic|doctor|doctors|medical_centre|health_centre\"](around:10000,{$latitude},{$longitude});
                way[\"healthcare\"~\"hospital|clinic|doctor|doctors|medical_centre|health_centre\"](around:10000,{$latitude},{$longitude});
                relation[\"healthcare\"~\"hospital|clinic|doctor|doctors|medical_centre|health_centre\"](around:10000,{$latitude},{$longitude});
                node[\"emergency\"=\"hospital\"](around:10000,{$latitude},{$longitude});
                way[\"emergency\"=\"hospital\"](around:10000,{$latitude},{$longitude});
                relation[\"emergency\"=\"hospital\"](around:10000,{$latitude},{$longitude});
            );
            out center meta;
        ";

        try {
            $client = new Client();
            $response = $client->post('https://overpass-api.de/api/interpreter', [
                'form_params' => [
                    'data' => $overpassQuery,
                ],
                'timeout' => 30,
            ]);

            $data = json_decode($response->getBody(), true);

            $hospitals = [];
            foreach ($data['elements'] as $element) {
                if (!empty($element['tags']['name'])) {
                    $hospLat = $element['lat'] ?? $element['center']['lat'] ?? null;
                    $hospLon = $element['lon'] ?? $element['center']['lon'] ?? null;

                    if ($hospLat && $hospLon) {
                        $distanceKm = $this->calculateDistance($latitude, $longitude, $hospLat, $hospLon);

                        $hospitals[] = [
                            'name' => $element['tags']['name'],
                            'latitude' => $hospLat,
                            'longitude' => $hospLon,
                            'phone' => $element['tags']['phone'] ?? ($element['tags']['contact:phone'] ?? 'غير محدد'),
                            'address' => $element['tags']['addr:full'] ?? ($element['tags']['addr:street'] ?? 'غير معروف'),
                            'distance_km' => round($distanceKm, 2),
                            'distance' => round($distanceKm, 2) . ' كم',
                            'eta' => max(1, round(($distanceKm / 50) * 60)), // تقدير زمن الوصول بالسيارة
                        ];
                    }
                }
            }

            // ترتيب حسب المسافة الأقرب و إزالة التكرار بحسب الاسم والموقع
            $hospitals = collect($hospitals)
                ->unique(function ($item) {
                    return $item['name'] . $item['latitude'] . $item['longitude'];
                })
                ->sortBy('distance_km')
                ->values()
                ->take(10)
                ->all();

            return response()->json([
                'success' => true,
                'hospitals' => $hospitals,
            ]);

        } catch (\Exception $e) {
            \Log::error('خطأ في جلب بيانات المستشفيات من Overpass API: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'لم يتم العثور على مستشفيات أو مرافق صحية قريبة في الوقت الحالي. يرجى المحاولة مرة أخرى.',
                'hospitals' => [],
            ], 500);
        }
    }

    /**
     * حساب المسافة بين نقطتين باستخدام صيغة هافيرسين
     */
    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371; // كيلومتر
        $latFrom = deg2rad($lat1);
        $lonFrom = deg2rad($lon1);
        $latTo = deg2rad($lat2);
        $lonTo = deg2rad($lon2);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $angle = sin($latDelta / 2) * sin($latDelta / 2) +
            cos($latFrom) * cos($latTo) *
            sin($lonDelta / 2) * sin($lonDelta / 2);
        $angle = 2 * asin(min(1, sqrt($angle)));

        return $earthRadius * $angle;
    }

    /**
     * الحصول على سجل حالات الطوارئ
     */
    public function getEmergencyHistory()
    {
        $user = Auth::user();
        
        $emergencies = Notification::where('user_id', $user->id)
            ->where('type', 'emergency')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('emergency.history', compact('emergencies'));
    }
}
