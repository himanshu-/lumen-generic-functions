<?php namespace App;

use Illuminate\Support\Facades\Hash;

class TransitionalHasher extends \Illuminate\Hashing\BcryptHasher {

    public function check($value, $hashedValue, array $options = array())
    {
        // If check fails, is it a hash from a previous version?
        if ( !password_verify($value, $hashedValue) )
        {

            $user = User::where('email', $_POST['email'])->first();
            // Attempt to match user using previous version hash check
            $oldHashCheck = hash('sha256', $user->password_salt.$value);
            if($oldHashCheck !== $user->password_encrypted) {
                $user = null;
            }

            if ($user)  // We found a user with a matching hash
            {
                // Update the password to Laravel's Bcrypt hash
                $user->password_encrypted = Hash::make($value);
                $user->save();

                // Log in the user
                return true;
            }
        }

        return password_verify($value, $hashedValue);
    }

}