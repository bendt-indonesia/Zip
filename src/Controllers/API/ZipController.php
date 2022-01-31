<?php

namespace Bendt\Zip\Controllers\API;

use Bendt\Zip\Controllers\ApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

use Bendt\Zip\Models\Zip as Model;
use Bendt\Zip\Services\ZipService;

class ZipController extends ApiController
{

    private $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * Display a listing of the province city
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function province_city(Request $request)
    {
        try {
            if (config('bendt-zip.authorize', false)) {
                $this->authorize('view', Auth::user(), Model::class);
            }

            $models = DB::table('zip')
                ->distinct()
                ->select('province_id', 'province_name', 'city_id', 'city_name');

            return datatables($models)
                ->make(true);
        } catch (\Exception $e) {
            return $this->sendException($e);
        }
    }

    /**
     * Display a listing of the province
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function province(Request $request)
    {
        try {
            if (config('bendt-zip.authorize', false)) {
                $this->authorize('view', Auth::user(), Model::class);
            }

            $models = ZipService::getProvinces();

            return datatables($models)->make(true);
        } catch (\Exception $e) {
            return $this->sendException($e);
        }
    }

    /**
     * Display a listing of the city
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function city(Request $request)
    {
        try {
            if (config('bendt-zip.authorize', false)) {
                $this->authorize('view', Auth::user(), Model::class);
            }

            $province_id = $request->input('province_id');

            if (!$province_id) {
                return $this->sendError('Province is required!', 422);
            }

            $models = ZipService::findCity($province_id);

            return datatables($models)->make(true);
        } catch (\Exception $e) {
            return $this->sendException($e);
        }
    }

    /**
     * Display a listing of the Kecamatan
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function kecamatan(Request $request)
    {
        try {
            if (config('bendt-zip.authorize', false)) {
                $this->authorize('view', Auth::user(), Model::class);
            }

            $province_id = $request->input('province_id');
            $city_id = $request->input('city_id');


            if (!$province_id || !$city_id) {
                return $this->sendError('Province, City are required!', 422);
            }

            $models = ZipService::findKecamatan($province_id, $city_id);

            return datatables($models)
                ->make(true);
        } catch (\Exception $e) {
            return $this->sendException($e);
        }
    }

    /**
     * Display a listing of the Kelurahan
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function kelurahan(Request $request)
    {
        try {
            if (config('bendt-zip.authorize', false)) {
                $this->authorize('view', Auth::user(), Model::class);
            }

            $province_id = $request->input('province_id');
            $city_id = $request->input('city_id');
            $kec_id = $request->input('kec_id');

            if (!$province_id || !$city_id || !$kec_id) {
                return $this->sendError('Province, City, Kecamatan are required!', 422);
            }

            $models = ZipService::findKelurahan($province_id, $city_id, $kec_id);

            return datatables($models)
                ->make(true);
        } catch (\Exception $e) {
            return $this->sendException($e);
        }
    }

    /**
     * Find Province, City and Kecamatan based on zip
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function check_zip(Request $request)
    {
        try {
            if (config('bendt-zip.authorize', false)) {
                $this->authorize('view', Auth::user(), Model::class);
            }

            $this->validate($request, [
                'zip' => 'required',
            ]);

            $zip = $request->input('zip');
            $return = [];
            $find = ZipService::findZip($zip);

            if ($find) {
                $return['result'] = $find;
                $return['selected'] = [
                    'province' => [
                        'label' => $find->province_name,
                        'value' => $find->province_id,
                    ],
                    'city' => [
                        'label' => $find->city_name,
                        'value' => $find->city_id,
                        'type' => $find->city_type,
                        'raja_city_id' => $find->raja_city_id,
                    ],
                    'kecamatan' => [
                        'label' => $find->kec_name,
                        'value' => $find->kec_id,
                    ],
                ];
                $return['options'] = [
                    'province' => ZipService::getProvinces()->get(),
                    'city' => ZipService::findCity($find->province_id)->get(),
                    'kecamatan' => ZipService::findKecamatan($find->province_id, $find->city_id)->get(),
                    'kelurahan' => ZipService::findKelurahan($find->province_id, $find->city_id, $find->kec_id)->get(),
                ];
            } else {
                return $this->sendError(['Kode Pos tidak ditemukan!']);
            }


            return $this->sendResponse($return);
        } catch (\Exception $e) {
            return $this->sendException($e);
        }
    }

}
