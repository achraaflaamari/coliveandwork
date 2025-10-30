<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\MessageRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

// Entité représentant un message échangé entre utilisateurs
#[ORM\Entity(repositoryClass: MessageRepository::class)]
#[ApiResource]
#[ORM\Table(name: 'message')]
class Message
{
    // Identifiant unique auto-généré
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    // Contenu du message
    #[ORM\Column(type: Types::TEXT)]
    private ?string $content = null;

    // Date d'envoi
    #[ORM\Column]
    private ?\DateTimeImmutable $sendAt = null;

    // Date de lecture (optionnelle)
    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $seenAt = null;

    // Destinataire du message
    #[ORM\ManyToOne(inversedBy: 'messages')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $receiver = null;

    // Expéditeur du message
    #[ORM\ManyToOne(inversedBy: 'messages')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $sender = null;

    // Getters / Setters générés automatiquement
    public function getId(): ?int { return $this->id; }

    public function getContent(): ?string { return $this->content; }
    public function setContent(string $content): static {
        $this->content = $content;
        return $this;
    }

    public function getSendAt(): ?\DateTimeImmutable { return $this->sendAt; }
    public function setSendAt(\DateTimeImmutable $sendAt): static {
        $this->sendAt = $sendAt;
        return $this;
    }

    public function getSeenAt(): ?\DateTimeImmutable { return $this->seenAt; }
    public function setSeenAt(?\DateTimeImmutable $seenAt): static {
        $this->seenAt = $seenAt;
        return $this;
    }

    public function getReceiver(): ?User { return $this->receiver; }
    public function setReceiver(?User $receiver): static {
        $this->receiver = $receiver;
        return $this;
    }

    public function getSender(): ?User { return $this->sender; }
    public function setSender(?User $sender): static {
        $this->sender = $sender;
        return $this;
    }
}
