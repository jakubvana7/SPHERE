<?php
declare(strict_types=1);

namespace App\Model;

use Nette\Security\AuthenticationException;
use Nette\Security\Authenticator as IAuthenticator;
use Nette\Security\Passwords;
use Nette\Security\SimpleIdentity;

class Authenticator implements IAuthenticator
{
    public function __construct(
        private readonly string $adminPassword,
        private readonly UserService $userService,
        private readonly Passwords $passwords,
    ) {}

    public function authenticate(string $user, string $password): SimpleIdentity
    {
        // Admin přihlášení
        if ($user === 'admin') {
            if (!hash_equals($this->adminPassword, $password)) {
                throw new AuthenticationException('Invalid credentials.', self::InvalidCredential);
            }
            return new SimpleIdentity(0, 'admin', ['name' => 'Admin']);
        }

        // Zákaznické přihlášení (email)
        $dbUser = $this->userService->getByEmail($user);
        if (!$dbUser || !$this->passwords->verify($password, $dbUser->password)) {
            throw new AuthenticationException('Invalid email or password.', self::InvalidCredential);
        }

        return new SimpleIdentity($dbUser->id, 'user', [
            'name'  => $dbUser->name,
            'email' => $dbUser->email,
        ]);
    }
}
