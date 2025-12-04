<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use App\Controller\HoursRandomController;
use App\Repository\TimeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\UuidV7 as Uuid;

#[ORM\Entity(repositoryClass: TimeRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(
            name: 'getRandomHours',
            uriTemplate: '/hours/random',
            controller: HoursRandomController::class,
            read: false,
            write: false,
            validate: false
        ),
    ]
)]
#[Post]
#[GetCollection()]
#[Get()]
class Time
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private ?Uuid $id = null;

    #[ORM\Column(length: 255)]
    private ?string $hour = null;

    #[ORM\Column(length: 255)]
    private ?string $minute = null;

    #[ORM\Column(length: 255)]
    private ?string $meridiem = null;

    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'times')]
    private Collection $user;

    public function __construct()
    {
        $this->user = new ArrayCollection();
    }

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getHour(): ?string
    {
        return $this->hour;
    }

    public function setHour(string $hour): static
    {
        $this->hour = $hour;

        return $this;
    }

    public function getMinute(): ?string
    {
        return $this->minute;
    }

    public function setMinute(string $minute): static
    {
        $this->minute = $minute;

        return $this;
    }

    public function getMeridiem(): ?string
    {
        return $this->meridiem;
    }

    public function setMeridiem(string $meridiem): static
    {
        $this->meridiem = $meridiem;

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getUser(): Collection
    {
        return $this->user;
    }

    public function addUser(User $user): static
    {
        if (!$this->user->contains($user)) {
            $this->user->add($user);
        }

        return $this;
    }

    public function removeUser(User $user): static
    {
        $this->user->removeElement($user);

        return $this;
    }
}
