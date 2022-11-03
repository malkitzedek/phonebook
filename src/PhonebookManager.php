<?php

namespace Novostruev;

use Novostruev\phonebook\{ExternalPhonebook, InternalPhonebook, Phonebook};

class PhonebookManager
{
    public static function getInstance(string $type): Phonebook
    {
        switch ($type) {
            case 'internal':
                return new InternalPhonebook(App::$db);
            case 'external':
                return new ExternalPhonebook(App::$db);
            default:
                return new Phonebook(App::$db);
        }
    }
}
