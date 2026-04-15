<?php
declare(strict_types=1);

namespace App\Presenters;

use App\Model\UserService;

final class AccountPresenter extends BasePresenter
{
    public function __construct(
        private readonly UserService $userService,
    ) {}

    protected function startup(): void
    {
        parent::startup();
        if (!$this->getUser()->isLoggedIn()) {
            $this->flashMessage('Please sign in to view your account.', 'info');
            $this->redirect('Sign:in');
        }
        if ($this->getUser()->isInRole('admin')) {
            $this->redirect('Admin:default');
        }
    }

    public function renderDefault(): void
    {
        $userId = (int) $this->getUser()->getId();
        $this->template->userInfo = $this->userService->getById($userId);
        $this->template->orders   = $this->userService->getOrders($userId);
    }
}
