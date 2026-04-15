<?php
declare(strict_types=1);

namespace App\Model;

use Nette\Http\Session;
use Nette\Http\SessionSection;

class CartService
{
    private SessionSection $section;

    public function __construct(
        Session $session,
        private readonly ShoeService $shoeService,
    ) {
        $this->section = $session->getSection('cart');
    }

    public function addItem(int $shoeId, float $size): void
    {
        $shoe = $this->shoeService->getById($shoeId);
        if (!$shoe) {
            return;
        }

        $items = $this->section->items ?? [];
        $items[] = [
            'shoeId' => $shoeId,
            'size'   => $size,
            'name'   => $shoe->shoeName,
            'price'  => (int) $shoe->price,
            'img'    => $shoe->img1,
        ];
        $this->section->items = $items;
    }

    public function removeItem(int $index): void
    {
        $items = $this->section->items ?? [];
        if (array_key_exists($index, $items)) {
            array_splice($items, $index, 1);
            $this->section->items = array_values($items);
        }
    }

    /** @return array<int, array{shoeId: int, size: float, name: string, price: int, img: string}> */
    public function getItems(): array
    {
        return $this->section->items ?? [];
    }

    public function clear(): void
    {
        $this->section->items = [];
    }

    public function getSubtotal(): int
    {
        return (int) array_sum(array_column($this->getItems(), 'price'));
    }

    public function isEmpty(): bool
    {
        return empty($this->section->items);
    }
}
