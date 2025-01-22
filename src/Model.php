<?php
namespace EasyScf;
use Medoo\Medoo;
use Hashids\Hashids;
class Model
{
    public $table;
    public $fields;
    public $deleteField;
    public $screenFields;
    public $db;
    public $dbRead;
    public $textFields;
    public $textFunctions;

    public $id = 'id';
    public $hashIds = [];
    public $config;
    public $hashidsModel;

    public function __construct($db = null, $dbRead = null)
    {
        $this->db = $db;
        $this->dbRead = $dbRead;
        $this->config = require 'config.php';
        $this->hashidsModel = new Hashids($this->config['hashId']['salt'], $this->config['hashId']['length']);
    }

    public function insertD($data)
    {
        $this->db->insert($this->table, $data);
        if (!$this->db->id()) {
            $this->db->debug()->insert($this->table, $data);
        }
        if ($this->hashIds) {
            return $this->hashidsModel->encode($this->db->id());
        }
        return $this->db->id();
    }

    public function updateD($data, $map, $at = true)
    {
        if (!is_array($map)) {
            $map = [$this->id => $map];
        }
        if ($this->hashIds) {
            foreach ($this->hashIds as $key) {
                $map[$key] && $map[$key] = $this->hashidsModel->decode($map[$key])[0];
            }
        }
        $result = $this->db->update($this->table, $data, $map);
        if ($result->rowCount() == 0) {
            $this->db->debug()->update($this->table, $data, $map);
            return false;
        }
        return true;
    }

    public function deleteD($map)
    {
        if ($this->deleteField) {//软删除
            return $this->updateD([$this->deleteField => date('Y-m-d H:i:s')], $map);
        }
        if (!is_array($map)) {
            $map = [$this->id => $map];
        }
        if ($this->hashIds) {
            foreach ($this->hashIds as $key) {
                $map[$key] && $map[$key] = $this->hashidsModel->decode($map[$key])[0];
            }
        }
        $result = $this->db->delete($this->table, $map);
        if ($result->rowCount() == 0) {
            $this->db->debug()->delete($this->table, $map);
            return false;
        }
        return true;
    }

    /**
     * 默认查询
     * @param $map
     * @param string|array $fields
     * @return array|false
     */
    public function selectD($map, $fields = '*')
    {
        if ($fields == '*') {
            $fields = $this->fields;
        } else {
            $fields = array_intersect($fields, $this->fields);
        }
        $list = $this->dbRead->select($this->table, $fields, $map);
        if (!$list) {
            $this->dbRead->debug()->select($this->table, $fields, $map);
        }
        if ($this->hashIds) {
            foreach ($list as &$v) {
                foreach ($this->hashIds as $key) {
                    $v[$key] && $v[$key] = $this->hashidsModel->encode($v[$key]);
                }
            }
        }
        if ($this->textFields) {
            foreach ($list as &$v) {
                foreach ($this->textFields as $key => $value) {
                    if (!$v[$key]) {
                        continue;
                    }
                    $v[$key . '_text'] = is_array($value) ? $value[$v[$key]] : $this->$value($v[$key]);
                }
            }
        }
        return $list;
    }

    /**
     * 文本字段转换
     *
     * @param $results
     * @return void
     */
    public function resultsText($results)
    {
        foreach ($results as &$result) {
            foreach ($this->textFields as $key => $value) {
                if (!$result[$key]) {
                    continue;
                }
                $result[$key . '_text'] = is_array($value) ? $value[$result[$key]] : $this->$value($result[$key]);
            }
        }
        return $results;
    }

    /**
     * 默认查询
     * @param $map
     * @param $fields
     * @return array|false
     */
    public function getD($map, $fields = '*')
    {
        if ($fields == '*') {
            $fields = $this->fields;
        } else {
            $fields = is_array($fields) ? array_intersect($fields, $this->fields) : $fields;
        }
        if (!is_array($map)) {
            $map = [$this->id => $map];
        }
        $data = $this->dbRead->get($this->table, $fields, $map);
        if (!$data) {
            $this->dbRead->debug()->get($this->table, $fields, $map);
            return false;
        }
        if ($this->hashIds) {
            foreach ($this->hashIds as $key) {
                $data[$key] && $data[$key] = $this->hashidsModel->encode($data[$key]);
            }
        }
        if ($this->textFields) {
            foreach ($this->textFields as $key => $value) {
                if (!$data[$key]) {
                    continue;
                }
                $data[$key . '_text'] = is_array($value) ? $value[$data[$key]] : $this->$value($data[$key]);
            }
        }
        return $data;
    }

    /**
     * 默认聚合
     * @param $map
     * @param string $field
     * @return array|false
     */
    public function countD($map, $field = '*')
    {
        if ($this->hashIds) {
            foreach ($this->hashIds as $key) {
                $map[$key] && $map[$key] = $this->hashidsModel->decode($map[$key])[0];
            }
        }
        $data = $this->dbRead->count($this->table, $field, $map);
        if (!$data) {
            $this->dbRead->debug()->count($this->table, $field, $map);
        }
        return $data;
    }

    public function decodeHashId($hash)
    {
        return $this->hashidsModel->decode($hash)[0];
    }

    public function encodeHashId($id)
    {
        return $this->hashidsModel->encode($id);
    }
}
