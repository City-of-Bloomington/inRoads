<?php
/**
 * @copyright 2018 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param array $this->users
 */
declare (strict_types=1);

header('Content-Disposition: attachment; filename="users.csv"');
$out = fopen('php://output', 'w');
fputcsv($out, array_keys(get_class_vars('Domain\Users\Entities\User')));

foreach ($this->users as $i=>$user) {
    fputcsv($out, (array)$user);
}
fclose($out);
