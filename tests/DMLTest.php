<?php

use PHPUnit\Framework\TestCase;
use SimpleSQL\DML;

class SQLTest extends TestCase {
    protected function setUp() {
        $this->q = DML::query();
    }

    private function assertSQL(string $s, DML $d) {
        $this->assertEquals($s, (string) $d);
    }

    private function compareSQL(DML $d1, DML $d2) {
        $this->assertEquals((string) $d1, (string) $d2);
    }

    public function testSelectByDefault() {
        $this->assertSQL(
            'SELECT *',
            $this->q->select()
        );
    }

    public function testSelectSingleColumn() {
        $this->assertSQL(
            'SELECT name',
            $this->q->select('name')
        );
    }

    public function testFromSingleTable() {
        $this->assertSQL(
            'FROM user',
            $this->q->from('user')
        );
    }

    public function testSelectSingleColumnFromSingleTable() {
        $this->assertSQL(
            'SELECT name FROM user',
            $this->q->select('name')->from('user')
        );
    }

    public function testSelectMultipleColumnsFromSingleTable() {
        $this->assertSQL(
            'SELECT id, name FROM user',
            $this->q->select('id', 'name')->from('user')
        );
    }

    public function testSelectFromWhere() {
        $this->assertSQL(
            'SELECT name FROM user WHERE id = 1',
            $this->q->select('name')->from('user')->where('id = 1')
        );
    }

    public function testDeleteFromWhere() {
        $this->assertSQL(
            'DELETE FROM user WHERE id = 1',
            $this->q->delete()->from('user')->where('id = 1')
        );
    }

    public function testCallingOrderShouldNotChangeResult() {
        $this->compareSQL(
            DML::query()->select('name')->from('user'),
            DML::query()->from('user')->select('name')
        );
        $this->compareSQL(
            DML::query()->select('name')->from('user')->where('id = 1'),
            DML::query()->where('id = 1')->from('user')->select('name')
        );
    }

    public function testFromSubquery() {
        $subquery = DML::query()->select('name')->from('user');
        $this->assertSQL(
            'SELECT * FROM (SELECT name FROM user)',
            $this->q->select('*')->from($subquery)
        );
    }

    public function testWhereNot() {
        $this->assertSQL(
            'SELECT * FROM user WHERE NOT name = "John"',
            $this->q->select()->from('user')->whereNot('name = "John"')
        );
    }

    public function testWhereAnd() {
        $this->assertSQL(
            'SELECT * FROM user WHERE name = "John" AND id = 1',
            $this->q->select()->from('user')->whereAnd('name = "John"', 'id = 1')
        );
    }

    public function testWhereOr() {
        $this->assertSQL(
            'SELECT * FROM user WHERE name = "John" OR id = 1',
            $this->q->select()->from('user')->whereOr('name = "John"', 'id = 1')
        );
    }

    public function testOrderBy() {
        $this->assertSQL(
            'SELECT * FROM user ORDER BY name',
            $this->q->select()->from('user')->orderBy('name')
        );
    }

    public function testJoin() {
        $this->assertSQL(
            'SELECT user.name FROM user INNER JOIN order ON user.id = order.user_id',
            $this->q->select('user.name')->from('user')->join('order', 'user.id', 'order.user_id')
        );
    }

    public function testLeftJoin() {
        $this->assertSQL(
            'SELECT user.name FROM user LEFT JOIN order ON user.id = order.user_id',
            $this->q->select('user.name')->from('user')->leftJoin('order', 'user.id', 'order.user_id')
        );
    }

    public function testRightJoin() {
        $this->assertSQL(
            'SELECT user.name FROM user RIGHT JOIN order ON user.id = order.user_id',
            $this->q->select('user.name')->from('user')->rightJoin('order', 'user.id', 'order.user_id')
        );
    }

    public function testJoinAlternativeSyntax() {
        $this->assertEquals(
            $this->q->select('user.name')->from('user')->join('order')->on('user.id = order.user_id'),
            $this->q->select('user.name')->from('user')->join('order', 'user.id', 'order.user_id')
        );
    }

    public function testMultipleJoins() {
        $this->assertSQL(
            'SELECT user.name FROM user INNER JOIN order ON user.id = order.user_id LEFT JOIN order_detail ON order.id = order_detail.order_id',
            $this->q
                ->select('user.name')
                ->from('user')
                ->join('order')
                ->on('user.id = order.user_id')
                ->leftJoin('order_detail')
                ->on('order.id = order_detail.order_id')
        );
    }

    public function testAnd() {
        $this->assertSQL(
            'SELECT * FROM user WHERE name = "John" AND id = 1',
            $this->q->select()->from('user')->where('name = "John"')->and('id = 1')
        );
    }

    public function testOr() {
        $this->assertSQL(
            'SELECT * FROM user WHERE name = "John" OR id = 1',
            $this->q->select()->from('user')->where('name = "John"')->or('id = 1')
        );
    }

    public function testAndWithinJoin() {
        $this->assertSQL(
            'SELECT user.name FROM user INNER JOIN order ON user.id = order.user_id AND user.id < 100',
            $this->q
                ->select('user.name')
                ->from('user')
                ->join('order')
                ->on('user.id = order.user_id')
                ->and('user.id < 100')
        );

    }

    public function testLimit() {
        $this->assertSQL(
            'SELECT * FROM user LIMIT 10',
            $this->q->select()->from('user')->limit(10)
        );
    }

    public function testLimitByDefault() {
        $this->assertSQL(
            'SELECT * FROM user LIMIT 1',
            $this->q->select()->from('user')->limit()
        );
    }

    public function testInsert() {
        $this->assertSQL(
            'INSERT INTO user',
            $this->q->insert('user')
        );
    }

    public function testUpdate() {
        $this->assertSQL(
            'UPDATE user',
            $this->q->update('user')
        );
    }

    public function testSet() {
        $this->assertSQL(
            'UPDATE user SET is_active = 1',
            $this->q->update('user')->set('is_active', 1)
        );
    }

    public function testSetMultipleFields() {
        $this->assertSQL(
            'UPDATE user SET is_active = 1, updated = UNIX_TIMESTAMP()',
            $this->q->update('user')->set('is_active', 1)->set('updated', 'UNIX_TIMESTAMP()')
        );
    }

    public function testInsertFields() {
        $this->assertSQL(
            'INSERT INTO user (name, is_active)',
            $this->q->insert('user')->fields('name', 'is_active')
        );
    }

    public function testInsertValues() {
        $this->assertSQL(
            'INSERT INTO user (name, is_active) VALUES ("John", 1)',
            $this->q->insert('user')->fields('name', 'is_active')->values('"John"', 1)
        );
    }

    public function testGroupBy() {
        $this->assertSQL(
            'SELECT is_active FROM user GROUP BY is_active',
            $this->q->select('is_active')->from('user')->groupBy('is_active')
        );
    }

    public function testHaving() {
        $this->assertSQL(
            'SELECT is_active FROM user GROUP BY is_active HAVING COUNT(*) > 1',
            $this->q->select('is_active')->from('user')->groupBy('is_active')->having('COUNT(*) > 1')
        );
    }
}
