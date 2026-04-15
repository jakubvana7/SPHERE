<?php
declare(strict_types=1);

namespace App\Model;

use Nette\Database\Explorer;

class CustomerService
{
    public function __construct(
        private readonly Explorer $db,
    ) {}

    public function getOrderCount(): int
    {
        return $this->db->table('zakaznik')->count('*');
    }

    /**
     * Uloží objednávku i položky, vrátí ID objednávky.
     * @param array<string, mixed> $data
     * @param array<int, array{shoeId: int, size: float, name: string, price: int, img: string}> $items
     */
    public function saveOrder(array $data, array $items, ?int $userId): int
    {
        $order = $this->db->table('zakaznik')->insert([
            'Cname'          => $data['name'],
            'surname'        => $data['surname'],
            'email'          => $data['email'],
            'phone'          => $data['phone'],
            'address1'       => $data['address1'],
            'address2'       => $data['address2'] ?: null,
            'city'           => $data['city'],
            'country'        => $data['country'],
            'zip'            => $data['zip'],
            'payment_method' => $data['payment_method'],
            'user_id'        => $userId,
        ]);

        foreach ($items as $item) {
            $this->db->table('order_items')->insert([
                'order_id'  => $order->idZ,
                'shoe_id'   => $item['shoeId'],
                'shoe_name' => $item['name'],
                'size'      => $item['size'],
                'price'     => $item['price'],
                'img'       => $item['img'],
            ]);
        }

        return (int) $order->idZ;
    }
}
