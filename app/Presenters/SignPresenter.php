<?php
declare(strict_types=1);

namespace App\Presenters;

use App\Model\UserService;
use Nette\Application\UI\Form;
use Nette\Security\AuthenticationException;

final class SignPresenter extends BasePresenter
{
    public function __construct(
        private readonly UserService $userService,
    ) {}

    public function renderIn(): void
    {
        if ($this->getUser()->isLoggedIn()) {
            $this->redirect($this->getUser()->isInRole('admin') ? 'Admin:default' : 'Account:default');
        }
    }

    public function renderRegister(): void
    {
        if ($this->getUser()->isLoggedIn()) {
            $this->redirect('Account:default');
        }
    }

    public function actionOut(): void
    {
        $this->getUser()->logout(true);
        $this->flashMessage('You have been logged out.', 'info');
        $this->redirect('Homepage:default');
    }

    // ── Login form ────────────────────────────────────────────

    protected function createComponentLoginForm(): Form
    {
        $input  = 'w-full border border-gray-200 px-4 py-3 text-sm focus:outline-none focus:border-black transition-colors';
        $label  = 'block text-xs font-bold tracking-[0.2em] uppercase mb-2 text-gray-700';
        $submit = 'w-full bg-black text-white py-4 text-xs font-bold tracking-[0.3em] uppercase hover:bg-gray-800 transition-colors cursor-pointer';

        $form = new Form;
        $form->addText('email', 'E-mail:')
            ->setRequired('Please enter your email.')
            ->setHtmlAttribute('class', $input)
            ->setHtmlAttribute('placeholder', 'your@email.com')
            ->setHtmlAttribute('autocomplete', 'email');
        $form['email']->getLabelPrototype()->setAttribute('class', $label);

        $form->addPassword('password', 'Password:')
            ->setRequired('Please enter your password.')
            ->setHtmlAttribute('class', $input)
            ->setHtmlAttribute('placeholder', '••••••••')
            ->setHtmlAttribute('autocomplete', 'current-password');
        $form['password']->getLabelPrototype()->setAttribute('class', $label);

        $form->addSubmit('send', 'SIGN IN')
            ->setHtmlAttribute('class', $submit);

        $this->applyFormStyles($form);
        $form->onSuccess[] = $this->loginFormSucceeded(...);
        return $form;
    }

    public function loginFormSucceeded(Form $form, \stdClass $values): void
    {
        try {
            $this->getUser()->login($values->email, $values->password);
            $this->redirect($this->getUser()->isInRole('admin') ? 'Admin:default' : 'Account:default');
        } catch (AuthenticationException $e) {
            $form->addError($e->getMessage());
        }
    }

    // ── Register form ─────────────────────────────────────────

    protected function createComponentRegisterForm(): Form
    {
        $input  = 'w-full border border-gray-200 px-4 py-3 text-sm focus:outline-none focus:border-black transition-colors';
        $label  = 'block text-xs font-bold tracking-[0.2em] uppercase mb-2 text-gray-700';
        $submit = 'w-full bg-black text-white py-4 text-xs font-bold tracking-[0.3em] uppercase hover:bg-gray-800 transition-colors cursor-pointer';

        $form = new Form;
        $form->addText('name', 'Full Name:')
            ->setRequired('Please enter your name.')
            ->setMaxLength(100)
            ->setHtmlAttribute('class', $input)
            ->setHtmlAttribute('placeholder', 'John Doe');
        $form['name']->getLabelPrototype()->setAttribute('class', $label);

        $form->addEmail('email', 'E-mail:')
            ->setRequired('Please enter your email.')
            ->setHtmlAttribute('class', $input)
            ->setHtmlAttribute('placeholder', 'your@email.com')
            ->setHtmlAttribute('autocomplete', 'email');
        $form['email']->getLabelPrototype()->setAttribute('class', $label);

        $form->addPassword('password', 'Password:')
            ->setRequired('Please enter a password.')
            ->addRule(Form::MinLength, 'Password must be at least %d characters.', 6)
            ->setHtmlAttribute('class', $input)
            ->setHtmlAttribute('placeholder', 'at least 6 characters')
            ->setHtmlAttribute('autocomplete', 'new-password');
        $form['password']->getLabelPrototype()->setAttribute('class', $label);

        $form->addPassword('passwordVerify', 'Confirm Password:')
            ->setRequired('Please confirm your password.')
            ->addRule(Form::Equal, 'Passwords do not match.', $form['password'])
            ->setHtmlAttribute('class', $input)
            ->setHtmlAttribute('placeholder', '••••••••')
            ->setHtmlAttribute('autocomplete', 'new-password');
        $form['passwordVerify']->getLabelPrototype()->setAttribute('class', $label);

        $form->addSubmit('send', 'CREATE ACCOUNT')
            ->setHtmlAttribute('class', $submit);

        $this->applyFormStyles($form);
        $form->onSuccess[] = $this->registerFormSucceeded(...);
        return $form;
    }

    public function registerFormSucceeded(Form $form, \stdClass $values): void
    {
        if ($this->userService->emailExists($values->email)) {
            $form['email']->addError('This email is already registered.');
            return;
        }

        $this->userService->register($values->name, $values->email, $values->password);

        try {
            $this->getUser()->login($values->email, $values->password);
        } catch (AuthenticationException) {
        }

        $this->flashMessage('Account created. Welcome!', 'success');
        $this->redirect('Account:default');
    }
}
