<?php
/*
 *
  ____                 _ _     _____           _                       _
 |  _ \               | | |   |_   _|         | |                     (_)
 | |_) | ___ _ __   __| | |_    | |  _ __   __| | ___  _ __   ___  ___ _  __ _
 |  _ < / _ \ '_ \ / _` | __|   | | | '_ \ / _` |/ _ \| '_ \ / _ \/ __| |/ _` |
 | |_) |  __/ | | | (_| | |_   _| |_| | | | (_| | (_) | | | |  __/\__ \ | (_| |
 |____/ \___|_| |_|\__,_|\__| |_____|_| |_|\__,_|\___/|_| |_|\___||___/_|\__,_|

 Last Update 16 Aug 2020
 */

/**
 * Option Helper
 *
 * @param string $key
 *
 * @return \App\Store
 */
if (!function_exists('option')) {
    function option($key)
    {
        return \App\Store::get($key);
    }
}


/**
 * List Options Key By Value
 * [
 *      value => option_detail model
 * ]
 * @param string $slug
 *
 * @return array
 */
if (!function_exists('options')) {
    function options($slug, $map_value_idx = false)
    {
        $option = option($slug);
        $option_details = collect($option->option_detail)
            ->keyBy('value');

        if ($map_value_idx) {
            foreach ($option_details as $value => $model) {
                $option_details[$value] = $model[$map_value_idx];
            }
        }

        $option_details = $option_details->toArray();

        return $option_details;
    }
}

/**
 * ADS List Option Detail
 *
 * @param string $slug
 *
 * @return array
 */
if (!function_exists('option_detail')) {
    function option_detail($slug, $pluck = null)
    {
        if ($pluck === null) $pluck = 'id';

        $option = option($slug);
        $data = collect($option->option_detail)->pluck($pluck)->toArray();

        return $data;
    }
}

/**
 * Find Option Helper

 * @param string $option_key
 * @param string $key
 * @param string $value
 *
 * @return array
 */
if (!function_exists('foption')) {
    function foption($slug, $value, $key = 'value')
    {
        try {
            $option = option($slug);
            if (!$option) abt('BENDT', 'Option ' . $slug . ' not found!');
            $data = collect($option->option_detail)->firstWhere($key, $value)->toArray();

            return $data;
        } catch (\Exception $e) {
            abt('Helper > foption',$e->getMessage());
        }
    }
}

/**
 * Throw Error String Generator
 *
 * @param string $code
 * @param string $message
 * @param int $http_code
 *
 * @return Array
 */
if (!function_exists('abt')) {
    function abt($error_code, $message, $http_code = 400)
    {
        //$errorMsg = "[ ERR: ".$error_code." ] ".$message;
        $errorMsg = $message . ' ( ' . $error_code . ' )';
        throw new \Exception($errorMsg, $http_code);
    }
}


/**
 * Specify which DataList to be used, based on request origin header.
 *
 * @param  Illuminate\Http\Request $request
 * @param  stdClass $DataList
 * @param  boolean $alwaysIndex
 * @return array $filters
 */
if (!function_exists('checkListType')) {
    function checkListType($request, $DataList, $alwaysIndex = false)
    {
        $listType = $request->get('list_type');
        $origin_path = $request->get('origin_path');

        if (!$listType || !$origin_path) return $DataList->index();
        if ($alwaysIndex) return $DataList->index();

        $filters = $DataList->{$listType}();

        if ($listType === 'common') {
            foreach ($DataList::$mapping as $path => $function) {
                if (Str::startsWith($origin_path, $path)) {
                    return $DataList->{$DataList::$mapping[$path]}();
                }
            }
        }

        return $filters;
    }
}

/**
 * Specify which DataList $with_relations to be used, based on request origin header.
 *
 * @param  Illuminate\Http\Request $request
 * @param  stdClass $DataList
 * @param  boolean $alwaysIndex
 * @return array $filters
 */
if (!function_exists('checkWithRelations')) {
    function checkWithRelations($request, $DataList, $alwaysIndex = false)
    {
        $listType = $request->get('list_type');
        $origin_path = $request->get('origin_path');
        $withs = $request->get('withs') ? explode(',', $request->get('withs')) : [];

        if ($alwaysIndex) return array_merge($withs, $DataList::$with_relations['index']);
        if (!$listType || !$origin_path) return [];

        if ($listType === 'common') {
            foreach ($DataList::$with_relations as $path => $list) {
                if (Str::startsWith($origin_path, $path)) {
                    return array_merge($withs, $DataList::$with_relations[$path]);
                }
            }
            return array_merge($withs, $DataList::$with_relations['common']);
        }

        return array_merge($withs, $DataList::$with_relations['index']);
    }
}

if (!function_exists('filterDataTables')) {
    /**
     * @param Illuminate\Http\Request $request
     * @param array $filters
     * @param object $query
     * @param integer $limit
     *
     * @return string
     */
    function filterDataTables($request, $filters, $query, $limit = 5000)
    {
        foreach ($filters as $field => $like) {
            if ($request->has($field) && $field !== 'deleted')
                if ($request->get($field) === 'null') {
                    $query->where($field, NULL);
                } else if ($like === 'like') {
                    $query->where($field, 'like', "%{$request->get($field)}%");
                } else if ($like === 'multi') {
                    $arr = explode(',', $request->get($field));
                    $query->whereIn($field, $arr);
                } else {
                    $query->where($field, $like, $request->get($field));
                }
        }

        if ($request->has('offset') && $request->has('limit')) {
            $query->skip($request->has('offset'))->take($request->get('limit'));
        } else if ($request->has('limit')) {
            $query->limit($request->get('limit'));
        } else if($limit !== null) {
            $query->limit($limit);
        }

        return $query;
    }
}