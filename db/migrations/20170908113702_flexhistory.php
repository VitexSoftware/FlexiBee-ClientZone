<?php

use Phinx\Migration\AbstractMigration;

class Flexhistory extends AbstractMigration
{

    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
     *
     * The following commands can be used in this method and Phinx will
     * automatically reverse them when rolling back:
     *
     *    createTable
     *    renameTable
     *    addColumn
     *    renameColumn
     *    addIndex
     *    addForeignKey
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change()
    {
        $table = $this->table('flexihistory');
        $table
            ->addColumn('operation', 'string', ['limit' => 32])
            ->addColumn('evidence', 'string', ['limit' => 128])
            ->addColumn('when', 'timestamp', ['default' => 'CURRENT_TIMESTAMP'])
            ->addColumn('changeid', 'integer',
                ['null' => true, 'signed' => false])
            ->addColumn('recordid', 'integer',
                ['null' => false, 'signed' => false])
            ->addColumn('json', 'text')
            ->addIndex(['recordid', 'evidence'])
            ->addIndex(['changeid'], ['unique' => true])
            ->create();
    }
}
