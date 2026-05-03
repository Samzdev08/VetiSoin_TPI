<?php
/**
 * Fichier : Validator.php
 * Auteur  : Samuel Tido Kaze
 * Date    : 22.04.2026
 * Projet  : TPI VetiSoin
 * Role    : Validation des entrees utilisateur
 */

namespace App\Outils;


class Validator
{
    public static function isEmail($email)
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    public static function isNotEmpty($value)
    {
        return isset($value) && trim($value) !== '';
    }

    public static function minLength($value, $length)
    {
        return strlen($value) >= $length;
    }
    public static function maxLength($value, $length)
    {
        return strlen($value) <= $length;
    }

    public static function isNumeric($value)
    {
        return is_numeric($value);
    }
}