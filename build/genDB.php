<?php
$tables = [
    'waka_city',
    'waka_user',
    'waka_wechat_user',
    'waka_usertoken',
    'waka_mobile_verify'
];

$db = mysqli_init();
$db->options(MYSQLI_OPT_CONNECT_TIMEOUT, 1);
$db->real_connect('127.0.0.1', 'root', 'swerfvcx', 'waka', 3306);
$db->set_charset('utf8mb4');

$noType = [];
foreach ($tables as $table) {
    $res = $db->query('show columns from ' . $table);
    $props = [];
    foreach ($res as $row) {
        $tmp = [];
        $tmp['col'] = $row['Field'];
        $tmp['name'] = lcfirst(implode('',array_map('ucfirst', explode('_', $row['Field']))));
        $tmp['key'] = $row['Key'];
        $tmp['default'] = $row['Default'];
        $tmp['extra'] = $row['Extra'];
        if (stripos($row['Type'], 'int') !== FALSE) {
            $tmp['type'] = gettype(1);
        } elseif (stripos($row['Type'], 'float') !== FALSE) {
            $tmp['type'] = gettype(1.0);
        } elseif (stripos($row['Type'], 'double') !== FALSE) {
            $tmp['type'] = gettype(1.0);
        } elseif (stripos($row['Type'], 'char') !== FALSE) {
            $tmp['type'] = gettype('a');
        } elseif (stripos($row['Type'], 'time') !== FALSE) {
            $tmp['type'] = gettype('a');
        } elseif (stripos($row['Type'], 'date') !== FALSE) {
            $tmp['type'] = gettype('a');
        } elseif (stripos($row['Type'], 'text') !== FALSE) {
            $tmp['type'] = gettype('a');
        } else {
            if (!in_array($row['Type'], $noType)) {
                $noType[] = $row['Type'];
            }
        }
        $props[] = $tmp;
    }
    $className = implode('',array_map('ucfirst', explode('_', preg_replace('/^waka_/', '',$table,1))));
    $fp = fopen(__DIR__ . '/../application/models/ActiveRecord/TAR_' . $className . '.php', 'w');
    flock($fp, LOCK_EX);
    fwrite($fp, "<?php\n");
    fwrite($fp, "class TAR_{$className} extends Wk_ActiveRecord {\n");
    fwrite($fp, "\n");
    fwrite($fp, "    public function __construct(array \$sqlRow = null) {\n");
    fwrite($fp, "        if (isset(\$sqlRow)) {\n");
    foreach ($props as $prop) {
        if ($prop['type'] == gettype(1)) {
            fwrite($fp, "            \$this->{$prop['name']} = isset(\$sqlRow['{$prop['col']}']) ? intval(\$sqlRow['{$prop['col']}']) : null;\n");
        } elseif($prop['type'] == gettype(1.0)) {
            fwrite($fp, "            \$this->{$prop['name']} = isset(\$sqlRow['{$prop['col']}']) ? doubleval(\$sqlRow['{$prop['col']}']) : null;\n");
        }
        else {
            fwrite($fp, "            \$this->{$prop['name']} = \$sqlRow['{$prop['col']}'];\n");
        }
    }
    fwrite($fp, "        }\n");
    fwrite($fp, "    }\n");
    fwrite($fp, "\n");
    fwrite($fp, "    static protected function getTableName() {\n");
    fwrite($fp, "        return '{$table}';\n");
    fwrite($fp, "    }\n");
    fwrite($fp, "\n");
    fwrite($fp, "    static protected function getColNames() {\n");
    fwrite($fp, "        return [\n");
    foreach ($props as $prop) {
        if ($prop['name'] == 'id') continue;
        $type = 's';
        if ($prop['type'] == gettype(1)) $type = 'i';
        if ($prop['type'] == gettype(1.0)) $type = 'd';
        fwrite($fp, "            '{$prop['col']}' => ['name' => '{$prop['name']}', 'type' => '{$type}'],\n");
    }
    fwrite($fp, "        ];\n");
    fwrite($fp, "    }\n");
    fwrite($fp, "\n");
    foreach ($props as $prop) {
        if ($prop['name'] == 'id') continue;
        if (isset($prop['type'])) {
            fwrite($fp, "    /**\n");
            fwrite($fp, "     * @var {$prop['type']}\n");
            fwrite($fp, "     */\n");
        }
        if (isset($prop['default']) && $prop['default'] != 'CURRENT_TIMESTAMP') {
            if ($prop['type'] == gettype('a')) {
                fwrite($fp, "    public \${$prop['name']} = '{$prop['default']}';\n");
            } else {
                fwrite($fp, "    public \${$prop['name']} = {$prop['default']};\n");
            }
        } else {
            fwrite($fp, "    public \${$prop['name']};\n");
        }
        fwrite($fp, "\n");
    }
    fwrite($fp, "}");
    flock($fp, LOCK_UN);
    fclose($fp);
}
var_dump($noType);
