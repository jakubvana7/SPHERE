<?php
declare(strict_types=1);

namespace App\Presenters;

use App\Model\ShoeService;

final class WomenPresenter extends BasePresenter
{
    public function __construct(
        private readonly ShoeService $shoeService,
    ) {}

    public function renderDefault(): void
    {
        $this->template->shoes = $this->shoeService->getWomen();
    }
}
