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
            ->select('province_id', 'province_name');

        return $models;
    }

    public static function findCity($province_id)
    {
        $models = DB::table('zip')
            ->distinct()
            ->select('city_id', 'city_name', 'city_type', 'raja_city_id')
            ->where('province_id', $province_id);

        return $models;
    }

    public static function findKecamatan($province_id, $city_id)
    {
        $models = DB::table('zip')
            ->distinct()
            ->select('kec_id', 'kec_name')
            ->where('province_id', $province_id)
            ->where('city_id', $city_id);

        return $models;
    }

    public static function findKelurahan($province_id, $city_id, $kec_id)
    {
        $models = DB::table('zip')
            ->distinct()
            ->select('kel_id', 'kel_name', 'zip')
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

    public static function findRajaCityId($province_id, $city_id, $kec_id = null, $kel_id = null) {
        if(!$province_id || !$city_id) return null;

        $model = DB::table('zip')
            ->distinct()
            ->select('province_id', 'province_name', 'city_id', 'city_name', 'city_type', 'kec_id', 'kec_name', 'zip','raja_city_id')
            ->where('province_id', $province_id)
            ->where('city_id', $city_id);

        if($kec_id) {
            $model = $model->where('kec_id',$kec_id);
        }
        if($kel_id) {
            $model = $model->where('kel_id',$kel_id);
        }

        $model = $model->first();

        return $model;
    }
}
