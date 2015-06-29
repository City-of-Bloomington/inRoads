<?php
/**
 * Provides markup for button links
 *
 * @copyright 2014-2015 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
namespace Application\Templates\Helpers;

use Blossom\Classes\Helper;

class ButtonLink extends Helper
{
    const SIZE_BUTTON = 'button';
    const SIZE_ICON   = 'icon';

    public function buttonLink($url, $label, $type, $size=self::SIZE_BUTTON)
    {
        return "<a href=\"$url\" class=\"$size $type\">$label</a>";
    }
}
