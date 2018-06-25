<?php
/**
 * @copyright 2018 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
declare (strict_types=1);
namespace Domain\People\UseCases\Update;

use Domain\People\Entities\Person;
use Domain\People\DataStorage\PeopleRepository;
use Domain\People\UseCases\Validate\Validate;

class Update
{
    private $repo;

    public function __construct(PeopleRepository $repository)
    {
        $this->repo = $repository;
    }

    public function __invoke(UpdateRequest $req): UpdateResponse
    {
        $validate = new Validate();
        $validation = $validate(new Person((array)$req));
        if ($validation->errors) { return new UpdateResponse(null, $validation->errors); }

        try {
            $id  = $this->repo->save($validation->person);
            $res = new UpdateResponse($id);
        }
        catch (\Exception $e) {
            $res = new UpdateResponse(null, [$e->getMessage()]);
        }
        return $res;
    }
}
