<?php

namespace App\Models;

use CodeIgniter\Model;

class SettingModel extends Model
{
    protected $table      = 'settings';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'name',
        'value',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function getValue(string $name, $default = null)
    {
        $record = $this->where('name', $name)->first();
        return $record ? $record['value'] : $default;
    }

    public function setValue(string $name, $value): bool
    {
        $record = $this->where('name', $name)->first();

        if ($record) {
            return (bool) $this->update($record['id'], ['value' => $value]);
        }

        return (bool) $this->insert(['name' => $name, 'value' => $value]);
    }
}
