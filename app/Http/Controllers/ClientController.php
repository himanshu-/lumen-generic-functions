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
use App\Clients;
use App\Brand;

class ClientController extends Controller
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
    //Function Name : getClients
    //Author : Himanshu <himanshuvarun@gmail.com>
    //Purpose : To get list of clients or a single client by passing client $id
    //In Params : client $id (optional)
    //Return : Array of client(s) in return response
    //###############################################################
    public function getClients($id = '')
    {
        try
        {
            $returnData = UtilityController::Setreturnvariables();  // Global declaration of response and input parameters.
            if($id != '')
            {
        	   $list = Clients::select('package','archive','name','website','address1','address2','locality',
                                        'post','state','country','referred','updated','player','player_color','client')
                                ->where("client", "=", $id)
                                ->get()->toArray();
            }
            else {
                $list = Clients::select('package','archive','name','website','address1','address2','locality',
                                        'post','state','country','referred','updated','player','player_color','client')
                                ->get()->toArray();
            }
            if(!empty($list))
            {
                $responseData['clients'] = $list;
                $returnData = UtilityController::Generateresponse(1,'CLIENT_LIST', Response::HTTP_OK, $responseData);
            }
            else {
                $returnData = UtilityController::Generateresponse(0,'NO_DATA_FOUND', Config('constant.standard_response_values.NO_DATA_FOUND'));   
            }
        }
        catch (\Exception $e) {
            $sendExceptionMail = UtilityController::Sendexceptionmail($e);
            $returnData = UtilityController::Generateresponse(0,$e->getMessage(), Response::HTTP_BAD_REQUEST, '');
        }
        // All good so return the token
        return response($returnData);

    }

    //###############################################################
    //Function Name : postCreateclient
    //Author : Himanshu <himanshuvarun@gmail.com>
    //Purpose : To create new client
    //In Params : clients details parameters
    //Return : Newly generated client id.
    //###############################################################
    public function postCreateclient(Request $request)
    {
        try
        {
            $returnData = UtilityController::Setreturnvariables();  // Global declaration of response and input parameters.
            $inputData = $request->all();

            // Defining validation rules
            $rule = array(
                'package'       => 'required|max:10|integer',
                'name'          => 'required|max:255|string',
                'website'       => 'required|string',
                'address1'      => 'required|string',
                'locality'      => 'required|string',
                'post'          => 'required|string',
                'state'         => 'required|string',
                'country'       => 'required|string',
                'player'        => 'required|string',
                'player_color'  => 'required|string'
            );

            $validator = \Validator::make($inputData,$rule);    // Execute validation.
            if ($validator->fails())
            {
                // $validationError = $validator->messages()->all();
                $returnData = UtilityController::Generateresponse(0,'VALIDATION_ERROR',Response::HTTP_BAD_REQUEST, '', $validator);
                
            }else {
                $data = UtilityController::Makemodelobject($inputData, 'Clients', 'client');
                if($data)
                {
                    $responseData['client_id'] = $data->client;
                    $returnData = UtilityController::Generateresponse(1,'CLIENT_CREATED', Response::HTTP_OK, $responseData);
                }
            }
        }
        catch (\Exception $e) {
            $sendExceptionMail = UtilityController::Sendexceptionmail($e);
            $returnData = UtilityController::Generateresponse(0,$e->getMessage(), Response::HTTP_BAD_REQUEST, '');
        }

        return response($returnData);
    }

    //###############################################################
    //Function Name : putClients
    //Author : Himanshu <himanshuvarun@gmail.com>
    //Purpose : To create new client
    //In Params : clients details parameters
    //Return : Newly generated client id.
    //###############################################################
    public function putClients(Request $request, $id = '')
    {
        try
        {
            $returnData = UtilityController::Setreturnvariables();  // Global declaration of response and input parameters.
            $inputData = $request->all();

            if($id != '')
            {
                $data = UtilityController::Makemodelobject($inputData, 'Clients', 'client', $id);
                if($data)
                {
                    $responseFields = array('package','archive','name','website','address1','address2','locality',
                                        'post','state','country','referred','updated','player','player_color','client');
                    foreach($responseFields as $k => $v)
                    {
                        $responseData[$v] = $data->$v;
                    }
                    $returnData = UtilityController::Generateresponse(1,'CLIENT_INFO_UPDATE_SUCCESS', Response::HTTP_OK, $responseData);
                }

            }
        }
        catch (\Exception $e) {
            $sendExceptionMail = UtilityController::Sendexceptionmail($e);
            $returnData = UtilityController::Generateresponse(0,$e->getMessage(), Response::HTTP_BAD_REQUEST, '');
        }
        return response($returnData);

    }

    //###############################################################
    //Function Name : deleteClients
    //Author : Himanshu <himanshuvarun@gmail.com>
    //Purpose : To delete a client
    //In Params : client id to be deleted
    //Return : Success message for deletion
    //###############################################################
    public function deleteClients($id)
    {
        try
        {
            $returnData = UtilityController::Setreturnvariables();  // Global declaration of response and input parameters.

            if($id != '')
            {
                $client = Clients::find($id);
                if($client)
                {
                    $client->destroy($id);
                    $returnData = UtilityController::Generateresponse(1,'CLIENT_DELETED', Response::HTTP_OK);
                } else {
                    $returnData = UtilityController::Generateresponse(0,'NO_DATA_FOUND', Config('constant.standard_response_values.NO_DATA_FOUND'));
                }

            }
        }
        catch (\Exception $e) {
            $sendExceptionMail = UtilityController::Sendexceptionmail($e);
            $returnData = UtilityController::Generateresponse(0,$e->getMessage(), Response::HTTP_BAD_REQUEST, '');
        }
        return response($returnData);

    }

    //###############################################################
    //Function Name : getClientsintegrations
    //Author : Himanshu <himanshuvarun@gmail.com>
    //Purpose : To get specific fields of selected client.
    //In Params : client id to get details
    //Return : Integrations fields of the specific client.
    //###############################################################
    public function getClientsintegrations($id)
    {
        try
        {
            $returnData = UtilityController::Setreturnvariables();  // Global declaration of response and input parameters.
            if($id != '')
            {
                $clientData = Clients::select('xero_id','brightcove_account','brightcove_secret','brightcove_id','slack_token',
                                        'viostream_api_username','viostream_access_key','wistia_api_token','vidyard_api_token')
                                ->where("client", "=", $id)
                                ->get()->toArray();
                if(!empty($clientData))     //  Check if client object is created
                {
                    $responseData = $clientData;
                    $returnData = UtilityController::Generateresponse(1,'CLIENT_INTEGRATION_DATA', Response::HTTP_OK, $responseData);
                }
                else {      // else, send No data found error message.
                    $returnData = UtilityController::Generateresponse(0,'NO_DATA_FOUND', Config('constant.standard_response_values.NO_DATA_FOUND'));   
                }
            }
            else {  //  If $id is not passed.
                $returnData = UtilityController::Generateresponse(0,'GENERAL_ERROR', Response::HTTP_BAD_REQUEST);
            }
        }
        catch (\Exception $e) {
            $sendExceptionMail = UtilityController::Sendexceptionmail($e);
            $returnData = UtilityController::Generateresponse(0,$e->getMessage(), Response::HTTP_BAD_REQUEST, '');
        }

        return response($returnData);

    }

    //###############################################################
    //Function Name : getClientsintegrations
    //Author : Himanshu <himanshuvarun@gmail.com>
    //Purpose : To get specific fields of selected client.
    //In Params : client id to get details
    //Return : Integrations fields of the specific client.
    //###############################################################
    public function getClientbranding($id)
    {
        try
        {
            $returnData = UtilityController::Setreturnvariables();  // Global declaration of response and input parameters.
            if($id != '')
            {
                $clientBrandData = Brand::select('*')
                                ->where("clientId", "=", $id)
                                 ->with([
                                    'brandfiles' => function ($query) {
                                        $query->select('file','state','sequence','bytes','starts','start','finish',
                                                        'thumb_update','format','filename','player','ingested','trimming',
                                                        'captions_id','status','client','uploaded','thumbStatus','fileKey',
                                                        'comments');
                                    },
                                ])
                                ->get()->toArray();

                if(!empty($clientBrandData))     //  Check if model object is created
                {
                    $responseData = $clientBrandData;
                    $returnData = UtilityController::Generateresponse(1,'CLIENT_BRAND_DATA', Response::HTTP_OK, $responseData);
                }
                else {      // else, send No data found error message.
                    $returnData = UtilityController::Generateresponse(0,'NO_DATA_FOUND', Config('constant.standard_response_values.NO_DATA_FOUND'));   
                }
            }
            else {  //  If $id is not passed.
                $returnData = UtilityController::Generateresponse(0,'GENERAL_ERROR', Response::HTTP_BAD_REQUEST);
            }
        }
        catch (\Exception $e) {
            $sendExceptionMail = UtilityController::Sendexceptionmail($e);
            $returnData = UtilityController::Generateresponse(0,$e->getMessage(), Response::HTTP_BAD_REQUEST, '');
        }

        return response($returnData);

    }

    //###############################################################
    //Function Name : postCreateclientbranding
    //Author : Himanshu <himanshuvarun@gmail.com>
    //Purpose : To create new client branding
    //In Params : clients branding parameters
    //Return : Newly generated client branding fields.
    //###############################################################
    public function postCreateclientbranding(Request $request, $id)
    {
        try
        {
            if($id != '')
            {
                $returnData = UtilityController::Setreturnvariables();  // Global declaration of response and input parameters.
                $inputData = $request->all();

                // Defining validation rules
                $rule = array(
                    'title'     => 'required|max:500',
                    'notes'     => 'required|max:255|string',
                    'color'     => 'required|string'
                );

                $validator = \Validator::make($inputData,$rule);    // Execute validation.
                if ($validator->fails())
                {
                    // $validationError = $validator->messages()->all();
                    $returnData = UtilityController::Generateresponse(0,'VALIDATION_ERROR',Response::HTTP_BAD_REQUEST, '', $validator);
                    
                }else {
                    $inputData['clientId'] = $id;   //  Adding $id (coming from url) into input array.
                    $data = UtilityController::Makemodelobject($inputData, 'Brand', 'brand');
                    if(isset($data->brand))
                    {
                        $responseData['brand'] = $data->brand;
                        $responseData['title'] = $data->title;
                        $responseData['notes'] = $data->notes;
                        $responseData['color'] = $data->color;
                        $returnData = UtilityController::Generateresponse(1,'CLIENT_BRAND_CREATED', Response::HTTP_OK, $responseData);
                    }
                }
            }else {  //  If $id is not passed.
                $returnData = UtilityController::Generateresponse(0,'GENERAL_ERROR', Response::HTTP_BAD_REQUEST);
            }
        }
        catch (\Exception $e) {
            $sendExceptionMail = UtilityController::Sendexceptionmail($e);
            $returnData = UtilityController::Generateresponse(0,$e->getMessage(), Response::HTTP_BAD_REQUEST, '');
        }

        return response($returnData);
    }

}
