<?php
namespace App\Service;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use App\Entity\User;

class JwtService
{
    private string $privateKey;
    private string $publicKey;

    public function __construct(string $privateKey, string $publicKey)
    {
        // Chargement des clés RSA (chemin vers fichier ou clé en chaîne de caractères)
        if (is_file($privateKey)) {
            $this->privateKey = file_get_contents($privateKey);
        } else {
            $this->privateKey = $privateKey;
        }

        if (is_file($publicKey)) {
            $this->publicKey = file_get_contents($publicKey);
        } else {
            $this->publicKey = $publicKey;
        }

      
    }

    public function generateToken(User $user): string
    {
        // Chargement des données dans le payload du JWT
        $payload = [
            'email' => $user->getEmail(),
            'exp' => time() + 3600, // Expire dans 1 heure
        ];
  
        // Encodage du token avec une clé secrète et l'algorithme HS256
        $secretKey = 's3cureR@nd0mStr!ng123'; // Clé secrète partagée
        $token = JWT::encode($payload, $secretKey, 'HS256');
    
        // Journalisation du token généré pour le débogage
        error_log('Generated Token: ' . $token);
    
        return $token;
    }
    

    public function decodeToken(string $token): array
    {
        try {
            // Décodage du token avec la même clé secrète et l'algorithme HS256
            $secretKey = 's3cureR@nd0mStr!ng123'; // Clé secrète partagée
            $decoded = JWT::decode($token, new Key($secretKey, 'HS256'));
            
            // Conversion du token décodé en tableau
            return (array) $decoded;
        } catch (\Exception $e) {
            // Gestion des erreurs en cas de token invalide ou de problème de décodage
            error_log('Error decoding token: ' . $e->getMessage());
            throw new \Exception('Invalid token');
        }
    }
    

    private function isValidKey(string $key): bool
    {
        
        // Journalisation du contenu de la clé publique pour le débogage
        error_log('Public Key: ' . $key);
        
        // Vérification de la validité de la clé
        return is_string($key) && preg_match('/^-----BEGIN PUBLIC KEY-----.*-----END PUBLIC KEY-----/s', $key);
    }
    
}
