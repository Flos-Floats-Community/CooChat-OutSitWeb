<?php

use think\migration\Migrator;
use think\migration\db\Column;

class CreateFriendTable extends Migrator
{
    public function change()
    {
        // 创建好友表
        $friendTable = $this->table('friend', ['prefix' => 'cc_', 'engine' => 'InnoDB']);
        $friendTable->addColumn('user_ccal', 'string', ['limit' => 20, 'comment' => '用户CCAL'])
                   ->addColumn('friend_ccal', 'string', ['limit' => 20, 'comment' => '好友CCAL'])
                   ->addColumn('status', 'integer', ['limit' => 1, 'default' => 0, 'comment' => '状态: 0-待确认, 1-已确认'])
                   ->addColumn('created_at', 'datetime', ['comment' => '创建时间'])
                   ->addColumn('updated_at', 'datetime', ['comment' => '更新时间'])
                   ->create();
        
        // 创建索引
        $this->db->query('CREATE INDEX idx_friend_user ON cc_friend (user_ccal)');
        $this->db->query('CREATE INDEX idx_friend_friend ON cc_friend (friend_ccal)');
        $this->db->query('CREATE INDEX idx_friend_status ON cc_friend (status)');
    }
}
