<?php

namespace App\DataFixtures;

use App\Entity\CardType;
use App\Entity\Currency;
use App\Entity\Editable;
use App\Entity\Permission;
use App\Entity\Role;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        // 1️⃣ Card Types
        $credit = new CardType();
        $credit->setName('credit');
        $credit->setCardType(0);
        $manager->persist($credit);

        $debit = new CardType();
        $debit->setName('debit');
        $debit->setCardType(2);
        $manager->persist($debit);

        // 2️⃣ Currencies
        $eur = new Currency();
        $eur->setCode('EUR');
        $eur->setName('Euro');
        $eur->setSymbol('€');
        $manager->persist($eur);

        $usd = new Currency();
        $usd->setCode('USD');
        $usd->setName('US Dollar');
        $usd->setSymbol('$');
        $manager->persist($usd);

        // 3️⃣ Editable Fields
        $editable1 = new Editable();
        $editable1->setTableName('credit_cards');
        $editable1->setFieldName('name');
        $manager->persist($editable1);

        $editable2 = new Editable();
        $editable2->setTableName('credit_card_features');
        $editable2->setFieldName('notes');
        $manager->persist($editable2);

        $editable3 = new Editable();
        $editable3->setTableName('credit_card_features');
        $editable3->setFieldName('annual_fees');
        $manager->persist($editable3);

        // 4️⃣ Role - Admin
        $adminRole = new Role();
        $adminRole->setName('admin');
        $manager->persist($adminRole);

        // 5️⃣ Permissions - CRUD for all tables + Edit CreditCardEdit
        $permissions = [];
        $tables = ['users', 'roles', 'permissions', 'credit_cards', 'banks', 'card_types', 'currencies', 'editables', 'credit_card_features', 'credit_card_images', 'credit_card_edits', 'api_logs'];

        foreach ($tables as $table) {
            foreach (['create', 'read', 'update', 'delete'] as $action) {
                $permission = new Permission();
                $permission->setName("{$action}_{$table}");
                $manager->persist($permission);
                $permissions["{$action}_{$table}"] = $permission;
            }
        }

        // Extra Edit Permission
        $editCreditCard = new Permission();
        $editCreditCard->setName('edit_credit_card');
        $manager->persist($editCreditCard);

        // Assign All Permissions to Admin Role
        foreach ($permissions as $perm) {
            $adminRole->getPermissions()->add($perm);
        }
        $adminRole->getPermissions()->add($editCreditCard);

        // 6️⃣ Create Admin User
        $adminUser = new User();
        $adminUser->setEmail('r.joveini@gmail.com');
        $adminUser->setPassword($this->passwordHasher->hashPassword($adminUser, '123456'));
        $adminUser->setIsActive(true);
        $adminUser->addRole($adminRole);
        $manager->persist($adminUser);

        // 7️⃣ Save All to Database
        $manager->flush();
    }
}
