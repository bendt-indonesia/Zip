<?php

namespace Bendt\Zip\Services;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class ZipService
{
    public static function getProvinces()
    {
        $models = DB::table('zip')
            ->distinct()
            ->select('province_id as id', 'province_name as name');

        return $models;
    }

    public static function findCity($province_id)
    {
        $models = DB::table('zip')
            ->distinct()
            ->select('city_id as id', 'city_name as name', 'city_type')
            ->where('province_id', $province_id);

        return $models;
    }

    public static function findKecamatan($province_id, $city_id)
    {
        $models = DB::table('zip')
            ->distinct()
            ->select('kec_id as id', 'kec_name as name')
            ->where('province_id', $province_id)
            ->where('city_id', $city_id);

        return $models;
    }

    public static function findKelurahan($province_id, $city_id, $kec_id)
    {
        $models = DB::table('zip')
            ->distinct()
            ->select('kel_id as id', 'kel_name as name', 'zip')
            ->where('province_id', $province_id)
            ->where('city_id', $city_id)
            ->where('kec_id', $kec_id);

        return $models;
    }

    public static function findZip($zip)
    {
        $models = DB::table('zip')
            ->distinct()
            ->select('province_id', 'province_name', 'city_id', 'city_name', 'city_type', 'kec_id', 'kec_name', 'zip')
            ->where('zip', $zip)
            ->first();

        return $models;
    }
}
