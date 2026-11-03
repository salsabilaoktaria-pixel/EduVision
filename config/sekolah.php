<?php

// Atur koordinat sekolahmu di sini (bisa dicari lewat Google Maps: klik kanan lokasi > koordinat akan muncul).
// radius_meter = jarak toleransi dari titik sekolah yang masih dianggap "di area sekolah".

return [
    'latitude' => env('SEKOLAH_LATITUDE', -2.990934),
    'longitude' => env('SEKOLAH_LONGITUDE', 104.756554),
    'radius_meter' => env('SEKOLAH_RADIUS_METER', 200),
];
