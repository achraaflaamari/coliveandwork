<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


// === Entités ===
use App\Entity\Address;
use App\Entity\ColivingCity;
use App\Entity\User;
use App\Entity\ColivingSpace;
use App\Entity\PrivateSpace;
use App\Entity\Photo;
use App\Entity\Amenity;
use App\Entity\Reservation;
use App\Entity\Message;
use App\Entity\Review;
use App\Entity\VerificationUser;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        /* COLIVING CITIES — 9 villes fixes (réelles, pas Faker) */
        $cityNames = [
            'Paris',
            'Lyon',
            'Marseille',
            'Bordeaux',
            'Toulouse',
            'Lille',
            'Nantes',
            'Strasbourg',
            'Montpellier'
        ];

        foreach ($cityNames as $cityName) {
            $city = new ColivingCity();
            $city->setName($cityName);
            $manager->persist($city);
            $this->addReference('city_' . strtolower($cityName), $city);
        }

        /* ADDRESSES */
        for ($i = 1; $i <= 40; $i++) {
            $address = new Address();
            $address->setStreetNumber((string)$faker->buildingNumber)
                ->setStreetName($faker->streetName)
                ->setPostalCode($faker->postcode)
                ->setOtherCityName($faker->city)
                ->setLatitude($faker->latitude(43.0, 49.5))
                ->setLongitude($faker->longitude(-1.5, 7.5));
            $manager->persist($address);
            $this->addReference("address_$i", $address);
        }

        /* USERS — Admin, employés, propriétaires, clients */

        // 1 admin
        $admin = new User();
        $admin->setEmail('admin@coliveandwork.fr')
            ->setFirstname('Admin')
            ->setLastname('Root')
            ->setRoles(['ROLE_ADMIN'])
            ->setAddress($this->getReference('address_' . rand(1, 40), Address::class));
        $admin->setPassword($this->passwordHasher->hashPassword($admin, 'Admin1234!'));
        $manager->persist($admin);
        $this->addReference('user_admin', $admin);

        // 3 employés
        for ($i = 1; $i <= 3; $i++) {
            $employee = new User();
            $employee->setEmail("employee$i@coliveandwork.fr")
                ->setFirstname($faker->firstName)
                ->setLastname($faker->lastName)
                ->setRoles(['ROLE_EMPLOYEE'])
                ->setAddress($this->getReference('address_' . rand(1, 40), Address::class));
            $employee->setPassword($this->passwordHasher->hashPassword($employee, 'Employee123!'));
            $manager->persist($employee);
            $this->addReference("employee_$i", $employee);
        }

        // --- Compte PROPRIÉTAIRE fixe pour les tests ---
        $ownerTest = new User();
        $ownerTest->setEmail('owner@coliveandwork.fr')
            ->setFirstname('Paul')
            ->setLastname('Proprio')
            ->setRoles(['ROLE_OWNER'])
            ->setAddress($this->getReference('address_' . rand(1, 40), Address::class))
            ->setPassword($this->passwordHasher->hashPassword($ownerTest, 'Owner123!'));
        $manager->persist($ownerTest);
        $this->addReference('owner_test', $ownerTest);

        // 10 propriétaires faker 
        for ($i = 1; $i <= 10; $i++) {
            $owner = new User();
            $owner->setEmail($faker->unique()->safeEmail)
                ->setFirstname($faker->firstName)
                ->setLastname($faker->lastName)
                ->setRoles(['ROLE_OWNER'])
                ->setAddress($this->getReference('address_' . rand(1, 40), Address::class));
            $owner->setPassword($this->passwordHasher->hashPassword($owner, 'Owner123!'));
            $manager->persist($owner);
            $this->addReference("owner_$i", $owner);
        }
        // --- Compte CLIENT fixe pour les tests ---
        $clientTest = new User();
        $clientTest->setEmail('client@coliveandwork.fr')
            ->setFirstname('Clara')
            ->setLastname('Client')
            ->setRoles(['ROLE_USER'])
            ->setAddress($this->getReference('address_' . rand(1, 40), Address::class))
            ->setPassword($this->passwordHasher->hashPassword($clientTest, 'Client123!'));
        $manager->persist($clientTest);
        $this->addReference('client_test', $clientTest);

        // 25 clients faker
        for ($i = 1; $i <= 25; $i++) {
            $client = new User();
            $client->setEmail($faker->unique()->safeEmail)
                ->setFirstname($faker->firstName)
                ->setLastname($faker->lastName)
                ->setRoles(['ROLE_USER'])
                ->setAddress($this->getReference('address_' . rand(1, 40), Address::class));
            $client->setPassword($this->passwordHasher->hashPassword($client, 'Client123!'));
            $manager->persist($client);
            $this->addReference("client_$i", $client);
        }

        // === Utilisateur de test pour JWT ===
        $user = new User();
        $user->setEmail('test@example.com')
            ->setFirstname('Test')
            ->setLastname('User')
            ->setRoles(['ROLE_USER'])
            ->setAddress($this->getReference('address_' . rand(1, 40), Address::class));
        $user->setPassword($this->passwordHasher->hashPassword($user, 'password123'));
        $manager->persist($user);
        $this->addReference('user_test', $user);


        /* AMENITIES */
        $amenityNames = [
            // Équipements Espace privé
            'Lit double',
            'Bureau et chaise',
            'Rangements',
            'Télévision connectée',
            'Douche privative',
            'WC privatif',
            // Équipements Espaces communs
            'Cuisine équipée',
            'Salle à manger',
            'Salon / espace de vie',
            'Espace détente',
            'Buanderie partagée',
            'Espace de travail partagé',
            // Services inclus
            'Wi-Fi haut débit',
            'Maintenance technique',
            'Ménage des espaces communs',
            'Eau et électricité incluses',
            'Chauffage',
            'Assurance habitation',
            'Parking inclus'
        ];

        foreach ($amenityNames as $name) {
            $amenity = new Amenity();
            $amenity->setName($name)
                ->setDescription($faker->sentence())
                ->setAmenityType('service');
            $manager->persist($amenity);
            $this->addReference('amenity_' . strtolower(str_replace(' ', '_', $name)), $amenity);
        }

        /* COLIVING SPACES */
        for ($i = 1; $i <= 20; $i++) {
            $space = new ColivingSpace();
            $space->setTitleColivingSpace($faker->sentence(3))
                ->setDescriptionColivingSpace($faker->paragraph(3))
                ->setAddress($this->getReference('address_' . rand(1, 40), Address::class))
                ->setTotalAreaM2((string)$faker->randomFloat(2, 80, 250))
                ->setCapacityMax($faker->numberBetween(2, 10))
                ->setRoomCount($faker->numberBetween(2, 8))
                ->setColivingCity($this->getReference('city_' . strtolower($faker->randomElement($cityNames)), ColivingCity::class))
                ->setOwner($this->getReference('owner_' . rand(1, 10), User::class))
                ->setHousingType($faker->randomElement(['Appartement', 'Maison', 'Duplex', 'Studio', 'Villa']))
                ->setCreatedAt(new \DateTimeImmutable());

            for ($a = 0; $a < rand(2, 4); $a++) {
                $space->addAmenity(
                    $this->getReference('amenity_' . strtolower(str_replace(' ', '_', $faker->randomElement($amenityNames))), Amenity::class)
                );
            }

            $manager->persist($space);
            $this->addReference("coliving_space_$i", $space);
        }

        /* PHOTOS */
        for ($i = 1; $i <= 60; $i++) {
            $photo = new Photo();
            $photo->setPhotoUrl("https://picsum.photos/seed/photo$i/800/600")
                ->setDescription($faker->optional()->sentence())
                ->setIsMain($faker->boolean())
                ->setUploadedAt(\DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-6 months', 'now')))
                ->setColivingSpace($this->getReference('coliving_space_' . rand(1, 20), ColivingSpace::class));
            $manager->persist($photo);
        }

        /* PRIVATE SPACES */
        for ($i = 1; $i <= 60; $i++) {
            $private = new PrivateSpace();
            $private->setTitlePrivateSpace('Chambre ' . $faker->word())
                ->setDescriptionPrivateSpace($faker->paragraph())
                ->setCapacity($faker->numberBetween(1, 2))
                ->setAreaM2((string)$faker->randomFloat(2, 10, 35))
                ->setPricePerMonth((string)$faker->randomFloat(2, 400, 1200))
                ->setCreatedAt(new \DateTimeImmutable())
                ->setIsActive($faker->boolean())
                ->setColivingSpace($this->getReference('coliving_space_' . rand(1, 20), ColivingSpace::class));

            for ($a = 0; $a < rand(1, 5); $a++) {
                $private->addAmenity(
                    $this->getReference('amenity_' . strtolower(str_replace(' ', '_', $faker->randomElement($amenityNames))), Amenity::class)
                );
            }

            $manager->persist($private);
            $this->addReference("private_space_$i", $private);
        }

        /* RESERVATIONS */
        for ($i = 1; $i <= 50; $i++) {
            $start = $faker->dateTimeBetween('-1 months', '+1 months');
            $end = (clone $start)->modify('+' . rand(5, 20) . ' days');

            $reservation = new Reservation();
            $reservation->setStartDate($start)
                ->setEndDate($end)
                ->setIsForTwo($faker->boolean())
                ->setLodgingTax((string)$faker->randomFloat(2, 20, 100))
                ->setTotalPrice((string)$faker->randomFloat(2, 400, 1500))
                ->setStatus($faker->randomElement(['en attente', 'confirmée', 'annulée', 'terminée']))
                ->setCreatedAt(new \DateTimeImmutable())
                ->setPrivateSpace($this->getReference('private_space_' . rand(1, 60), PrivateSpace::class))
                ->setClient($this->getReference('client_' . rand(1, 25), User::class));

            $manager->persist($reservation);
            $this->addReference("reservation_$i", $reservation);
        }

        /* MESSAGES */
        for ($i = 1; $i <= 120; $i++) {
            $msg = new Message();
            $msg->setContent($faker->sentence(12))
                ->setSendAt(\DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-3 months', 'now')))
                ->setReceiver($this->getReference('owner_' . rand(1, 10), User::class))
                ->setSender($this->getReference('client_' . rand(1, 25), User::class));

            if ($faker->boolean()) {
                $sendAtMutable = \DateTime::createFromImmutable($msg->getSendAt());
                $msg->setSeenAt(
                    \DateTimeImmutable::createFromMutable(
                        $faker->dateTimeBetween($sendAtMutable, 'now')
                    )
                );
            }

            $manager->persist($msg);
        }

        /* REVIEWS */
        /* REVIEWS — 1 seule review par réservation */
        for ($i = 1; $i <= 50; $i++) {
            $review = new Review();
            $review->setRating((string)$faker->randomFloat(1, 1, 5))
                ->setComment($faker->optional()->sentence(15))
                ->setCreatedAt(new \DateTimeImmutable())
                ->setReservation($this->getReference('reservation_' . $i, Reservation::class)); // chaque review liée à une réservation unique
            $manager->persist($review);
        }


        /* VERIFICATION USER */
        for ($i = 1; $i <= 15; $i++) {
            $verification = new VerificationUser();
            $verification->setDocumentType($faker->randomElement(['CNI', 'Passeport', 'Justificatif de domicile']))
                ->setDocumentUrl("https://picsum.photos/seed/document$i/600/400")
                ->setCreatedAt(new \DateTimeImmutable())
                ->setStatus($faker->randomElement(['en attente', 'validé', 'refusé']))
                ->setNotes($faker->optional()->sentence(10))
                ->setOwner($this->getReference('employee_' . rand(1, 3), User::class))
                ->setUser($this->getReference('owner_' . rand(1, 10), User::class))
                ->setVerifiedAt($faker->boolean() ? \DateTimeImmutable::createFromMutable($faker->dateTimeThisYear()) : null);

            $manager->persist($verification);
        }

        // Envoi final en base de données
        $manager->flush();
    }
}
