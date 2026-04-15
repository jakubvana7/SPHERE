<?php
declare(strict_types=1);

namespace App\Presenters;

use App\Model\CartService;
use Nette\Application\UI\Presenter;

abstract class BasePresenter extends Presenter
{
    private CartService $cartService;

    public function injectCartService(CartService $cartService): void
    {
        $this->cartService = $cartService;
    }

    protected function beforeRender(): void
    {
        parent::beforeRender();
        $this->template->addFilter('img', fn(string $path): string => '/' . ltrim($path, './'));
        $this->template->addFilter('sizeFormat', fn(float $size): string =>
            fmod($size, 1) === 0.0 ? number_format($size, 0) : number_format($size, 1)
        );
        $this->template->cartCount = isset($this->cartService) ? count($this->cartService->getItems()) : 0;
    }

    protected function applyFormStyles(\Nette\Application\UI\Form $form): void
    {
        /** @var \Nette\Forms\Rendering\DefaultFormRenderer $r */
        $r = $form->getRenderer();
        $r->wrappers['form']['container']         = null;
        $r->wrappers['controls']['container']     = 'div class="space-y-5"';
        $r->wrappers['pair']['container']         = 'div';
        $r->wrappers['pair']['.error']            = '';
        $r->wrappers['label']['container']        = null;
        $r->wrappers['label']['suffix']           = null;
        $r->wrappers['control']['container']      = null;
        $r->wrappers['control']['errorcontainer'] = 'p class="mt-1 text-xs text-red-500"';
        $r->wrappers['control']['erroritem']      = null;
        $r->wrappers['control']['.error']         = '';
        $r->wrappers['error']['container']        = 'div class="mb-4 p-3 bg-red-50 border border-red-200 text-xs text-red-600 space-y-1 rounded-sm"';
        $r->wrappers['error']['item']             = 'p';
    }
}
