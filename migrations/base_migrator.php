<?php

abstract class migrate
{
    protected mysqli $mysqli;

    public function __construct()
    {
        $this->mysqli = DBConnection::getInstance()->getConnection();
    }

    abstract public function run(): void;
}
