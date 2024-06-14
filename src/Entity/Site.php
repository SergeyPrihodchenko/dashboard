<?php

namespace App\Entity;

use App\Repository\SiteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SiteRepository::class)]
class Site
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $href = null;

    #[ORM\OneToMany(targetEntity: Calls::class, mappedBy: 'site_id', orphanRemoval: true)]
    private Collection $calls;

    #[ORM\OneToMany(targetEntity: Mails::class, mappedBy: 'site_id', orphanRemoval: true)]
    private Collection $mails;

    public function __construct()
    {
        $this->calls = new ArrayCollection();
        $this->mails = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getHref(): ?string
    {
        return $this->href;
    }

    public function setHref(string $href): static
    {
        $this->href = $href;

        return $this;
    }

    /**
     * @return Collection<int, Calls>
     */
    public function getCalls(): Collection
    {
        return $this->calls;
    }

    public function addCall(Calls $call): static
    {
        if (!$this->calls->contains($call)) {
            $this->calls->add($call);
            $call->setSiteId($this);
        }

        return $this;
    }

    public function removeCall(Calls $call): static
    {
        if ($this->calls->removeElement($call)) {
            // set the owning side to null (unless already changed)
            if ($call->getSiteId() === $this) {
                $call->setSiteId(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Mails>
     */
    public function getMails(): Collection
    {
        return $this->mails;
    }

    public function addMail(Mails $mail): static
    {
        if (!$this->mails->contains($mail)) {
            $this->mails->add($mail);
            $mail->setSiteId($this);
        }

        return $this;
    }

    public function removeMail(Mails $mail): static
    {
        if ($this->mails->removeElement($mail)) {
            // set the owning side to null (unless already changed)
            if ($mail->getSiteId() === $this) {
                $mail->setSiteId(null);
            }
        }

        return $this;
    }
}
