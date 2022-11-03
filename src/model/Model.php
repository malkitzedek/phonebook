<?php
/**
 * С помощью Небес!
 *
 * @copyright   2020 Novostruev Ivan - rusmatrix@gmail.com
 */

namespace Novostruev\model;

abstract class Model {
    /**
     * @return array
     */
    abstract public function getInsertedValues(): array;

    /**
     * @return string
     */
    abstract public function getInsertSql(): string;
}
