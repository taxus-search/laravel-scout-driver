<?php

namespace TaxusSearch\LaravelScoutDriver;

use Laravel\Scout\Builder;
use TaxusSearch\Client as taxus;
use Laravel\Scout\Engines\Engine;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\SoftDeletes;


class LaravelScoutDriverEngine extends Engine
{
  /**
   * Index where the models will be saved.
   *
   * @var string
   */
  protected $index;

  /**
   * 
   *
   * @var object
   */
  protected $taxus;

  /**
   * Create a new engine instance.
   *
   * @param 
   * @return void
   */
  public function __construct()
  {


  }

  /**
   * Update the given model in the index.
   *
   * @param  Collection  $models
   * @return void
   */
  public function update($models)
  {
    if ($models->isEmpty()) {
      return;
    }
    foreach ($models as $model)
    {
      $model = $model->toSearchableArray();
      $model['type'] = $model['type'].'-'.$model['id'];
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
   * @param  Collection  $models
   * @return void
   */
  public function delete($models)
  {
    if ($models->isEmpty()) {
      return;
    }
    foreach ($models as $model) {
      $model = $model->toSearchableArray();


      ob_start();
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, config('taxus.base_url').'/items/' . $model->id);
      curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Authorization: ' . config('taxus.authorization'),
        'Content-Type: application/json'
      ));
      curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");

      $server_output = curl_exec($ch);

      curl_close($ch);
    }
  }

  /**
   * Perform the given search on the engine.
   *
   * @param  Builder  $builder
   * @return mixed
   */
  public function search(Builder $builder)
  {

  }

  /**
   * Perform the given search on the engine.
   *
   * @param  Builder  $builder
   * @param  int  $perPage
   * @param  int  $page
   * @return mixed
   */
  public function paginate(Builder $builder, $perPage, $page)
  {

  }

  /**
   * Perform the given search on the engine.
   *
   * @param  Builder  $builder
   * @param  array  $options
   * @return mixed
   */
  protected function performSearch(Builder $builder, array $options = [])
  {

  }

  /**
   * Get the filter array for the query.
   *
   * @param  Builder  $builder
   * @return array
   */
  protected function filters(Builder $builder)
  {

  }

  /**
   * Pluck and return the primary keys of the given results.
   *
   * @param  mixed  $results
   * @return \Illuminate\Support\Collection
   */
  public function mapIds($results)
  {

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

  }

  /**
   * Get the total count from a raw result returned by the engine.
   *
   * @param  mixed  $results
   * @return int
   */
  public function getTotalCount($results)
  {
    return 0;
  }

  /**
   * Generates the sort if theres any.
   *
   * @param  Builder $builder
   * @return array|null
   */
  protected function sort($builder)
  {

  }
}
