<?php namespace App\Services;


abstract class Service
{

    public abstract  function pageTitle(string $title);

    public abstract function jsColumns(array $columns);

    public abstract function tableColumns(array $columns);

    public abstract function addButton(string $route, string $title);

    public abstract function downloadExcel(bool $download);

    public abstract function showAdvancedFilter(bool $download);

    public abstract function filterIgnoreColumns(array $columns);

    public abstract function otherData(array $data);




}
