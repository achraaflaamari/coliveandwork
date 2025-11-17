<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use App\Repository\MessageRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/*Entité représentant un message échangé entre utilisateurs*/
#[ORM\Entity(repositoryClass: MessageRepository::class)]
#[ORM\Table(name: 'message')]
#[ApiResource(
    operations: [
        // Voir tous ses messages (collection)
        new GetCollection(
            security: "is_granted('ROLE_USER')",
            securityMessage: "Vous devez être connecté pour consulter vos messages."
        ),

        // Voir un message individuel
        new Get(
            security: "object.getSender() == user or object.getReceiver() == user or is_granted('ROLE_EMPLOYEE') or is_granted('ROLE_ADMIN')",
            securityMessage: "Vous ne pouvez consulter que vos propres messages."
        ),

        // Envoyer un message
        new Post(
            security: "is_granted('ROLE_USER')",
            securityMessage: "Vous devez être connecté pour envoyer un message."
        ),

        // Marquer un message comme lu
        new Patch(
            security: "object.getReceiver() == user",
            securityMessage: "Seul le destinataire peut marquer un message comme lu."
        )
    ],
    normalizationContext: ['groups' => ['message:read']],
    denormalizationContext: ['groups' => ['message:write']]
)]

/*Permet de filtrer les messages par email de l’expéditeur ou du destinataire*/
#[ApiFilter(SearchFilter::class, properties: [
    'sender.email' => 'iexact',
    'receiver.email' => 'iexact'
])]
class Message
{
    // Identifiant unique du message
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['message:read'])]
    private ?int $id = null;

    // Contenu du message
    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['message:read', 'message:write'])]
    private ?string $content = null;

    // Date d’envoi du message (fixée automatiquement)
    #[ORM\Column]
    #[Groups(['message:read'])]
    private ?\DateTimeImmutable $sendAt = null;

    // Date à laquelle le message a été lu (facultatif)
    #[ORM\Column(nullable: true)]
    #[Groups(['message:read'])]
    private ?\DateTimeImmutable $seenAt = null;

    // Expéditeur du message
    #[ORM\ManyToOne(inversedBy: 'messagesSent')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['message:read', 'message:write'])]
    private ?User $sender = null;

    // Destinataire du message
    #[ORM\ManyToOne(inversedBy: 'messagesReceived')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['message:read', 'message:write'])]
    private ?User $receiver = null;

    public function __construct()
    {
        // Quand un message est créé, on fixe automatiquement sa date d’envoi
        $this->sendAt = new \DateTimeImmutable();
    }

    // === GETTERS & SETTERS ===

    public function getId(): ?int { return $this->id; }

    public function getContent(): ?string { return $this->content; }
    public function setContent(string $content): static { $this->content = $content; return $this; }

    public function getSendAt(): ?\DateTimeImmutable { return $this->sendAt; }
    public function setSendAt(\DateTimeImmutable $sendAt): static { $this->sendAt = $sendAt; return $this; }

    public function getSeenAt(): ?\DateTimeImmutable { return $this->seenAt; }
    public function setSeenAt(?\DateTimeImmutable $seenAt): static { $this->seenAt = $seenAt; return $this; }

    public function getSender(): ?User { return $this->sender; }
    public function setSender(?User $sender): static { $this->sender = $sender; return $this; }

    public function getReceiver(): ?User { return $this->receiver; }
    public function setReceiver(?User $receiver): static { $this->receiver = $receiver; return $this; }
}
