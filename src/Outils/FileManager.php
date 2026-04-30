<?php

/**
 * Fichier : FileManager.php
 * Auteur  : Samuel Tido Kaze
 * Date    : 22.04.2026
 * Projet  : TPI VetiSoin
 * Role    : Media Manager
 */

namespace App\Outils;


class FileManager
{


    public static function checkMedia($file)
    {


        if ($file['size'] == 0 || $file['size'] > 5000000)
            return ['success' => false, 'message' => 'Taille de fichier trop grande (max 5 Mo)'];

        $uploadDir = __DIR__ . '/../../public/uploads/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0775, true);

        $finalName   = time() . '_' . $file['name'];
        $destination = $uploadDir . $finalName;
        $extension   = strtolower(pathinfo($finalName, PATHINFO_EXTENSION));


        $allowed = ['jpg', 'jpeg', 'png'];

        if (!in_array($extension, $allowed)) {
            return ['success' => false, 'message' => 'Extension non autorisée. Formats acceptés :JPG, PNG'];
        }

        $nameParts = explode('.', $finalName);
        if (count($nameParts) !== 2) {
            return ['success' => false, 'message' => 'Nom de fichier invalide (une seule extension autorisée).'];
        }


        $allowedMimes = [
            'jpg'  => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png'  => 'image/png',
        ];
        $realMime = mime_content_type($file['tmp_name']);
        if ($realMime !== $allowedMimes[$extension]) {
            return ['success' => false, 'message' => 'Le contenu du fichier ne correspond pas à son extension.'];
        }

        if (!move_uploaded_file($file['tmp_name'], $destination)) {
            return ['success' => false, 'message' => 'Échec du déplacement du fichier.'];
        }

        return [
            'success' => true,
            'filename' => '/uploads/' . $finalName
        ];
    }
}
