<?php

declare(strict_types=1);

namespace App\Module\Catalog\Domain;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'items')]
class Item
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 32)]
    private string $id;

    #[ORM\Column(type: 'string', length: 255)]
    private string $name;

    #[ORM\Column(type: 'integer')]
    private int $priceMinor;

    #[ORM\Column(type: 'string', length: 3)]
    private string $currency;

    #[ORM\Column(type: Types::DATETIMETZ_IMMUTABLE)]
    private \DateTimeImmutable $createdAt;

    #[ORM\ManyToOne(targetEntity: Category::class, fetch: 'EAGER')]
    #[ORM\JoinColumn(name: 'category_id', referencedColumnName: 'id', nullable: false)]
    private Category $category;

    public function __construct(
        string $id,
        string $name,
        int $priceMinor,
        string $currency,
        \DateTimeImmutable $createdAt,
        Category $category,
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->priceMinor = $priceMinor;
        $this->currency = $currency;
        $this->createdAt = $createdAt;
        $this->category = $category;
    }

    public function id(): string
    {
        return $this->id;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function priceMinor(): int
    {
        return $this->priceMinor;
    }

    public function currency(): string
    {
        return $this->currency;
    }

    public function createdAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function category(): Category
    {
        return $this->category;
    }
}
