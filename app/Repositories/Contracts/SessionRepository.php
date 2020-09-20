<?php //app/Repositories/Contracts/BaseRepository.php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Model;
use stdClass;

interface SessionRepository
{
    /**
     * Find a resource by id
     *
     * @param $id
     * @return array|stdClass|null
     */
    public function find(int $id);


    /**
     * Save a resource
     *
     * @param int $id
     * @param stdClass $data
     * @return void
     */
    public function save(int $id, stdClass $data);

    /**
     * Update a resource
     *
     * @param int $id
     * @param stdClass $data
     * @return void
     */
    public function update(int $id, stdClass $data);

    /**
     * Delete a resource
     *
     * @param int $id
     * @return boolean
     */
    public function delete(int $id);
}
