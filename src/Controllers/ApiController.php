<?php
/*
 *
  ____                 _ _     _____           _                       _
 |  _ \               | | |   |_   _|         | |                     (_)
 | |_) | ___ _ __   __| | |_    | |  _ __   __| | ___  _ __   ___  ___ _  __ _
 |  _ < / _ \ '_ \ / _` | __|   | | | '_ \ / _` |/ _ \| '_ \ / _ \/ __| |/ _` |
 | |_) |  __/ | | | (_| | |_   _| |_| | | | (_| | (_) | | | |  __/\__ \ | (_| |
 |____/ \___|_| |_|\__,_|\__| |_____|_| |_|\__,_|\___/|_| |_|\___||___/_|\__,_|

 Please don't modify this file because it may be overwritten when re-generated.
 */

namespace Bendt\Option\Controllers;

use App\Http\Controllers\Controller as AppController;
use Bendt\Option\Models\ErrorLog;
use Bendt\Option\Exceptions\MyValidationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ApiController extends AppController
{
    /**
     * success response method.
     *
     * @param  array $data
     * @param  string $message
     * @param  integer $code
     * @return \Illuminate\Http\Response
     */
    public function sendResponse($data = [], $message = null, $code = 200)
    {
        $response = [
            'success' => true,
        ];

        if ($data) $response['data'] = $data;
        if (!is_null($message)) $response['message'] = $message;

        return response()->json($response, $code);
    }


    /**
     * return error response.
     *
     * @param  array $errors || string $errors
     * @param  integer $code
     * @return \Illuminate\Http\Response
     */
    public function sendError($errors = [], $code = 400)
    {
        $response = [
            'success' => false,
            'error' => $errors,
        ];

        if (is_string($errors)) {
            $response['error'] = [
                'message' => $errors
            ];
        }

        return response()->json($response, $code);
    }

    /**
     * return custom validation error response.
     *
     * @param  array $errors || string $errors
     * @param  integer $code
     * @return \Illuminate\Http\Response
     */
    public function sendValidationError($errors = [], $code = 422)
    {
        return response()->json($errors, $code);
    }

    /**
     * Return error response.
     *
     *
     * @param  \Exception $exception
     * @return \Illuminate\Http\Response
     */
    public function sendException(\Exception $exception)
    {
        if ($exception instanceof MyValidationException) {
            $errors = json_decode($exception->getMessage());

            return $this->sendValidationError($errors);

        } else if ($exception instanceof ValidationException) {
            $error = $this->error_object($exception, 'ValidationException', null, [
                'response' => $exception->getResponse()
            ]);
            $response = [
                'success' => false,
                'error' => $error,
            ];

            return response()->json($response, 400);

        } else if ($exception instanceof AuthorizationException) {
            $error = $this->error_object($exception, 'AuthorizationException', 401);
            $response = [
                'success' => false,
                'error' => $error,
            ];
            return response()->json($response, 401);

        } else if ($exception instanceof QueryException) {
            $error = $this->error_object($exception, 'QueryException');
            $response = [
                'success' => false,
                'error' => $error,
            ];
            return response()->json($response, 400);

        } else {
            $code = $this->getCodeException($exception);
            $error = $this->error_object($exception, 'Exception');
            $response = [
                'success' => false,
                'error' => $error
            ];

            return response()->json($response, $code);
        }

    }

    private function getCodeException($exception)
    {
        $code = $exception->getCode();
        $code = ($code && $code !== 0) ? $code : 500;
        return $code;
    }

    private function error_object($exception, $type, $code = null, $opt_array = [])
    {

        $error = [
            'message' => $exception->getMessage(),
            'exception_message' => $exception->getMessage(),
            'type' => $type,
            'code' => $this->getCodeException($exception),

            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'class' => '',
            'function' => '',
        ];

        if ($type === 'QueryException') {
            $error['message'] = 'Database Query Exception (' . $error['code'] . ')';
        }

        if (env('LOG_EXCEPTION')) {
            foreach ($exception->getTrace() as $trace) {
                if (!isset($trace['line'])) {
                    $error['class'] = $trace['class'];
                    $error['function'] = $trace['function'];
                    break;
                }
            }

            $log = $this->log_error($error, $type);
            $error['last_log'] = $log;
            $error['message'] .= ' - REF#' . $log->id;
        }

        if (env('LOG_EXCEPTION_STACK')) {
            $error['getTrace'] = $exception->getTrace();
        }

        return array_merge(
            $error, $opt_array
        );
    }

    private function log_error($obj, $type)
    {
        $user = Auth::user();
        $obj['user_id'] = $user ? $user->id : null;

        $check = $this->eligible_for_logging($obj);

        if ($check === true) {
            DB::rollBack();
            if ($type === 'QueryException') {
                $obj['message'] = $obj['exception_message'];
            }
            unset($obj['exception_message']);
            $model = new ErrorLog($obj);
            $model->save();
            DB::commit();
            return $model;
        } else {
            return $check;
        }

    }

    private function eligible_for_logging($obj)
    {
        $logs = ErrorLog::where('user_id', $obj['user_id'])
            ->orderBy('id', 'desc')
            ->limit(5)
            ->get();

        if ($logs) {
            foreach ($logs as $check) {
                if ($check->code == $obj['code'] &&
                    $check->type == $obj['type'] &&
                    $check->message == $obj['message'] &&
                    $check->line == $obj['line'] &&
                    $check->function == $obj['function'] &&
                    $check->class == $obj['class'] &&
                    $check->file == $obj['file']) {
                    return $check;
                }
            }
        }

        return true;
    }
}
