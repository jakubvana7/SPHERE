<?php
declare(strict_types=1);

namespace App\Presenters;

use App\Model\CartService;
use App\Model\ShoeService;
use Nette\Application\UI\Form;

final class ProductPresenter extends BasePresenter
{
    public function __construct(
        private readonly ShoeService $shoeService,
        private readonly CartService $cartService,
    ) {}

    public function renderDetail(int $id): void
    {
        $shoe = $this->shoeService->getById($id);
        if (!$shoe) {
            $this->error('Product not found.', 404);
        }

        $availableSizes = $this->shoeService->getAvailableSizes($id);
        $allSizes       = $this->shoeService->getSizes($id);

        $this->template->shoe           = $shoe;
        $this->template->availableSizes = $availableSizes;
        $this->template->allSizes       = $allSizes;
        $this->template->isSoldOut      = count($availableSizes) === 0;
    }

    protected function createComponentAddToCartForm(): Form
    {
        $id = (int) $this->getParameter('id');
        $sizes = $this->shoeService->getAvailableSizes($id);

        $sizeOptions = [];
        foreach ($sizes as $s) {
            $label = fmod((float) $s->size, 1) === 0.0
                ? number_format((float) $s->size, 0)
                : number_format((float) $s->size, 1);
            if ($s->stock <= 3) {
                $label .= '  (only ' . $s->stock . ' left)';
            }
            $sizeOptions[(string) $s->size] = $label;
        }

        $form = new Form;

        $select = $form->addSelect('size', '', $sizeOptions)
            ->setPrompt('— SELECT SIZE —')
            ->setRequired('Please select a size.');

        if (empty($sizeOptions)) {
            $select->setDisabled();
        }

        $form->addSubmit('addToCart', 'ADD TO CART');

        $this->applyFormStyles($form);
        $form->onSuccess[] = $this->addToCartFormSucceeded(...);
        return $form;
    }

    public function addToCartFormSucceeded(Form $form, \stdClass $values): void
    {
        $id = (int) $this->getParameter('id');
        $shoe = $this->shoeService->getById($id);

        if (!$shoe || !$shoe->available) {
            $form->addError('This product is not available.');
            return;
        }

        $this->cartService->addItem($id, (float) $values->size);
        $this->flashMessage('Added to cart.', 'success');
        $this->redirect('this');
    }
}
