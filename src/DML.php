<?php

namespace SimpleSQL;

class DML {
    private $currentCategory = '';
    private $statement = '';
    private $from = '';
    private $join = '';
    private $set = '';
    private $where = '';
    private $groupBy = '';
    private $having = '';
    private $orderBy = '';
    private $limit = '';

    private function baseJoin($type, $table, $fieldFrom = '', $fieldJoin = '') {
        $this->currentCategory = 'join';
        if ($this->join) {
            $this->join .= ' ';
        }
        $this->join .= $type . ' JOIN ' . $table;
        return $fieldFrom ? $this->on($fieldFrom . ' = ' . $fieldJoin) : $this;
    }

    public static function query() {
        return new self;
    }

    public function __toString() {
        return trim(join(' ', array_filter([
            $this->statement,
            $this->from,
            $this->join,
            $this->set,
            $this->where,
            $this->groupBy,
            $this->having,
            $this->orderBy,
            $this->limit
        ])));
    }

    public function select(...$values) {
        $this->statement = 'SELECT ' . ($values ? join(', ', $values) : '*');
        return $this;
    }

    public function delete() {
        $this->statement = 'DELETE';
        return $this;
    }

    public function from($value) {
        $this->from = 'FROM ' . ($value instanceof DML ? '(' . $value . ')' : $value);
        return $this;
    }

    public function where($value) {
        $this->currentCategory = 'where';
        $this->where = 'WHERE ' . $value;
        return $this;
    }

    public function whereNot($value) {
        return $this->where('NOT ' . $value);
    }

    public function whereAnd(...$values) {
        return $this->where(join(' AND ', $values));
    }

    public function whereOr(...$values) {
        return $this->where(join(' OR ', $values));
    }

    public function orderBy($value) {
        $this->orderBy = 'ORDER BY ' . $value;
        return $this;
    }

    public function join($table, $fieldFrom = '', $fieldJoin = '') {
        return $this->baseJoin('INNER', $table, $fieldFrom, $fieldJoin);
    }

    public function leftJoin($table, $fieldFrom = '', $fieldJoin = '') {
        return $this->baseJoin('LEFT', $table, $fieldFrom, $fieldJoin);
    }

    public function rightJoin($table, $fieldFrom = '', $fieldJoin = '') {
        return $this->baseJoin('RIGHT', $table, $fieldFrom, $fieldJoin);
    }

    public function on($value) {
        $this->join .= ' ON ' . $value;
        return $this;
    }

    public function and($value) {
        $this->{$this->currentCategory} .= ' AND ' . $value;
        return $this;
    }

    public function or($value) {
        $this->{$this->currentCategory} .= ' OR ' . $value;
        return $this;
    }

    public function limit(int $value = 1) {
        $this->limit = 'LIMIT ' . $value;
        return $this;
    }

    public function insert($value) {
        $this->statement = 'INSERT INTO ' . $value;
        return $this;
    }

    public function update($value) {
        $this->statement = 'UPDATE ' . $value;
        return $this;
    }

    public function set($field, $value) {
        $this->set .= $this->set ? ',' : 'SET';
        $this->set .= ' ' . $field . ' = ' . $value;
        return $this;
    }

    public function fields(...$fields) {
        $this->statement .= ' (' . join(', ', $fields) . ')';
        return $this;
    }

    public function values(...$values) {
        $this->statement .= ' VALUES (' . join(', ', $values) . ')';
        return $this;
    }

    public function groupBy($value) {
        $this->groupBy = 'GROUP BY ' . $value;
        return $this;
    }

    public function having($value) {
        $this->having = 'HAVING ' . $value;
        return $this;
    }
}
