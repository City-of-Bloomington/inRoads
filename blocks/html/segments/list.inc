<?php
/**
 * @copyright 2015 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param array $this->segments
 */
?>
<div class="panelList">
<?php
    foreach ($this->segments as $segment) {
        $street = View::escape($segment->getStreet());
        $from   = View::escape($segment->getStreetFrom());
        $to     = View::escape($segment->getStreetTo());
        echo "
        <table>
            <tr><th>{$this->_('street')}</th>     <td>$street</td></tr>
            <tr><th>{$this->_('streetFrom')}</th> <td>$from</td></tr>
            <tr><th>{$this->_('streetTo')}</th>   <td>$to</td></tr>
        <table>
        ";
    }
?>
</div>