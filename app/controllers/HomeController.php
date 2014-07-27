<?php
class HomeController extends BaseController
{
    public function showWelcome()
    {
        return View::make('hello');
    }

    public function doLogin()
    {

        // validate the info, create rules for the inputs
        $rules = array(
            'email' => 'required|email', // make sure the email is an actual email
            'password' => 'required'
        );

        // run the validation rules on the inputs from the form
        $validator = Validator::make(Input::all() , $rules);

        // if the validator fails, redirect back to the form
        if ($validator->fails())
        {
            return Redirect::to('/')->withErrors($validator) // send back all errors to the login form
            ->withInput(Input::except('password')); // send back the input (not the password) so that we can repopulate the form
        }
        else
        {

            // create our user data for the authentication
            $userdata = array(
                'mail' => Input::get('email') ,
                'password' => Input::get('password')
            );

            // attempt to do the login
            if (Auth::attempt($userdata))
            {
                return Redirect::to('home');
            }
            else
            {
                // validation not successful, send back to form
                return Redirect::to('/')->withErrors('Combinazione email/password errata.');
            }
        }
    }

    public function doLogout()
    {
        Auth::logout(); // log the user out of our application
        return Redirect::to('/'); // redirect the user to the login screen
    }

    public function newUser()
    {
        $rules = array(
            'mail' => array(
                'required',
                'email',
                'unique:qa_users'
            ) ,
            'password' => array(
                'required',
                'min:6'
            ) ,
            'name' => array(
                'required'
            ) ,
        );

        $validation = Validator::make(Input::all() , $rules);
        if ($validation->fails())
        {
            return Redirect::to('registration')->withInput()->withErrors($validation);
        }

        $user = new User;
        $user->mail = Input::get('mail');
        $user->password = Hash::make(Input::get('password'));
        $user->name = Input::get('name');
        $user->save();
        return Redirect::to('/')->with('success', 'Registrazione effettuata con successo!');
    }
}