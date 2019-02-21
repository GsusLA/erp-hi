<?php
/**
 * Created by PhpStorm.
 * User: jfesquivel
 * Date: 5/02/19
 * Time: 05:21 PM
 */

namespace App\Repositories;


use Illuminate\Database\Eloquent\Model;

class Repository implements RepositoryInterface
{
    /**
     * @var Model
     */
    protected $model;

    /**
     * Repository constructor.
     * @param Model $model
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function all()
    {
        $this->search();
        $this->scope();
        $this->limit();

        return $this->model->get();
    }

    public function paginate()
    {
        $this->search();
        $this->scope();
        $query = $this->model;
        if (request('sort')) {
            $query = $query->orderBy(request('sort'), request('order'));
        }

        if (request('limit') && request('offset') != '') {
            return $query->paginate(request('limit'), ['*'], 'page', (request('offset') / request('limit')) + 1);
        }

        return $query->paginate(10);
    }

    public function update(array $data, $id)
    {
        $item = $this->show($id);
        $item->update($data);

        return $item;
    }

    public function with($relations)
    {
        $this->model = $this->model->with($relations);
    }

    public function scope()
    {
        if (request('scope')) {
            $scope = request('scope');

            if (is_string($scope)) {
                $scope = [$scope];
            }

            foreach ($scope as $s) {
                $explode = explode(':', $s);
                $fn = $explode[0];
                $params = isset($explode[1]) ? $explode[1] : null;
                $this->model = $this->model->$fn($params);
            }
        }
    }

    public function where($where)
    {
        $this->model = $this->model->where($where);
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function delete(array $data, $id)
    {
        $this->model->destroy($id);
    }

    public function show($id)
    {
        $this->scope();
        return $this->model->find($id);
    }

    private function search()
    {
        if (request()->has('search'))
        {
            $this->model = $this->model->where(function($query) {
                foreach ($this->model->searchable as $col)
                {
                    $query->orWhere($col, 'LIKE', '%' . request('search') . '%');
                }
            });
        }
    }

    private function limit()
    {
        if (request()->has('limit')) {
            $this->model = $this->model->limit(request('limit'));
        }
    }
}