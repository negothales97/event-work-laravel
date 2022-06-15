<?php

/**
 * retorna o usuário logado no momento ou alguma propriedade dele
 * @param $property
 *
 * @return User|null
 */
function user($property = null)
{
    $user = auth()->user()
        ?? request()->user();

    return !is_null($user) && !is_null($property) ? $user->{$property} : $user;
}
