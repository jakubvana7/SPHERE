<?php
declare(strict_types=1);

namespace App\Presenters;

use App\Model\ShoeService;
use Nette\Application\UI\Form;

final class AdminPresenter extends BasePresenter
{
    public function __construct(
        private readonly ShoeService $shoeService,
    ) {}

    protected function startup(): void
    {
        parent::startup();
        if (!$this->getUser()->isLoggedIn()) {
            $this->redirect('Sign:in');
        }
    }

    public function renderDefault(): void
    {
        $this->template->shoes = $this->shoeService->getAll();
    }

    public function handleToggle(int $id): void
    {
        $this->shoeService->toggleAvailable($id);
        $this->redirect('this');
    }

    public function handleDelete(int $id): void
    {
        $this->shoeService->delete($id);
        $this->flashMessage('Product deleted.', 'info');
        $this->redirect('Admin:default');
    }

    public function renderAdd(): void
    {
        $this->template->shoe  = null;
        $this->template->sizes = [];
    }

    public function renderEdit(int $id): void
    {
        $shoe = $this->shoeService->getById($id);
        if (!$shoe) {
            $this->error('Product not found.', 404);
        }

        $this->shoeService->ensureDefaultSizes($id);
        $sizes = $this->shoeService->getSizes($id);

        $this->template->shoe  = $shoe;
        $this->template->sizes = $sizes;

        $this['productForm']->setDefaults([
            'shoeName'  => $shoe->shoeName,
            'popisek'   => $shoe->popisek,
            'price'     => $shoe->price,
            'img1'      => $shoe->img1,
            'img2'      => $shoe->img2,
            'img3'      => $shoe->img3,
            'available' => (bool) $shoe->available,
        ]);
    }

    protected function createComponentProductForm(): Form
    {
        $input    = 'w-full border border-gray-200 px-4 py-3 text-sm focus:outline-none focus:border-black transition-colors';
        $textarea = $input . ' resize-y min-h-[120px]';
        $label    = 'block text-xs font-bold tracking-[0.2em] uppercase mb-2 text-gray-700';
        $submit   = 'bg-black text-white px-8 py-3 text-xs font-bold tracking-[0.3em] uppercase hover:bg-gray-800 transition-colors cursor-pointer';

        $form = new Form;
        $form->addText('shoeName', 'Name:')
            ->setRequired()->setMaxLength(2000)
            ->setHtmlAttribute('class', $input);
        $form['shoeName']->getLabelPrototype()->setAttribute('class', $label);

        $form->addTextArea('popisek', 'Description:')
            ->setRequired()
            ->setHtmlAttribute('class', $textarea);
        $form['popisek']->getLabelPrototype()->setAttribute('class', $label);

        $form->addInteger('price', 'Price (€):')
            ->setRequired()->addRule(Form::Min, 'Price must be positive.', 1)
            ->setHtmlAttribute('class', $input);
        $form['price']->getLabelPrototype()->setAttribute('class', $label);

        foreach (['img1' => 'Image 1', 'img2' => 'Image 2', 'img3' => 'Image 3'] as $name => $title) {
            $form->addText($name, $title . ' (path):')
                ->setRequired()->setMaxLength(200)
                ->setHtmlAttribute('class', $input)
                ->setHtmlAttribute('placeholder', './images/shoe-name-1.webp');
            $form[$name]->getLabelPrototype()->setAttribute('class', $label);
        }

        $form->addCheckbox('available', ' Product is available (visible to customers)')
            ->setDefaultValue(true)
            ->setHtmlAttribute('class', 'w-4 h-4 cursor-pointer accent-black');

        $form->addSubmit('save', 'SAVE PRODUCT')
            ->setHtmlAttribute('class', $submit);

        $this->applyFormStyles($form);
        $form->onSuccess[] = $this->productFormSucceeded(...);
        return $form;
    }

    public function productFormSucceeded(Form $form, \stdClass $values): void
    {
        $id = (int) ($this->getParameter('id') ?? 0);
        $data = [
            'shoeName'  => $values->shoeName,
            'popisek'   => $values->popisek,
            'price'     => $values->price,
            'img1'      => $values->img1,
            'img2'      => $values->img2,
            'img3'      => $values->img3,
            'available' => $values->available ? 1 : 0,
        ];

        if ($id) {
            $this->shoeService->update($id, $data);
            $this->flashMessage('Product updated.', 'success');
            $this->redirect('Admin:edit', $id);
        } else {
            $new = $this->shoeService->insert($data);
            $this->flashMessage('Product added.', 'success');
            $this->redirect('Admin:edit', $new->IDB);
        }
    }

    protected function createComponentSizesForm(): Form
    {
        $id    = (int) $this->getParameter('id');
        $sizes = $id > 0 ? $this->shoeService->getSizes($id) : [];

        $form = new Form;
        foreach ($sizes as $s) {
            $sizeLabel = fmod((float) $s->size, 1) === 0.0
                ? number_format((float) $s->size, 0)
                : number_format((float) $s->size, 1);
            $form->addInteger('s_' . $s->id, $sizeLabel)
                ->setDefaultValue((int) $s->stock)
                ->addRule(Form::Min, 'Min. 0', 0)
                ->setHtmlAttribute('class', 'w-full border border-gray-200 px-2 py-2 text-sm text-center focus:outline-none focus:border-black transition-colors')
                ->setHtmlAttribute('min', 0);
        }

        $form->addSubmit('save', 'SAVE INVENTORY')
            ->setHtmlAttribute('class', 'bg-black text-white px-6 py-2.5 text-xs font-bold tracking-[0.25em] uppercase hover:bg-gray-800 transition-colors cursor-pointer col-span-full mt-2');

        /** @var \Nette\Forms\Rendering\DefaultFormRenderer $r */
        $r = $form->getRenderer();
        $r->wrappers['form']['container']     = null;
        $r->wrappers['controls']['container'] = 'div class="grid grid-cols-5 sm:grid-cols-7 gap-2"';
        $r->wrappers['pair']['container']     = 'div';
        $r->wrappers['pair']['.error']        = '';
        $r->wrappers['label']['container']    = null;
        $r->wrappers['label']['suffix']       = null;
        $r->wrappers['control']['container']  = null;
        $r->wrappers['control']['errorcontainer'] = 'p class="text-[9px] text-red-500"';
        $r->wrappers['control']['erroritem']  = null;
        $r->wrappers['control']['.error']     = '';
        $r->wrappers['error']['container']    = null;

        $form->onSuccess[] = $this->sizesFormSucceeded(...);
        return $form;
    }

    public function sizesFormSucceeded(Form $form, \stdClass $values): void
    {
        foreach ((array) $values as $key => $stock) {
            if (str_starts_with($key, 's_')) {
                $sizeId = (int) substr($key, 2);
                $this->shoeService->updateSizeStock($sizeId, (int) $stock);
            }
        }
        $this->flashMessage('Inventory saved.', 'success');
        $this->redirect('this');
    }
}
