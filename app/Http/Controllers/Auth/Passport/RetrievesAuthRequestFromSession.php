<?php

namespace App\Http\Controllers\Auth\Passport;

use Exception;
use Illuminate\Http\Request;
use Laravel\Passport\Bridge\User;
use League\OAuth2\Server\RequestTypes\AuthorizationRequest;

trait RetrievesAuthRequestFromSession
{
    /**
     * Get the authorization request from the session.
     *
     * @param Request $request
     * @return AuthorizationRequest
     * @throws \Exception
     */
    protected function getAuthRequestFromSession(Request $request)
    {
        return tap($request->session()->get('authRequest'), function ($authRequest) use ($request) {
            if (! $authRequest) {
                throw new Exception('Authorization request was not present in the session.');
            }

            $authRequest->setUser(new User($request->user()->getKey()));

            $authRequest->setAuthorizationApproved(true);
        });
    }
}
