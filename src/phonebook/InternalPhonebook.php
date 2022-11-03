<?php
/**
 * С помощью Небес!
 *
 * @copyright   2020 Novostruev Ivan - rusmatrix@gmail.com
 */

namespace Novostruev\phonebook;

use Novostruev\Helper;

class InternalPhonebook extends Phonebook
{
    const VIEW_FILENAME = 'internal.php';

    protected function outputHook()
    {
        //Helper::checkIP();
    }
}
