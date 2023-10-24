<?php

namespace App\Http\Controllers;

use App\Models\LoginAttempt;
use App\Models\ResponseModel;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Providers\RequestValidatorServiceProvider;
use Illuminate\Support\Facades\Schema;
use Laravel\Sanctum\Sanctum;

class APIController extends Controller
{
    protected $model;

    protected $data;

    protected $error;

    protected $status;

    protected $numberSet = '0123456789';
    protected $alphaNumerics = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';

    protected $response = array(
        "data" => null,
        "error" => null, // {status, message}
        'status' => null,
        "request_timestamp" => 0,
        "timezone" => 'Asia/Manila'
    );

    public function getResponse()
    {
        return response()->json($this->response);
    }

    public function getError()
    {
        return response()->json($this->response);
    }

    public function getModel()
    {
        return $this->model ? $this->model : 'Not Found';
    }

    public function insertDB($data)
    {
        dd(Schema::getColumnListing($this->model->getTable()));
    }


}
