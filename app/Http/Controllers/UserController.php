<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Application;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\UtilityController;
use Illuminate\Support\Facades\Config;
use Illuminate\Http\Exception\HttpResponseException;

use App\User;

class UserController extends Controller
{
    /**
     * Get root url.
     *
     * @return \Illuminate\Http\Response
     */
    public function getIndex(Application $app)
    {
        return new JsonResponse(['message' => $app->version()]);
    }

    //###############################################################
    //Function Name : getUsers
    //Author : Himanshu <himanshuvarun@gmail.com>
    //Purpose : To get list of users or a single user by passing user $id
    //In Params : user $id (optional)
    //Return : Array of user(s) in return response
    //###############################################################
    public function getUsers()
    {
    	$list = User::select('*')->get()->toArray();
    	print_r($list);die;
    }

}
