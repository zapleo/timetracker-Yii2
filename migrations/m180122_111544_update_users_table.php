<?php

use yii\db\Migration;

class m180122_111544_update_users_table extends Migration
{
    public function safeUp()
    {
        $this->renameColumn('users', 'rights', 'role');
        $this->update('users', ['role' => 20], ['role' => 1]);
        $this->update('users', ['role' => 10], ['role' => 0]);

        $this->addColumn('users', 'token_hash', $this->string());
    }

    public function safeDown()
    {
        $this->update('users', ['role' => 1], ['role' => 20]);
        $this->update('users', ['role' => 0], ['role' => 10]);
        $this->renameColumn('users', 'role', 'rights');
        $this->dropColumn('users', 'token_hash');
    }
}
