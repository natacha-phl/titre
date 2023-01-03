<?php

namespace App\Entity;

use App\Repository\PurchaseRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PurchaseRepository::class)
 */
class Purchase
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $total_amount;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createAt;

    /**
     * @ORM\ManyToMany(targetEntity=DetailCommande::class, inversedBy="purchases")
     */
    private $detail_commande;

    /**
     * @ORM\ManyToMany(targetEntity=User::class, mappedBy="purchase")
     */
    private $users;

    public function __construct()
    {
        $this->detail_commande = new ArrayCollection();
        $this->users = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTotalAmount(): ?int
    {
        return $this->total_amount;
    }

    public function setTotalAmount(int $total_amount): self
    {
        $this->total_amount = $total_amount;

        return $this;
    }

    public function getCreateAt(): ?\DateTimeInterface
    {
        return $this->createAt;
    }

    public function setCreateAt(\DateTimeInterface $createAt): self
    {
        $this->createAt = $createAt;

        return $this;
    }

    /**
     * @return Collection|DetailCommande[]
     */
    public function getDetailCommande(): Collection
    {
        return $this->detail_commande;
    }

    public function addDetailCommande(DetailCommande $detailCommande): self
    {
        if (!$this->detail_commande->contains($detailCommande)) {
            $this->detail_commande[] = $detailCommande;
        }

        return $this;
    }

    public function removeDetailCommande(DetailCommande $detailCommande): self
    {
        $this->detail_commande->removeElement($detailCommande);

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users[] = $user;
            $user->addPurchase($this);
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->users->removeElement($user)) {
            $user->removePurchase($this);
        }

        return $this;
    }
}
