<?php
/**
 * @copyright 2018 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
declare (strict_types=1);
namespace Domain\Users\UseCases\Update;

use Domain\Auth\AuthenticationService;
use Domain\Users\Entities\User;
use Domain\Users\DataStorage\UsersRepository;
use Domain\Users\UseCases\Validate\Validate;

class Update
{
    private $repo;
    private $auth;

    public function __construct(UsersRepository $repository, AuthenticationService $auth)
    {
        $this->repo = $repository;
        $this->auth = $auth;
    }

    public function __invoke(UpdateRequest $req): UpdateResponse
    {
        if ($req->authenticationMethod != 'local'
            && (empty($req->firstname) || empty($req->lastname) || empty($req->email))) {

            $o = $this->auth->externalIdentify($req->authenticationMethod, $req->username);
            if ($o) {
                if (empty($req->firstname)) { $req->firstname = $o->firstname; }
                if (empty($req->lastname )) { $req->lastname  = $o->lastname;  }
                if (empty($req->email    )) { $req->email     = $o->email;     }
            }
        }

        $validate = new Validate();
        $validation = $validate(new User((array)$req));
        if ($validation->errors) { return new UpdateResponse(null, $validation->errors); }

        $user = $validation->user;
        if ( $req->password) {
            $user->password = $this->auth->password_hash($req->password);
        }

        try {
            $id  = $this->repo->save($user);
            $res = new UpdateResponse($id);
        }
        catch (\Exception $e) {
            $res = new UpdateResponse(null, [$e->getMessage()]);
        }
        return $res;
    }
}
