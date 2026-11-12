<?php
// helpers/admin/db.php — ADMIN HELPER: database queries (gumagamit ng db() sa config/database.php)
// Nilo-load lang sa admin pages (routes na may 'admin' => true).

if (!defined('ROOT_PATH')) {
    exit('No direct access');
}

/* =========================================================
 |  DATABASE — para hindi paulit-ulit ang prepare/bind/execute
 |
 |  db_all('SELECT * FROM portproject ORDER BY id DESC');
 |  db_one('SELECT * FROM portproject WHERE id = ?', [$id]);
 |  db_insert('portproject', ['name' => $name, 'image' => $img]);
 |  db_update('portproject', ['name' => $name], 'id = ?', [$id]);
 |  db_delete('portproject', 'id = ?', [$id]);
 * ========================================================= */

/** Prepared query — kusa nang inaalam ang types (i / d / s) */
function db_query(string $sql, array $params = []): mysqli_stmt
{
    $stmt = db()->prepare($sql);
    if ($params) {
        $types = '';
        foreach ($params as $p) {
            $types .= is_int($p) ? 'i' : (is_float($p) ? 'd' : 's');
        }
        $stmt->bind_param($types, ...array_values($params));
    }
    $stmt->execute();
    return $stmt;
}

/** Lahat ng rows */
function db_all(string $sql, array $params = []): array
{
    $result = db_query($sql, $params)->get_result();
    return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
}

/** Isang row lang, o null kung wala */
function db_one(string $sql, array $params = []): ?array
{
    $result = db_query($sql, $params)->get_result();
    return ($result ? $result->fetch_assoc() : null) ?: null;
}

/** INSERT gamit ang array → ibinabalik ang bagong id */
function db_insert(string $table, array $data): int
{
    db_check_names($table, array_keys($data));
    $cols  = '`' . implode('`, `', array_keys($data)) . '`';
    $marks = implode(', ', array_fill(0, count($data), '?'));
    db_query("INSERT INTO `$table` ($cols) VALUES ($marks)", array_values($data));
    return (int) db()->insert_id;
}

/** UPDATE gamit ang array → ibinabalik kung ilang row ang nabago */
function db_update(string $table, array $data, string $where, array $whereParams = []): int
{
    db_check_names($table, array_keys($data));
    $set = implode(', ', array_map(fn($c) => "`$c` = ?", array_keys($data)));
    return db_query("UPDATE `$table` SET $set WHERE $where", [...array_values($data), ...$whereParams])->affected_rows;
}

/** DELETE → ibinabalik kung ilang row ang nabura */
function db_delete(string $table, string $where, array $params = []): int
{
    db_check_names($table, []);
    return db_query("DELETE FROM `$table` WHERE $where", $params)->affected_rows;
}

/** Bantay: letters, numbers at _ lang ang puwede sa table/column name */
function db_check_names(string $table, array $columns): void
{
    foreach ([$table, ...$columns] as $name) {
        if (!preg_match('/^[A-Za-z0-9_]+$/', (string) $name)) {
            throw new InvalidArgumentException("Invalid table/column name: $name");
        }
    }
}

/**
 * Transaction — lahat ng query sa loob ay sabay na mase-save, o walang mase-save kapag may pumalya.
 *
 *   $id = db_transaction(function () {
 *       $id = db_insert('portproject', [...]);
 *       db_insert('portproject_image', [...]);
 *       return $id;
 *   });
 */
function db_transaction(callable $work): mixed
{
    $conn = db();
    $conn->begin_transaction();
    try {
        $result = $work();
        $conn->commit();
        return $result;
    } catch (Throwable $ex) {
        $conn->rollback();
        throw $ex;
    }
}

/** "?, ?, ?" para sa IN (...) — hal. 'id IN (' . db_marks($ids) . ')' */
function db_marks(array $values): string
{
    return implode(', ', array_fill(0, max(1, count($values)), '?'));
}