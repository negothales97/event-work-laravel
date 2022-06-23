<?php

/**
 * retorna o usuário logado no momento ou alguma propriedade dele
 * @param $property
 *
 * @return mixed
 */
function user($property = null)
{
    // $user = auth()->user() ?? request()->user();
    $user = App\Models\User::first();

    return !is_null($user) && !is_null($property) ? $user->{$property} : $user;
}
function company($property = null)
{
    $company = user()->company;
    return !is_null($company) && !is_null($property) ? $company->{$property} : $company;
    # code...
}
