<?php
/**
 * This file is part of PHPPresentation - A pure PHP library for reading and writing
 * presentations documents.
 *
 * PHPPresentation is free software distributed under the terms of the GNU Lesser
 * General Public License version 3 as published by the Free Software Foundation.
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code. For the full list of
 * contributors, visit https://github.com/PHPOffice/PHPPresentation/contributors.
 *
 * @link        https://github.com/PHPOffice/PHPPresentation
 * @copyright   2009-2015 PHPPresentation contributors
 * @license     http://www.gnu.org/licenses/lgpl.txt LGPL version 3
 */

namespace PhpOffice\PhpPresentation\Shape\Chart;

/**
 * \PhpOffice\PhpPresentation\Shape\Chart\Axis
 */
class Marker
{
    public const SYMBOL_CIRCLE = 'circle';
    public const SYMBOL_DASH = 'dash';
    public const SYMBOL_DIAMOND = 'diamond';
    public const SYMBOL_DOT = 'dot';
    public const SYMBOL_NONE = 'none';
    public const SYMBOL_PLUS = 'plus';
    public const SYMBOL_SQUARE = 'square';
    public const SYMBOL_STAR = 'star';
    public const SYMBOL_TRIANGLE = 'triangle';
    public const SYMBOL_X = 'x';

    public static $arraySymbol = array(
        self::SYMBOL_CIRCLE,
        self::SYMBOL_DASH,
        self::SYMBOL_DIAMOND,
        self::SYMBOL_DOT,
        self::SYMBOL_NONE,
        self::SYMBOL_PLUS,
        self::SYMBOL_SQUARE,
        self::SYMBOL_STAR,
        self::SYMBOL_TRIANGLE,
        self::SYMBOL_X
    );

    /**
     * @var string
     */
    protected $symbol = self::SYMBOL_NONE;

    /**
     * @var int
     */
    protected $size = 5;

    /**
     * @return string
     */
    public function getSymbol()
    {
        return $this->symbol;
    }

    /**
     * @param string $symbol
     * @return $this
     */
    public function setSymbol($symbol = self::SYMBOL_NONE)
    {
        $this->symbol = $symbol;
        return $this;
    }

    /**
     * @return int
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * @param int $size
     * @return $this
     */
    public function setSize($size = 5)
    {
        $this->size = $size;
        return $this;
    }
}
