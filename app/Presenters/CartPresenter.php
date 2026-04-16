<?php
declare(strict_types=1);

namespace App\Presenters;

use App\Model\CartService;
use App\Model\CustomerService;
use App\Model\UserService;
use Nette\Application\UI\Form;

final class CartPresenter extends BasePresenter
{
    public function __construct(
        private readonly CartService $cartService,
        private readonly CustomerService $customerService,
        private readonly UserService $userService,
    ) {}

    public function renderDefault(): void
    {
        $subtotal = $this->cartService->getSubtotal();
        $shipping = $subtotal > 0 ? 15 : 0;

        $this->template->items    = $this->cartService->getItems();
        $this->template->subtotal = $subtotal;
        $this->template->shipping = $shipping;
        $this->template->total    = $subtotal + $shipping;

        if ($this->getUser()->isLoggedIn() && !$this->getUser()->isInRole('admin')) {
            $userId  = (int) $this->getUser()->getId();
            $profile = $this->userService->getById($userId);
            $nameParts = explode(' ', $profile->name ?? '', 2);

            $defaults = [
                'name'    => $nameParts[0] ?? '',
                'surname' => $nameParts[1] ?? '',
                'email'   => $profile->email ?? '',
            ];

            if ($profile->phone)    $defaults['phone']    = $profile->phone;
            if ($profile->address1) $defaults['address1'] = $profile->address1;
            if ($profile->address2) $defaults['address2'] = $profile->address2;
            if ($profile->city)     $defaults['city']     = $profile->city;
            if ($profile->country)  $defaults['country']  = $profile->country;
            if ($profile->zip)      $defaults['zip']      = $profile->zip;

            $this['checkoutForm']->setDefaults($defaults);
        }
    }

    public function handleRemove(int $index): void
    {
        $this->cartService->removeItem($index);
        $this->flashMessage('Item removed from cart.', 'info');
        $this->redirect('this');
    }

    protected function createComponentCheckoutForm(): Form
    {
        $input  = 'w-full border border-gray-200 px-4 py-3 text-sm focus:outline-none focus:border-black transition-colors';
        $label  = 'block text-xs font-bold tracking-[0.2em] uppercase mb-2 text-gray-700';
        $submit = 'w-full bg-black text-white py-4 text-xs font-bold tracking-[0.3em] uppercase hover:bg-gray-800 transition-colors cursor-pointer mt-4';

        $form = new Form;

        $form->addText('name', 'First Name:')
            ->setRequired('Please enter your first name.')
            ->setMaxLength(100)
            ->setHtmlAttribute('class', $input)
            ->setHtmlAttribute('placeholder', 'First Name');
        $form['name']->getLabelPrototype()->setAttribute('class', $label);

        $form->addText('surname', 'Last Name:')
            ->setRequired('Please enter your last name.')
            ->setMaxLength(100)
            ->setHtmlAttribute('class', $input)
            ->setHtmlAttribute('placeholder', 'Last Name');
        $form['surname']->getLabelPrototype()->setAttribute('class', $label);

        $form->addEmail('email', 'E-mail:')
            ->setRequired('Please enter your email.')
            ->setHtmlAttribute('class', $input)
            ->setHtmlAttribute('placeholder', 'email@example.com');
        $form['email']->getLabelPrototype()->setAttribute('class', $label);

        $form->addText('phone', 'Phone:')
            ->setRequired('Please enter your phone number.')
            ->setMaxLength(30)
            ->addRule(Form::Pattern, 'Invalid phone number.', '[\+\d\s\-()\/.]{6,25}')
            ->setHtmlAttribute('class', $input)
            ->setHtmlAttribute('placeholder', '+1 234 567 890');
        $form['phone']->getLabelPrototype()->setAttribute('class', $label);

        $form->addText('address1', 'Address:')
            ->setRequired('Please enter your address.')
            ->setMaxLength(255)
            ->setHtmlAttribute('class', $input)
            ->setHtmlAttribute('placeholder', 'Street and number');
        $form['address1']->getLabelPrototype()->setAttribute('class', $label);

        $form->addText('address2', 'Address 2:')
            ->setMaxLength(255)
            ->setHtmlAttribute('class', $input)
            ->setHtmlAttribute('placeholder', 'Apartment, suite, etc. (optional)');
        $form['address2']->getLabelPrototype()->setAttribute('class', $label);

        $form->addText('city', 'City:')
            ->setRequired('Please enter your city.')
            ->setMaxLength(100)
            ->setHtmlAttribute('class', $input)
            ->setHtmlAttribute('placeholder', 'City');
        $form['city']->getLabelPrototype()->setAttribute('class', $label);

        $form->addText('country', 'Country:')
            ->setRequired('Please enter your country.')
            ->setMaxLength(100)
            ->setHtmlAttribute('class', $input)
            ->setHtmlAttribute('placeholder', 'United States');
        $form['country']->getLabelPrototype()->setAttribute('class', $label);

        $form->addText('zip', 'ZIP / Postal Code:')
            ->setRequired('Please enter your ZIP code.')
            ->setMaxLength(20)
            ->setHtmlAttribute('class', $input)
            ->setHtmlAttribute('placeholder', '10001');
        $form['zip']->getLabelPrototype()->setAttribute('class', $label);

        $form->addRadioList('payment_method', 'Payment Method:', [
            'apple-pay'   => 'Apple Pay',
            'paypal'      => 'PayPal',
            'master-card' => 'Mastercard',
            'visa'        => 'Visa',
        ])->setRequired('Please select a payment method.');
        $form['payment_method']->getLabelPrototype()->setAttribute('class', $label);

        $form->addSubmit('submit', 'PLACE ORDER')
            ->setHtmlAttribute('class', $submit);

        $this->applyFormStyles($form);
        $form->onSuccess[] = $this->checkoutFormSucceeded(...);

        return $form;
    }

    public function checkoutFormSucceeded(Form $form, \stdClass $values): void
    {
        if ($this->cartService->isEmpty()) {
            $this->flashMessage('Your cart is empty.', 'error');
            $this->redirect('this');
        }

        $userId = $this->getUser()->isLoggedIn() && !$this->getUser()->isInRole('admin')
            ? (int) $this->getUser()->getId()
            : null;

        $this->customerService->saveOrder(
            [
                'name'           => $values->name,
                'surname'        => $values->surname,
                'email'          => $values->email,
                'phone'          => $values->phone,
                'address1'       => $values->address1,
                'address2'       => $values->address2,
                'city'           => $values->city,
                'country'        => $values->country,
                'zip'            => $values->zip,
                'payment_method' => $values->payment_method,
            ],
            $this->cartService->getItems(),
            $userId,
        );

        if ($userId !== null) {
            $this->userService->updateProfile($userId, [
                'phone'    => $values->phone,
                'address1' => $values->address1,
                'address2' => $values->address2,
                'city'     => $values->city,
                'country'  => $values->country,
                'zip'      => $values->zip,
            ]);
        }

        $this->cartService->clear();
        $this->flashMessage('Order placed successfully! Thank you.', 'success');
        $this->redirect('this');
    }
}
