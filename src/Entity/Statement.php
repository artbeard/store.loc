<?php

namespace App\Entity;

use App\Repository\StatementRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\StatementTypes;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: StatementRepository::class)]
class Statement
{

	#[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(cascade: ['persist', 'remove'])]
    private ?Product $product = null;

    #[ORM\Column(length: 50)]
    private ?string $document_prop = null;

    #[ORM\Column(length: 10)]
    private ?string $post_type = StatementTypes::POST_IN;
    
    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $posted_at = null;

    #[ORM\Column]
    private ?int $amount = 0;

    #[ORM\Column]
    private ?int $cost = 0;

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

    public function getDocumentProp(): ?string
    {
        return $this->document_prop;
    }

    public function setDocumentProp(string $document_prop): self
    {
        $this->document_prop = $document_prop;

        return $this;
    }

    public function getPostType(): ?string
    {
        return $this->post_type;
    }

    public function setPostType(string $post_type): self
    {
        $this->post_type = $post_type;

        return $this;
    }

    public function getPostedAt(): ?\DateTimeInterface
    {
        return $this->posted_at;
    }

    public function setPostedAt(\DateTimeInterface $posted_at): self
    {
        $this->posted_at = $posted_at;

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

    public function getCost(): ?int
    {
        return $this->cost;
    }

    public function setCost(int $cost): self
    {
        $this->cost = $cost;

        return $this;
    }
}
