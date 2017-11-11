<?php
/**
 * Short description for file
 *
 * PHP version 5
 *
 * LICENSE: This source file is subject to version 3.01 of the PHP license
 * that is available through the world-wide-web at the following URI:
 * http://www.php.net/license/3_01.txt.  If you did not receive a copy of
 * the PHP License and are unable to obtain it through the web, please
 * send a note to license@php.net so we can mail you a copy immediately.
 *
 * @category   Utility Components
 * @package    UtilityController
 * @author     Himanshu Upadhyay <himanshuvarun@gmail.com>
 * @copyright  2017 Himanshu Upadhyay
 * @license    http://www.php.net/license/3_01.txt  PHP License 3.01
 * @version    Git Branch: issue_7_project_module
 * @since      File available since Release 1.0.0
 * @deprecated N/A
 */
/*
 * Place includes controller for project module.
 */

/**
Pre-Load all necessary library & name space
 */

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Mail;
use DB;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Schema;
use App\User;
use App\Clients;

// Load models

class UtilityController extends BaseController
{
    public function __construct()
    {
    }

    protected $ident;
    //###############################################################
    //Function Name : Sendexceptionmail
    //Author : Himanshu <himanshuvarun@gmail.com>
    //Purpose : To send the mail when any Exception is catched
    //In Params : Exception object only. (From controller)
    //Return : 'true' when email will be sent successfully.
    //###############################################################
    public static function Sendexceptionmail($object)
    {
        $viewFile = Config('constant.config_variables.VIEW_FILE_EXCEPTION');
        $fromEmail = Config('constant.config_variables.FROM_EMAIL');
        $fromEmailName = Config('constant.config_variables.FROM_EMAIL_NAME');
        $recepients = Config('constant.send_mail_to');
        $mailSubject = Config('constant.config_variables.EXCEPTION_MAIL_SUBJECT');

        $dataArray           = array();
        $dataArray['Error']  = $object->getMessage();
        $dataArray['File']   = $object->getFile();
        $dataArray['Line']   = $object->getLine();
        $dataArray['Source'] = isset($_SERVER['SYSTEM_NAME']) ? $_SERVER['SYSTEM_NAME'] : '';

        //Check Condition if not run from local server
        if (strpos(url('/'), 'localhost') == true) {
            Mail::send($viewFile, $dataArray, function ($message) use ($dataArray, $viewFile, $fromEmail, $fromEmailName, $recepients, $mailSubject) {
                $message->from($fromEmail, $fromEmailName);
                $message->to($recepients);
                $message->subject($mailSubject);
            });
        }
        return true;
    }

    //###############################################################
    //Function Name : Getlatlongfromaddress
    //Author : Himanshu <himanshuvarun@gmail.com>
    //Purpose : To get the Latitude and Longitude of Address From Google API
    //In Params : Address As get from the form
    //Return : The Latitude and Longitude of the address
    //###############################################################
    public static function Getlatlongfromaddress($Address)
    {
        //Define The return Latitude and Longitude
        $returnArray              = array();
        $returnArray['latitude']  = "-1.184203";
        $returnArray['longitude'] = "36.749285";

        $prepareAddress = str_replace(' ', '+', $Address);
        $url            = "https://maps.googleapis.com/maps/api/geocode/json?address=" . $prepareAddress;
        $ch             = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_PROXYPORT, 3128);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        $response = curl_exec($ch);
        curl_close($ch);
        $output = json_decode($response);

        if (!empty($output->results)) {
            $returnArray['latitude']  = $output->results[0]->geometry->location->lat;
            $returnArray['longitude'] = $output->results[0]->geometry->location->lng;
        }
        return $returnArray;
    }
    //###############################################################
    //Function Name : Imageexist
    //Author : Himanshu <himanshuvarun@gmail.com>
    //Purpose : TO Check whether the image exists or not
    //In Params : File name and physical path and Url path and placeholder image path
    //Return : If Image exists then full path else placeholder image path
    //###############################################################
    public static function Imageexist($imageName, $path, $urlPath, $placeHolderPath)
    {
        $fileName         = $urlPath . $imageName;
        $physicalFileName = $path . $imageName;
        if (file_exists($physicalFileName) && $imageName != '') {
            $fileName = $fileName;
        } else {
            $fileName = $placeHolderPath;
        }
        return $fileName;
    }

    //###############################################################
    //Function Name : Getcountrylist
    //Author : Himanshu <himanshuvarun@gmail.com>
    //Purpose : To get the country list from the Database
    //In Params :
    //Return : The list of all the Countries
    //###############################################################
    public static function Getcountrylist()
    {
        try {
            $returnData            = array();
            $returnData['success'] = Config('constant.standard_response_values.FAILURE');
            $returnData['data']    = array();

            $countryList = Countries::all()->toArray();
            if (!empty($countryList)) {
                $returnData['success'] = Config('constant.standard_response_values.SUCCESS');
                $returnData['data']    = $countryList;
            } else {
                $returnData['success'] = Config('constant.standard_response_values.FAILURE');
                $returnData['data']    = array();
            }
        } catch (\Exception $e) {
            //Code to send mail starts here
            $sendExceptionMail = UtilityController::Sendexceptionmail($e, Config('constant.config_variables.VIEW_FILE_EXCEPTION'), Config('constant.config_variables.FROM_EMAIL'), Config('constant.config_variables.FROM_EMAIL_NAME'), Config('constant.send_mail_to'), Config('constant.config_variables.EXCEPTION_MAIL_SUBJECT'));
            //Code to send mail ends here

        }
        //Return the data in JSON format
        return response()->json($returnData);
    }

    //###############################################################
    //Function Name : Setreturnvariables
    //Author : Himanshu <himanshuvarun@gmail.com>
    //Purpose : To initialize the return variables
    //In Params : N/A
    //Return : Array of return response
    //###############################################################
    public static function Setreturnvariables()
    {
        $returnData = array();

        $returnData['status']   = Config('constant.standard_response_values.FAILURE');
        $returnData['message']  = array(
                                            "general" => Config('constant.messages.TRY_AGAIN'),
                                            "fields" => [],
                                            "messages" => []
                                        );
        $returnData['code']     = Config('constant.standard_response_values.STATUS_CODE_100');
        $returnData['data']     = array();

        return $returnData;
    }

    //###############################################################
    //Function Name : Generateresponse
    //Author : Himanshu <himanshuvarun@gmail.com>
    //Purpose : To initialize the return variables
    //In Params : N/A
    //Return : Array of return response
    //###############################################################
    public static function Generateresponse($type, $message, $statusCode, $responseData = array(), $validationObj = array())
    {
        $returnData = array();

        $returnData['status']   = ($type)? Config('constant.standard_response_values.SUCCESS') : Config('constant.standard_response_values.FAILURE');
        if(!empty($validationObj))
        {
            $returnData['message']  = array(
                                            "general" => Config('constant.messages.VALIDATION_ERROR'),
                                            "fields" => $validationObj->errors()->keys(),
                                            "messages" => $validationObj->messages()->all()
                                        );
        }
        else if(Config('constant.messages.'.$message) != '')
        {
            $returnData['message']  = array(
                                            "general" => Config('constant.messages.'.$message),
                                            "fields" => [],
                                            "messages" => []
                                        );
        }
        else
        {
            $returnData['message']  = array(
                                            "general" => $message,
                                            "fields" => [],
                                            "messages" => []
                                        );
        }
        $returnData['code']     = $statusCode;
        $returnData['data']     = ($responseData != '')? $responseData : array();
        
        return $returnData;
    }

    //###############################################################
    //Function Name : Setvalidationresponse
    //Author : Himanshu <himanshuvarun@gmail.com>
    //Purpose : To initialize the return variables when validation exception occurs
    //In Params : N/A
    //Return : Array of return response
    //###############################################################
    public static function Setvalidationresponse($type)
    {
        $error = $type->original;
        foreach($error as $k => $v)
        {
            $error[$k] = $v[0]->classes;
        }
        return $error;
    }

    //###############################################################
    //Function Name : Setuserpolicy
    //Author : Himanshu <himanshuvarun@gmail.com>
    //Purpose : To initialize the return array with access levels values
    //In Params : User object
    //Return : Array of acl values.
    //###############################################################
    public static function Setuserpolicy($user)
    {
        return array (
            'user' =>
                array (
                    'userProfile' =>
                        array (
                            'view' => true,
                        ),
                    'userDashboard' =>
                        array (
                            'view' => true,
                        ),
                    'notifications' =>
                        array (
                            'view' => true,
                        ),
                    'viewOwn' => true,
                    'viewAll' => UtilityController::PolicyCheck(Config('constant.acl_values.APP_POLICY_CLIENTS'), $user),
                    'updateOwn' => true,
                    'updateAll' => UtilityController::PolicyCheck(Config('constant.acl_values.APP_POLICY_CLIENTS'), $user),
                ),
            'client' =>
                array (
                    'viewOwn' => true,
                    'viewAll' => UtilityController::PolicyCheck(Config('constant.acl_values.APP_POLICY_CLIENTS'), $user),
                    'updateOwn' => UtilityController::PolicyCheck(Config('constant.acl_values.APP_POLICY_USERS'), $user),
                    'updateAll' => UtilityController::PolicyCheck(Config('constant.acl_values.APP_POLICY_CLIENTS'), $user),
                    'create' => UtilityController::PolicyCheck(Config('constant.acl_values.APP_POLICY_CLIENTS'), $user),
                    'brand' =>
                        array (
                            'viewOwn' => true,
                            'viewAll' => UtilityController::PolicyCheck(Config('constant.acl_values.APP_POLICY_CLIENTS'), $user),
                            'updateOwn' => UtilityController::PolicyCheck(Config('constant.acl_values.APP_POLICY_USERS'), $user),
                            'updateAll' => UtilityController::PolicyCheck(Config('constant.acl_values.APP_POLICY_CLIENTS'), $user),
                        ),
                    'assets' =>
                        array (
                            'viewOwn' => true,
                            'viewAll' => UtilityController::PolicyCheck(Config('constant.acl_values.APP_POLICY_CLIENTS'), $user),
                            'updateOwn' => UtilityController::PolicyCheck(Config('constant.acl_values.APP_POLICY_USERS'), $user),
                            'updateAll' => UtilityController::PolicyCheck(Config('constant.acl_values.APP_POLICY_CLIENTS'), $user),
                        ),
                    'package' =>
                        array (
                            'viewOwn' => UtilityController::PolicyCheck(Config('constant.acl_values.APP_POLICY_USERS'), $user),
                            'viewAll' => UtilityController::PolicyCheck(Config('constant.acl_values.APP_POLICY_CLIENTS'), $user),
                            'updateOwn' => UtilityController::PolicyCheck(Config('constant.acl_values.APP_POLICY_USERS'), $user),
                            'updateAll' => UtilityController::PolicyCheck(Config('constant.acl_values.APP_POLICY_CLIENTS'), $user),
                        ),
                    'integrations' =>
                        array (
                            'viewOwn' => UtilityController::PolicyCheck(Config('constant.acl_values.APP_POLICY_USERS'), $user),
                            'viewAll' => UtilityController::PolicyCheck(Config('constant.acl_values.APP_POLICY_CLIENTS'), $user),
                            'updateOwn' => UtilityController::PolicyCheck(Config('constant.acl_values.APP_POLICY_USERS'), $user),
                            'updateAll' => UtilityController::PolicyCheck(Config('constant.acl_values.APP_POLICY_CLIENTS'), $user),
                        ),
                    'team' =>
                        array (
                            'viewAll' => UtilityController::PolicyCheck(Config('constant.acl_values.APP_POLICY_USERS'), $user),
                            'updateAll' => UtilityController::PolicyCheck(Config('constant.acl_values.APP_POLICY_USERS'), $user),
                            'createUser' => UtilityController::PolicyCheck(Config('constant.acl_values.APP_POLICY_USERS'), $user),
                            'createTeam' => UtilityController::PolicyCheck(Config('constant.acl_values.APP_POLICY_USERS'), $user),
                            'deleteUser' => UtilityController::PolicyCheck(Config('constant.acl_values.APP_POLICY_USERS'), $user),
                            'deleteTeam' => UtilityController::PolicyCheck(Config('constant.acl_values.APP_POLICY_USERS'), $user),
                            'user' =>
                                array (
                                    'viewAll' => true,
                                    'updateAll' => UtilityController::PolicyCheck(Config('constant.acl_values.APP_POLICY_USERS'), $user),
                                ),
                            'project' =>
                                array (
                                    'viewOpenAll' => true,
                                    'viewCompleteAll' => true,
                                    'viewInternalReviewAll' => true,
                                    'approveInternalReviewAll' => UtilityController::PolicyCheck(Config('constant.acl_values.APP_POLICY_ADOPTIONS'), $user),
                                    'viewClientReviewAll' => true,
                                    'approveClientReviewAll' => true,
                                    'deleteAll' => UtilityController::PolicyCheck(Config('constant.acl_values.APP_POLICY_USERS'), $user),
                                ),
                        ),
                    'user' =>
                        array (
                            'viewAll' => true,
                            'updateAll' => true,
                        ),
                    'project' =>
                        array (
                            'viewOpenAll' => true,
                            'viewCompleteAll' => true,
                            'viewInternalReviewAll' => true,
                            'approveInternalReviewAll' => UtilityController::PolicyCheck(Config('constant.acl_values.APP_POLICY_USERS'), $user),
                            'viewClientReviewAll' => true,
                            'approveClientReviewAll' => UtilityController::PolicyCheck(Config('constant.acl_values.APP_POLICY_USERS'), $user),
                            'deleteAll' => UtilityController::PolicyCheck(Config('constant.acl_values.APP_POLICY_USERS'), $user),
                        ),
                ),
            'team' =>
                array (
                    'viewOwn' => true,
                    'viewAll' => UtilityController::PolicyCheck(Config('constant.acl_values.APP_POLICY_CLIENTS'), $user),
                    'updateOwn' => true,
                    'updateAll' => UtilityController::PolicyCheck(Config('constant.acl_values.APP_POLICY_CLIENTS'), $user),
                    'createUser' => true,
                    'createTeam' => UtilityController::PolicyCheck(Config('constant.acl_values.APP_POLICY_USERS'), $user),
                    'deleteUser' => UtilityController::PolicyCheck(Config('constant.acl_values.APP_POLICY_CLIENTS'), $user),
                    'deleteTeam' => UtilityController::PolicyCheck(Config('constant.acl_values.APP_POLICY_CLIENTS'), $user),
                ),
            'help' =>
                array (
                    'trainingVideos' =>
                        array (
                            'view' => true,
                        ),
                    'faq' =>
                        array (
                            'view' => true,
                        ),
                    'scripts' =>
                        array (
                            'view' => true,
                        ),
                ),
            'library' =>
                array (
                    'updateOwn' => true,
                    'updateAll' => UtilityController::PolicyCheck(Config('constant.acl_values.APP_POLICY_CLIENTS'), $user),
                    'deleteOwn' => true,
                    'deleteAll' => UtilityController::PolicyCheck(Config('constant.acl_values.APP_POLICY_CLIENTS'), $user),
                ),
            'project' =>
                array (
                    'create' => true,
                    'viewOpenOwn' => true,
                    'viewOpenAll' => UtilityController::PolicyCheck(Config('constant.acl_values.APP_POLICY_ADOPTIONS'), $user),
                    'viewCompleteOwn' => true,
                    'viewCompleteAll' => UtilityController::PolicyCheck(Config('constant.acl_values.APP_POLICY_ADOPTIONS'), $user),
                    'viewInternalReviewOwn' => true,
                    'viewInternalReviewAll' => UtilityController::PolicyCheck(Config('constant.acl_values.APP_POLICY_ADOPTIONS'), $user),
                    'approveInternalReviewOwn' => true,
                    'approveInternalReviewAll' => UtilityController::PolicyCheck(Config('constant.acl_values.APP_POLICY_ADOPTIONS'), $user),
                    'viewClientReviewOwn' => true,
                    'viewClientReviewAll' => UtilityController::PolicyCheck(Config('constant.acl_values.APP_POLICY_ADOPTIONS'), $user),
                    'approveClientReviewOwn' => true,
                    'approveClientReviewAll' => UtilityController::PolicyCheck(Config('constant.acl_values.APP_POLICY_CLIENTS'), $user),
                    'deleteOwn' => true,
                    'deleteAll' => UtilityController::PolicyCheck(Config('constant.acl_values.APP_POLICY_ADOPTIONS'), $user),
                ),
        );
    }

    //###############################################################
    //Function Name : PolicyCheck
    //Author : Himanshu <himanshuvarun@gmail.com>
    //Purpose : Compares the user policy to a feature id. If the policy contains the feature, returns true otherwise false.
    //Return : boolean
    //###############################################################
    public static function PolicyCheck($feature, $user=null)
    {
        if($user)
        {
            $user = is_a($user, 'User') || is_a($user, 'UserObjectPolicy') ? $user : User::find($user->user);
            if(!$user)
            {
                return false;
            }
        }

        $result = ($user && ($user->policy & $feature )) ? true : false;
        return $result;
    }

    //###############################################################
    //Function Name : Makemodelobject
    //Author : Himanshu <himanshuvarun@gmail.com>
    //Purpose : Generic function to create model object to save()
    //Return : model object
    //###############################################################
    public static function Makemodelobject($data, $modelClassName, $primaryKey = 'id', $idToEdit = '')
    {
        try
        {
            if(!empty($data) && $modelClassName != '')
            {
                $modelClassName = 'App\\'.$modelClassName;  //  To make model object using string variable, use full reference of model class
                if($idToEdit == '') {    //  If id is passed to edit the record. (Update function)
                    $$modelClassName = new $modelClassName;
                }
                else {
                    $$modelClassName = $modelClassName::find($idToEdit);
                }

                if($$modelClassName != '')  //  If object created then proceed.
                {
                    // Getting all columns of the table.
                    $columns = Schema::getColumnListing($$modelClassName->getTable());

                    //  Looping through all the table columns to check all input values in $data variable.
                    foreach($columns as $k => $v) {
                        if(isset($data[$v]))
                            $$modelClassName->$v = $data[$v];
                    }

                    if($$modelClassName->save()) {
                        return $$modelClassName;
                    } else {
                        return false;
                    }
                }
                else{                       //  Else return false to controller.
                    return false;
                }
            }
        }
        catch (\Exception $e) {
            $sendExceptionMail = UtilityController::Sendexceptionmail($e);
            $returnData = UtilityController::Generateresponse(0,$e->getMessage(), Response::HTTP_BAD_REQUEST, '');
            return $returnData;
        }
    }

}
