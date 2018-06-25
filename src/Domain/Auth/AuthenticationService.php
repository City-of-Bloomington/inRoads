<?php
/**
 * @copyright 2018 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
declare (strict_types=1);
namespace Domain\Auth;

use Domain\Users\Entities\User;
use Domain\Users\DataStorage\UsersRepository;

class AuthenticationService
{
    private $repo;
    private $config;

    public function __construct(UsersRepository $repository, array $config)
    {
        $this->repo   = $repository;
        $this->config = $config;
    }

    public function identify(string $username): ?User
    {
        $user = $this->repo->loadByUsername($username);
        if ($user) {
            return $user;
        }
    }

    public function externalIdentify(string $method, string $username): ?ExternalIdentity
    {
        $o = $this->loadAuthenticationMethod($method);
        return $o->identify($username);
    }

    /**
     * Returns a User on success or null on failure
     *
     * @return User
     */
    public function authenticate(string $username, string $password): ?User
    {
        $row = $this->repo->loadByUsername($username);
        if ($row && !empty($row['authentication_method'])) {
            switch ($row['authentication_method']) {
                case 'local':
                    if ($row['password'] == self::password_hash($password)) {
                        return new User($row);
                    }
                break;

                default:
                    $o = $this->loadAuthenticationMethod($row['authentication_method']);
                    if ($o->authenticate($username, $password)) {
                        return new User($row);
                    }
            }
        }
    }

    public static function password_hash(string $password): string
    {
        return password_hash($password, PASSWORD_BCRYPT);
    }

    public function getAuthenticationMethods(): array
    {
        return array_keys($this->config);
    }

    private function loadAuthenticationMethod(string $method): AuthenticationInterface
    {
        $class = $this->config[$method]['classname'];
        return new $class($this->config[$method]);
    }
}
