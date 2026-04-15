<?php
declare(strict_types=1);

namespace App\Model;

use Nette\Database\Explorer;
use Nette\Database\Table\ActiveRow;
use Nette\Security\Passwords;

class UserService
{
    public function __construct(
        private readonly Explorer $db,
        private readonly Passwords $passwords,
    ) {}

    public function register(string $name, string $email, string $password): ActiveRow
    {
        return $this->db->table('users')->insert([
            'name'     => $name,
            'email'    => $email,
            'password' => $this->passwords->hash($password),
        ]);
    }

    public function emailExists(string $email): bool
    {
        return (bool) $this->db->table('users')->where('email', $email)->fetch();
    }

    public function getById(int $id): ?ActiveRow
    {
        return $this->db->table('users')->get($id);
    }

    public function getByEmail(string $email): ?ActiveRow
    {
        return $this->db->table('users')->where('email', $email)->fetch();
    }

    /** Save shipping/contact info on the user record so it pre-fills next time */
    public function updateProfile(int $id, array $data): void
    {
        $this->db->table('users')->get($id)?->update([
            'phone'    => $data['phone']    ?? null,
            'address1' => $data['address1'] ?? null,
            'address2' => $data['address2'] ?: null,
            'city'     => $data['city']     ?? null,
            'country'  => $data['country']  ?? null,
            'zip'      => $data['zip']      ?? null,
        ]);
    }

    /** Vrátí objednávky uživatele i s položkami */
    public function getOrders(int $userId): array
    {
        $orders = $this->db->table('zakaznik')
            ->where('user_id', $userId)
            ->order('created_at DESC')
            ->fetchAll();

        $result = [];
        foreach ($orders as $order) {
            $items = $this->db->table('order_items')
                ->where('order_id', $order->idZ)
                ->fetchAll();
            $result[] = ['order' => $order, 'items' => $items];
        }
        return $result;
    }
}
