<?php

class JSBJson {

    function __construct($dbname="ExampleTable", $flags="if not exists", bool $load=false, bool $auto_dump=false) {
        $this->name = $dbname;
        $this->flags = $flags;
        $this->auto_increment_name = "id";
        $this->data = json_decode('{}', false);
        $this->columns = [];
        $this->auto_dump = $auto_dump;

        if($load) {
            $this->loads();
        }
    }
    function column($name, $type="", $tag="") {
        $this->columns[] = $name;
        if(str_contains(mb_strtoupper($tag), "AUTO_INCREMENT")) $this->auto_increment_name = $name;
        if($tag!="") $this->data->$name = json_decode('{"type": "'.$type.'", "tag":"'.$tag.'", "values": []}', false);
        else $this->data->$name = json_decode('{"type": "'.$type.'", "values": []}', false);

        if($this->auto_dump) $this->dumps();

        return $this->data->$name;
    }
    function columns(array $columns) {
        foreach($columns as $column) {
            if(count($column) == 3) $this->column($column[0], $column[1], $column[2]);
            elseif(count($column) == 2) $this->column($column[0], $column[1]);
        }
        if($this->auto_dump) $this->dumps();
    }
    function export() {
        $table = new StdClass();
        $table->name = $this->name;
        $table->columns = $this->columns;
        $table->auto_increment_column = $this->auto_increment_name;
        $table->flags = $this->flags;
        $table->data = $this->data;

        return $table;
    }
    function loads() {
        if(file_exists($this->name.".jsb.json")) {
            $data = json_decode(file_get_contents($this->name.".jsb.json"), false);
            $this->name = $data->name;
            $this->columns = $data->columns;
            $this->auto_increment_name = $data->auto_increment_column;
            $this->flags = $data->flags;
            $this->data = $data->data;
        }
    }
    function dumps() {
        $exported_data = json_encode($this->export(), 128);
        file_put_contents($this->name.".jsb.json", $exported_data);
    }
    function get_by_value($column, $value) {
        if (property_exists($this->data, $column)) return array_search($value, $this->data->$column->values);
        else return false;
    }
    function get_by_ids($ids)
    {
        if (gettype($ids) == "integer") {
            $values = [];
            foreach ($this->columns as $clmn) $values[] = $this->data->$clmn->values[$ids];
            return $values;
        } elseif(gettype($ids) == "array") {
            $report_values = [];
            foreach($ids as $id) {
                $values = [];
                foreach ($this->columns as $clmn) $values[] = $this->data->$clmn->values[$id];
                $report_values[] = $values;
            }
            return $report_values;
        }
        if($this->auto_dump) $this->dumps();
    }
    function add(array $values) {
        if(count($values) == count($this->columns)-1) {
            $aiclmn = $this->auto_increment_name;
            $items_in_base = count($this->data->$aiclmn->values);
            $id = $items_in_base;
            $data_add = [$id];
            foreach($values as $v) $data_add[] = $v;
            $i = 0;
            foreach($this->columns as $column) { $this->data->$column->values[] = $data_add[$i]; $i = $i + 1; }

            if($this->auto_dump) $this->dumps();

            return $items_in_base;
        } else {
            if($this->auto_dump) $this->dumps();
            return false;
        }
    }
}
