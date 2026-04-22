<?php
/**
 * Fichier : Csrf.php
 * Auteur  : Samuel Tido Kaze
 * Date    : 22.04.2026
 * Projet  : TPI VetiSoin
 * Role    : Generation et verification des tokens CSRF
 */

namespace App\Outils;

class Csrf
{
    public static function generate()
    {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        return $_SESSION['csrf_token'];
    }

    public static function check($token)
    {
        if(isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token)){

            return true;
        }

        return false;
    }
}