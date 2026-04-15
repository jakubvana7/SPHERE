<?php
declare(strict_types=1);

namespace App\Presenters;

use App\Model\ShoeService;

final class HomepagePresenter extends BasePresenter
{
    public function __construct(
        private readonly ShoeService $shoeService,
    ) {}

    public function renderDefault(): void
    {
        $this->template->menShoes    = $this->shoeService->getMen();
        $this->template->womenShoes  = $this->shoeService->getWomen();
        $this->template->firstMenImg = $this->shoeService->getMen()->fetch()?->img1 ?? '';
        $this->template->firstWomenImg = $this->shoeService->getWomen()->fetch()?->img1 ?? '';
    }
}
