<?php
/**
 * @copyright 2013-2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
namespace Application\Templates\Helpers;

use Blossom\Classes\Helper;

class SaveAndCancelButtons extends Helper
{
	public function saveAndCancelButtons($cancelURL)
	{
        $helper = $this->template->getHelper('buttonLink');

        $buttons = "
        <button type=\"submit\"><span class=\"fa fa-floppy-o\"></span>
            {$this->template->translate('save')}
        </button>
        ".$helper->buttonLink(
            $cancelURL,
            $this->template->translate('cancel'),
            'cancel'
        );
        return $buttons;
	}
}
