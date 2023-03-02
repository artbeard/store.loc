<?php

namespace App\Entity;

use App\Repository\BalanceRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Вспомогательная сущность для хранения данных о балансе и истории его изменения
 * Нужна чтобы осовободить БД от постоянных подсчетов количества товаров и их стоимости
 */
#[ORM\Entity(repositoryClass: BalanceRepository::class)]
class Balance
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(cascade: ['persist', 'remove'])]
    private ?Product $product = null;

    #[ORM\Column]
    private ?int $amount = 0;

    #[ORM\Column]
    private ?float $cost = 0;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $balance_at = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): self
    {
        $this->product = $product;

        return $this;
    }

    public function getAmount(): ?int
    {
        return $this->amount;
    }

    public function setAmount(int $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function getCost(): ?float
    {
        return $this->cost;
    }

    public function setCost(float $cost): self
    {
        $this->cost = $cost;

        return $this;
    }

    public function getBalanceAt(): ?\DateTimeInterface
    {
        return $this->balance_at;
    }

    public function setBalanceAt(\DateTimeInterface $balance_at): self
    {
        $this->balance_at = $balance_at;

        return $this;
    }
}
