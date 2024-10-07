<?php namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;

abstract class BaseRepository implements Repository
{

    /**
     * @var
     */
    protected $object, $modeler;

    /**
     * CrudableRepository constructor.
     *
     * @throws \Exception
     */
    public function __construct()
    {
        if (!$this->modeler or !class_exists($this->modeler)) {
            throw new \Exception('Please set the $modeler property to your repository path.');
        }

        $this->modeler = new $this->modeler;
    }

    public function getModelar()
    {
        return $this->modeler;
    }

    public function modelPath()
    {
        return get_class($this->modeler);
    }

    /**
     * @param $id
     *
     * @return mixed
     */
    public function count($where)
    {
        return $this->object = $this->modeler->where($where)->count();
    }

    /**
     * @param $id
     *
     * @return mixed
     */
    public function find($id)
    {
        if ($this->object && $this->object->id === $id) {
            return $this->object;
        }

        return $this->object = $this->modeler->findOrfail($id);
    }

    /**
     * @param array $columns
     *
     * @return mixed
     */
    public function first(array $columns = ['*'])
    {
        return $this->modeler->first($columns);
    }

    /**
     * @param array $columns
     *
     * @return mixed
     */
    public function get(array $columns = ['*'])
    {
        return $this->modeler->get($columns);
    }

    public function getByLanguage($language, $where = '', array $columns = ['*'])
    {
        return $where ? $this->modeler->where(['language_id' => $language])->where($where)->get($columns) : $this->modeler->where(['language_id' => $language])->get($columns);
    }

    /**
     * @param array $data
     *
     * @return mixed
     */
    public function store(array $data)
    {
        return $this->modeler->create($data);
    }

    /**
     * @param array $data
     *
     * @return mixed
     */
    public function insert(array $data)
    {
        return $this->modeler->insert($data);
    }

    /**
     * @param $data
     * @param $identifier
     *
     * @return mixed|void
     */
    public function update($data, $identifier)
    {
        $object = ($identifier instanceof Model) ? $identifier : $this->find($identifier);

        return $object->update($data);
    }

    /**
     * @param $data
     * @param $language
     * @param $where
     *
     * @return mixed|void
     */
    public function updateByLangWithId($data, $language, $where)
    {
        return $where ? $this->modeler->where(['language_id' => $language])->where($where)->update($data) : $this->modeler->where(['language_id' => $language])->update($data);
    }

    /**
     * @param $data
     * @param $where
     *
     * @return mixed|void
     */
    public function updateByColumn($data, $where)
    {
        return $this->modeler->where($where)->update($data);
    }

    /**
     * @param $where
     * @param array $columns
     *
     * @return mixed
     */
    public function getWhere($where, array $columns = ['*'])
    {
        return $this->modeler->where($where)->get($columns);
    }

    /**
     * @param $id
     *
     * @return mixed|void
     */
    public function destroy($id)
    {
        $object = $this->find($id);

        return $object->destroy($id);
    }

    public function where($where)
    {
        return $this->modeler->where($where);
    }

    public function getWhereIn($column,$array_data, array $columns = ['*'],$orderBy= [])
    {
        return $this->modeler->whereIn($column,$array_data)->get($columns);
    }


        /**
     * @param $where
     * @param array $columns
     *
     * @return mixed
     */
    public function getFirstWhere($where, array $columns = ['*'])
    {
        return $this->modeler->where($where)->first($columns);
    }

}
