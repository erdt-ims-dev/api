<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ResponseModel
{
    protected $statusCode;
    protected $error;

    protected $data;

    public function __construct($data)
    {
        if(isset($data['error'])){
            $this->error = $data['error'];
        }else{
            $this->data = $data['data'];
        }
        $this->statusCode = $data['status_code'];
    }

    public function getError(){
        return array(
            'error' => $this->error,
            'code' => $this->statusCode
        );
    }

    public function getResponse(){
        return array(
            'data' => $this->error,
            'code' => $this->statusCode
        );
    }
}
