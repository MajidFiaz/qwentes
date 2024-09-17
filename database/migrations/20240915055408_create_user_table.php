<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateUserTable extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * https://book.cakephp.org/phinx/0/en/migrations.html#the-change-method
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change(): void
    {
        // create the table
        $table = $this->table('users');
        $table->addColumn('givenName', 'string', ['length' => 55, 'null' => true])
            ->addColumn('familyName', 'string', ['length' => 55, 'null' => true])
            ->addColumn('email', 'string', ['length' => 100, 'null' => true])
            ->addIndex(['email'], ['unique' => true])
            ->addColumn('password', 'string', ['length' => 255, 'null' => true])
            ->addColumn('dateOfBirth', 'date', ['null' => true])
            ->addColumn('street', 'string', ['length' => 255])
            ->addColumn('city', 'string', ['length' => 55])
            ->addColumn('postalCode', 'string', ['length' => 10])
            ->addColumn('countryCode', 'string', ['length' => 2])
            ->addColumn('lat', 'float')
            ->addColumn('lng', 'float')
            ->addColumn('createdAt', 'datetime', ['default' => 'CURRENT_TIMESTAMP'])
            ->addColumn('updatedAt', 'datetime', ['default' => 'CURRENT_TIMESTAMP'])
            ->create();
    }
}
