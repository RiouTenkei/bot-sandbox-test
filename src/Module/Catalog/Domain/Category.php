<?php

declare(strict_types=1);

namespace App\Module\Catalog\Domain;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'categories')]
class Category
{
    public const string WORKSHOP_ID = 'workshop';
    public const string WRITING_ID = 'writing';
    public const string GARDEN_ID = 'garden';

    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 32)]
    private string $id;

    #[ORM\Column(type: 'string', length: 255)]
    private string $name;

    public function __construct(string $id, string $name)
    {
        $this->id = $id;
        $this->name = $name;
    }

    public function id(): string
    {
        return $this->id;
    }

    public function name(): string
    {
        return $this->name;
    }

    public static function workshop(): self
    {
        return new self(self::WORKSHOP_ID, 'Workshop');
    }

    public static function writing(): self
    {
        return new self(self::WRITING_ID, 'Writing');
    }

    public static function garden(): self
    {
        return new self(self::GARDEN_ID, 'Garden');
    }
}
