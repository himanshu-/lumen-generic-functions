<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Http\Controllers\Controller;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Exception\HttpResponseException;

use App\Http\Controllers\UtilityController;
use Illuminate\Support\Facades\Config;

class AuthController extends Controller
{
    /**
     * Handle a login request to the application.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    private $clientPrivateKey = "mTmR1NohaEYzS4x385AZrybLhXtivmFyWG7cUeE+";
    public $userPolicy;

    //###############################################################
    //Function Name : postLogin
    //Author : Himanshu <himanshuvarun@gmail.com>
    //Purpose : User authentication and JWtoken generation.
    //Return : JWtoken and user access permission values.
    //###############################################################
    public function postLogin(Request $request)
    {
        // Global declaration of response and input parameters.
        $returnData = UtilityController::Setreturnvariables();
        $inputData = $request->all();

        try
        {
            // Defining validation rules
            $rule = array(
                'email'     => 'required|email|max:255',
                'password'  => 'required'
            );
        
            // Execute validation.
            $validator = \Validator::make($inputData,$rule);
            if ($validator->fails())
            {
                // $validationError = $validator->messages()->all();
                $returnData = UtilityController::Generateresponse(0,'VALIDATION_ERROR',Response::HTTP_BAD_REQUEST, '', $validator);
                
            }else
            {
                if (!$token = JWTAuth::attempt($request->only('email', 'password'))) {    //TOKEN_GENERATION_FAILED
                    $returnData = UtilityController::Generateresponse(0,'INVALID_CREDENTIALS', Response::HTTP_UNAUTHORIZED);
                }
                else //TOKEN_GENERATED
                {
                    $user = $request->user();   //  Getting user object
                    $responseData['token'] = $token;
                    $returnData = UtilityController::Generateresponse(1,'TOKEN_GENERATED', Response::HTTP_OK, $responseData);
                }
            }

        }
        catch (\Exception $e) {
            $sendExceptionMail = UtilityController::Sendexceptionmail($e);
            $returnData = UtilityController::Generateresponse(0,$e->getMessage(), Response::HTTP_BAD_REQUEST, '');
        }
        // All good so return the token
        return response($returnData);
    }

    /**
     * Invalidate a token.
     *
     * @return \Illuminate\Http\Response
     */
    public function deleteInvalidate()
    {
        $token = JWTAuth::parseToken();

        $token->invalidate();

        return new JsonResponse(['message' => 'token_invalidated']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\Response
     */
    public function patchRefresh()
    {
        $token = JWTAuth::parseToken();

        $newToken = $token->refresh();

        return new JsonResponse([
            'message' => 'token_refreshed',
            'data' => [
                'token' => $newToken
            ]
        ]);
    }

    /**
     * Get authenticated user.
     *
     * @return \Illuminate\Http\Response
     */
    public function getUser()
    {
        return new JsonResponse([
            'message' => 'authenticated_user',
            'data' => JWTAuth::parseToken()->authenticate()
        ]);
    }
}
