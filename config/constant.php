<?php
// use Illuminate\Support\Facades\Config;


// $codes = Array(
//         200 => 'OK',
//         204 => 'No data found'
//         400 => 'Bad Request',
//         401 => 'Unauthorized',
//         402 => 'Payment Required',
//         403 => 'Forbidden',
//         404 => 'Not Found',
//         500 => 'Internal Server Error',
//         501 => 'Not Implemented',
//     );

return [

    'standard_response_values' => [
        'SUCCESS' => 1,
        'FAILURE' => 0,
        'STATUS_CODE_100' => 100,
        'STATUS_CODE_101' => 101,
        'STATUS_CODE_200' => 200,
        'STATUS_CODE_201' => 201,
        'STATUS_CODE_202' => 202,
        'STATUS_CODE_401' => 401,
        'NO_DATA_FOUND'   => 204
    ],

    'messages'                 => [
        'TRY_AGAIN'                 => 'Please try again!',
        'GENERAL_ERROR'             => 'Oops! Something just went wrong. Please try again later.',
        'GENERAL_SUCCESS'           => 'Done!',
        'GENERAL_NO_CHANGES'        => 'You have made no changes.',
        'GENERAL_NO_DATA'           => 'No data available.',
        'INVALID_CREDENTIALS'              => 'Invalid Credentials!',
        'TOKEN_GENERATION_FAILED'          => 'Could not create token.',
        'TOKEN_GENERATED'                  => 'Token generated.',
        'VALIDATION_ERROR'                 => 'Validation error occured,Please fill all the required fields.',
        'NO_DATA_FOUND'                    => 'Oops, No data found!',
        'CLIENT_CREATED'                   => 'New client has been created.',
        'CLIENT_LIST'                      => 'All clients list.',
        'CLIENT_INFO_UPDATE_SUCCESS'       => 'Client info successfully updated.',
        'CLIENT_DELETED'                   => 'Client deleted successfully.',
        'PACKAGE_LIST'                     => 'All packages list.'
    ],

    'paths' => [

        'BASEURL_MANAGE'                  => '/manage/',
        'DIR_UPLOADS'                     => '/uploads/',
        'LOGO_FOLDER_PATH'                => '/uploads/logo/',
        'LOGO_PATH'                       => '/public/uploads/logo/',
        'ASSETS_IMAGE'                    => '/resources/assets/images/',
        'ADMIN_PROFILE_PATH'              => '/profile_pics/',
        'USER_PHOTO_PATH'                 => '/uploads/staff/photos/',
        'USER_PHOTO_URL'                  => '/public/uploads/staff/photos/',
        'BASEURL_MANAGE'                  => '/manage/',
        'DIR_UPLOADS'                     => '/uploads/',
        'LOGO_FOLDER_PATH'                => '/uploads/logo/',
        'ASSETS_IMAGE'                    => '/resources/assets/images/',
        'ASSETS_FRONTHOME_IMAGE'          => '/resources/assets/images/front/',
        'ASSETS_FRONTHOME_IMAGE'          => '/resources/assets/images/front/',

    ],

    'config_variables' => [
        'FROM_EMAIL'                       => 'noreply@example.com',
        'FROM_EMAIL_NAME'                  => 'Company Name',
        'EXCEPTION_MAIL_SUBJECT'           => 'Company Name: Exception Caught (Local Server)',
        'VIEW_FILE_EXCEPTION'              => 'emails.exception',
        'VIEW_FILE_SERVICE_FEE_PAID'       => 'emails.servicefeepaid',
        'VIEW_FILE_RECEIPT_ACCEPTED'       => 'emails.receiptaccepted',
        'VIEW_FILE_RECEIPT_REJECTED'       => 'emails.receiptrejected',
        'VIEW_FILE_CONTACT_FORM'           => 'emails.contactform',
        'PER_PAGE_RECORDS'                 => '9'
    ],
    'send_mail_to'             => [
        "himanshuvarun@gmail.com"
    ],
    'send_contact_mail_to'     => [        
        "himanshuvarun@gmail.com"
    ],
];
