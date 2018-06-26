<?php
/**
 * @copyright 2018 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
declare (strict_types=1);
namespace Domain\People\UseCases\UpdateAccount;

use Domain\People\DataStorage\PeopleRepository;
use Domain\People\UseCases\Load\Load;
use Domain\People\UseCases\Validate\Validate;

class UpdateAccount
{
    private $repo;

    public function __construct(PeopleRepository $repository)
    {
        $this->repo = $repository;
    }

    public function __invoke(UpdateAccountRequest $req): UpdateAccountResponse
    {
        $load     = new Load    ($this->repo);
        $validate = new Validate($this->repo);
        $res  = $load($req->id);
        if ($res->person) {
            $res->person->firstname = $req->firstname;
            $res->person->lastname  = $req->lastname;
            $res->person->email     = $req->email;
            $res->person->phone     = $req->phone;
            $validation = $validate($res->person);
            if ($validation->errors) { return new UpdateAccountResponse(null, $validation->errors); }

            try {
                $id       = $this->repo->UpdateAccount($res->person);
                $response = new UpdateAccountResponse($id);
            }
            catch (\Exception $e) {
                $response = new UpdateAccountResponse(null, [$e->getMessage()]);
            }
        }
        else {
            $response = new UpdateAccountResponse(null, $res->errors);
        }
        return $response;
    }
}
