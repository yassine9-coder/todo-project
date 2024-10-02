<?php
// src/Command/GenerateKeysCommand.php
// src/Command/GenerateKeysCommand.php

namespace App\Command;

use phpseclib3\Crypt\RSA;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateKeysCommand extends Command
{
    protected static $defaultName = 'app:generate-keys';

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // Vérifie si le répertoire existe
        $dir = 'config/jwt';
        if (!is_dir($dir)) {
            $output->writeln("<error>Le répertoire $dir n'existe pas.</error>");
            return Command::FAILURE;
        }

        // Générer une paire de clés
        $key = RSA::createKey(2048);

        // Enregistrer la clé privée
        $privateKeyPath = "$dir/private.pem";
        if (file_put_contents($privateKeyPath, (string) $key->toString('PKCS1')) === false) {
            $output->writeln("<error>Échec de l'écriture dans $privateKeyPath.</error>");
            return Command::FAILURE;
        }

        // Enregistrer la clé publique
        $publicKeyPath = "$dir/public.pem";
        if (file_put_contents($publicKeyPath, (string) $key->toString('PKCS8')) === false) {
            $output->writeln("<error>Échec de l'écriture dans $publicKeyPath.</error>");
            return Command::FAILURE;
        }

        $output->writeln('Clés générées avec succès.');
        return Command::SUCCESS;
    }
}

