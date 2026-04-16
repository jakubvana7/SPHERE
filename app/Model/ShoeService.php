<?php
declare(strict_types=1);

namespace App\Model;

use Nette\Database\Explorer;
use Nette\Database\Table\ActiveRow;
use Nette\Database\Table\Selection;

class ShoeService
{
    /** @var float[] */
    public const DEFAULT_SIZES = [
        38.0, 38.5, 39.0, 39.5, 40.0, 40.5, 41.0, 41.5, 42.0, 42.5,
        43.0, 43.5, 44.0, 44.5, 45.0, 45.5, 46.0, 46.5, 47.0,
    ];

    public function __construct(
        private readonly Explorer $db,
    ) {}

    public function getMen(): Selection
    {
        return $this->db->table('boty')
            ->where('IDB >= ? AND IDB <= ?', 1, 10)
            ->order('IDB');
    }

    public function getWomen(): Selection
    {
        return $this->db->table('boty')
            ->where('IDB >= ? AND IDB <= ?', 11, 20)
            ->order('IDB');
    }

    public function getById(int $id): ?ActiveRow
    {
        return $this->db->table('boty')->get($id);
    }

    public function getTotalValue(): int
    {
        return (int) $this->db->table('boty')->sum('price');
    }

    public function getSizes(int $shoeId): array
    {
        return $this->db->table('shoe_sizes')
            ->where('shoe_id', $shoeId)
            ->order('size')
            ->fetchAll();
    }

    public function getAvailableSizes(int $shoeId): array
    {
        return $this->db->table('shoe_sizes')
            ->where('shoe_id = ? AND stock > 0', $shoeId)
            ->order('size')
            ->fetchAll();
    }

    public function ensureDefaultSizes(int $shoeId): void
    {
        $existing = $this->db->table('shoe_sizes')->where('shoe_id', $shoeId)->count('*');
        if ($existing > 0) {
            return;
        }
        foreach (self::DEFAULT_SIZES as $size) {
            $this->db->table('shoe_sizes')->insert([
                'shoe_id' => $shoeId,
                'size'    => $size,
                'stock'   => 5,
            ]);
        }
    }

    public function updateSizeStock(int $sizeId, int $stock): void
    {
        $this->db->table('shoe_sizes')->get($sizeId)?->update(['stock' => max(0, $stock)]);
    }

    public function getAll(): Selection
    {
        return $this->db->table('boty')->order('IDB');
    }

    public function toggleAvailable(int $id): void
    {
        $shoe = $this->db->table('boty')->get($id);
        if ($shoe) {
            $shoe->update(['available' => $shoe->available ? 0 : 1]);
        }
    }

    /** @param array<string, mixed> $data */
    public function update(int $id, array $data): void
    {
        $this->db->table('boty')->get($id)?->update($data);
    }

    /** @param array<string, mixed> $data */
    public function insert(array $data): ActiveRow
    {
        $row = $this->db->table('boty')->insert($data);
        $this->ensureDefaultSizes((int) $row->IDB);
        return $row;
    }

    public function delete(int $id): void
    {
        $this->db->table('shoe_sizes')->where('shoe_id', $id)->delete();
        $this->db->table('boty')->get($id)?->delete();
    }
}
