<?php

namespace TaxusSearch\LaravelScoutDriver;

use Laravel\Scout\Builder;
use TaxusSearch\Client as taxus;
use Laravel\Scout\Engines\Engine;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\SoftDeletes;


class LaravelScoutDriverEngine extends Engine
{
  protected $index;

  protected $taxus;

  public function __construct()
  {


  }

  private function getTaxusUniqueId($model)
  {
    $searchableModel = $model->toSearchableArray();
    $id=$model[$model->getKeyName()];
    if(@$searchableModel['type'])
    {
      return $searchableModel['type'].'-'.$id;
    }
    else
    {
      return $id;
    }
  }

  private function getModelId($item,$model)
  {
    $id=$item->id;
    if($item->type!=null)
    {
      $id=preg_replace('/'.$item->type.'-'.'/', '', $id, 1);
      return $id;
    }
    else
    {
      return $id;
    }
  }

  /**
   * Update the given model in the index.
   *
   * @param  \Illuminate\Database\Eloquent\Collection  $models
   * @throws \AlgoliaSearch\AlgoliaException
   * @return void
   */

  public function update($models)
  {
    if ($models->isEmpty()) {
      return;
    }
    foreach ($models as $model)
    {
      $id=$this->getTaxusUniqueId($model);

      $model = $model->toSearchableArray();
      $model['id'] = $id;
      $model = json_encode($model);
      ob_start();
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL,config('taxus.base_url').'/items');
      curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Authorization: '.config('taxus.authorization'),
        'Content-Type: application/json'
      ));
      curl_setopt($ch, CURLOPT_POST, 1);
      curl_setopt($ch, CURLOPT_POSTFIELDS, $model);

      $server_output = curl_exec ($ch);
      curl_close ($ch);
    }


  }

  /**
   * Remove the given model from the index.
   *
   * @param  \Illuminate\Database\Eloquent\Collection  $models
   * @return void
   */

  public function delete($models)
  {
    if ($models->isEmpty()) {
      return;
    }
    foreach ($models as $model) {
      $id=$this->getTaxusUniqueId($model);

      ob_start();
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, config('taxus.base_url').'/items/' . $id);
      curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Authorization: ' . config('taxus.key'),
        'Content-Type: application/json'
      ));
      curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");

      $server_output = curl_exec($ch);

      curl_close($ch);
    }
  }

  /**
   * Run the given search on the engine.
   *
   * @param  \Laravel\Scout\Builder  $builder
   * @return mixed
   */

  public function search(Builder $builder)
  {
    return $this->executeQuery($builder);
  }

  /**
   * Paginate the given search on the engine.
   *
   * @param  \Laravel\Scout\Builder  $builder
   * @param  int  $perPage
   * @param  int  $page
   * @return mixed
   */

  public function paginate(Builder $builder, $perPage, $page)
  {
    $from = ($page - 1) * $perPage;

    return $this->executeQuery($builder, $from, $perPage);
  }

  private function executeQuery(&$builder, $from = 0, $limit = null){


    $filters = "";
    foreach ($this->filters($builder) as $filter)
    {
      $filters .= $filter . '&';
    }


    $filters = rtrim($filters, '&');

    $curl_url = config('taxus.base_url').'/search/' . config('taxus.search_key').'/?query='.urlencode($builder->query);
    $defaultFieldList='fields=id,type';
    $curl_url .='&'.$defaultFieldList;

    $curl_url .='&'.'from='.$from;

    if($limit!=null)
    {
      $curl_url .='&'.'limit='.$limit;
    }


    if(!empty($filters))
    {
      $curl_url .='&'.$filters;
    }


    ob_start();
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $curl_url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
      'Content-Type: application/json'
    ));
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $server_output = json_decode( curl_exec($ch) );
    curl_close($ch);

    return $server_output;

  }

  /**
   * Get the filter array for the query.
   *
   * @param  \Laravel\Scout\Builder  $builder
   * @return array
   */

  protected function filters(Builder $builder)
  {
    return collect($builder->wheres)->map(function ($value, $key) {
      return 'filter['.$key.']='.urlencode($value);
    })->values()->all();
  }

  /**
   * Pluck and return the primary keys of the given results.
   *
   * @param  mixed  $results
   * @return \Illuminate\Support\Collection
   */

  public function mapIds($results)
  {
    $ids = [];
    foreach($results->result->items as $document)
      $ids[] = $document->id;
    return collect($ids);

  }

  /**
   * Map the given results to instances of the given model.
   *
   * @param  mixed  $results
   * @param  \Illuminate\Database\Eloquent\Model  $model
   * @return Collection
   */

  public function map($results, $model)
  {
    if ( count($results->result->items) === 0) {
      return Collection::make();
    }

    return Collection::make($results->result->items)->map(function ($item) use ($model)
    {
      $obj = new $model;
      $id =$this->getModelId($item,$model);
      $obj = $model->find($id);
      return $obj;
    })->filter()->values();

  }

  /**
   * Get the total count from a raw result returned by the engine.
   *
   * @param  mixed  $results
   * @return int
   */

  public function getTotalCount($results)
  {
    return count($results->result->items);
  }

}
