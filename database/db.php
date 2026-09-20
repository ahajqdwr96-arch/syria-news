<?php

$db = new SQLite3(__DIR__ . '/news.sqlite');

$db->exec("PRAGMA foreign_keys = ON;");
